<?php

namespace App\Controllers;

use App\Models\RoleModel;

class Rol extends BaseController
{
    private const PER_PAGE_PERMITIDOS = [5, 10, 25, 50, 100];

    private function filtrosGet(): array
    {
        $get = $this->request->getGet();
        return [
            'busqueda' => $get['busqueda'] ?? null,
            'estado'   => $get['estado'] ?? null,
        ];
    }

    private function perPageGet(): int
    {
        $perPage = (int) ($this->request->getGet('perPage') ?: 25);
        return in_array($perPage, self::PER_PAGE_PERMITIDOS, true) ? $perPage : 25;
    }

    public function index()
    {
        if ($resp = $this->exigirPermiso('roles.ver')) {
            return $resp;
        }

        $session = session();
        $model = new RoleModel();
        $perPage = $this->perPageGet();
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $filtros = $this->filtrosGet();

        $data['title']    = 'Sistema Rucma Certifícate';
        $data['nombre']   = $session->get('nombre');
        $data['usuario']  = $session->get('usuario');
        $data['page']     = 'roles';
        $data['datos']    = $model->listar($filtros, $page, $perPage);
        $data['total']    = $model->contar($filtros);
        $data['pagina']   = $page;
        $data['perPage']  = $perPage;
        $data['perPageOpciones'] = self::PER_PAGE_PERMITIDOS;
        $data['totalPermisosDisponibles'] = $model->totalPermisosDisponibles();
        $data['puedeCrear']  = $this->tienePermiso('roles.crear');
        $data['puedeEditar'] = $this->tienePermiso('roles.editar');

        return view('header', $data) . view('roles') . view('footer');
    }
    //----------------------------------------------------------------------------------------------
    public function filter()
    {
        if ($resp = $this->exigirPermiso('roles.ver')) {
            return $resp;
        }

        $model = new RoleModel();
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
            'totalPermisosDisponibles' => $model->totalPermisosDisponibles(),
            'puedeEditar' => $this->tienePermiso('roles.editar'),
        ]);
    }
    //----------------------------------------------------------------------------------------------
    public function editor($id = null)
    {
        if ($resp = $this->exigirPermiso($id ? 'roles.editar' : 'roles.crear')) {
            return $resp;
        }

        $session = session();
        $model = new RoleModel();
        $rol = $id ? $model->conPermisos((int) $id) : (object) ['idRol' => null, 'nombre' => '', 'descripcion' => '', 'estado' => 'A', 'idPermisos' => []];

        if ($id && !$rol) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['title']    = 'Sistema Rucma Certifícate';
        $data['nombre']   = $session->get('nombre');
        $data['usuario']  = $session->get('usuario');
        $data['page']     = 'rol_editor';
        $data['rol']      = $rol;
        $data['permisosPorModulo'] = $model->permisosPorModulo();

        return view('header', $data) . view('rol_editor') . view('footer');
    }
    //----------------------------------------------------------------------------------------------
    public function save()
    {
        $model = new RoleModel();
        $post = $this->request->getPost();
        $id = !empty($post['idRol']) ? (int) $post['idRol'] : null;

        if ($resp = $this->exigirPermiso($id ? 'roles.editar' : 'roles.crear')) {
            return $resp;
        }

        if (empty($post['nombre'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => lang('Roles.NameRequiredMsg'), 'csrf' => csrf_hash(),
            ]);
        }

        $data = [
            'nombre'      => $post['nombre'],
            'descripcion' => $post['descripcion'] ?? null,
            'estado'      => $post['estado'] ?? 'A',
        ];

        try {
            if ($id) {
                $model->update($id, $data);
            } else {
                $id = $model->insert($data);
            }

            $idPermisos = array_map('intval', $post['idPermisos'] ?? []);
            $model->asignarPermisos($id, $idPermisos);

            return $this->response->setJSON(['ok' => true, 'idRol' => $id, 'csrf' => csrf_hash()]);
        } catch (\Exception $e) {
            $msg = str_contains($e->getMessage(), 'uq_rol_nombre')
                ? 'Ya existe un rol con ese nombre.'
                : $e->getMessage();
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => $msg, 'csrf' => csrf_hash()]);
        }
    }
}
