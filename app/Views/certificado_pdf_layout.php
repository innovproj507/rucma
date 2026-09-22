<?php
// Convierte % de la hoja carta (215.9mm x 279.4mm) a mm absolutos.
$mm = function (array $pos) {
    return [
        'top'   => round($pos['y'] / 100 * 279.4, 2),
        'left'  => round($pos['x'] / 100 * 215.9, 2),
        'width' => round(($pos['w'] ?? 20) / 100 * 215.9, 2),
    ];
};
// Estilo inline de tamano de letra por elemento (configurable por plantilla
// en el editor); si no tiene "size" definido usa el tamano por defecto de la
// clase CSS de abajo.
$fs = function (string $key) use ($layout) {
    return isset($layout[$key]['size']) ? 'font-size:' . $layout[$key]['size'] . 'pt;' : '';
};
// Texto fijo de la plantilla (frases "CERTIFY THAT", "has completed...",
// etiqueta de documento) si lo definio, si no el texto estandar por defecto
// -- asi una plantilla especial (p.ej. PDE) puede tener su propia redaccion
// sin afectar a las demas.
$txt = function (string $campoPlantilla, string $claveDefault) use ($plantilla) {
    return !empty($plantilla->{$campoPlantilla}) ? $plantilla->{$campoPlantilla} : \App\Models\PlantillaModel::TEXTOS_DEFAULT[$claveDefault];
};
// India Assessment tiene su propia variante: documento reubicado y alineado a
// la izquierda, texto legal en itálica, y la resolución dentro del bloque de
// detalle (antes de las fechas) en vez de como bloque aparte al final.
$esAssessment = in_array($plantilla->tipoDetalle ?? 'india', \App\Models\PlantillaModel::TIPOS_DETALLE_ASSESSMENT, true);
?>
<style>
  body { font-family: helvetica, sans-serif; color: #222; margin: 0; }
  .el { position: absolute; }
  .el img { width: 100%; height: auto; display: block; }
  .txt-center { text-align: center; }
  .nombre { font-size: 20pt; font-weight: bold; color: #111; text-transform: uppercase; margin: 0; text-align: center; }
  .documento { font-size: 9pt; color: #444; margin: 0; text-align: center; }
  .curso { font-size: 14pt; font-weight: bold; color: #111; margin: 0; text-align: center; }
  .encabezado { font-size: 9pt; text-align: center; line-height: 1.4; }
  .legal, .resolucion { font-size: 8.5pt; color: #333; text-align: center; line-height: 1.4; }
  .codigo { font-size: 9pt; color: #333; }
  .detalle table { font-size: inherit; color: #333; border-collapse: collapse; }
  .detalle td { text-align: left; vertical-align: top; }
  .firma-texto { text-align: center; font-size: 9pt; color: #333; border-top: 0.5pt solid #333; padding-top: 1mm; }
  .certifica, .completo { font-size: 10pt; color: #333; text-align: center; margin: 0; }
  .titulo { font-size: 13pt; font-weight: bold; color: #111; text-align: center; margin: 0; text-transform: uppercase; }
  .subtitulo { font-size: 10pt; color: #333; text-align: center; margin: 0; }
</style>

<?php if ($logoIzq) { $p = $mm($layout['logoIzq']); ?>
<div class="el" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm;"><img src="<?= $logoIzq ?>"></div>
<?php } ?>

<?php if ($logoDer) { $p = $mm($layout['logoDer']); ?>
<div class="el" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm;"><img src="<?= $logoDer ?>"></div>
<?php } ?>

<?php if (!empty($plantilla->encabezado)) { $p = $mm($layout['encabezado']); ?>
<div class="el encabezado" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('encabezado') ?>"><?= nl2br(esc($plantilla->encabezado)) ?></div>
<?php } ?>

<?php if (!empty($plantilla->titulo)) { $p = $mm($layout['titulo']); ?>
<div class="el titulo" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('titulo') ?>"><?= esc($plantilla->titulo) ?></div>
<?php } ?>

<?php $p = $mm($layout['codigo']); ?>
<div class="el codigo" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('codigo') ?>">Certificate No.: <strong><?= esc($certificado->codigo) ?></strong></div>

<?php $p = $mm($layout['certifica']); ?>
<div class="el certifica" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('certifica') ?>"><?= esc($txt('textoCertifica', 'certifica')) ?></div>

<?php $p = $mm($layout['nombre']); ?>
<div class="el nombre" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('nombre') ?>"><?= esc($certificado->nombre . ' ' . $certificado->apellido) ?></div>

<?php $p = $mm($layout['documento']); ?>
<div class="el documento" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('documento') ?> <?= $esAssessment ? 'text-align:left;' : '' ?>"><?= nl2br(esc($txt('textoDocumento', 'documento'))) ?> <?= esc($certificado->dni) ?></div>

<?php $p = $mm($layout['completo']); ?>
<div class="el completo" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('completo') ?>"><?= esc($txt('textoCompleto', 'completo')) ?></div>

<?php $p = $mm($layout['curso']); ?>
<div class="el curso" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('curso') ?>"><?= esc($certificado->nombreCursoIngesEmitido ?: $certificado->nombreCursoEmitido) ?></div>

<?php if (!empty($certificado->nivelSTCWEmitido)) { $p = $mm($layout['subtitulo']); ?>
<div class="el subtitulo" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('subtitulo') ?>"><?= esc($certificado->nivelSTCWEmitido) ?></div>
<?php } ?>

<?php
// El texto legal es del curso (cada curso cita su propia norma STCW); el
// texto de la plantilla queda solo como respaldo si el curso no tiene uno.
// En Assessment el texto legal es una cita fija de la plantilla (la misma
// resolucion de evaluacion de competencia para cualquier curso/rango), no la
// norma STCW propia de un curso de entrenamiento -- por eso no usa el
// respaldo del curso como las demas estructuras.
$textoLegal = $esAssessment
    ? ($plantilla->textoLegal ?? null)
    : ($certificado->reglasIngesEmitidas ?? $certificado->reglasEmitidas ?? $plantilla->textoLegal ?? null);
if (!empty($textoLegal)) { $p = $mm($layout['legal']); ?>
<div class="el legal" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('legal') ?> <?= $esAssessment ? 'font-style:italic;' : '' ?>"><?= nl2br(esc($textoLegal)) ?></div>
<?php } ?>

<?php $p = $mm($layout['detalle']); $colDetalle = round($p['width'] / 2, 2); ?>
<div class="el detalle" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('detalle') ?>">
  <?php if (in_array($plantilla->tipoDetalle ?? 'india', \App\Models\PlantillaModel::TIPOS_DETALLE_PDE, true)) { ?>
  <p style="margin:0;">Place and date of performed: <strong><?= esc($certificado->lugarEntrega) ?>, <?= $certificado->fechaInicio ? date('M-d-Y', strtotime($certificado->fechaInicio)) : '' ?></strong></p>
  <p style="margin:0;">Place and date of issue: <strong><?= esc($certificado->lugarEntrega) ?>, <?= $certificado->fechaEmision ? date('M-d-Y', strtotime($certificado->fechaEmision)) : '' ?></strong></p>
  <?php } elseif ($esAssessment) { ?>
  <table>
    <tr>
      <td width="<?= $colDetalle ?>mm">Start date: <strong><?= $certificado->fechaInicio ? date('M-d-Y', strtotime($certificado->fechaInicio)) : '' ?></strong></td>
      <td width="<?= $colDetalle ?>mm">End Date: <strong><?= $certificado->fechaFinal ? date('M-d-Y', strtotime($certificado->fechaFinal)) : '' ?></strong></td>
    </tr>
    <tr>
      <td width="<?= $colDetalle ?>mm">Place: <strong><?= esc($certificado->lugarEntrega) ?></strong></td>
      <td width="<?= $colDetalle ?>mm"></td>
    </tr>
  </table>
  <p style="margin:3mm 0 0;">Date of Issue: <strong><?= $certificado->fechaEmision ? date('M-d-Y', strtotime($certificado->fechaEmision)) : '' ?></strong></p>
  <?php } else { ?>
  <table>
    <tr>
      <td width="<?= $colDetalle ?>mm">From: <strong><?= $certificado->fechaInicio ? date('M-d-Y', strtotime($certificado->fechaInicio)) : '' ?></strong></td>
      <td width="<?= $colDetalle ?>mm">To: <strong><?= $certificado->fechaFinal ? date('M-d-Y', strtotime($certificado->fechaFinal)) : '' ?></strong></td>
    </tr>
    <tr>
      <td width="<?= $colDetalle ?>mm">Method: <strong><?= esc($certificado->modalidadEmitida) ?></strong></td>
      <td width="<?= $colDetalle ?>mm">Place: <strong><?= esc($certificado->lugarEntrega) ?></strong></td>
    </tr>
    <tr>
      <td width="<?= $colDetalle ?>mm">Duration: <strong><?= esc($certificado->horasEmitidas) ?> hours</strong></td>
      <td width="<?= $colDetalle ?>mm"></td>
    </tr>
  </table>
  <table style="margin-top:3mm;">
    <tr>
      <td width="<?= $colDetalle ?>mm">Issue: <strong><?= $certificado->fechaEmision ? date('M-d-Y', strtotime($certificado->fechaEmision)) : '' ?></strong></td>
      <td width="<?= $colDetalle ?>mm">Expiry: <strong><?= $certificado->fechaExpiracion ? date('M-d-Y', strtotime($certificado->fechaExpiracion)) : '' ?></strong></td>
    </tr>
  </table>
  <?php } ?>
</div>

<?php if (!empty($plantilla->textoResolucion)) { $p = $mm($layout['resolucion']); ?>
<div class="el resolucion" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('resolucion') ?>"><?= nl2br(esc($plantilla->textoResolucion)) ?></div>
<?php } ?>

<?php $p = $mm($layout['qr']); ?>
<div class="el txt-center" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm;">
  <barcode code="<?= esc($urlValidacion) ?>" type="QR" size="0.8" error="M" disableborder="1" />
  <div style="font-size:6.5pt; color:#555;">Scan to Verify or Verify Online at<br><?= esc($urlValidacion) ?></div>
  <?php if (!empty($plantilla->textoVersion)) { ?>
  <div style="font-size:6.5pt; color:#555;"><?= esc($plantilla->textoVersion) ?></div>
  <?php } ?>
</div>

<?php if ($firmaImg) { $p = $mm($layout['firma']); ?>
<div class="el txt-center" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm;"><img src="<?= $firmaImg ?>"></div>
<?php } ?>

<?php if (!empty($plantilla->firmaNombre)) { $p = $mm($layout['firmaTexto']); ?>
<div class="el firma-texto" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('firmaTexto') ?>">
  <?= esc($plantilla->firmaNombre) ?><br><?= esc($plantilla->firmaCargo) ?>
</div>
<?php } ?>

<?php if ($firma2Img) { $p = $mm($layout['firma2']); ?>
<div class="el txt-center" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm;"><img src="<?= $firma2Img ?>"></div>
<?php } ?>

<?php if (!empty($plantilla->firmaNombre2)) { $p = $mm($layout['firmaTexto2']); ?>
<div class="el firma-texto" style="top:<?= $p['top'] ?>mm; left:<?= $p['left'] ?>mm; width:<?= $p['width'] ?>mm; <?= $fs('firmaTexto2') ?>">
  <?= esc($plantilla->firmaNombre2) ?><br><?= esc($plantilla->firmaCargo2) ?>
</div>
<?php } ?>
