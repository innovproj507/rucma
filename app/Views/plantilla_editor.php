<div class="p-4 md:p-6">
  <div class="flex items-center justify-between">
    <div>
      <a href="/plantillas" class="text-sm text-brand-600 hover:text-brand-900">&larr; <?= lang('PlantillaEditor.BackToTemplates') ?></a>
      <h1 class="text-xl font-semibold text-gray-800"><?= lang('PlantillaEditor.EditorTitle') ?> <?= esc($plantilla->nombre) ?></h1>
    </div>
    <div class="flex gap-2">
      <button type="button" onclick="vistaPrevia();" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><?= lang('PlantillaEditor.PreviewPdf') ?></button>
      <button type="button" onclick="guardarEditor();" class="rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800"><?= lang('Common.Save') ?></button>
    </div>
  </div>

  <p class="mt-2 text-sm text-gray-500"><?= lang('PlantillaEditor.DragHint') ?></p>

  <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Lienzo -->
    <div class="lg:col-span-2">
      <div id="lienzo" class="relative mx-auto w-full max-w-[560px] select-none border border-gray-300 bg-white shadow-sm"
           style="aspect-ratio: 215.9 / 279.4; background-image: url('<?= base_url('img/' . $plantilla->marco) ?>'); background-size: cover; background-color: #fff; color-scheme: light;">
        <?php
        $camposImagen = ['logoIzq' => 'logoIzq', 'logoDer' => 'logoDer', 'firma' => 'firma', 'firma2' => 'firma2'];
        foreach ($elementos as $key => $etiqueta) {
          $pos = $layout[$key];
          $rutaImagen = isset($camposImagen[$key]) ? ($plantilla->{$camposImagen[$key]} ?? null) : null;
        ?>
        <div class="draggable absolute cursor-move rounded border border-dashed border-brand-500/70 hover:border-brand-700"
             data-key="<?= $key ?>"
             style="left: <?= $pos['x'] ?>%; top: <?= $pos['y'] ?>%; width: <?= $pos['w'] ?>%;">
          <?php if ($rutaImagen) { ?>
          <img src="<?= base_url('img/' . $rutaImagen) ?>" class="pointer-events-none block w-full" alt="<?= esc($etiqueta) ?>">
          <?php } elseif ($key === 'qr') { ?>
          <span class="block bg-white/80 px-1 py-0.5 text-center text-[8px] leading-tight text-brand-900">[QR]<br>Scan to Verify</span>
          <?php } elseif (!empty($vistaPrevia[$key])) { ?>
          <span class="block whitespace-pre-line bg-white/85 px-1 py-0.5 text-[8px] leading-tight text-gray-800"><?= esc($vistaPrevia[$key]) ?></span>
          <?php } else { ?>
          <span class="block bg-white/70 px-1 py-0.5 text-[9px] leading-tight text-brand-900"><?= esc($etiqueta) ?> <em class="text-gray-400"><?= lang('PlantillaEditor.Empty') ?></em></span>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
      <p class="mt-2 text-center text-xs text-gray-400"><?= lang('PlantillaEditor.ApproxPreview') ?></p>
    </div>

    <!-- Panel de propiedades -->
    <div class="space-y-4">
      <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <p class="mb-2 text-sm font-semibold text-gray-800"><?= lang('PlantillaEditor.SelectedElement') ?></p>
        <select id="elementoSeleccionado" onchange="cargarPropiedades();" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
          <?php foreach ($elementos as $key => $etiqueta) { ?>
          <option value="<?= $key ?>" data-tiene-tamano="<?= in_array($key, \App\Models\PlantillaModel::ELEMENTOS_CON_TAMANO, true) ? '1' : '0' ?>"><?= esc($etiqueta) ?></option>
          <?php } ?>
        </select>
        <div class="mt-3 grid grid-cols-4 gap-2">
          <div>
            <label class="mb-1 block text-xs text-gray-600">X %</label>
            <input type="number" step="0.5" id="propX" onchange="aplicarPropiedades();" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
          </div>
          <div>
            <label class="mb-1 block text-xs text-gray-600">Y %</label>
            <input type="number" step="0.5" id="propY" onchange="aplicarPropiedades();" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
          </div>
          <div>
            <label class="mb-1 block text-xs text-gray-600"><?= lang('Common.Width') ?> %</label>
            <input type="number" step="0.5" id="propW" onchange="aplicarPropiedades();" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
          </div>
          <div id="propTamanoWrap">
            <label class="mb-1 block text-xs text-gray-600"><?= lang('PlantillaEditor.LetterPt') ?></label>
            <input type="number" step="0.5" min="4" id="propTamano" onchange="aplicarPropiedades();" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
          </div>
        </div>
      </div>

      <form id="formEditor" enctype="multipart/form-data" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" id="idPlantilla" name="idPlantilla" value="<?= (int) $plantilla->idPlantilla ?>">
        <input type="hidden" id="nombre" name="nombre" value="<?= esc($plantilla->nombre) ?>">
        <input type="hidden" id="estado" name="estado" value="<?= esc($plantilla->estado) ?>">
        <input type="hidden" id="layout" name="layout">

        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
          <p class="mb-2 text-sm font-semibold text-gray-800"><?= lang('PlantillaEditor.Images') ?></p>
          <?php foreach (['marco' => lang('PlantillaEditor.Background'), 'logoIzq' => lang('PlantillaEditor.LeftLogo'), 'logoDer' => lang('PlantillaEditor.RightLogo'), 'firma' => lang('PlantillaEditor.Signature'), 'firma2' => lang('PlantillaEditor.SecondSignatureOptional')] as $campo => $etiqueta) { ?>
          <?php $rutaActual = $plantilla->{$campo} ?? null; ?>
          <div class="mb-3 last:mb-0">
            <label class="mb-1 block text-xs font-medium text-gray-700"><?= $etiqueta ?></label>
            <div class="mb-1.5 flex items-center gap-2">
              <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center overflow-hidden rounded border border-gray-200 bg-gray-50">
                <img id="preview_<?= $campo ?>" src="<?= $rutaActual ? base_url('img/' . $rutaActual) : '' ?>"
                     class="<?= $rutaActual ? '' : 'hidden' ?> h-full w-full object-contain" alt="">
                <svg id="previewVacio_<?= $campo ?>" class="<?= $rutaActual ? 'hidden' : '' ?> h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 6h18M3 6a2.25 2.25 0 00-2.25 2.25v9A2.25 2.25 0 003 19.5h18a2.25 2.25 0 002.25-2.25v-9A2.25 2.25 0 0021 6M3 6h18"/>
                </svg>
              </div>
              <span id="nombreArchivo_<?= $campo ?>" class="truncate text-xs text-gray-500">
                <?= $rutaActual ? esc(basename($rutaActual)) : lang('PlantillaEditor.NoImageAssigned') ?>
              </span>
            </div>
            <input type="file" name="<?= $campo ?>" onchange="previsualizarImagen(this, '<?= $campo ?>');"
                   accept="image/png,image/jpeg,image/svg+xml"
                   class="w-full text-xs text-gray-600 file:mr-2 file:rounded file:border-0 file:bg-gray-100 file:px-2 file:py-1 file:text-xs">
          </div>
          <?php } ?>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
          <p class="mb-2 text-sm font-semibold text-gray-800"><?= lang('PlantillaEditor.CertificateType') ?></p>
          <label class="mb-1 block text-xs font-medium text-gray-700"><?= lang('PlantillaEditor.DateStructure') ?></label>
          <select id="tipoDetalle" name="tipoDetalle" class="mb-3 w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
            <?php foreach (\App\Models\PlantillaModel::TIPOS_DETALLE as $valor => $etiqueta) { ?>
            <option value="<?= esc($valor) ?>" <?= ($plantilla->tipoDetalle ?? 'india') === $valor ? 'selected' : '' ?>>
              <?= esc($etiqueta) ?> — <?= in_array($valor, \App\Models\PlantillaModel::TIPOS_DETALLE_PDE, true) ? lang('PlantillaEditor.PdeStructure') : lang('PlantillaEditor.BothStructures') ?>
            </option>
            <?php } ?>
          </select>

          <label class="mb-1 block text-xs font-medium text-gray-700"><?= lang('PlantillaEditor.CertificateTitleOptional') ?></label>
          <input type="text" id="titulo" name="titulo" placeholder='Ej: PREVIOUS DOCUMENTARY EVALUATION (PDE)' value="<?= esc($plantilla->titulo ?? '') ?>" class="mb-3 w-full rounded-md border border-gray-300 px-2 py-1 text-sm">

          <label class="mb-1 block text-xs font-medium text-gray-700"><?= lang('PlantillaEditor.CertifyPhrase') ?> <span class="font-normal text-gray-400">(<?= esc(\App\Models\PlantillaModel::TEXTOS_DEFAULT['certifica']) ?>)</span></label>
          <input type="text" id="textoCertifica" name="textoCertifica" placeholder="<?= esc(\App\Models\PlantillaModel::TEXTOS_DEFAULT['certifica']) ?>" value="<?= esc($plantilla->textoCertifica ?? '') ?>" class="mb-3 w-full rounded-md border border-gray-300 px-2 py-1 text-sm">

          <label class="mb-1 block text-xs font-medium text-gray-700"><?= lang('PlantillaEditor.DocumentLabel') ?> <span class="font-normal text-gray-400">(<?= esc(\App\Models\PlantillaModel::TEXTOS_DEFAULT['documento']) ?>, <?= lang('PlantillaEditor.DocumentLabelHint') ?>)</span></label>
          <textarea id="textoDocumento" name="textoDocumento" rows="2" placeholder="<?= esc(\App\Models\PlantillaModel::TEXTOS_DEFAULT['documento']) ?>" class="mb-3 w-full rounded-md border border-gray-300 px-2 py-1 text-sm"><?= esc($plantilla->textoDocumento ?? '') ?></textarea>

          <label class="mb-1 block text-xs font-medium text-gray-700"><?= lang('PlantillaEditor.CompletedPhrase') ?> <span class="font-normal text-gray-400">(<?= esc(\App\Models\PlantillaModel::TEXTOS_DEFAULT['completo']) ?>)</span></label>
          <input type="text" id="textoCompleto" name="textoCompleto" placeholder="<?= esc(\App\Models\PlantillaModel::TEXTOS_DEFAULT['completo']) ?>" value="<?= esc($plantilla->textoCompleto ?? '') ?>" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
        </div>

        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
          <p class="mb-2 text-sm font-semibold text-gray-800"><?= lang('PlantillaEditor.Header') ?></p>
          <textarea id="encabezado" name="encabezado" rows="4" class="w-full rounded-md border border-gray-300 px-2 py-1 text-xs"><?= esc($plantilla->encabezado ?? '') ?></textarea>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
          <p class="text-sm font-semibold text-gray-800"><?= lang('PlantillaEditor.LegalText') ?></p>
          <p class="mb-2 text-xs text-gray-500"><?= lang('PlantillaEditor.LegalTextHint') ?></p>
          <textarea id="textoLegal" name="textoLegal" rows="3" class="w-full rounded-md border border-gray-300 px-2 py-1 text-xs"><?= esc($plantilla->textoLegal ?? '') ?></textarea>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
          <p class="mb-2 text-sm font-semibold text-gray-800"><?= lang('PlantillaEditor.ResolutionText') ?></p>
          <textarea id="textoResolucion" name="textoResolucion" rows="3" class="w-full rounded-md border border-gray-300 px-2 py-1 text-xs"><?= esc($plantilla->textoResolucion ?? '') ?></textarea>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
          <p class="mb-2 text-sm font-semibold text-gray-800"><?= lang('PlantillaEditor.SignatureSection') ?></p>
          <label class="mb-1 block text-xs font-medium text-gray-700"><?= lang('PlantillaEditor.Name') ?></label>
          <input type="text" id="firmaNombre" name="firmaNombre" value="<?= esc($plantilla->firmaNombre ?? '') ?>" class="mb-2 w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
          <label class="mb-1 block text-xs font-medium text-gray-700"><?= lang('PlantillaEditor.Title') ?></label>
          <input type="text" id="firmaCargo" name="firmaCargo" value="<?= esc($plantilla->firmaCargo ?? '') ?>" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
        </div>

        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
          <p class="mb-2 text-sm font-semibold text-gray-800"><?= lang('PlantillaEditor.SecondSignatureOptional') ?></p>
          <label class="mb-1 block text-xs font-medium text-gray-700"><?= lang('PlantillaEditor.Name') ?></label>
          <input type="text" id="firmaNombre2" name="firmaNombre2" value="<?= esc($plantilla->firmaNombre2 ?? '') ?>" class="mb-2 w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
          <label class="mb-1 block text-xs font-medium text-gray-700"><?= lang('PlantillaEditor.Title') ?></label>
          <input type="text" id="firmaCargo2" name="firmaCargo2" value="<?= esc($plantilla->firmaCargo2 ?? '') ?>" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
        </div>

        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
          <p class="mb-2 text-sm font-semibold text-gray-800"><?= lang('PlantillaEditor.VersionSection') ?></p>
          <input type="text" id="textoVersion" name="textoVersion" placeholder="Ej: V. 03/26" value="<?= esc($plantilla->textoVersion ?? '') ?>" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
        </div>
      </form>
    </div>
  </div>
</div>

<script>
window.CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
window.PLANTILLA_LAYOUT = <?= json_encode($layout) ?>;
window.PLANTILLA_ID = <?= (int) $plantilla->idPlantilla ?>;
</script>
