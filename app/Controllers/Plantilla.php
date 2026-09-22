<?php

namespace App\Controllers;

use App\Models\CertificadoModel;
use App\Models\PlantillaModel;

class Plantilla extends BaseController
{
    private const CARPETA = 'plantillas';

    public function index()
    {
        if ($resp = $this->exigirPermiso('plantillas.ver')) {
            return $resp;
        }

        $session = session();
        $model = new PlantillaModel();

        $data['title']    = 'Sistema Rucma Certifícate';
        $data['nombre']   = $session->get('nombre');
        $data['usuario']  = $session->get('usuario');
        $data['page']     = 'plantillas';
        $data['datos']    = $model->listar();
        $data['oficinas'] = $model->oficinas();

        return view('header', $data) . view('plantillas') . view('footer');
    }
    //----------------------------------------------------------------------------------------------
    public function get($id)
    {
        if ($resp = $this->exigirPermiso('plantillas.ver')) {
            return $resp;
        }

        $model = new PlantillaModel();
        $data = $model->conOficinas((int) $id);

        header('Content-Type: application/json; charset=utf-8');
        return $this->response->setJSON($data);
    }
    //----------------------------------------------------------------------------------------------
    public function editor($id)
    {
        if ($resp = $this->exigirPermiso('plantillas.editar')) {
            return $resp;
        }

        $session = session();
        $model = new PlantillaModel();
        $plantilla = $model->conOficinas((int) $id);
        if (!$plantilla) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $muestra = self::muestraCertificado();

        $data['title']     = 'Sistema Rucma Certifícate';
        $data['nombre']    = $session->get('nombre');
        $data['usuario']   = $session->get('usuario');
        $data['page']      = 'plantilla_editor';
        $data['plantilla'] = $plantilla;
        $data['layout']    = $plantilla->layout;
        $data['elementos'] = PlantillaModel::elementos();

        // Texto real a mostrar dentro de cada caja del lienzo: el contenido
        // ya guardado en la plantilla si es estatico (encabezado, resolucion,
        // firma), o un dato de ejemplo si es dinamico por certificado.
        $textos = PlantillaModel::TEXTOS_DEFAULT;
        $data['vistaPrevia'] = [
            'titulo'     => $plantilla->titulo ?: null,
            'encabezado' => $plantilla->encabezado ?: null,
            'codigo'     => 'Certificate No.: ' . $muestra->codigo,
            'certifica'  => $plantilla->textoCertifica ?: $textos['certifica'],
            'nombre'     => $muestra->nombre . ' ' . $muestra->apellido,
            'documento'  => ($plantilla->textoDocumento ?: $textos['documento']) . ' ' . $muestra->dni,
            'completo'   => $plantilla->textoCompleto ?: $textos['completo'],
            'curso'      => $muestra->nombreCursoIngesEmitido,
            // El nivel STCW es del curso (ver Cursos), no de la plantilla --
            // se muestra un dato de ejemplo aqui igual que "curso"/"legal".
            'subtitulo'  => $muestra->nivelSTCWEmitido . ' (dato real: del curso, no de la plantilla)',
            // El curso trae su propia norma STCW y esa siempre gana sobre el
            // textoLegal de la plantilla en el PDF real -- pero mientras se
            // edita la plantilla, mostramos lo que el usuario esta escribiendo
            // aqui (asi ve que si se guardo), aclarando cuando se usaria.
            'legal'      => $plantilla->textoLegal
                ? $plantilla->textoLegal . ' (se usa solo si el curso no trae su propia norma STCW)'
                : $muestra->reglasIngesEmitidas . ' (texto de ejemplo del curso -- así se ve cuando el curso SÍ trae su norma)',
            'detalle'    => in_array($plantilla->tipoDetalle ?? 'india', PlantillaModel::TIPOS_DETALLE_PDE, true)
                ? 'Place and date of performed / issue (datos del certificado)'
                : (in_array($plantilla->tipoDetalle ?? 'india', PlantillaModel::TIPOS_DETALLE_ASSESSMENT, true)
                    ? 'Start/End date, Place, Date of Issue (datos del certificado)'
                    : 'From/To/Method/Place/Duration (datos del certificado)'),
            'resolucion' => $plantilla->textoResolucion ?: null,
            'qr'         => null,
            'firmaTexto' => $plantilla->firmaNombre ? ($plantilla->firmaNombre . ' / ' . $plantilla->firmaCargo) : null,
            'firmaTexto2' => $plantilla->firmaNombre2 ? ($plantilla->firmaNombre2 . ' / ' . $plantilla->firmaCargo2) : null,
        ];

        return view('header', $data) . view('plantilla_editor') . view('footer');
    }
    //----------------------------------------------------------------------------------------------
    private static function muestraCertificado(): object
    {
        return (object) [
            'nombre'                  => 'JUAN',
            'apellido'                => 'PÉREZ EJEMPLO',
            'dni'                     => 'X0000000',
            'nombreCursoEmitido'      => 'Curso de Ejemplo',
            'nombreCursoIngesEmitido' => 'Sample Training Course',
            'nivelSTCWEmitido'        => '(Sample STCW Level)',
            'reglasEmitidas'          => 'Regla de ejemplo, Párrafo 1, Sección A de la Convención Internacional.',
            'reglasIngesEmitidas'     => 'Sample Regulation, Paragraph 1, Section A of the International Convention.',
            'categoriaEmitida'        => 'New',
            'modalidadEmitida'        => 'In Classroom',
            'horasEmitidas'           => '40.0',
            'lugarEntrega'            => 'India',
            'duracion'                => 40,
            'fechaInicio'             => date('Y-m-d'),
            'fechaFinal'              => date('Y-m-d', strtotime('+4 days')),
            'fechaEmision'            => date('Y-m-d'),
            'fechaExpiracion'         => date('Y-m-d', strtotime('+5 years')),
            'codigo'                  => 'PAM-XXX-SAMPLE-AA00000',
        ];
    }
    //----------------------------------------------------------------------------------------------
    public function save()
    {
        if ($resp = $this->exigirPermiso('plantillas.editar')) {
            return $resp;
        }

        $model = new PlantillaModel();
        $post = $this->request->getPost();
        $id = !empty($post['idPlantilla']) ? (int) $post['idPlantilla'] : null;

        if (empty($post['nombre'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => lang('Plantillas.NameRequiredMsg'), 'csrf' => csrf_hash(),
            ]);
        }

        $tiposImagen = 'image/jpg,image/jpeg,image/png,image/svg+xml,image/svg,text/xml,application/xml';
        $extensionesImagen = 'jpg,jpeg,png,svg';

        $rules = [
            'marco'   => "permit_empty|uploaded[marco]|ext_in[marco,{$extensionesImagen}]|mime_in[marco,{$tiposImagen}]|max_size[marco,4096]",
            'logoIzq' => "permit_empty|uploaded[logoIzq]|ext_in[logoIzq,{$extensionesImagen}]|mime_in[logoIzq,{$tiposImagen}]|max_size[logoIzq,2048]",
            'logoDer' => "permit_empty|uploaded[logoDer]|ext_in[logoDer,{$extensionesImagen}]|mime_in[logoDer,{$tiposImagen}]|max_size[logoDer,2048]",
            'firma'   => "permit_empty|uploaded[firma]|ext_in[firma,{$extensionesImagen}]|mime_in[firma,{$tiposImagen}]|max_size[firma,2048]",
            'firma2'  => "permit_empty|uploaded[firma2]|ext_in[firma2,{$extensionesImagen}]|mime_in[firma2,{$tiposImagen}]|max_size[firma2,2048]",
        ];
        if (!$id) {
            $rules['marco'] = "uploaded[marco]|ext_in[marco,{$extensionesImagen}]|mime_in[marco,{$tiposImagen}]|max_size[marco,4096]";
        }

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => implode(' ', $this->validator->getErrors()), 'csrf' => csrf_hash(),
            ]);
        }

        $data = [
            'nombre'          => $post['nombre'],
            'titulo'          => $post['titulo'] ?? null,
            'estado'          => $post['estado'] ?? 'A',
            'encabezado'      => $post['encabezado'] ?? null,
            'textoLegal'      => $post['textoLegal'] ?? null,
            'textoResolucion' => $post['textoResolucion'] ?? null,
            'textoCertifica'  => $post['textoCertifica'] ?? null,
            'textoDocumento'  => $post['textoDocumento'] ?? null,
            'textoCompleto'   => $post['textoCompleto'] ?? null,
            'tipoDetalle'     => array_key_exists($post['tipoDetalle'] ?? '', PlantillaModel::TIPOS_DETALLE) ? $post['tipoDetalle'] : 'india',
            'firmaNombre'     => $post['firmaNombre'] ?? null,
            'firmaCargo'      => $post['firmaCargo'] ?? null,
            'firmaNombre2'    => $post['firmaNombre2'] ?? null,
            'firmaCargo2'     => $post['firmaCargo2'] ?? null,
            'textoVersion'    => $post['textoVersion'] ?? null,
        ];

        if (!empty($post['layout'])) {
            $decodificado = json_decode($post['layout'], true);
            if (is_array($decodificado)) {
                $data['layout'] = json_encode($decodificado);
            }
        }

        foreach (['marco', 'logoIzq', 'logoDer', 'firma', 'firma2'] as $campo) {
            $archivo = $this->request->getFile($campo);
            if ($archivo && $archivo->isValid() && !$archivo->hasMoved()) {
                $rutaDestino = FCPATH . 'img' . DIRECTORY_SEPARATOR . self::CARPETA;
                $extension = strtolower($archivo->getExtension());

                if ($extension === 'svg') {
                    self::sanitizarSvg($archivo->getTempName());
                }

                $nombreArchivo = $archivo->getRandomName();
                $archivo->move($rutaDestino, $nombreArchivo);
                $data[$campo] = self::CARPETA . '/' . $nombreArchivo;
            }
        }

        try {
            if ($id) {
                $model->update($id, $data);
            } else {
                $id = $model->insert($data);
            }

            // Solo el formulario de la lista (con checkboxes de oficina) trae este
            // marcador -- el editor visual no gestiona oficinas y no debe borrarlas.
            if ($this->request->getPost('gestionarOficinas')) {
                $idOficinas = array_map('intval', $post['idOficinas'] ?? []);
                $model->asignarOficinas($id, $idOficinas);
            }

            return $this->response->setJSON(['ok' => true, 'idPlantilla' => $id, 'csrf' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false, 'error' => $e->getMessage(), 'csrf' => csrf_hash(),
            ]);
        }
    }
    //----------------------------------------------------------------------------------------------
    // Un SVG es XML, a diferencia de un PNG/JPG puede traer <script> o
    // manejadores de eventos embebidos. Se quitan antes de guardarlo, ya que
    // solo se usa como imagen (logo/firma/fondo) dentro del certificado.
    //
    // El motor de SVG interno de mPDF no soporta <defs>/<clipPath> (los
    // exportan Illustrator/Figma para recortar el diseno): en vez de
    // ignorarlos los renderiza tal cual, y como el relleno por defecto de
    // SVG es negro, termina pintando un rectangulo negro solido. Illustrator
    // usa este patron de dos formas distintas:
    //  1. Un rectangulo de pagina completa, solo para que el arte no se
    //     salga de los bordes -- quitarlo no cambia nada visualmente.
    //  2. Una "ventana" de recorte sobre una imagen incrustada (raster) mas
    //     grande que la pagina, para mostrar solo una porcion de ella (p.ej.
    //     un logo/marca de agua). Quitar el clip aqui sin mas hace que se
    //     vea la imagen completa sin recortar, mucho mas grande de lo
    //     debido.
    // resolverImagenesRecortadas() detecta el caso 2 y recorta el propio
    // archivo de imagen (con GD) para que no necesite clip-path. Despues,
    // limpiarClips() quita lo que sobre de <defs>/<clipPath> (case 1 y los
    // restos del caso 2).
    private static function sanitizarSvg(string $ruta): void
    {
        $contenido = file_get_contents($ruta);
        if ($contenido === false) {
            return;
        }

        $contenido = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $contenido);
        $contenido = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $contenido);
        $contenido = preg_replace("/\son\w+\s*=\s*'[^']*'/i", '', $contenido);
        $contenido = preg_replace('/(href|xlink:href)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1=$2#$2', $contenido);

        $conImagenesResueltas = self::resolverImagenesRecortadas($contenido);
        $contenido = $conImagenesResueltas ?? $contenido;
        $contenido = self::limpiarClips($contenido);

        $conOpacidadResuelta = self::resolverOpacidadDeGrupos($contenido);
        $contenido = $conOpacidadResuelta ?? $contenido;

        $contenido = self::asegurarAnchoAlto($contenido);

        file_put_contents($ruta, $contenido);
    }
    //----------------------------------------------------------------------------------------------
    // mPDF renderiza mal la opacidad de <g> cuando el grupo tiene muchos
    // trazos vectoriales (marca de agua vectorizada con cientos de paths):
    // en visores estrictos (Acrobat, PDF.js) el grupo de transparencia queda
    // mal ubicado o distorsionado -- aunque un renderizador mas tolerante
    // (poppler) lo disimule. Bajar la opacidad a cada trazo por separado
    // (fill-opacity) evita ese grupo de transparencia, pero pierde contraste
    // donde los trazos se superponen (p.ej. letras sobre un circulo del
    // mismo tono), porque cada capa se difumina otra vez sobre la anterior
    // en vez de mezclarse a color completo antes del desvanecido final.
    //
    // La solucion real es no usar transparencia en absoluto: se calcula a
    // que color solido equivale cada trazo si se mezclara con blanco a la
    // opacidad del grupo (mismo resultado visual que agua group-opacity real,
    // porque el ultimo trazo dibujado sigue cubriendo por completo a los de
    // abajo donde se superponen, igual que en un grupo opaco) y se pinta ese
    // color directo, sin opacity en absoluto. mPDF dibuja figuras solidas de
    // forma perfectamente confiable -- el problema siempre fue la
    // transparencia, no el color ni la posicion.
    private static function resolverOpacidadDeGrupos(string $svg): ?string
    {
        $dom = new \DOMDocument();
        $anterior = libxml_use_internal_errors(true);
        $cargo = $dom->loadXML($svg);
        libxml_use_internal_errors($anterior);
        if (!$cargo) {
            return null;
        }

        $xpath = new \DOMXPath($dom);
        $huboCambios = false;

        foreach ($xpath->query('//*[local-name()="g"][@opacity]') as $grupo) {
            if (!$grupo instanceof \DOMElement) {
                continue;
            }
            if ($xpath->query('.//*[local-name()="image"]', $grupo)->length > 0) {
                continue;
            }
            $opacidad = (float) $grupo->getAttribute('opacity');
            if ($opacidad <= 0 || $opacidad >= 1) {
                continue;
            }

            $figuras = $xpath->query(
                './/*[local-name()="path" or local-name()="rect" or local-name()="circle" '
                . 'or local-name()="ellipse" or local-name()="polygon" or local-name()="polyline" '
                . 'or local-name()="line" or local-name()="text"]',
                $grupo
            );
            foreach ($figuras as $figura) {
                if (!$figura instanceof \DOMElement) {
                    continue;
                }
                foreach (['fill', 'stroke'] as $atributo) {
                    $mezclado = self::mezclarColorConBlanco($figura->getAttribute($atributo), $opacidad);
                    if ($mezclado !== null) {
                        $figura->setAttribute($atributo, $mezclado);
                    }
                }
                $figura->removeAttribute('fill-opacity');
                $figura->removeAttribute('stroke-opacity');
                $figura->removeAttribute('opacity');
            }
            $grupo->removeAttribute('opacity');
            $huboCambios = true;
        }

        if (!$huboCambios) {
            return null;
        }

        return $dom->saveXML();
    }
    //----------------------------------------------------------------------------------------------
    private static function mezclarColorConBlanco(string $color, float $opacidad): ?string
    {
        $color = trim($color);
        if ($color === '' || strtolower($color) === 'none') {
            return null;
        }
        if (preg_match('/^#([0-9a-f]{3})$/i', $color, $m)) {
            $h = $m[1];
            $color = '#' . $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2];
        }
        if (!preg_match('/^#([0-9a-f]{6})$/i', $color, $m)) {
            return null;
        }

        $r = (int) round(255 * (1 - $opacidad) + hexdec(substr($m[1], 0, 2)) * $opacidad);
        $g = (int) round(255 * (1 - $opacidad) + hexdec(substr($m[1], 2, 2)) * $opacidad);
        $b = (int) round(255 * (1 - $opacidad) + hexdec(substr($m[1], 4, 2)) * $opacidad);

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
    //----------------------------------------------------------------------------------------------
    // Illustrator exporta el SVG sin atributos width/height en la etiqueta
    // raiz (solo viewBox) -- de forma aislada un navegador lo interpreta bien
    // (usa el viewBox como tamano intrinseco), pero mPDF a veces necesita el
    // tamano explicito para calcular correctamente grupos vectoriales
    // complejos (marcas de agua con cientos de trazos); sin el, esos grupos
    // pueden salir mal ubicados/recortados aunque el resto del SVG se vea
    // bien. Agregarlo es inocuo y no cambia como se ve en ningun otro lado.
    private static function asegurarAnchoAlto(string $svg): string
    {
        if (preg_match('/<svg\b[^>]*\bwidth\s*=/i', $svg)) {
            return $svg;
        }
        if (!preg_match('/<svg\b[^>]*\bviewBox\s*=\s*(["\'])([^"\']*)\1/i', $svg, $m)) {
            return $svg;
        }
        $partes = preg_split('/[\s,]+/', trim($m[2]));
        if (count($partes) !== 4 || (float) $partes[2] <= 0 || (float) $partes[3] <= 0) {
            return $svg;
        }
        [, , $ancho, $alto] = $partes;

        return preg_replace(
            '/<svg\b/i',
            '<svg width="' . $ancho . 'px" height="' . $alto . 'px"',
            $svg,
            1
        );
    }
    //----------------------------------------------------------------------------------------------
    private static function limpiarClips(string $contenido): string
    {
        $contenido = preg_replace('#<defs\b[^>]*>.*?</defs>#is', '', $contenido);
        $contenido = preg_replace('#<clipPath\b[^>]*>.*?</clipPath>#is', '', $contenido);
        $contenido = preg_replace('/\sclip-path="url\(#[^)]*\)"/i', '', $contenido);
        $contenido = preg_replace("/\sclip-path='url\(#[^)]*\)'/i", '', $contenido);

        return $contenido;
    }
    //----------------------------------------------------------------------------------------------
    // Busca <image> dentro de una cadena de <g clip-path="..."> (el patron de
    // "ventana de recorte" de Illustrator), calcula el rectangulo visible
    // real (interseccion de todos los clip-path anidados) y recorta la
    // imagen incrustada con GD para que ya no dependa del clip-path. Solo
    // toca imagenes cuyos ancestros no tengan una transformacion mas
    // compleja que una traslacion simple (una matriz con escala/rotacion
    // real invalidaria el calculo en pixeles) -- si algo no calza, deja esa
    // imagen intacta en vez de arriesgarse a romperla.
    private static function resolverImagenesRecortadas(string $svg): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        $dom = new \DOMDocument();
        $anterior = libxml_use_internal_errors(true);
        $cargo = $dom->loadXML($svg);
        libxml_use_internal_errors($anterior);
        if (!$cargo) {
            return null;
        }

        $xpath = new \DOMXPath($dom);

        $rects = [];
        foreach ($xpath->query('//*[local-name()="rect"][@id]') as $rect) {
            $rects[$rect->getAttribute('id')] = [
                'x' => (float) $rect->getAttribute('x'),
                'y' => (float) $rect->getAttribute('y'),
                'w' => (float) $rect->getAttribute('width'),
                'h' => (float) $rect->getAttribute('height'),
            ];
        }

        $clipPaths = [];
        foreach ($xpath->query('//*[local-name()="clipPath"][@id]') as $cp) {
            $rectDirecto = $xpath->query('.//*[local-name()="rect"]', $cp)->item(0);
            if ($rectDirecto) {
                $clipPaths[$cp->getAttribute('id')] = [
                    'x' => (float) $rectDirecto->getAttribute('x'),
                    'y' => (float) $rectDirecto->getAttribute('y'),
                    'w' => (float) $rectDirecto->getAttribute('width'),
                    'h' => (float) $rectDirecto->getAttribute('height'),
                ];
                continue;
            }
            $use = $xpath->query('.//*[local-name()="use"]', $cp)->item(0);
            if ($use instanceof \DOMElement) {
                $href = $use->getAttribute('xlink:href') ?: $use->getAttributeNS('http://www.w3.org/1999/xlink', 'href');
                $refId = ltrim((string) $href, '#');
                if (isset($rects[$refId])) {
                    $clipPaths[$cp->getAttribute('id')] = $rects[$refId];
                }
            }
        }

        // Tamano de "pagina" del propio SVG (su viewBox), para poder detectar
        // una imagen incrustada mas grande que el lienzo -- pasa cuando el
        // SVG dependia de un clip-path que ya no esta (se perdio al editar el
        // archivo antes, o el exportador no lo genero) y sin el la imagen se
        // ve a su tamano real, mucho mas grande de lo debido.
        $svgRoot = $dom->documentElement;
        $viewBox = array_map('floatval', preg_split('/[\s,]+/', trim($svgRoot->getAttribute('viewBox') ?: '0 0 0 0')));
        [$vbX, $vbY, $vbW, $vbH] = $viewBox + [0, 0, 0, 0];

        $huboCambios = false;

        foreach ($xpath->query('//*[local-name()="image"]') as $image) {
            if (!$image instanceof \DOMElement) {
                continue;
            }

            $rectFinal = null;
            $transformSeguro = true;
            $esMarcaDeAgua = false;
            $nodo = $image->parentNode;
            while ($nodo instanceof \DOMElement) {
                if ($nodo->hasAttribute('transform')) {
                    $t = trim($nodo->getAttribute('transform'));
                    if ($t !== '' && !preg_match('/^matrix\(\s*1[, ]+0[, ]+0[, ]+1[, ]+[-\d.]+[, ]+[-\d.]+\s*\)$/', $t)) {
                        $transformSeguro = false;
                    }
                }
                if ($nodo->hasAttribute('opacity') && (float) $nodo->getAttribute('opacity') < 0.5) {
                    // Una imagen dentro de un grupo con opacidad baja es un
                    // tratamiento de marca de agua a proposito -- debe verse
                    // chica y discreta, no ocupar buena parte de la pagina
                    // aunque "quepa" dentro del lienzo.
                    $esMarcaDeAgua = true;
                }
                if ($nodo->hasAttribute('clip-path')) {
                    $refRaw = trim($nodo->getAttribute('clip-path'));
                    if (preg_match('/^url\(#(.*)\)$/', $refRaw, $mm) && isset($clipPaths[$mm[1]])) {
                        $r = $clipPaths[$mm[1]];
                        $rectFinal = $rectFinal === null ? $r : [
                            'x' => max($rectFinal['x'], $r['x']),
                            'y' => max($rectFinal['y'], $r['y']),
                            'w' => min($rectFinal['x'] + $rectFinal['w'], $r['x'] + $r['w']) - max($rectFinal['x'], $r['x']),
                            'h' => min($rectFinal['y'] + $rectFinal['h'], $r['y'] + $r['h']) - max($rectFinal['y'], $r['y']),
                        ];
                    }
                }
                $nodo = $nodo->parentNode;
            }

            if (!$transformSeguro) {
                continue;
            }

            // Sin clip-path que la recorte: si la imagen es mas grande que el
            // lienzo del SVG (o es una marca de agua que ocupa una porcion
            // grande de la pagina), se reescala para que quepa comoda -- una
            // marca de agua se limita a una fraccion chica del lienzo, no
            // solo a que "quepa". No hace falta tocar los pixeles de la
            // imagen, solo su tamano/posicion declarados.
            if (!$rectFinal) {
                if ($vbW <= 0 || $vbH <= 0) {
                    continue;
                }
                $anchoImg = (float) $image->getAttribute('width');
                $altoImg = (float) $image->getAttribute('height');
                if ($anchoImg <= 0 || $altoImg <= 0) {
                    continue;
                }

                $limiteAncho = $esMarcaDeAgua ? $vbW * 0.4 : $vbW;
                $limiteAlto = $esMarcaDeAgua ? $vbH * 0.4 : $vbH;
                if ($anchoImg <= $limiteAncho && $altoImg <= $limiteAlto) {
                    continue;
                }

                $escala = min($limiteAncho / $anchoImg, $limiteAlto / $altoImg) * 0.9;
                $nuevoAncho = $anchoImg * $escala;
                $nuevoAlto = $altoImg * $escala;

                $image->setAttribute('width', (string) $nuevoAncho);
                $image->setAttribute('height', (string) $nuevoAlto);
                $image->setAttribute('x', (string) ($vbX + ($vbW - $nuevoAncho) / 2));
                $image->setAttribute('y', (string) ($vbY + ($vbH - $nuevoAlto) / 2));

                $huboCambios = true;
                continue;
            }

            if ($rectFinal['w'] <= 0 || $rectFinal['h'] <= 0) {
                continue;
            }

            $href = $image->getAttribute('xlink:href') ?: $image->getAttributeNS('http://www.w3.org/1999/xlink', 'href');
            if (!preg_match('/^data:image\/(png|jpe?g);base64,(.+)$/s', (string) $href, $m)) {
                continue;
            }

            $formato = strtolower($m[1]) === 'jpg' ? 'jpeg' : strtolower($m[1]);
            $raw = base64_decode(preg_replace('/\s+/', '', $m[2]));
            $src = @imagecreatefromstring($raw);
            if (!$src) {
                continue;
            }

            $anchoDeclarado = (float) $image->getAttribute('width');
            $altoDeclarado = (float) $image->getAttribute('height');
            if ($anchoDeclarado <= 0 || $altoDeclarado <= 0) {
                continue;
            }
            $escalaX = imagesx($src) / $anchoDeclarado;
            $escalaY = imagesy($src) / $altoDeclarado;

            $cropX = max(0, (int) round($rectFinal['x'] * $escalaX));
            $cropY = max(0, (int) round($rectFinal['y'] * $escalaY));
            $cropW = min((int) round($rectFinal['w'] * $escalaX), imagesx($src) - $cropX);
            $cropH = min((int) round($rectFinal['h'] * $escalaY), imagesy($src) - $cropY);
            if ($cropW <= 0 || $cropH <= 0) {
                continue;
            }

            $recortada = @imagecrop($src, ['x' => $cropX, 'y' => $cropY, 'width' => $cropW, 'height' => $cropH]);
            if (!$recortada) {
                continue;
            }
            imagesavealpha($recortada, true);

            ob_start();
            if ($formato === 'jpeg') {
                imagejpeg($recortada, null, 92);
            } else {
                imagepng($recortada);
            }
            $nuevaData = ob_get_clean();

            $image->setAttribute('xlink:href', 'data:image/' . $formato . ';base64,' . base64_encode($nuevaData));
            $image->setAttribute('x', (string) $rectFinal['x']);
            $image->setAttribute('y', (string) $rectFinal['y']);
            $image->setAttribute('width', (string) $rectFinal['w']);
            $image->setAttribute('height', (string) $rectFinal['h']);

            $huboCambios = true;
        }

        if (!$huboCambios) {
            return null;
        }

        return $dom->saveXML();
    }
    //----------------------------------------------------------------------------------------------
    public function preview($id)
    {
        if ($resp = $this->exigirPermiso('plantillas.ver')) {
            return $resp;
        }

        $model = new PlantillaModel();
        $plantilla = $model->conOficinas((int) $id);
        if (!$plantilla) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $muestra = self::muestraCertificado();

        $contenido = (new CertificadoModel())->renderizarPdf($plantilla, $muestra, base_url('consult/validate/SAMPLE'));

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="vista-previa.pdf"')
            ->setBody($contenido);
    }
}
