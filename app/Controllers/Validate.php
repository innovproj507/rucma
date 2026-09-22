<?php

namespace App\Controllers;

use App\Models\CertificadoModel;

// Verificacion publica de certificados via el codigo QR impreso en el PDF
// (ver certificado_pdf_layout.php: "Scan to Verify or Verify Online at
// {base_url}/consult/validate/{codigo}"). Sin login -- cualquiera con el
// codigo (empleador, autoridad, etc.) puede confirmar que un certificado es
// autentico y esta vigente.
class Validate extends BaseController
{
    public function certificado(string $codigo)
    {
        $certificado = (new CertificadoModel())->validarPorCodigo($codigo);

        return view('validate_certificado', [
            'title'       => 'Verificación de Certificado',
            'codigo'      => $codigo,
            'certificado' => $certificado,
        ]);
    }
}
