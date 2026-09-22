<?php namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table         = 'tbl_roles';
    protected $primaryKey    = 'idRol';
    protected $returnType    = 'object';
    protected $allowedFields = ['nombre', 'descripcion', 'estado'];

    private function builderFiltrado(array $f)
    {
        $builder = $this->db->table('tbl_roles');

        if (!empty($f['busqueda'])) {
            $builder->groupStart()
                ->like('nombre', $f['busqueda'])
                ->orLike('descripcion', $f['busqueda'])
                ->groupEnd();
        }
        if (!empty($f['estado'])) {
            $builder->where('estado', $f['estado']);
        }

        return $builder;
    }

    public function listar(array $filtros = [], int $page = 1, int $perPage = 25)
    {
        $offset = max(0, ($page - 1) * $perPage);

        $roles = $this->builderFiltrado($filtros)
            ->orderBy('nombre')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();

        foreach ($roles as $r) {
            $r->totalUsuarios = $this->db->table('tbl_user_roles')->where('idRol', $r->idRol)->countAllResults();
            $r->totalPermisos = $this->db->table('tbl_rol_permisos')->where('idRol', $r->idRol)->countAllResults();
        }

        return $roles;
    }

    public function contar(array $filtros = []): int
    {
        return $this->builderFiltrado($filtros)->countAllResults();
    }

    public function totalPermisosDisponibles(): int
    {
        return $this->db->table('tbl_permisos')->countAllResults();
    }

    public function conPermisos(int $idRol)
    {
        $rol = $this->find($idRol);
        if (!$rol) {
            return null;
        }
        $rol->idPermisos = array_map(
            fn ($p) => (int) $p->idPermiso,
            $this->db->table('tbl_rol_permisos')->select('idPermiso')->where('idRol', $idRol)->get()->getResult()
        );

        return $rol;
    }

    public function asignarPermisos(int $idRol, array $idPermisos): void
    {
        $this->db->table('tbl_rol_permisos')->where('idRol', $idRol)->delete();

        foreach (array_unique($idPermisos) as $idPermiso) {
            $this->db->table('tbl_rol_permisos')->insert(['idRol' => $idRol, 'idPermiso' => $idPermiso]);
        }
    }

    // Catalogo completo de permisos, agrupado por modulo, para dibujar la matriz.
    public function permisosPorModulo(): array
    {
        $permisos = $this->db->table('tbl_permisos')->orderBy('modulo')->orderBy('codigo')->get()->getResult();

        $agrupado = [];
        foreach ($permisos as $p) {
            $agrupado[$p->modulo][] = $p;
        }

        return $agrupado;
    }
}
