<?php

namespace App\Controllers;

class Idioma extends BaseController
{
    public function cambiar(string $codigo)
    {
        session()->set('idioma', in_array($codigo, ['en', 'es'], true) ? $codigo : 'en');

        return redirect()->back();
    }
}
