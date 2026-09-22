<?php

namespace App\Controllers;

use App\Models\UserModel;

class Usuario extends BaseController
{
    private const PER_PAGE_PERMITIDOS = [5, 10, 25, 50, 100];

    private function filtrosGet(): array
    {
        $get = $this->request->getGet();
        return [
            'busqueda'  => $get['busqueda'] ?? null,
            'idOficina' => $get['idOficina'] ?? null,
            'idRol'     => $get['idRol'] ?? null,
            'estado'    => $get['estado'] ?? null,
        ];
    }

    private function perPageGet(): int
    {
        $perPage = (int) ($this->request->getGet('perPage') ?: 25);
        return in_array($perPage, self::PER_PAGE_PERMITIDOS, true) ? $perPage : 25;
    }

    public function index()
    {
        if ($resp = $this->exigirPermiso('usuarios.ver')) {
            return $resp;
        }

        $session = session();
        $model = new UserModel();
        $perPage = $this->perPageGet();
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $filtros = $this->filtrosGet();

        $data['title']    = 'Sistema Rucma Certifícate';
        $data['nombre']   = $session->get('nombre');
        $data['usuario']  = $session->get('usuario');
        $data['page']     = 'usuarios';
        $data['datos']    = $model->listar($filtros, $page, $perPage);
        $data['total']    = $model->contar($filtros);
        $data['pagina']   = $page;
        $data['perPage']  = $perPage;
        $data['perPageOpciones'] = self::PER_PAGE_PERMITIDOS;
        $data['oficinas'] = $model->oficinas();
        $data['rolesDisponibles'] = $model->rolesDisponibles();
        $data['puedeCrear']    = $this->tienePermiso('usuarios.crear');
        $data['puedeEditar']   = $this->tienePermiso('usuarios.editar');
        $data['puedeEliminar'] = $this->tienePermiso('usuarios.eliminar');

        return view('header', $data) . view('usuarios') . view('footer');
    }
    //----------------------------------------------------------------------------------------------
    public function filter()
    {
        if ($resp = $this->exigirPermiso('usuarios.ver')) {
            return $resp;
        }

        $model = new UserModel();
        $perPage = $this->perPageGet();
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $filtros = $this->filtrosGet();

        $datos = $model->listar($filtros, $page, $perPage);
        $total = $model->contar($filtros);

        header('Content-Type: application/json; charset=utf-8');
        return $this->response->setJSON([
            'datos'    => $datos,
            'total'    => $total,
            'pagina'   => $page,
            'perPage'  => $perPage,
            'ultimaPagina' => max(1, (int) ceil($total / $perPage)),
            'puedeEditar'   => $this->tienePermiso('usuarios.editar'),
            'puedeEliminar' => $this->tienePermiso('usuarios.eliminar'),
        ]);
    }
    //----------------------------------------------------------------------------------------------
    public function get($id)
    {
        if ($resp = $this->exigirPermiso('usuarios.ver')) {
            return $resp;
        }

        $model = new UserModel();
        $data = $model->conRoles((int) $id);

        header('Content-Type: application/json; charset=utf-8');
        return $this->response->setJSON($data);
    }
    //----------------------------------------------------------------------------------------------
    public function save()
    {
        $model = new UserModel();
        $post = $this->request->getPost();
        $id = !empty($post['idUser']) ? (int) $post['idUser'] : null;

        if ($resp = $this->exigirPermiso($id ? 'usuarios.editar' : 'usuarios.crear')) {
            return $resp;
        }

        if (empty($post['nombre']) || empty($post['usuario'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => lang('Usuarios.RequiredFieldsMsg'), 'csrf' => csrf_hash(),
            ]);
        }
        if (!$id && empty($post['password'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => lang('Usuarios.PasswordRequiredMsg'), 'csrf' => csrf_hash(),
            ]);
        }

        $idRoles = array_map('intval', $post['idRoles'] ?? []);
        $esAdministrador = !empty($idRoles) && $this->db()->table('tbl_roles')
            ->whereIn('nombre', self::ROLES_TODAS_OFICINAS)
            ->whereIn('idRol', $idRoles)
            ->countAllResults() > 0;

        if (!$esAdministrador && empty($post['idOficina'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => lang('Usuarios.OfficeRequiredMsg'), 'csrf' => csrf_hash(),
            ]);
        }

        $data = [
            'idOficina' => !empty($post['idOficina']) ? $post['idOficina'] : null,
            'nombre'    => $post['nombre'],
            'apellido'  => $post['apellido'] ?? null,
            'correo'    => !empty($post['correo']) ? $post['correo'] : null,
            'usuario'   => $post['usuario'],
            'estado'    => $post['estado'] ?? 'A',
        ];
        if (!empty($post['password'])) {
            $data['password'] = sha1($post['password']);
        }

        try {
            if ($id) {
                $model->update($id, $data);
            } else {
                $id = $model->insert($data);
            }

            $model->asignarRoles($id, $idRoles);

            return $this->response->setJSON(['ok' => true, 'csrf' => csrf_hash()]);
        } catch (\Exception $e) {
            $msg = str_contains($e->getMessage(), 'uq_user_usuario')
                ? 'Ya existe un usuario con ese nombre de usuario.'
                : (str_contains($e->getMessage(), 'uq_user_correo') ? 'Ya existe un usuario con ese correo.' : $e->getMessage());
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => $msg, 'csrf' => csrf_hash()]);
        }
    }
    //----------------------------------------------------------------------------------------------
    public function eliminar($id)
    {
        if ($resp = $this->exigirPermiso('usuarios.eliminar')) {
            return $resp;
        }

        $id = (int) $id;
        $session = session();

        if ($id === (int) $session->get('idUser')) {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => lang('Usuarios.CannotDeleteOwnAccount'), 'csrf' => csrf_hash()]);
        }

        $model = new UserModel();
        $esAdmin = count(array_intersect(self::ROLES_TODAS_OFICINAS, array_map(fn ($r) => $r->nombre, $model->getRoles($id)))) > 0;
        if ($esAdmin) {
            $totalAdmins = $this->db()->table('tbl_user_roles ur')
                ->join('tbl_roles r', 'r.idRol = ur.idRol')
                ->whereIn('r.nombre', self::ROLES_TODAS_OFICINAS)
                ->countAllResults();
            if ($totalAdmins <= 1) {
                return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => lang('Usuarios.CannotDeleteLastAdmin'), 'csrf' => csrf_hash()]);
            }
        }

        $model->delete($id);

        return $this->response->setJSON(['ok' => true, 'csrf' => csrf_hash()]);
    }

    private function db()
    {
        return \Config\Database::connect();
    }
}
