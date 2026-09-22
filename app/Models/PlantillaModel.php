<?php namespace App\Models;

use CodeIgniter\Model;

class PlantillaModel extends Model
{
    protected $table         = 'tbl_plantillas_certificado';
    protected $primaryKey    = 'idPlantilla';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'nombre', 'titulo', 'marco', 'logoIzq', 'logoDer', 'firma', 'firma2', 'estado',
        'encabezado', 'textoLegal', 'textoResolucion',
        'textoCertifica', 'textoDocumento', 'textoCompleto', 'tipoDetalle',
        'firmaNombre', 'firmaCargo', 'firmaNombre2', 'firmaCargo2', 'textoVersion', 'layout',
    ];

    // Textos fijos por defecto -- se usan cuando la plantilla no define su
    // propia version (textoCertifica/textoDocumento/textoCompleto vacios).
    // Existen para poder tener plantillas de tipo distinto (p.ej. "Previous
    // Documentary Evaluation" en vez de un curso normal) sin romper las que
    // ya usan la redaccion estandar.
    public const TEXTOS_DEFAULT = [
        'certifica'  => 'CERTIFY THAT',
        'documento'  => 'Passport/ ID No.:',
        'completo'   => 'Has successfully completed the Training Course in:',
    ];

    // Las 6 oficinas/plantillas del sistema, cada una con su propia
    // estructura de fechas/lugar seleccionable en el editor. Hoy solo existen
    // dos formas de renderizado en el codigo (curso estandar vs. evaluacion
    // documental/PDE) -- TIPOS_DETALLE_PDE lista cuales de las 6 usan la
    // variante PDE; el resto usa la de curso estandar. Si alguna oficina
    // necesita a futuro una estructura propia distinta, se agrega su propia
    // rama en certificado_pdf_layout.php usando este mismo valor.
    public const TIPOS_DETALLE = [
        'india'            => 'India',
        'india_pde'        => 'India PDE',
        'india_assessment' => 'India Assessment',
        'panama_omi'       => 'Panamá OMI',
        'panama_no_omi'    => 'Panamá No OMI',
        'grecia'           => 'Grecia',
    ];

    public const TIPOS_DETALLE_PDE = ['india_pde'];
    public const TIPOS_DETALLE_ASSESSMENT = ['india_assessment'];

    // Posiciones/tamaños por defecto (% del ancho/alto de la hoja carta),
    // basadas en el diseño real de rucmapamel.com. El editor visual permite
    // ajustarlas por plantilla sin tocar codigo.
    public const LAYOUT_DEFAULT = [
        'logoIzq'    => ['x' => 3,  'y' => 3,  'w' => 12],
        'logoDer'    => ['x' => 85, 'y' => 3,  'w' => 12],
        'encabezado' => ['x' => 18, 'y' => 3,  'w' => 64, 'size' => 9],
        'titulo'     => ['x' => 8,  'y' => 17, 'w' => 84, 'size' => 13],
        'codigo'     => ['x' => 5,  'y' => 20, 'w' => 55, 'size' => 9],
        'certifica'  => ['x' => 8,  'y' => 22, 'w' => 84, 'size' => 10],
        'nombre'     => ['x' => 8,  'y' => 25, 'w' => 84, 'size' => 20],
        'documento'  => ['x' => 8,  'y' => 30, 'w' => 84, 'size' => 9],
        'completo'   => ['x' => 8,  'y' => 32.5, 'w' => 84, 'size' => 9.5],
        'curso'      => ['x' => 8,  'y' => 35, 'w' => 84, 'size' => 14],
        'subtitulo'  => ['x' => 8,  'y' => 44, 'w' => 84, 'size' => 10],
        'legal'      => ['x' => 10, 'y' => 47, 'w' => 80, 'size' => 8.5],
        'detalle'    => ['x' => 10, 'y' => 62, 'w' => 80, 'size' => 9.5],
        'resolucion' => ['x' => 10, 'y' => 72, 'w' => 80, 'size' => 8.5],
        'qr'         => ['x' => 41, 'y' => 82, 'w' => 14],
        'firma'      => ['x' => 62, 'y' => 78, 'w' => 18],
        'firmaTexto' => ['x' => 58, 'y' => 90, 'w' => 26, 'size' => 9],
        'firma2'     => ['x' => 8,  'y' => 78, 'w' => 18],
        'firmaTexto2' => ['x' => 4, 'y' => 90, 'w' => 26, 'size' => 9],
    ];

    // Elementos de texto a los que se les puede cambiar el tamaño de letra
    // desde el editor (los de imagen -- logos, firma, QR -- no aplican).
    public const ELEMENTOS_CON_TAMANO = [
        'encabezado', 'titulo', 'codigo', 'certifica', 'nombre', 'documento', 'completo', 'curso', 'subtitulo', 'legal', 'detalle', 'resolucion', 'firmaTexto', 'firmaTexto2',
    ];

    // Etiquetas de los elementos del lienzo/dropdown del editor -- metodo (no
    // const) porque necesitan pasar por lang() segun el idioma activo de la
    // sesion (panel bilingue ingles/espanol).
    public static function elementos(): array
    {
        return [
            'logoIzq'    => lang('PlantillaEditor.ElLogoIzq'),
            'logoDer'    => lang('PlantillaEditor.ElLogoDer'),
            'encabezado' => lang('PlantillaEditor.ElEncabezado'),
            'titulo'     => lang('PlantillaEditor.ElTitulo'),
            'codigo'     => lang('PlantillaEditor.ElCodigo'),
            'certifica'  => lang('PlantillaEditor.ElCertifica'),
            'nombre'     => lang('PlantillaEditor.ElNombre'),
            'documento'  => lang('PlantillaEditor.ElDocumento'),
            'completo'   => lang('PlantillaEditor.ElCompleto'),
            'curso'      => lang('PlantillaEditor.ElCurso'),
            'subtitulo'  => lang('PlantillaEditor.ElSubtitulo'),
            'legal'      => lang('PlantillaEditor.ElLegal'),
            'detalle'    => lang('PlantillaEditor.ElDetalle'),
            'resolucion' => lang('PlantillaEditor.ElResolucion'),
            'qr'         => lang('PlantillaEditor.ElQr'),
            'firma'      => lang('PlantillaEditor.ElFirma'),
            'firmaTexto' => lang('PlantillaEditor.ElFirmaTexto'),
            'firma2'     => lang('PlantillaEditor.ElFirma2'),
            'firmaTexto2' => lang('PlantillaEditor.ElFirmaTexto2'),
        ];
    }

    public function listar()
    {
        $plantillas = $this->orderBy('nombre')->findAll();

        foreach ($plantillas as $p) {
            $p->oficinas = $this->db->table('tbl_oficina')
                ->select('idOficina, nombre, descripcion')
                ->where('idPlantillaCertificado', $p->idPlantilla)
                ->get()
                ->getResult();
        }

        return $plantillas;
    }

    public function conOficinas(int $idPlantilla)
    {
        $plantilla = $this->find($idPlantilla);
        if (!$plantilla) {
            return null;
        }
        $plantilla->idOficinas = array_map(
            fn ($o) => (int) $o->idOficina,
            $this->db->table('tbl_oficina')->select('idOficina')->where('idPlantillaCertificado', $idPlantilla)->get()->getResult()
        );
        $plantilla->layout = self::layoutDe($plantilla);

        return $plantilla;
    }

    public static function layoutDe(object $plantilla): array
    {
        $guardado = $plantilla->layout ? json_decode($plantilla->layout, true) : [];
        if (!is_array($guardado)) {
            $guardado = [];
        }

        return array_replace_recursive(self::LAYOUT_DEFAULT, $guardado);
    }

    public function porOficina(int $idOficina)
    {
        $plantilla = $this->db->table('tbl_oficina o')
            ->select('p.*')
            ->join('tbl_plantillas_certificado p', 'p.idPlantilla = o.idPlantillaCertificado')
            ->where('o.idOficina', $idOficina)
            ->get()
            ->getRow();

        if ($plantilla) {
            $plantilla->layout = self::layoutDe($plantilla);
        }

        return $plantilla;
    }

    public function oficinas()
    {
        return $this->db->table('tbl_oficina')->where('estado', 'A')->orderBy('nombre')->get()->getResult();
    }

    public function asignarOficinas(int $idPlantilla, array $idOficinas): void
    {
        $this->db->table('tbl_oficina')->where('idPlantillaCertificado', $idPlantilla)->update(['idPlantillaCertificado' => null]);

        if (!empty($idOficinas)) {
            $this->db->table('tbl_oficina')->whereIn('idOficina', $idOficinas)->update(['idPlantillaCertificado' => $idPlantilla]);
        }
    }
}
