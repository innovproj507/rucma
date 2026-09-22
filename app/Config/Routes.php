<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Login');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

$routes->get('/', 'Login::index');
$routes->get('login', 'Login::index');
$routes->post('auth', 'Login::auth');
$routes->get('logout', 'Login::logout');

$routes->get('consult/validate/(:any)', 'Validate::certificado/$1');

$routes->get('dashboard', 'Dashboard::index');
$routes->get('oficina-vista/(:any)', 'OficinaVista::cambiar/$1');
$routes->get('idioma/(:any)', 'Idioma::cambiar/$1');

$routes->get('students', 'Student::index');
$routes->get('students/filter', 'Student::filter');
$routes->get('students/get/(:num)', 'Student::get/$1');
$routes->post('students/save', 'Student::save');

$routes->get('certificates', 'Certificado::listado');
$routes->get('certificates/filter', 'Certificado::listadoFiltrar');

$routes->get('certificados', 'Certificado::index');
$routes->get('certificados/cursos', 'Certificado::cursos');
$routes->get('certificados/estudiante', 'Certificado::buscarEstudiante');
$routes->post('certificados/guardar', 'Certificado::guardar');
$routes->get('certificados/(:num)/ver', 'Certificado::pdf/$1/ver');
$routes->get('certificados/(:num)/descargar', 'Certificado::pdf/$1/descargar');
$routes->post('certificados/(:num)/cancelar', 'Certificado::cancelar/$1');
$routes->post('certificados/(:num)/eliminar', 'Certificado::eliminar/$1');

$routes->get('reportes', 'Report::emitidos');
$routes->get('reportes/filtrar', 'Report::emitidosFiltrar');
$routes->get('reportes/exportar', 'Report::emitidosExportar');
$routes->get('reportes/vencer', 'Report::vencer');
$routes->get('reportes/cursos', 'Report::cursos');
$routes->get('reportes/estudiantes', 'Report::estudiantes');

$routes->get('cursos', 'Curso::index');
$routes->get('cursos/filter', 'Curso::filter');
$routes->get('cursos/get/(:num)', 'Curso::get/$1');
$routes->post('cursos/save', 'Curso::save');
$routes->post('cursos/eliminar/(:num)', 'Curso::eliminar/$1');

$routes->get('plantillas', 'Plantilla::index');
$routes->get('plantillas/get/(:num)', 'Plantilla::get/$1');
$routes->post('plantillas/save', 'Plantilla::save');
$routes->get('plantillas/(:num)/editor', 'Plantilla::editor/$1');
$routes->get('plantillas/(:num)/preview', 'Plantilla::preview/$1');

$routes->get('usuarios', 'Usuario::index');
$routes->get('usuarios/filter', 'Usuario::filter');
$routes->get('usuarios/get/(:num)', 'Usuario::get/$1');
$routes->post('usuarios/save', 'Usuario::save');
$routes->post('usuarios/eliminar/(:num)', 'Usuario::eliminar/$1');

$routes->get('roles', 'Rol::index');
$routes->get('roles/filter', 'Rol::filter');
$routes->get('roles/nuevo', 'Rol::editor');
$routes->get('roles/(:num)/editar', 'Rol::editor/$1');
$routes->post('roles/save', 'Rol::save');

if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
