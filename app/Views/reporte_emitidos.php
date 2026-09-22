<div class="p-4 md:p-6">
  <h1 class="text-xl font-semibold text-gray-800"><?= lang('Reportes.Title') ?></h1>

  <div class="mt-4"><?= view('reportes_tabs', ['tab' => $tab]) ?></div>

  <form id="formFiltro" class="mt-4 flex flex-wrap items-end gap-3">
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Search') ?></label>
      <input type="text" id="busqueda" name="busqueda" placeholder="<?= lang('Reportes.SearchPlaceholder') ?>"
             class="w-56 rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Status') ?></label>
      <select id="estado" name="estado" class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <option value=""><?= lang('Common.All') ?></option>
        <option value="E"><?= lang('CertificatesList.Issued') ?></option>
        <option value="C"><?= lang('Reportes.Cancelled') ?></option>
        <option value="A"><?= lang('Reportes.Voided') ?></option>
        <option value="R"><?= lang('Reportes.Revoked') ?></option>
      </select>
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Reportes.IssuedFrom') ?></label>
      <input type="date" id="desde" name="desde" class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Reportes.IssuedTo') ?></label>
      <input type="date" id="hasta" name="hasta" class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
    </div>
    <button type="button" onclick="filtrarEmitidos();" class="rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800"><?= lang('Reportes.Filter') ?></button>
    <button type="button" onclick="exportarEmitidos();" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><?= lang('Reportes.ExportCsv') ?></button>
  </form>

  <div class="mt-4 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-brand-50">
          <tr class="text-left text-xs font-medium uppercase tracking-wide text-brand-800">
            <th class="px-4 py-3"><?= lang('Common.Code') ?></th>
            <th class="px-4 py-3"><?= lang('Reportes.Student') ?></th>
            <th class="px-4 py-3">DNI</th>
            <th class="px-4 py-3"><?= lang('Reportes.Course') ?></th>
            <th class="px-4 py-3"><?= lang('Reportes.Office') ?></th>
            <th class="px-4 py-3"><?= lang('Reportes.Issuance') ?></th>
            <th class="px-4 py-3"><?= lang('Reportes.Expiration') ?></th>
            <th class="px-4 py-3"><?= lang('Common.Status') ?></th>
            <th class="px-4 py-3 text-right">PDF</th>
          </tr>
        </thead>
        <tbody id="tbReporte" class="divide-y divide-gray-100">
          <?php if (empty($datos)) { ?>
          <tr><td colspan="9" class="px-4 py-6 text-center text-gray-400"><?= lang('Common.NoResults') ?></td></tr>
          <?php } foreach ($datos as $d) { ?>
          <tr>
            <td class="px-4 py-3 font-medium text-gray-700"><?= esc($d->codigo) ?></td>
            <td class="px-4 py-3"><?= esc($d->nombre . ' ' . $d->apellido) ?></td>
            <td class="px-4 py-3"><?= esc($d->dni) ?></td>
            <td class="px-4 py-3 text-gray-500"><?= esc($d->nombreCursoEmitido) ?></td>
            <td class="px-4 py-3"><?= esc($d->oficina) ?></td>
            <td class="px-4 py-3"><?= $d->fechaEmision ? date('d/m/Y', strtotime($d->fechaEmision)) : '-' ?></td>
            <td class="px-4 py-3"><?= $d->fechaExpiracion ? date('d/m/Y', strtotime($d->fechaExpiracion)) : '-' ?></td>
            <td class="px-4 py-3"><?= esc($d->estado) ?></td>
            <td class="px-4 py-3 text-right">
              <a href="/certificados/<?= (int) $d->idCertificado ?>/ver" target="_blank" class="text-brand-600 hover:text-brand-900">
                <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
