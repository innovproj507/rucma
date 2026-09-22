<div class="p-4 md:p-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="text-xl font-semibold text-gray-800"><?= lang('Plantillas.Title') ?></h1>
    <button type="button" onclick="nuevaPlantilla();"
            class="inline-flex items-center gap-2 rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800">
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
      <?= lang('Plantillas.AddTemplate') ?>
    </button>
  </div>

  <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($datos as $p) { ?>
    <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
      <img src="<?= base_url('img/' . $p->marco) ?>" class="h-40 w-full object-cover object-top">
      <div class="p-4">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-gray-800"><?= esc($p->nombre) ?></h3>
          <?php if ($p->estado === 'A') { ?>
          <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700"><?= lang('Plantillas.Active') ?></span>
          <?php } else { ?>
          <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600"><?= lang('Plantillas.InactiveFem') ?></span>
          <?php } ?>
        </div>
        <div class="mt-2 flex flex-wrap gap-1">
          <?php if (empty($p->oficinas)) { ?>
          <span class="text-xs text-gray-400"><?= lang('Plantillas.NoOfficesAssigned') ?></span>
          <?php } foreach ($p->oficinas as $o) { ?>
          <span class="inline-flex rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-800"><?= esc($o->nombre . ' ' . $o->descripcion) ?></span>
          <?php } ?>
        </div>
        <div class="mt-3 flex items-center gap-4">
          <button type="button" onclick="editarPlantilla(<?= (int) $p->idPlantilla ?>);"
                  class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-900">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 19.5H4.5"/></svg>
            <?= lang('Plantillas.Edit') ?>
          </button>
          <a href="/plantillas/<?= (int) $p->idPlantilla ?>/editor" class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-900">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>
            <?= lang('Plantillas.Design') ?>
          </a>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</div>

<!-- Modal Agregar/Editar Plantilla -->
<div id="modalPlantilla" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/50" onclick="cerrarModalPlantilla();"></div>
  <div class="relative flex min-h-full items-center justify-center p-4">
    <div class="w-full max-w-2xl rounded-lg bg-white shadow-xl">
      <form id="formPlantilla" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="gestionarOficinas" value="1">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
          <h3 class="text-base font-semibold text-gray-800" id="tituloModalPlantilla"><?= lang('Plantillas.AddTemplate') ?></h3>
          <button type="button" onclick="cerrarModalPlantilla();" class="text-gray-400 hover:text-gray-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="max-h-[70vh] space-y-4 overflow-y-auto px-6 py-4">
          <input type="hidden" id="idPlantilla" name="idPlantilla">

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Common.Name') ?> *</label>
              <input type="text" id="nombre" name="nombre" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Common.Status') ?></label>
              <select id="estado" name="estado" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <option value="A"><?= lang('Plantillas.Active') ?></option>
                <option value="I"><?= lang('Plantillas.InactiveFem') ?></option>
              </select>
            </div>
          </div>

          <?php
          $campos = [
            'marco'   => lang('Plantillas.BackgroundLabel'),
            'logoIzq' => lang('Plantillas.LeftLogoLabel'),
            'logoDer' => lang('Plantillas.RightLogoLabel'),
            'firma'   => lang('Plantillas.SignatureLabel'),
          ];
          foreach ($campos as $campo => $etiqueta) { ?>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700"><?= $etiqueta ?></label>
            <div class="flex items-center gap-3">
              <img id="preview_<?= $campo ?>" class="hidden h-14 w-14 rounded border border-gray-200 object-contain bg-gray-50">
              <input type="file" id="<?= $campo ?>" name="<?= $campo ?>" accept="image/png,image/jpeg" onchange="previsualizar('<?= $campo ?>');"
                     class="w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium hover:file:bg-gray-200">
            </div>
          </div>
          <?php } ?>

          <div class="rounded-md bg-gray-50 p-3">
            <p class="mb-2 text-sm font-medium text-gray-700"><?= lang('Plantillas.OfficesUsingTemplate') ?></p>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
              <?php foreach ($oficinas as $o) { ?>
              <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="idOficinas[]" value="<?= (int) $o->idOficina ?>" class="oficina-checkbox rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                <?= esc($o->nombre . ' ' . $o->descripcion) ?>
              </label>
              <?php } ?>
            </div>
          </div>
        </div>
        <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">
          <button type="button" onclick="cerrarModalPlantilla();" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><?= lang('Common.Cancel') ?></button>
          <button type="button" onclick="guardarPlantilla();" class="rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800"><?= lang('Common.Save') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>window.CSRF_TOKEN_NAME = '<?= csrf_token() ?>';</script>
