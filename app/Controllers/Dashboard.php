<?php

namespace App\Controllers;

use App\Models\DashboardModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        $model = new DashboardModel();
        $idOficina = $this->oficinaEfectiva();
        $isAdmin = (bool) $session->get('isAdmin');

        $data['title']   = 'Sistema Rucma Certifícate';
        $data['nombre']  = $session->get('nombre');
        $data['usuario'] = $session->get('usuario');
        $data['page']    = 'dashboard';

        $data['certificadosHoy']      = $model->certificadosHoy($idOficina);
        $data['estudiantesNuevosMes'] = $model->estudiantesNuevosMes($idOficina);
        $data['certificadosMes']      = $model->certificadosEsteMes($idOficina);
        $data['certificadosPorVencer'] = $model->certificadosPorVencer(30, $idOficina);

        $data['totalCertificados'] = $model->totalCertificadosEmitidos($idOficina);
        $data['totalEstudiantes']  = $model->totalEstudiantes($idOficina);
        $data['totalCursos']       = $model->totalCursosActivos($idOficina);

        $certMesAnterior = $model->certificadosMesAnterior($idOficina);
        $data['crecimientoCertificados'] = $this->porcentajeCrecimiento($data['certificadosMes'], $certMesAnterior);

        // El ranking y comparativo por oficina solo aportan viendo "Todas".
        $data['porOficina'] = $idOficina === null ? $model->certificadosPorOficina() : null;
        $data['porMes']     = $model->certificadosPorMes(4, $idOficina);
        $data['ultimos']    = $model->ultimosCertificados(8, $idOficina);

        $data['isAdmin'] = $isAdmin;
        if ($isAdmin) {
            $data['oficinasSinPlantilla'] = $model->oficinasSinPlantilla();
            $data['usuariosInactivos']    = $model->usuariosInactivos();
        }

        return view('header', $data)
            . view('dashboard')
            . view('footer');
    }

    private function porcentajeCrecimiento(int $actual, int $anterior): ?float
    {
        if ($anterior === 0) {
            return $actual > 0 ? 100.0 : null;
        }
        return round((($actual - $anterior) / $anterior) * 100, 1);
    }
}
