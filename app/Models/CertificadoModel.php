<?php namespace App\Models;

use CodeIgniter\Model;

class CertificadoModel extends Model
{
    protected $table         = 'tbl_certificados';
    protected $primaryKey    = 'idCertificado';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'idEstudiante', 'idOficina', 'idCurso', 'idModalidad', 'idCategoria', 'idUsuarioEmisor',
        'nombreCursoEmitido', 'nombreCursoIngesEmitido', 'nivelSTCWEmitido', 'reglasEmitidas', 'reglasIngesEmitidas',
        'horasEmitidas', 'modalidadEmitida', 'categoriaEmitida',
        'lugarEntrega', 'duracion', 'fechaInicio', 'fechaFinal', 'fechaEmision', 'fechaExpiracion',
        'codigo', 'code', 'hashcode', 'estado',
    ];

    // Mismos 104 prefijos de dos letras que usaba Form::getPrefijo() en rucma,
    // ciclando cada 100,000 certificados dentro de una misma oficina.
    private const PREFIJOS = [
        'AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
        'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
        'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
        'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ',
    ];

    public function categorias()
    {
        return $this->db->table('tbl_categoria')->where('estado', 'A')->orderBy('idCategoria')->get()->getResult();
    }

    public function modalidades()
    {
        return $this->db->table('tbl_modalidad')->where('estado', 'A')->orderBy('idModalidad')->get()->getResult();
    }

    // Cursos emitibles: los de la oficina (tbl_cursos.idOficina) que tengan
    // horas definidas para la categoria (New/Refresher) via tbl_curso_categoria_horas.
    public function cursosPorCategoria(?int $idCategoria = null, ?int $idOficina = null)
    {
        $builder = $this->db->table('tbl_cursos c')
            ->select('c.idCurso, c.codigo, c.nombre, c.nombreIngles, cch.horas, cch.idCategoria')
            ->join('tbl_curso_categoria_horas cch', 'cch.idCurso = c.idCurso')
            ->where('c.estado', 'A');

        // Cada curso pertenece a una oficina (tbl_cursos.idOficina); al emitir
        // un certificado solo se ofrecen los cursos de esa oficina.
        if ($idOficina !== null) {
            $builder->where('c.idOficina', $idOficina);
        }

        if (!empty($idCategoria)) {
            $builder->where('cch.idCategoria', $idCategoria);
        }

        return $builder->orderBy('c.nombre')->get()->getResult();
    }

    public function curso(int $idCurso)
    {
        return $this->db->table('tbl_cursos')->where('idCurso', $idCurso)->get()->getRow();
    }

    public function conDetalle(int $idCertificado)
    {
        return $this->db->table('tbl_certificados c')
            ->select('c.*, e.nombre, e.apellido, e.dni, o.nombre AS oficinaNombre')
            ->join('tbl_estudiantes e', 'e.idEstudiante = c.idEstudiante')
            ->join('tbl_oficina o', 'o.idOficina = c.idOficina', 'left')
            ->where('c.idCertificado', $idCertificado)
            ->get()
            ->getRow();
    }

    // Usado por la verificacion publica del QR (consult/validate/{codigo}) --
    // reproduce el mismo esquema salt+hash con el que se genera el
    // certificado en crear(): re-deriva el hash a partir del salt guardado y
    // lo compara con el guardado en la fila (hash_equals para evitar timing
    // attacks). Solo certificados vigentes (estado 'E') validan.
    public function validarPorCodigo(string $codigo): ?object
    {
        $certificado = $this->db->table('tbl_certificados c')
            ->select('c.*, e.nombre, e.apellido, e.dni, o.nombre AS oficinaNombre')
            ->join('tbl_estudiantes e', 'e.idEstudiante = c.idEstudiante')
            ->join('tbl_oficina o', 'o.idOficina = c.idOficina', 'left')
            ->where('c.codigo', $codigo)
            ->where('c.estado', 'E')
            ->get()
            ->getRow();

        if (!$certificado) {
            return null;
        }

        $hashEsperado = hash('sha512', $certificado->codigo . $certificado->code);
        if (!hash_equals($hashEsperado, $certificado->hashcode)) {
            return null;
        }

        return $certificado;
    }

    // Unico punto que arma el PDF de un certificado -- lo usan tanto la
    // generacion real (Certificado::pdf) como la vista previa del editor
    // de plantillas (Plantilla::preview), para que nunca se desincronicen.
    public function renderizarPdf(object $plantilla, object $certificado, string $urlValidacion): string
    {
        $imgPath = FCPATH . 'img' . DIRECTORY_SEPARATOR;
        $layout = $plantilla->layout ?? \App\Models\PlantillaModel::LAYOUT_DEFAULT;

        $html = view('certificado_pdf_layout', [
            'plantilla'     => $plantilla,
            'certificado'   => $certificado,
            'layout'        => $layout,
            'logoIzq'       => $plantilla->logoIzq ? $imgPath . $plantilla->logoIzq : null,
            'logoDer'       => $plantilla->logoDer ? $imgPath . $plantilla->logoDer : null,
            'firmaImg'      => $plantilla->firma ? $imgPath . $plantilla->firma : null,
            'firma2Img'     => $plantilla->firma2 ? $imgPath . $plantilla->firma2 : null,
            'urlValidacion' => $urlValidacion,
        ]);

        $mpdf = new \Mpdf\Mpdf([
            'format'        => 'Letter',
            'margin_top'    => 0,
            'margin_bottom' => 0,
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showWatermarkImage = true;
        $mpdf->watermarkImgBehind = true;
        $mpdf->SetWatermarkImage($imgPath . $plantilla->marco, 1, 'F', [0, 0]);

        if (($certificado->estado ?? null) === 'C') {
            $mpdf->SetWatermarkText(new \Mpdf\WatermarkText('CANCELLED', 90, 45, '#dc2626', 0.35));
            $mpdf->showWatermarkText = true;
        }

        $mpdf->WriteHTML($html);

        return $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
    }

    // Reproduce Form::consecutivo()/getPrefijo() de rucma: incrementa el
    // consecutivo de la oficina de forma atomica y arma el numero final.
    public function siguienteConsecutivo(int $idOficina): string
    {
        $this->db->transStart();
        $this->db->query(
            "UPDATE tbl_secuencias SET consecutivo = consecutivo + 1 WHERE tipo = 'certificado' AND idOficina = ?",
            [$idOficina]
        );
        $row = $this->db->query(
            "SELECT consecutivo, longitud FROM tbl_secuencias WHERE tipo = 'certificado' AND idOficina = ?",
            [$idOficina]
        )->getRow();
        $this->db->transComplete();

        $numero = (int) $row->consecutivo;
        $prefijo = self::PREFIJOS[intdiv($numero, 100000)] ?? 'ZZ';

        return sprintf('%s%05d', $prefijo, $numero % 100000);
    }

    public function generarCodigo(int $idOficina, int $idCurso): string
    {
        $oficina = $this->db->table('tbl_oficina')->where('idOficina', $idOficina)->get()->getRow();
        $curso = $this->curso($idCurso);
        $numCert = $this->siguienteConsecutivo($idOficina);

        return $oficina->prefijoCodigo . '-' . $curso->codigo . '-' . $numCert;
    }

    public function crear(array $datos): object
    {
        $curso = $this->curso($datos['idCurso']);
        $categoria = $this->db->table('tbl_categoria')->where('idCategoria', $datos['idCategoria'])->get()->getRow();
        $modalidad = $this->db->table('tbl_modalidad')->where('idModalidad', $datos['idModalidad'])->get()->getRow();

        $codigo = $this->generarCodigo($datos['idOficina'], $datos['idCurso']);
        $salt = bin2hex(random_bytes(3));
        $hashcode = hash('sha512', $codigo . $salt);

        $registro = [
            'idEstudiante'            => $datos['idEstudiante'],
            'idOficina'               => $datos['idOficina'],
            'idCurso'                 => $datos['idCurso'],
            'idModalidad'             => $datos['idModalidad'],
            'idCategoria'             => $datos['idCategoria'],
            'idUsuarioEmisor'         => $datos['idUsuarioEmisor'],
            'nombreCursoEmitido'      => $curso->nombre,
            'nombreCursoIngesEmitido' => $curso->nombreIngles,
            'nivelSTCWEmitido'        => $curso->nivelSTCW,
            'reglasEmitidas'          => $curso->reglas,
            'reglasIngesEmitidas'     => $curso->reglasIngles,
            'horasEmitidas'           => $datos['horas'] ?? null,
            'modalidadEmitida'        => $modalidad->descripcion ?? null,
            'categoriaEmitida'        => $categoria->descripcion ?? null,
            'lugarEntrega'            => $datos['lugarEntrega'],
            'duracion'                => $datos['duracion'],
            'fechaInicio'             => $datos['fechaInicio'],
            'fechaFinal'              => $datos['fechaFinal'],
            'fechaEmision'            => $datos['fechaEmision'],
            'fechaExpiracion'         => $datos['fechaExpiracion'],
            'codigo'                  => $codigo,
            'code'                    => $salt,
            'hashcode'                => $hashcode,
        ];

        $this->insert($registro);
        $registro['idCertificado'] = $this->getInsertID();

        return (object) $registro;
    }
}
