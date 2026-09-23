<?php

namespace App\Controllers;

use App\Models\StudentModel;

class Student extends BaseController
{
    private const PER_PAGE_PERMITIDOS = [5, 10, 25, 50, 100];

    private function perPageGet(): int
    {
        $perPage = (int) ($this->request->getGet('perPage') ?: 25);
        return in_array($perPage, self::PER_PAGE_PERMITIDOS, true) ? $perPage : 25;
    }

    public function index()
    {
        if ($resp = $this->exigirPermiso('estudiantes.ver')) {
            return $resp;
        }

        $session = session();
        $model = new StudentModel();
        $perPage = $this->perPageGet();
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $idPais = $this->request->getGet('idPais') ? (int) $this->request->getGet('idPais') : null;
        $idOficina = $this->oficinaEfectiva();

        $data['title']   = 'Sistema Rucma Certifícate';
        $data['nombre']  = $session->get('nombre');
        $data['usuario'] = $session->get('usuario');
        $data['page']    = 'students';
        $data['datos']   = $model->listar(null, $page, $perPage, $idOficina, $idPais);
        $data['total']   = $model->contar(null, $idOficina, $idPais);
        $data['pagina']  = $page;
        $data['perPage'] = $perPage;
        $data['perPageOpciones'] = self::PER_PAGE_PERMITIDOS;
        $data['paises']  = $model->paises();
        $data['puedeCrear']  = $this->tienePermiso('estudiantes.crear');
        $data['puedeEditar'] = $this->tienePermiso('estudiantes.editar');

        return view('header', $data)
            . view('students')
            . view('footer');
    }
    //----------------------------------------------------------------------------------------------
    public function filter()
    {
        if ($resp = $this->exigirPermiso('estudiantes.ver')) {
            return $resp;
        }

        $model = new StudentModel();
        $perPage = $this->perPageGet();
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $busqueda = $this->request->getGet('txtbusqueda');
        $idPais = $this->request->getGet('idPais') ? (int) $this->request->getGet('idPais') : null;
        $idOficina = $this->oficinaEfectiva();

        $datos = $model->listar($busqueda, $page, $perPage, $idOficina, $idPais);
        $total = $model->contar($busqueda, $idOficina, $idPais);

        header('Content-Type: application/json; charset=utf-8');
        return $this->response->setJSON([
            'datos'   => $datos,
            'total'   => $total,
            'pagina'  => $page,
            'perPage' => $perPage,
            'ultimaPagina' => max(1, (int) ceil($total / $perPage)),
            'puedeEditar' => $this->tienePermiso('estudiantes.editar'),
        ]);
    }
    //----------------------------------------------------------------------------------------------
    public function get($id)
    {
        if ($resp = $this->exigirPermiso('estudiantes.ver')) {
            return $resp;
        }

        $model = new StudentModel();
        $idOficina = $this->oficinaEfectiva();

        if ($idOficina && !$model->tieneCertificadoEnOficina((int) $id, $idOficina)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => lang('Common.Unauthorized')]);
        }

        $data = $model->find($id);

        header('Content-Type: application/json; charset=utf-8');
        return $this->response->setJSON($data);
    }
    //----------------------------------------------------------------------------------------------
    public function save()
    {
        $post = $this->request->getPost();
        $id = !empty($post['idEstudiante']) ? $post['idEstudiante'] : null;

        if ($resp = $this->exigirPermiso($id ? 'estudiantes.editar' : 'estudiantes.crear')) {
            return $resp;
        }

        $model = new StudentModel();

        if (empty($post['nombre']) || empty($post['apellido']) || empty($post['dni'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'    => false,
                'error' => lang('Students.RequiredFieldsMsg'),
                'csrf'  => csrf_hash(),
            ]);
        }

        $data = [
            'nombre'       => $post['nombre'],
            'apellido'     => $post['apellido'],
            'dni'          => $post['dni'],
            'idPais'       => !empty($post['idPais']) ? $post['idPais'] : null,
            'nacionalidad' => $post['nacionalidad'] ?? null,
            'fechaNac'     => !empty($post['fechaNac']) ? $post['fechaNac'] : null,
            'lugarNac'     => $post['lugarNac'] ?? null,
            'sexo'         => !empty($post['sexo']) ? $post['sexo'] : null,
            'email'        => $post['email'] ?? null,
            'telefono'     => $post['telefono'] ?? null,
        ];

        try {
            if (!empty($id)) {
                $model->update($id, $data);
            } else {
                $model->insert($data);
            }
            return $this->response->setJSON(['ok' => true, 'csrf' => csrf_hash()]);
        } catch (\Exception $e) {
            $msg = str_contains($e->getMessage(), 'uq_estudiante_dni_pais')
                ? 'Ya existe un estudiante con ese DNI para ese país.'
                : $e->getMessage();
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => $msg, 'csrf' => csrf_hash()]);
        }
    }
}
