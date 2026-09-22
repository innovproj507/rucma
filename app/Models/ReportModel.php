<?php namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table = 'tbl_certificados';

    private function baseCertificados(array $f)
    {
        $builder = $this->db->table('tbl_certificados c')
            ->select('c.idCertificado, c.codigo, c.fechaEmision, c.fechaExpiracion, c.estado,
                      e.nombre, e.apellido, e.dni, c.nombreCursoEmitido, o.nombre AS oficina')
            ->join('tbl_estudiantes e', 'e.idEstudiante = c.idEstudiante')
            ->join('tbl_oficina o', 'o.idOficina = c.idOficina', 'left');

        if (!empty($f['busqueda'])) {
            $builder->groupStart()
                ->like('e.nombre', $f['busqueda'])
                ->orLike('e.apellido', $f['busqueda'])
                ->orLike('e.dni', $f['busqueda'])
                ->orLike('c.codigo', $f['busqueda'])
                ->groupEnd();
        }
        if (!empty($f['idOficina'])) {
            $builder->where('c.idOficina', $f['idOficina']);
        }
        if (!empty($f['estado'])) {
            $builder->where('c.estado', $f['estado']);
        }
        if (!empty($f['desde'])) {
            $builder->where('c.fechaEmision >=', $f['desde']);
        }
        if (!empty($f['hasta'])) {
            $builder->where('c.fechaEmision <=', $f['hasta']);
        }

        return $builder;
    }

    public function certificadosEmitidos(array $filtros, int $limit = 500)
    {
        return $this->baseCertificados($filtros)->orderBy('c.created_at', 'DESC')->orderBy('c.idCertificado', 'DESC')->limit($limit)->get()->getResult();
    }

    public function certificadosPaginados(array $filtros, int $page = 1, int $perPage = 25)
    {
        $offset = max(0, ($page - 1) * $perPage);

        return $this->baseCertificados($filtros)
            ->orderBy('c.created_at', 'DESC')->orderBy('c.idCertificado', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();
    }

    public function contarCertificados(array $filtros): int
    {
        return $this->baseCertificados($filtros)->countAllResults();
    }

    public function certificadosPorVencer(int $dias, ?int $idOficina = null)
    {
        $builder = $this->db->table('tbl_certificados c')
            ->select('c.codigo, e.nombre, e.apellido, e.dni, c.nombreCursoEmitido, o.nombre AS oficina,
                      c.fechaExpiracion, DATEDIFF(c.fechaExpiracion, CURDATE()) AS diasRestantes')
            ->join('tbl_estudiantes e', 'e.idEstudiante = c.idEstudiante')
            ->join('tbl_oficina o', 'o.idOficina = c.idOficina', 'left')
            ->where('c.estado', 'E')
            ->where('c.fechaExpiracion >=', date('Y-m-d'))
            ->where('c.fechaExpiracion <=', date('Y-m-d', strtotime("+{$dias} days")));

        if ($idOficina) {
            $builder->where('c.idOficina', $idOficina);
        }

        return $builder->orderBy('c.fechaExpiracion', 'ASC')->get()->getResult();
    }

    public function certificadosPorCurso(?int $idOficina = null)
    {
        $builder = $this->db->table('tbl_certificados c')
            ->select('c.nombreCursoEmitido AS curso, COUNT(*) AS total')
            ->where('c.estado', 'E');

        if ($idOficina) {
            $builder->where('c.idOficina', $idOficina);
        }

        return $builder->groupBy('c.nombreCursoEmitido')->orderBy('total', 'DESC')->get()->getResult();
    }

    public function estudiantesConCertificados(?string $busqueda = null, ?int $idOficina = null)
    {
        $builder = $this->db->table('tbl_estudiantes e')
            ->select('e.idEstudiante, e.nombre, e.apellido, e.dni, p.nombre AS pais, COUNT(c.idCertificado) AS totalCertificados')
            ->join('tbl_pais p', 'p.idPais = e.idPais', 'left');

        if ($idOficina) {
            // Solo estudiantes con al menos un certificado en esta oficina,
            // y el conteo es solo de esta oficina (no el total global).
            $builder->join('tbl_certificados c', 'c.idEstudiante = e.idEstudiante AND c.estado = "E" AND c.idOficina = ' . (int) $idOficina, 'inner');
        } else {
            $builder->join('tbl_certificados c', 'c.idEstudiante = e.idEstudiante AND c.estado = "E"', 'left');
        }

        $builder->groupBy('e.idEstudiante');

        if (!empty($busqueda)) {
            $builder->groupStart()
                ->like('e.nombre', $busqueda)
                ->orLike('e.apellido', $busqueda)
                ->orLike('e.dni', $busqueda)
                ->groupEnd();
        }

        return $builder->orderBy('e.nombre', 'ASC')->get()->getResult();
    }

    public function oficinas()
    {
        return $this->db->table('tbl_oficina')->where('estado', 'A')->orderBy('nombre')->get()->getResult();
    }
}
