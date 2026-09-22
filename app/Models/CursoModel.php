<?php namespace App\Models;

use CodeIgniter\Model;

class CursoModel extends Model
{
    protected $table         = 'tbl_cursos';
    protected $primaryKey    = 'idCurso';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'idOficina', 'idModalidad', 'codigo', 'nombre', 'nombreIngles', 'nivelSTCW',
        'reglas', 'reglasIngles', 'fechaIni', 'fechaFin', 'imo', 'estado',
    ];

    // Cada curso pertenece a una oficina (tbl_cursos.idOficina). Lo que varia por
    // categoria (New/Refresher), que determina las horas via tbl_curso_categoria_horas.
    private function builderFiltrado(array $f)
    {
        $builder = $this->db->table('tbl_cursos')
            ->select('tbl_cursos.*, tbl_modalidad.descripcion AS modalidad, tbl_oficina.nombre AS oficina, tbl_oficina.descripcion AS oficinaDescripcion')
            ->join('tbl_modalidad', 'tbl_modalidad.idModalidad = tbl_cursos.idModalidad', 'left')
            ->join('tbl_oficina', 'tbl_oficina.idOficina = tbl_cursos.idOficina', 'left');

        if (!empty($f['idOficina'])) {
            $builder->where('tbl_cursos.idOficina', (int) $f['idOficina']);
        }

        if (!empty($f['idCategoria'])) {
            $builder->join(
                'tbl_curso_categoria_horas',
                'tbl_curso_categoria_horas.idCurso = tbl_cursos.idCurso AND tbl_curso_categoria_horas.idCategoria = ' . (int) $f['idCategoria']
            );
        }
        if (!empty($f['busqueda'])) {
            $builder->groupStart()
                ->like('tbl_cursos.nombre', $f['busqueda'])
                ->orLike('tbl_cursos.nombreIngles', $f['busqueda'])
                ->orLike('tbl_cursos.codigo', $f['busqueda'])
                ->groupEnd();
        }
        if (!empty($f['idModalidad'])) {
            $builder->where('tbl_cursos.idModalidad', $f['idModalidad']);
        }
        if (!empty($f['estado'])) {
            $builder->where('tbl_cursos.estado', $f['estado']);
        }

        return $builder;
    }

    public function listar(array $filtros = [], int $page = 1, int $perPage = 25)
    {
        $offset = max(0, ($page - 1) * $perPage);

        return $this->builderFiltrado($filtros)
            ->orderBy('tbl_cursos.nombre', 'ASC')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();
    }

    public function contar(array $filtros = []): int
    {
        return $this->builderFiltrado($filtros)->countAllResults();
    }

    public function conHoras(int $idCurso)
    {
        $curso = $this->find($idCurso);
        if (!$curso) {
            return null;
        }
        $curso->horas = $this->db->table('tbl_curso_categoria_horas')
            ->where('idCurso', $idCurso)
            ->get()
            ->getResult();

        return $curso;
    }

    public function guardarHoras(int $idCurso, array $horasPorCategoria): void
    {
        foreach ($horasPorCategoria as $idCategoria => $horas) {
            if ($horas === null || $horas === '') {
                continue;
            }
            $this->db->table('tbl_curso_categoria_horas')->replace([
                'idCurso'     => $idCurso,
                'idCategoria' => $idCategoria,
                'horas'       => $horas,
            ]);
        }
    }

    public function modalidades()
    {
        return $this->db->table('tbl_modalidad')->where('estado', 'A')->orderBy('idModalidad')->get()->getResult();
    }

    public function categorias()
    {
        return $this->db->table('tbl_categoria')->where('estado', 'A')->orderBy('idCategoria')->get()->getResult();
    }
}
