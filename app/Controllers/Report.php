<?php

namespace App\Controllers;

use App\Models\ReportModel;

class Report extends BaseController
{
    private function baseData(string $tab): array
    {
        $session = session();
        return [
            'title'   => 'Sistema Rucma Certifícate',
            'nombre'  => $session->get('nombre'),
            'usuario' => $session->get('usuario'),
            'page'    => 'reportes',
            'tab'     => $tab,
        ];
    }

    // La oficina siempre viene de la sesion (oficinaEfectiva), nunca del
    // request -- asi un usuario normal no puede ver otra oficina aunque
    // arme la URL a mano.
    private function filtrosEmitidos(): array
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

    public function emitidos()
    {
        if ($resp = $this->exigirPermiso('reportes.ver_emitidos')) {
            return $resp;
        }

        $model = new ReportModel();
        $data = $this->baseData('emitidos');
        $data['datos'] = $model->certificadosEmitidos($this->filtrosEmitidos());

        return view('header', $data) . view('reporte_emitidos') . view('footer');
    }
    //----------------------------------------------------------------------------------------------
    public function emitidosFiltrar()
    {
        if ($resp = $this->exigirPermiso('reportes.ver_emitidos')) {
            return $resp;
        }

        $model = new ReportModel();
        $datos = $model->certificadosEmitidos($this->filtrosEmitidos());
        header('Content-Type: application/json; charset=utf-8');
        return $this->response->setJSON($datos);
    }
    //----------------------------------------------------------------------------------------------
    public function emitidosExportar()
    {
        if ($resp = $this->exigirPermiso('reportes.exportar')) {
            return $resp;
        }

        $model = new ReportModel();
        $datos = $model->certificadosEmitidos($this->filtrosEmitidos(), 10000);

        $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="certificados_emitidos.csv"');

        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF");
        fputcsv($out, [
            lang('Common.Code'), lang('Reportes.Student'), 'DNI', lang('Reportes.Course'), lang('Reportes.Office'),
            lang('Reportes.Issuance'), lang('Reportes.Expiration'), lang('Common.Status'),
        ]);
        foreach ($datos as $d) {
            fputcsv($out, [
                $d->codigo, $d->nombre . ' ' . $d->apellido, $d->dni, $d->nombreCursoEmitido, $d->oficina,
                $d->fechaEmision, $d->fechaExpiracion, $d->estado,
            ]);
        }
        fclose($out);
        return $this->response;
    }
    //----------------------------------------------------------------------------------------------
    public function vencer()
    {
        if ($resp = $this->exigirPermiso('reportes.ver_vencer')) {
            return $resp;
        }

        $model = new ReportModel();
        $dias = (int) ($this->request->getGet('dias') ?: 30);
        $data = $this->baseData('vencer');
        $data['dias'] = $dias;
        $data['datos'] = $model->certificadosPorVencer($dias, $this->oficinaEfectiva());

        return view('header', $data) . view('reporte_vencer') . view('footer');
    }
    //----------------------------------------------------------------------------------------------
    public function cursos()
    {
        if ($resp = $this->exigirPermiso('reportes.ver_cursos')) {
            return $resp;
        }

        $model = new ReportModel();
        $data = $this->baseData('cursos');
        $data['datos'] = $model->certificadosPorCurso($this->oficinaEfectiva());

        return view('header', $data) . view('reporte_cursos') . view('footer');
    }
    //----------------------------------------------------------------------------------------------
    public function estudiantes()
    {
        if ($resp = $this->exigirPermiso('reportes.ver_estudiantes')) {
            return $resp;
        }

        $model = new ReportModel();
        $busqueda = $this->request->getGet('busqueda');
        $data = $this->baseData('estudiantes');
        $data['busqueda'] = $busqueda;
        $data['datos'] = $model->estudiantesConCertificados($busqueda, $this->oficinaEfectiva());

        return view('header', $data) . view('reporte_estudiantes') . view('footer');
    }
}
