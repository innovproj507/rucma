<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
  <meta charset="UTF-8">
  <title>RUCMA · Verificación de Certificado</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="color-scheme" content="light only">
  <link rel="icon" href="<?=base_url();?>/favicon.png?v=<?=VERSION?>">
  <link href="<?=base_url();?>/css/tailwind.css?v=<?=VERSION?>" rel="stylesheet">
</head>
<body class="min-h-full bg-gradient-to-br from-brand-900 via-brand-800 to-brand-600">
  <div class="flex min-h-full items-center justify-center px-4 py-10">
    <div class="w-full max-w-lg rounded-xl bg-white p-8 shadow-xl">
      <img src="<?=base_url();?>/img/logo2.png" class="mx-auto mb-4 h-24 w-24 object-contain" alt="RUCMA">
      <p class="mb-6 text-center text-lg font-semibold text-gray-800">Verificación de Certificado</p>

      <?php if ($certificado) { ?>
        <?php $expirado = !empty($certificado->fechaExpiracion) && strtotime($certificado->fechaExpiracion) < time(); ?>
        <div class="mb-4 flex items-center gap-3 rounded-md <?= $expirado ? 'bg-amber-50 text-amber-800' : 'bg-emerald-50 text-emerald-800' ?> px-4 py-3 text-sm">
          <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span><?= $expirado ? 'Certificado válido, pero su vigencia expiró.' : 'Certificado válido y vigente.' ?></span>
        </div>

        <dl class="divide-y divide-gray-100 rounded-lg border border-gray-200 text-sm">
          <div class="flex justify-between px-4 py-3"><dt class="text-gray-500">Nombre</dt><dd class="font-medium text-gray-800"><?= esc($certificado->nombre . ' ' . $certificado->apellido) ?></dd></div>
          <div class="flex justify-between px-4 py-3"><dt class="text-gray-500">Documento</dt><dd class="font-medium text-gray-800"><?= esc($certificado->dni) ?></dd></div>
          <div class="flex justify-between px-4 py-3"><dt class="text-gray-500">Curso</dt><dd class="text-right font-medium text-gray-800"><?= esc($certificado->nombreCursoIngesEmitido ?: $certificado->nombreCursoEmitido) ?></dd></div>
          <div class="flex justify-between px-4 py-3"><dt class="text-gray-500">Código</dt><dd class="font-mono font-medium text-gray-800"><?= esc($certificado->codigo) ?></dd></div>
          <div class="flex justify-between px-4 py-3"><dt class="text-gray-500">Oficina</dt><dd class="font-medium text-gray-800"><?= esc($certificado->oficinaNombre ?? '-') ?></dd></div>
          <?php if (!empty($certificado->fechaEmision)) { ?>
          <div class="flex justify-between px-4 py-3"><dt class="text-gray-500">Fecha de emisión</dt><dd class="font-medium text-gray-800"><?= esc(date('M-d-Y', strtotime($certificado->fechaEmision))) ?></dd></div>
          <?php } ?>
          <?php if (!empty($certificado->fechaExpiracion)) { ?>
          <div class="flex justify-between px-4 py-3"><dt class="text-gray-500">Fecha de expiración</dt><dd class="font-medium text-gray-800"><?= esc(date('M-d-Y', strtotime($certificado->fechaExpiracion))) ?></dd></div>
          <?php } ?>
        </dl>
      <?php } else { ?>
        <div class="mb-4 flex items-center gap-3 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
          <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          <span>Este código no corresponde a un certificado válido o vigente.</span>
        </div>
        <p class="text-center font-mono text-sm text-gray-400"><?= esc($codigo) ?></p>
      <?php } ?>

      <a href="<?=base_url();?>" class="mt-6 block w-full rounded-lg bg-brand-900 px-4 py-3 text-center text-sm font-medium text-white hover:bg-brand-800">Volver al inicio</a>
    </div>
  </div>
</body>
</html>
