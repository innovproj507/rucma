<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    // Roles que ven todas las oficinas (y no necesitan oficina asignada).
    public const ROLES_TODAS_OFICINAS = ["Owner", "Administrator", "Administrador"];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');

        $this->aplicarIdioma();
        $this->refrescarPermisos();
    }

    /**
     * Todo el panel es bilingue ingles/espanol -- el idioma se guarda en
     * sesion (no en la URL) y por defecto es ingles. lang() usa el locale
     * del servicio Language, que es independiente del locale del request,
     * asi que hay que fijarlo explicitamente en cada request.
     */
    private function aplicarIdioma(): void
    {
        $idioma = session()->get('idioma');
        if (!in_array($idioma, ['en', 'es'], true)) {
            $idioma = 'en';
        }

        \Locale::setDefault($idioma);
        service('language')->setLocale($idioma);
    }

    /**
     * Vuelve a calcular roles/permisos/isAdmin desde la base de datos en
     * cada request (en vez de solo al iniciar sesion), para que un cambio
     * en Roles se vea de inmediato para usuarios ya conectados, sin tener
     * que volver a loguearse.
     */
    private function refrescarPermisos(): void
    {
        $session = session();
        $idUser = $session->get('idUser');
        if (!$idUser) {
            return;
        }

        $model = new \App\Models\UserModel();
        $roles = $model->getRoles((int) $idUser);
        $roleNames = array_map(fn ($r) => $r->nombre, $roles);
        $isAdmin = count(array_intersect(self::ROLES_TODAS_OFICINAS, $roleNames)) > 0;

        $datos = [
            'roles'    => $roleNames,
            'permisos' => $model->getPermisos((int) $idUser),
            'isAdmin'  => $isAdmin,
        ];

        // Si recien se vuelve administrador en este request, dale tambien la
        // lista de oficinas para el selector (antes estaba vacia).
        if ($isAdmin && empty($session->get('oficinasDisponibles'))) {
            $datos['oficinasDisponibles'] = $model->oficinas();
        }

        $session->set($datos);
    }

    /**
     * Oficina por la que hay que filtrar los datos operativos (certificados,
     * estudiantes, dashboard). Un usuario normal siempre ve solo la suya; un
     * administrador ve lo que haya elegido en el selector del header, o null
     * si eligio "Todas las oficinas".
     */
    protected function oficinaEfectiva(): ?int
    {
        $session = session();
        if (!$session->get('isAdmin')) {
            return (int) $session->get('idOficina');
        }

        $vista = $session->get('oficinaVista');
        return $vista !== null && $vista !== '' ? (int) $vista : null;
    }

    /**
     * True si el usuario logueado tiene el permiso dado (union de los
     * permisos de todos sus roles activos, calculada al iniciar sesion).
     */
    protected function tienePermiso(string $codigo): bool
    {
        return in_array($codigo, session()->get('permisos') ?? [], true);
    }

    /**
     * Guarda contra un permiso faltante. Usar al inicio de cada accion de
     * controlador:
     *   if ($resp = $this->exigirPermiso('estudiantes.crear')) { return $resp; }
     * Responde JSON 403 si la request espera JSON (AJAX/API), o redirige al
     * dashboard con un mensaje si es una carga de pagina normal. Devuelve
     * null (no hacer nada) cuando el permiso SI esta presente.
     */
    protected function exigirPermiso(string $codigo)
    {
        if ($this->tienePermiso($codigo)) {
            return null;
        }

        if ($this->request->isAJAX() || strpos((string) $this->request->getHeaderLine('Accept'), 'application/json') !== false) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'error' => lang('Common.NoPermissionAction')]);
        }

        session()->setFlashdata('msg', lang('Common.NoPermissionSection'));
        return redirect()->to('/dashboard');
    }
}
