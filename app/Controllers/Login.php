<?php

namespace App\Controllers;

use App\Models\UserModel;

class Login extends BaseController
{
    public function index()
    {
        $session = session();
        $data['msg'] = $session->get('msg');
        return view('login', $data);
    }

    public function auth()
    {
        $session = session();
        $model = new UserModel();

        $pUser = $this->request->getPost('user');
        $user = $model->findActiveByUsername((string) $pUser);

        if ($user) {
            $pPass = sha1(strval($this->request->getPost('pass')));
            if ($pPass === $user->password) {
                $model->touchLastAccess($user->idUser);
                $roles = $model->getRoles($user->idUser);
                $roleNames = array_map(fn ($r) => $r->nombre, $roles);
                $isAdmin = count(array_intersect(BaseController::ROLES_TODAS_OFICINAS, $roleNames)) > 0;
                $permisos = $model->getPermisos($user->idUser);

                $oficinas = $isAdmin
                    ? \Config\Database::connect()->table('tbl_oficina')->select('idOficina, nombre, descripcion')->where('estado', 'A')->orderBy('nombre')->get()->getResult()
                    : [];

                $ses_data = [
                    'idUser'      => $user->idUser,
                    'idOficina'   => $user->idOficina,
                    // Oficina que el usuario esta viendo ahora mismo. Para un
                    // usuario normal es siempre la suya (no puede cambiarla);
                    // el administrador arranca viendo "Todas" (null) y puede
                    // cambiarla desde el selector en el header.
                    'oficinaVista' => $isAdmin ? null : $user->idOficina,
                    'oficinasDisponibles' => $oficinas,
                    'roles'       => $roleNames,
                    'permisos'    => $permisos,
                    'isAdmin'     => $isAdmin,
                    'nombre'      => $user->nombre,
                    'correo'      => $user->correo,
                    'usuario'     => $user->usuario,
                    'logged_in'   => true,
                    'tokens'      => bin2hex(random_bytes(16)),
                ];
                $session->set($ses_data);
                return redirect()->to('/dashboard');
            }
            $session->setFlashdata('msg', lang('Translate.wrongPassword'));
            return redirect()->to('/login');
        }

        $session->setFlashdata('msg', lang('Translate.userNotFound'));
        return redirect()->to('/login');
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
}
