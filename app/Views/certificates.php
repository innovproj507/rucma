<div class="p-4 md:p-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="text-xl font-semibold text-gray-800"><?= lang('Translate.Certificates') ?></h1>
  </div>

  <div class="mt-4 flex flex-wrap items-end gap-3">
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Search') ?></label>
      <input type="search" id="busqueda" oninput="filterCertificates(1);" placeholder="<?= lang('CertificatesList.SearchPlaceholder') ?>"
             class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Status') ?></label>
      <select id="estadoFiltro" onchange="filterCertificates(1);"
              class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <option value=""><?= lang('Common.All') ?></option>
        <option value="E"><?= lang('CertificatesList.Issued') ?></option>
        <option value="C"><?= lang('CertificatesList.Cancelled') ?></option>
      </select>
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('CertificatesList.From') ?></label>
      <input type="date" id="desdeFiltro" onchange="filterCertificates(1);"
             class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('CertificatesList.To') ?></label>
      <input type="date" id="hastaFiltro" onchange="filterCertificates(1);"
             class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Show') ?></label>
      <select id="perPageFiltro" onchange="filterCertificates(1);"
              class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <?php foreach ($perPageOpciones as $opcion) { ?>
        <option value="<?= (int) $opcion ?>" <?= $opcion === $perPage ? 'selected' : '' ?>><?= (int) $opcion ?></option>
        <?php } ?>
      </select>
    </div>
  </div>

  <div class="mt-4 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-brand-50">
          <tr class="text-left text-xs font-medium uppercase tracking-wide text-brand-800">
            <th class="px-4 py-3"><?= lang('Common.Code') ?></th>
            <th class="px-4 py-3"><?= lang('CertificatesList.Student') ?></th>
            <th class="px-4 py-3">DNI</th>
            <th class="px-4 py-3"><?= lang('CertificatesList.Course') ?></th>
            <th class="px-4 py-3"><?= lang('CertificatesList.Office') ?></th>
            <th class="px-4 py-3"><?= lang('CertificatesList.Issuance') ?></th>
            <th class="px-4 py-3"><?= lang('CertificatesList.Expiration') ?></th>
            <th class="px-4 py-3"><?= lang('Common.Status') ?></th>
            <th class="px-4 py-3 text-right">PDF</th>
          </tr>
        </thead>
        <tbody id="tbCertificates" class="divide-y divide-gray-100">
          <?php if (empty($datos)) { ?>
          <tr><td colspan="9" class="px-4 py-6 text-center text-gray-400"><?= lang('Common.NoResults') ?></td></tr>
          <?php } ?>
          <?php foreach ($datos as $c) { ?>
          <tr>
            <td class="px-4 py-3 font-medium text-gray-700"><?= esc($c->codigo) ?></td>
            <td class="px-4 py-3"><?= esc($c->nombre . ' ' . $c->apellido) ?></td>
            <td class="px-4 py-3 text-gray-500"><?= esc($c->dni) ?></td>
            <td class="px-4 py-3"><?= esc($c->nombreCursoEmitido) ?></td>
            <td class="px-4 py-3"><?= esc($c->oficina) ?></td>
            <td class="px-4 py-3 text-gray-500"><?= esc($c->fechaEmision) ?></td>
            <td class="px-4 py-3 text-gray-500"><?= esc($c->fechaExpiracion) ?></td>
            <td class="px-4 py-3">
              <?php if (!empty($puedeCancelar)) { ?>
              <button type="button" onclick="cancelarCertificado(<?= (int) $c->idCertificado ?>, <?= esc(json_encode($c->codigo), 'attr') ?>, <?= esc(json_encode($c->estado), 'attr') ?>);"
                      title="<?= $c->estado === 'E' ? esc(lang('CertificatesList.CancelAction')) : esc(lang('CertificatesList.ReactivateAction')) ?>"
                      class="inline-flex items-center rounded-md border px-3 py-1.5 text-sm font-medium shadow-sm transition hover:shadow <?= $c->estado === 'E' ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100' ?>">
                <?= $c->estado === 'E' ? lang('CertificatesList.Issued') : lang('CertificatesList.Cancelled') ?>
              </button>
              <?php } elseif ($c->estado === 'E') { ?>
              <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700"><?= lang('CertificatesList.Issued') ?></span>
              <?php } else { ?>
              <span class="inline-flex rounded-full bg-rose-50 px-2 py-0.5 text-xs font-medium text-rose-600"><?= lang('CertificatesList.Cancelled') ?></span>
              <?php } ?>
            </td>
            <td class="px-4 py-3 text-right">
              <?php if ($puedeVerPdf) { ?>
              <a href="/certificados/<?= (int) $c->idCertificado ?>/ver" target="_blank" class="text-brand-600 hover:text-brand-900" title="<?= lang('CertificatesList.ViewPdf') ?>">
                <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </a>
              <a href="/certificados/<?= (int) $c->idCertificado ?>/descargar" class="ml-2 text-brand-600 hover:text-brand-900" title="<?= lang('CertificatesList.DownloadPdf') ?>">
                <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
              </a>
              <?php } ?>
              <?php if (!empty($puedeEliminar)) { ?>
              <button type="button" onclick="eliminarCertificado(<?= (int) $c->idCertificado ?>, <?= esc(json_encode($c->codigo), 'attr') ?>);" class="ml-2 text-rose-600 hover:text-rose-800" title="<?= esc(lang('CertificatesList.DeleteAction')) ?>">
                <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
              </button>
              <?php } ?>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <div class="flex items-center justify-between border-t border-gray-100 px-4 py-3 text-sm text-gray-500">
      <span id="paginadorInfo">
        <?= lang('Common.Showing') ?> <?= count($datos) ? (($pagina - 1) * $perPage + 1) : 0 ?>-<?= min($pagina * $perPage, $total) ?> <?= lang('Common.Of') ?> <?= $total ?>
      </span>
      <div class="flex gap-2">
        <button type="button" id="btnPagAnterior" onclick="cambiarPaginaCertificates(-1);" <?= $pagina <= 1 ? 'disabled' : '' ?>
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white">
          <?= lang('Common.Previous') ?>
        </button>
        <button type="button" id="btnPagSiguiente" onclick="cambiarPaginaCertificates(1);" <?= $pagina * $perPage >= $total ? 'disabled' : '' ?>
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white">
          <?= lang('Common.Next') ?>
        </button>
      </div>
    </div>
  </div>
</div>

<?= csrf_field() ?>
<script>
window.CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
window.CERTIFICATES_PAGINA_ACTUAL = <?= (int) $pagina ?>;
window.PUEDE_VER_PDF_CERTIFICATE = <?= $puedeVerPdf ? 'true' : 'false' ?>;
</script>
