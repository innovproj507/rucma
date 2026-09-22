<?php

namespace App\Controllers;

use App\Models\CertificadoModel;
use App\Models\ReportModel;
use App\Models\StudentModel;

class Certificado extends BaseController
{
    private const PER_PAGE_PERMITIDOS = [5, 10, 25, 50, 100];

    private function filtrosGet(): array
    {
        $get = $this->request->getGet();
        return [
            'busqueda'  => $get['busqueda'] ?? null,
            'idOficina' => $this->oficinaEfectiva(),
            'estado'    => $get['estado'] ?? null,
            'desde'     => $get['desde'] ?? null,
            'hasta'     => $get['hasta'] ?? null,
        ];
    }

    private function perPageGet(): int
    {
        $perPage = (int) ($this->request->getGet('perPage') ?: 25);
        return in_array($perPage, self::PER_PAGE_PERMITIDOS, true) ? $perPage : 25;
    }

    public function listado()
    {
        if ($resp = $this->exigirPermiso('certificados.ver_listado')) {
            return $resp;
        }

        $session = session();
        $model = new ReportModel();
        $perPage = $this->perPageGet();
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $filtros = $this->filtrosGet();

        $data['title']    = 'Sistema Rucma Certifícate';
        $data['nombre']   = $session->get('nombre');
        $data['usuario']  = $session->get('usuario');
        $data['page']     = 'certificates';
        $data['datos']    = $model->certificadosPaginados($filtros, $page, $perPage);
        $data['total']    = $model->contarCertificados($filtros);
        $data['pagina']   = $page;
        $data['perPage']  = $perPage;
        $data['perPageOpciones'] = self::PER_PAGE_PERMITIDOS;
        $data['puedeVerPdf'] = $this->tienePermiso('certificados.ver_pdf');
        $data['puedeCancelar'] = $this->tienePermiso('certificados.cancelar');
        $data['puedeEliminar'] = $this->tienePermiso('certificados.eliminar');

        return view('header', $data) . view('certificates') . view('footer');
    }
    //----------------------------------------------------------------------------------------------
    public function listadoFiltrar()
    {
        if ($resp = $this->exigirPermiso('certificados.ver_listado')) {
            return $resp;
        }

        $model = new ReportModel();
        $perPage = $this->perPageGet();
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $filtros = $this->filtrosGet();

        $datos = $model->certificadosPaginados($filtros, $page, $perPage);
        $total = $model->contarCertificados($filtros);

        header('Content-Type: application/json; charset=utf-8');
        return $this->response->setJSON([
            'datos'    => $datos,
            'total'    => $total,
            'pagina'   => $page,
            'perPage'  => $perPage,
            'ultimaPagina' => max(1, (int) ceil($total / $perPage)),
            'puedeVerPdf' => $this->tienePermiso('certificados.ver_pdf'),
            'puedeCancelar' => $this->tienePermiso('certificados.cancelar'),
            'puedeEliminar' => $this->tienePermiso('certificados.eliminar'),
        ]);
    }

    public function index()
    {
        if ($resp = $this->exigirPermiso('certificados.ver')) {
            return $resp;
        }

        $session = session();
        $model = new CertificadoModel();
        $idOficina = $this->oficinaEfectiva();

        $data['title']      = 'Sistema Rucma Certifícate';
        $data['nombre']     = $session->get('nombre');
        $data['usuario']    = $session->get('usuario');
        $data['page']       = 'certificado';
        $data['categorias'] = $model->categorias();
        $data['modalidades'] = $model->modalidades();
        $data['paises']     = (new StudentModel())->paises();
        $data['sinOficina'] = $idOficina === null;

        $oficina = $idOficina ? $this->db()->table('tbl_oficina')->where('idOficina', $idOficina)->get()->getRow() : null;
        $data['lugarEntregaDefault'] = $oficina->nombre ?? '';

        return view('header', $data)
            . view('certificado_form')
            . view('footer');
    }
    //----------------------------------------------------------------------------------------------
    public function cursos()
    {
        if ($resp = $this->exigirPermiso('certificados.ver')) {
            return $resp;
        }

        $model = new CertificadoModel();
        $idCategoria = $this->request->getGet('idCategoria');

        $idOficina = $this->oficinaEfectiva();
        $datos = $idOficina ? $model->cursosPorCategoria($idCategoria ? (int) $idCategoria : null, $idOficina) : [];

        header('Content-Type: application/json; charset=utf-8');
        return $this->response->setJSON($datos);
    }
    //----------------------------------------------------------------------------------------------
    public function buscarEstudiante()
    {
        if ($resp = $this->exigirPermiso('certificados.ver')) {
            return $resp;
        }

        $dni = $this->request->getGet('dni');
        $estudiante = (new StudentModel())
            ->where('dni', $dni)
            ->first();

        header('Content-Type: application/json; charset=utf-8');
        return $this->response->setJSON($estudiante);
    }
    //----------------------------------------------------------------------------------------------
    public function guardar()
    {
        if ($resp = $this->exigirPermiso('certificados.crear')) {
            return $resp;
        }

        $session = session();
        $post = $this->request->getPost();
        $idOficina = $this->oficinaEfectiva();

        if (!$idOficina) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => lang('CertificadoForm.SelectSpecificOffice'), 'csrf' => csrf_hash(),
            ]);
        }

        foreach (['nombre', 'apellido', 'dni', 'idCategoria', 'idCurso', 'idModalidad', 'fechaInicio', 'fechaFinal', 'fechaEmision', 'fechaExpiracion'] as $campo) {
            if (empty($post[$campo])) {
                return $this->response->setStatusCode(422)->setJSON([
                    'ok' => false, 'error' => sprintf(lang('CertificadoForm.FieldRequired'), $campo), 'csrf' => csrf_hash(),
                ]);
            }
        }

        $studentModel = new StudentModel();
        $idPaisEstudiante = !empty($post['idPais']) ? (int) $post['idPais'] : null;

        $existente = $studentModel->where('dni', $post['dni'])->first();
        $datosEstudiante = [
            'nombre'       => mb_strtoupper($post['nombre']),
            'apellido'     => mb_strtoupper($post['apellido']),
            'dni'          => $post['dni'],
            'idPais'       => $idPaisEstudiante,
            'nacionalidad' => $post['nacionalidad'] ?? null,
            'fechaNac'     => !empty($post['fechaNac']) ? $post['fechaNac'] : null,
            'lugarNac'     => $post['lugarNac'] ?? null,
            'sexo'         => !empty($post['sexo']) ? $post['sexo'] : null,
        ];

        if ($existente) {
            $studentModel->update($existente->idEstudiante, $datosEstudiante);
            $idEstudiante = $existente->idEstudiante;
        } else {
            $idEstudiante = $studentModel->insert($datosEstudiante);
        }

        $certModel = new CertificadoModel();
        $curso = $certModel->cursosPorCategoria((int) $post['idCategoria'], (int) $idOficina);
        $horas = null;
        $cursoValido = false;
        foreach ($curso as $c) {
            if ((int) $c->idCurso === (int) $post['idCurso']) {
                $horas = $c->horas;
                $cursoValido = true;
                break;
            }
        }
        if (!$cursoValido) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => 'El curso seleccionado no pertenece a esta oficina.', 'csrf' => csrf_hash(),
            ]);
        }

        try {
            $certificado = $certModel->crear([
                'idEstudiante'    => $idEstudiante,
                'idOficina'       => $idOficina,
                'idCurso'         => (int) $post['idCurso'],
                'idModalidad'     => (int) $post['idModalidad'],
                'idCategoria'     => (int) $post['idCategoria'],
                'idUsuarioEmisor' => $session->get('idUser'),
                'horas'           => $horas,
                'lugarEntrega'    => $post['lugarEntrega'] ?? null,
                'duracion'        => !empty($post['duracion']) ? $post['duracion'] : null,
                'fechaInicio'     => $post['fechaInicio'],
                'fechaFinal'      => $post['fechaFinal'],
                'fechaEmision'    => $post['fechaEmision'],
                'fechaExpiracion' => $post['fechaExpiracion'],
            ]);

            return $this->response->setJSON([
                'ok'            => true,
                'codigo'        => $certificado->codigo,
                'idCertificado' => $certificado->idCertificado,
                'csrf'          => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => $e->getMessage(), 'csrf' => csrf_hash(),
            ]);
        }
    }

    public function pdf($id, string $modo = 'ver')
    {
        if ($resp = $this->exigirPermiso('certificados.ver_pdf')) {
            return $resp;
        }

        $certificado = (new CertificadoModel())->conDetalle((int) $id);
        if (!$certificado) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Un usuario (o un admin viendo una oficina especifica) solo puede
        // ver/descargar certificados de su propia oficina -- sin esto,
        // cualquiera logueado podia ver el PDF de cualquier certificado de
        // cualquier oficina con solo cambiar el numero en la URL.
        $idOficinaEfectiva = $this->oficinaEfectiva();
        if ($idOficinaEfectiva !== null && (int) $certificado->idOficina !== $idOficinaEfectiva) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $plantilla = (new \App\Models\PlantillaModel())->porOficina((int) $certificado->idOficina);
        if (!$plantilla) {
            throw new \RuntimeException(lang('CertificatesList.NoTemplateAssigned'));
        }

        $urlValidacion = base_url('consult/validate/' . $certificado->codigo);
        $contenido = (new CertificadoModel())->renderizarPdf($plantilla, $certificado, $urlValidacion);

        $nombreArchivo = 'certificado-' . $certificado->codigo . '.pdf';
        $disposicion = $modo === 'descargar' ? 'attachment' : 'inline';

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', $disposicion . '; filename="' . $nombreArchivo . '"')
            ->setBody($contenido);
    }

    private function db()
    {
        return \Config\Database::connect();
    }
    //----------------------------------------------------------------------------------------------
    // Alterna Emitido <-> Cancelado. No se borra nada -- queda historial en
    // tbl_certificado_historial (auditoria de quien y cuando).
    public function cancelar($id)
    {
        if ($resp = $this->exigirPermiso('certificados.cancelar')) {
            return $resp;
        }

        $id = (int) $id;
        $model = new CertificadoModel();
        $certificado = $model->find($id);
        if (!$certificado) {
            return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'error' => lang('CertificatesList.CertificateNotFound'), 'csrf' => csrf_hash()]);
        }

        $idOficinaEfectiva = $this->oficinaEfectiva();
        if ($idOficinaEfectiva !== null && (int) $certificado->idOficina !== $idOficinaEfectiva) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'error' => lang('Common.NoPermissionAction'), 'csrf' => csrf_hash()]);
        }

        $nuevoEstado = $certificado->estado === 'C' ? 'E' : 'C';
        $model->update($id, ['estado' => $nuevoEstado]);

        $this->db()->table('tbl_certificado_historial')->insert([
            'idCertificado'     => $id,
            'accion'            => $nuevoEstado === 'C' ? 'ANULACION' : 'MODIFICACION',
            'campo_modificado'  => 'estado',
            'valor_anterior'    => $certificado->estado,
            'valor_nuevo'       => $nuevoEstado,
            'idUsuario'         => session()->get('idUser'),
        ]);

        return $this->response->setJSON(['ok' => true, 'estado' => $nuevoEstado, 'csrf' => csrf_hash()]);
    }
    //----------------------------------------------------------------------------------------------
    // Borrado definitivo -- solo permitido si el certificado ya esta
    // Cancelado, para no perder de forma irreversible un certificado vigente
    // (si esta activo hay que cancelarlo primero).
    public function eliminar($id)
    {
        if ($resp = $this->exigirPermiso('certificados.eliminar')) {
            return $resp;
        }

        $id = (int) $id;
        $model = new CertificadoModel();
        $certificado = $model->find($id);
        if (!$certificado) {
            return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'error' => lang('CertificatesList.CertificateNotFound'), 'csrf' => csrf_hash()]);
        }

        $idOficinaEfectiva = $this->oficinaEfectiva();
        if ($idOficinaEfectiva !== null && (int) $certificado->idOficina !== $idOficinaEfectiva) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'error' => lang('Common.NoPermissionAction'), 'csrf' => csrf_hash()]);
        }

        if ($certificado->estado !== 'C') {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => lang('CertificatesList.MustCancelBeforeDelete'), 'csrf' => csrf_hash()]);
        }

        $model->delete($id);

        return $this->response->setJSON(['ok' => true, 'csrf' => csrf_hash()]);
    }
}
