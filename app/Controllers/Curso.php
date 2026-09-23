<?php

namespace App\Controllers;

use App\Models\CursoModel;

class Curso extends BaseController
{
    private const PER_PAGE_PERMITIDOS = [5, 10, 25, 50, 100];

    // Un usuario normal siempre queda restringido a su propia oficina. Un
    // administrador filtra con el dropdown de esta misma pagina (independiente
    // del selector del header) -- por defecto arranca con la oficina que tenga
    // elegida en el header, pero puede cambiarla aqui sin afectar otras paginas.
    private function filtrosGet(): array
    {
        $get = $this->request->getGet();
        $idOficina = session()->get('isAdmin')
            ? ($get['idOficina'] ?? $this->oficinaEfectiva())
            : (int) session()->get('idOficina');

        return [
            'busqueda'    => $get['busqueda'] ?? null,
            'idModalidad' => $get['idModalidad'] ?? null,
            'idCategoria' => $get['idCategoria'] ?? null,
            'estado'      => $get['estado'] ?? null,
            'idOficina'   => $idOficina,
        ];
    }

    private function perPageGet(): int
    {
        $perPage = (int) ($this->request->getGet('perPage') ?: 25);
        return in_array($perPage, self::PER_PAGE_PERMITIDOS, true) ? $perPage : 25;
    }

    private function db()
    {
        return \Config\Database::connect();
    }

    public function index()
    {
        if ($resp = $this->exigirPermiso('cursos.ver')) {
            return $resp;
        }

        $session = session();
        $model = new CursoModel();
        $perPage = $this->perPageGet();
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));

        $data['title']       = 'Sistema Rucma Certifícate';
        $data['nombre']      = $session->get('nombre');
        $data['usuario']     = $session->get('usuario');
        $data['page']        = 'cursos';
        $filtros             = $this->filtrosGet();
        $data['datos']       = $model->listar($filtros, $page, $perPage);
        $data['total']       = $model->contar($filtros);
        $data['oficinas']    = $this->db()->table('tbl_oficina')->where('estado', 'A')->orderBy('nombre')->get()->getResult();
        $data['idOficinaFiltro'] = $filtros['idOficina'];
        $data['pagina']      = $page;
        $data['puedeEliminar'] = $this->tienePermiso('cursos.eliminar');
        $data['perPage']     = $perPage;
        $data['perPageOpciones'] = self::PER_PAGE_PERMITIDOS;
        $data['modalidades'] = $model->modalidades();
        $data['categorias']  = $model->categorias();

        return view('header', $data) . view('cursos') . view('footer');
    }
    //----------------------------------------------------------------------------------------------
    public function filter()
    {
        if ($resp = $this->exigirPermiso('cursos.ver')) {
            return $resp;
        }

        $model = new CursoModel();
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
            'puedeEliminar' => $this->tienePermiso('cursos.eliminar'),
        ]);
    }
    //----------------------------------------------------------------------------------------------
    public function get($id)
    {
        if ($resp = $this->exigirPermiso('cursos.ver')) {
            return $resp;
        }

        $model = new CursoModel();
        $data = $model->conHoras((int) $id);

        header('Content-Type: application/json; charset=utf-8');
        return $this->response->setJSON($data);
    }
    //----------------------------------------------------------------------------------------------
    public function save()
    {
        $post = $this->request->getPost();
        $id = !empty($post['idCurso']) ? $post['idCurso'] : null;

        if ($resp = $this->exigirPermiso($id ? 'cursos.editar' : 'cursos.crear')) {
            return $resp;
        }

        $model = new CursoModel();

        if (empty($post['nombre'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => lang('Validacion.CourseNameRequired'), 'csrf' => csrf_hash(),
            ]);
        }

        $data = [
            'idModalidad'  => !empty($post['idModalidad']) ? $post['idModalidad'] : null,
            'codigo'       => $post['codigo'] ?? null,
            'nombre'       => $post['nombre'],
            'nombreIngles' => $post['nombreIngles'] ?? null,
            'nivelSTCW'    => $post['nivelSTCW'] ?? null,
            'reglas'       => $post['reglas'] ?? null,
            'reglasIngles' => $post['reglasIngles'] ?? null,
            'imo'          => !empty($post['imo']) ? $post['imo'] : null,
            'estado'       => $post['estado'] ?? 'A',
        ];

        // Un usuario normal solo crea/edita cursos de su oficina; el admin elige la oficina.
        $idOficina = session()->get('isAdmin') ? (int) ($post['idOficina'] ?? 0) : (int) session()->get('idOficina');
        if ($idOficina > 0) {
            $data['idOficina'] = $idOficina;
        } elseif (empty($id)) {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => 'Selecciona la oficina del curso.', 'csrf' => csrf_hash()]);
        }

        try {
            if (!empty($id)) {
                $model->update($id, $data);
            } else {
                $id = $model->insert($data);
            }

            $horas = [];
            foreach ($post['horas'] ?? [] as $idCategoria => $valor) {
                $horas[(int) $idCategoria] = $valor;
            }
            $model->guardarHoras((int) $id, $horas);

            return $this->response->setJSON(['ok' => true, 'csrf' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => $e->getMessage(), 'csrf' => csrf_hash(),
            ]);
        }
    }
    //----------------------------------------------------------------------------------------------
    public function eliminar($id)
    {
        if ($resp = $this->exigirPermiso('cursos.eliminar')) {
            return $resp;
        }

        $id = (int) $id;
        $model = new CursoModel();
        $curso = $model->find($id);
        if (!$curso) {
            return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'error' => lang('Cursos.CourseNotFound'), 'csrf' => csrf_hash()]);
        }

        // Un usuario normal solo elimina cursos de su propia oficina.
        if (!session()->get('isAdmin') && (int) $curso->idOficina !== (int) session()->get('idOficina')) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'error' => lang('Common.NoPermissionAction'), 'csrf' => csrf_hash()]);
        }

        // Con certificados emitidos no se puede borrar (rompe el historial): se desactiva.
        $emitidos = $this->db()->table('tbl_certificados')->where('idCurso', $id)->countAllResults();
        if ($emitidos > 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => sprintf(lang('Cursos.CannotDeleteUsed'), $emitidos), 'csrf' => csrf_hash(),
            ]);
        }

        $this->db()->table('tbl_curso_categoria_horas')->where('idCurso', $id)->delete();
        $model->delete($id);

        return $this->response->setJSON(['ok' => true, 'csrf' => csrf_hash()]);
    }
}
