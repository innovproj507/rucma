<?php namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $table = 'tbl_certificados';

    public function totalCertificadosEmitidos(?int $idOficina = null): int
    {
        $builder = $this->db->table('tbl_certificados')->where('estado', 'E');
        if ($idOficina) {
            $builder->where('idOficina', $idOficina);
        }
        return $builder->countAllResults();
    }

    public function totalEstudiantes(?int $idOficina = null): int
    {
        if (!$idOficina) {
            return $this->db->table('tbl_estudiantes')->countAllResults();
        }
        return $this->db->table('tbl_certificados')
            ->select('idEstudiante')
            ->where('idOficina', $idOficina)
            ->groupBy('idEstudiante')
            ->get()
            ->getNumRows();
    }

    public function totalCursosActivos(?int $idOficina = null): int
    {
        $builder = $this->db->table('tbl_cursos')->where('estado', 'A');
        if ($idOficina) {
            $builder->where('idOficina', $idOficina);
        }

        return $builder->countAllResults();
    }

    public function certificadosHoy(?int $idOficina = null): int
    {
        $builder = $this->db->table('tbl_certificados')
            ->where('estado', 'E')
            ->where('fechaEmision', date('Y-m-d'));
        if ($idOficina) {
            $builder->where('idOficina', $idOficina);
        }
        return $builder->countAllResults();
    }

    public function certificadosEsteMes(?int $idOficina = null): int
    {
        $builder = $this->db->table('tbl_certificados')
            ->where('estado', 'E')
            ->where('fechaEmision >=', date('Y-m-01'));
        if ($idOficina) {
            $builder->where('idOficina', $idOficina);
        }
        return $builder->countAllResults();
    }

    public function certificadosMesAnterior(?int $idOficina = null): int
    {
        $inicio = date('Y-m-01', strtotime('first day of last month'));
        $fin    = date('Y-m-t', strtotime('last day of last month'));
        $builder = $this->db->table('tbl_certificados')
            ->where('estado', 'E')
            ->where('fechaEmision >=', $inicio)
            ->where('fechaEmision <=', $fin);
        if ($idOficina) {
            $builder->where('idOficina', $idOficina);
        }
        return $builder->countAllResults();
    }

    public function estudiantesNuevosMes(?int $idOficina = null): int
    {
        if (!$idOficina) {
            return $this->db->table('tbl_estudiantes')
                ->where('created_at >=', date('Y-m-01'))
                ->countAllResults();
        }
        return $this->db->table('tbl_certificados c')
            ->select('e.idEstudiante')
            ->join('tbl_estudiantes e', 'e.idEstudiante = c.idEstudiante')
            ->where('c.idOficina', $idOficina)
            ->where('e.created_at >=', date('Y-m-01'))
            ->groupBy('e.idEstudiante')
            ->get()
            ->getNumRows();
    }

    public function certificadosPorVencer(int $dias = 30, ?int $idOficina = null): int
    {
        $builder = $this->db->table('tbl_certificados')
            ->where('estado', 'E')
            ->where('fechaExpiracion >=', date('Y-m-d'))
            ->where('fechaExpiracion <=', date('Y-m-d', strtotime("+{$dias} days")));
        if ($idOficina) {
            $builder->where('idOficina', $idOficina);
        }
        return $builder->countAllResults();
    }

    public function oficinasSinPlantilla(): int
    {
        return $this->db->table('tbl_oficina')
            ->where('estado', 'A')
            ->where('idPlantillaCertificado', null)
            ->countAllResults();
    }

    public function usuariosInactivos(): int
    {
        return $this->db->table('tbl_users')->where('estado !=', 'A')->countAllResults();
    }

    // Certificados emitidos por mes, ultimos $meses meses (incluyendo el actual),
    // en orden cronologico. Rellena con 0 los meses sin certificados.
    public function certificadosPorMes(int $meses = 4, ?int $idOficina = null): array
    {
        $builder = $this->db->table('tbl_certificados')
            ->select("DATE_FORMAT(fechaEmision, '%Y-%m') AS mes, COUNT(*) AS total", false)
            ->where('estado', 'E')
            ->where('fechaEmision >=', date('Y-m-01', strtotime('-' . ($meses - 1) . ' months')))
            ->groupBy('mes');
        if ($idOficina) {
            $builder->where('idOficina', $idOficina);
        }
        $filas = $builder->get()->getResult();
        $porMes = [];
        foreach ($filas as $f) {
            $porMes[$f->mes] = (int) $f->total;
        }

        $resultado = [];
        for ($i = $meses - 1; $i >= 0; $i--) {
            $clave = date('Y-m', strtotime("-{$i} months"));
            $resultado[] = (object) [
                'mes'   => $clave,
                'label' => $this->etiquetaMes($clave),
                'total' => $porMes[$clave] ?? 0,
            ];
        }

        return $resultado;
    }

    // Nombre de mes en espanol para el mini-grafico mensual (evita depender de
    // la configuracion regional del servidor, que en Windows no siempre tiene
    // locales es_ES instalados).
    private function etiquetaMes(string $anioMes): string
    {
        static $meses = [1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'];
        [$anio, $mes] = explode('-', $anioMes);
        return $meses[(int) $mes] . ' ' . $anio;
    }

    // Solo tiene sentido cuando se estan viendo "Todas las oficinas" -- no se
    // debe llamar con una oficina especifica (filtrarla ahi mostraria un solo
    // valor y no aporta nada al usuario scoped a su propia oficina).
    public function certificadosPorOficina(): array
    {
        return $this->db->table('tbl_oficina o')
            ->select('o.nombre, o.descripcion, COUNT(c.idCertificado) AS total')
            ->join('tbl_certificados c', 'c.idOficina = o.idOficina AND c.estado = "E"', 'left')
            ->groupBy('o.idOficina')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResult();
    }

    public function ultimosCertificados(int $limit = 8, ?int $idOficina = null)
    {
        $builder = $this->db->table('tbl_certificados c')
            ->select('c.idCertificado, c.codigo, c.fechaEmision, c.created_at, c.estado, e.nombre, e.apellido, c.nombreCursoEmitido, o.nombre AS oficina')
            ->join('tbl_estudiantes e', 'e.idEstudiante = c.idEstudiante')
            ->join('tbl_oficina o', 'o.idOficina = c.idOficina', 'left');

        if ($idOficina) {
            $builder->where('c.idOficina', $idOficina);
        }

        return $builder->orderBy('c.idCertificado', 'DESC')->limit($limit)->get()->getResult();
    }
}
