<?php namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table         = 'tbl_estudiantes';
    protected $primaryKey    = 'idEstudiante';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'nombre', 'apellido', 'dni', 'idPais', 'nacionalidad',
        'fechaNac', 'lugarNac', 'sexo', 'email', 'telefono',
    ];

    private function builderFiltrado(?string $busqueda, ?int $idOficina, ?int $idPais = null)
    {
        $builder = $this->db->table('tbl_estudiantes')
            ->select('tbl_estudiantes.*, tbl_pais.nombre AS pais')
            ->join('tbl_pais', 'tbl_pais.idPais = tbl_estudiantes.idPais', 'left');

        if ($idOficina) {
            // Un estudiante no pertenece a una oficina directamente -- solo
            // se muestra si tiene al menos un certificado en esta oficina.
            $builder->where(
                'EXISTS (SELECT 1 FROM tbl_certificados c WHERE c.idEstudiante = tbl_estudiantes.idEstudiante AND c.idOficina = ' . (int) $idOficina . ')',
                null,
                false
            );
        }

        if ($idPais) {
            $builder->where('tbl_estudiantes.idPais', $idPais);
        }

        if (!empty($busqueda)) {
            $builder->groupStart()
                ->like('tbl_estudiantes.nombre', $busqueda)
                ->orLike('tbl_estudiantes.apellido', $busqueda)
                ->orLike('tbl_estudiantes.dni', $busqueda)
                ->groupEnd();
        }

        return $builder;
    }

    public function listar($busqueda = null, int $page = 1, int $perPage = 25, ?int $idOficina = null, ?int $idPais = null)
    {
        $offset = max(0, ($page - 1) * $perPage);

        return $this->builderFiltrado($busqueda, $idOficina, $idPais)
            ->orderBy('tbl_estudiantes.idEstudiante', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();
    }

    public function contar(?string $busqueda = null, ?int $idOficina = null, ?int $idPais = null): int
    {
        return $this->builderFiltrado($busqueda, $idOficina, $idPais)->countAllResults();
    }

    public function tieneCertificadoEnOficina(int $idEstudiante, int $idOficina): bool
    {
        return (bool) $this->db->table('tbl_certificados')
            ->where('idEstudiante', $idEstudiante)
            ->where('idOficina', $idOficina)
            ->countAllResults();
    }

    public function paises()
    {
        return $this->db->table('tbl_pais')
            ->where('estado', 'A')
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResult();
    }
}
