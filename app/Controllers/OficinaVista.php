<?php

namespace App\Controllers;

class OficinaVista extends BaseController
{
    public function cambiar($idOficina = null)
    {
        $session = session();

        // Solo el administrador puede cambiar que oficina esta viendo; un
        // usuario normal ignora esto y siempre queda en la suya.
        if ($session->get('isAdmin')) {
            $session->set('oficinaVista', ($idOficina === null || $idOficina === '0') ? null : (int) $idOficina);
        }

        return redirect()->back();
    }
}
