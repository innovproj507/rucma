<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'tbl_users';
    protected $primaryKey    = 'idUser';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'idOficina', 'nombre', 'apellido', 'correo', 'usuario', 'password', 'estado', 'ultimo_acceso',
    ];

    public function findActiveByUsername(string $usuario)
    {
        return $this->groupStart()
                ->where('usuario', $usuario)
                ->orWhere('correo', $usuario)
            ->groupEnd()
            ->where('estado', 'A')
            ->first();
    }

    public function touchLastAccess(int $idUser): void
    {
        $this->update($idUser, ['ultimo_acceso' => date('Y-m-d H:i:s')]);
    }

    public function getRoles(int $idUser): array
    {
        return $this->db->table('tbl_user_roles')
            ->select('tbl_roles.idRol, tbl_roles.nombre')
            ->join('tbl_roles', 'tbl_roles.idRol = tbl_user_roles.idRol')
            ->where('tbl_user_roles.idUser', $idUser)
            ->where('tbl_roles.estado', 'A')
            ->get()
            ->getResult();
    }

    // Union de los permisos de todos los roles activos del usuario.
    public function getPermisos(int $idUser): array
    {
        $filas = $this->db->table('tbl_user_roles ur')
            ->select('p.codigo')
            ->distinct()
            ->join('tbl_roles r', 'r.idRol = ur.idRol')
            ->join('tbl_rol_permisos rp', 'rp.idRol = ur.idRol')
            ->join('tbl_permisos p', 'p.idPermiso = rp.idPermiso')
            ->where('ur.idUser', $idUser)
            ->where('r.estado', 'A')
            ->get()
            ->getResult();

        return array_map(fn ($f) => $f->codigo, $filas);
    }

    private function builderFiltrado(array $f)
    {
        $builder = $this->db->table('tbl_users')
            ->select('tbl_users.idUser, tbl_users.idOficina, tbl_users.nombre, tbl_users.apellido, tbl_users.correo,
                      tbl_users.usuario, tbl_users.estado, tbl_users.ultimo_acceso,
                      tbl_oficina.nombre AS oficina, tbl_oficina.descripcion AS oficinaDescripcion')
            ->join('tbl_oficina', 'tbl_oficina.idOficina = tbl_users.idOficina', 'left');

        if (!empty($f['idRol'])) {
            $builder->join(
                'tbl_user_roles',
                'tbl_user_roles.idUser = tbl_users.idUser AND tbl_user_roles.idRol = ' . (int) $f['idRol']
            );
        }

        if (!empty($f['busqueda'])) {
            $builder->groupStart()
                ->like('tbl_users.nombre', $f['busqueda'])
                ->orLike('tbl_users.apellido', $f['busqueda'])
                ->orLike('tbl_users.usuario', $f['busqueda'])
                ->orLike('tbl_users.correo', $f['busqueda'])
                ->groupEnd();
        }
        if (!empty($f['idOficina'])) {
            $builder->where('tbl_users.idOficina', $f['idOficina']);
        }
        if (!empty($f['estado'])) {
            $builder->where('tbl_users.estado', $f['estado']);
        }

        return $builder;
    }

    public function listar(array $filtros = [], int $page = 1, int $perPage = 25)
    {
        $offset = max(0, ($page - 1) * $perPage);

        $resultado = $this->builderFiltrado($filtros)
            ->orderBy('tbl_users.nombre')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();

        foreach ($resultado as $u) {
            $u->roles = $this->getRoles($u->idUser);
        }

        return $resultado;
    }

    public function contar(array $filtros = []): int
    {
        return $this->builderFiltrado($filtros)->countAllResults();
    }

    public function conRoles(int $idUser)
    {
        $usuario = $this->select('idUser, idOficina, nombre, apellido, correo, usuario, estado, ultimo_acceso')->find($idUser);
        if (!$usuario) {
            return null;
        }
        $usuario->idRoles = array_map(fn ($r) => (int) $r->idRol, $this->getRoles($idUser));

        return $usuario;
    }

    public function asignarRoles(int $idUser, array $idRoles): void
    {
        $this->db->table('tbl_user_roles')->where('idUser', $idUser)->delete();

        foreach (array_unique($idRoles) as $idRol) {
            $this->db->table('tbl_user_roles')->insert(['idUser' => $idUser, 'idRol' => $idRol]);
        }
    }

    public function oficinas()
    {
        return $this->db->table('tbl_oficina')->where('estado', 'A')->orderBy('nombre')->get()->getResult();
    }

    public function rolesDisponibles()
    {
        return $this->db->table('tbl_roles')->where('estado', 'A')->orderBy('nombre')->get()->getResult();
    }
}
