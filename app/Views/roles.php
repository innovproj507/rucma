<div class="p-4 md:p-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="text-xl font-semibold text-gray-800"><?= lang('Roles.Title') ?></h1>
    <?php if ($puedeCrear) { ?>
    <a href="/roles/nuevo" class="inline-flex items-center gap-2 rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800">
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
      <?= lang('Roles.AddRole') ?>
    </a>
    <?php } ?>
  </div>

  <div class="mt-4 flex flex-wrap items-end gap-3">
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Search') ?></label>
      <input type="search" id="busqueda" oninput="filterRoles(1);" placeholder="<?= lang('Roles.SearchPlaceholder') ?>"
             class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Status') ?></label>
      <select id="estadoFiltro" onchange="filterRoles(1);"
              class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <option value=""><?= lang('Common.All') ?></option>
        <option value="A"><?= lang('Common.Active') ?></option>
        <option value="I"><?= lang('Common.Inactive') ?></option>
      </select>
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Show') ?></label>
      <select id="perPageFiltro" onchange="filterRoles(1);"
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
            <th class="px-4 py-3"><?= lang('Roles.Role') ?></th>
            <th class="px-4 py-3"><?= lang('Roles.Description') ?></th>
            <th class="px-4 py-3 text-center"><?= lang('Roles.Users') ?></th>
            <th class="px-4 py-3 text-center"><?= lang('Roles.Permissions') ?></th>
            <th class="px-4 py-3"><?= lang('Common.Status') ?></th>
            <th class="px-4 py-3 text-right"><?= lang('Common.Edit') ?></th>
          </tr>
        </thead>
        <tbody id="tbRoles" class="divide-y divide-gray-100">
          <?php if (empty($datos)) { ?>
          <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400"><?= lang('Common.NoResults') ?></td></tr>
          <?php } ?>
          <?php foreach ($datos as $r) { ?>
          <tr>
            <td class="px-4 py-3 font-medium text-gray-700"><?= esc($r->nombre) ?></td>
            <td class="px-4 py-3 text-gray-500"><?= esc($r->descripcion) ?></td>
            <td class="px-4 py-3 text-center"><?= (int) $r->totalUsuarios ?></td>
            <td class="px-4 py-3 text-center"><?= (int) $r->totalPermisos ?> / <?= (int) $totalPermisosDisponibles ?></td>
            <td class="px-4 py-3">
              <?php if ($r->estado === 'A') { ?>
              <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700"><?= lang('Common.Active') ?></span>
              <?php } else { ?>
              <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600"><?= esc($r->estado) ?></span>
              <?php } ?>
            </td>
            <td class="px-4 py-3 text-right">
              <?php if ($puedeEditar) { ?>
              <a href="/roles/<?= (int) $r->idRol ?>/editar" class="text-brand-600 hover:text-brand-900">
                <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 19.5H4.5"/></svg>
              </a>
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
        <button type="button" id="btnPagAnterior" onclick="cambiarPaginaRoles(-1);" <?= $pagina <= 1 ? 'disabled' : '' ?>
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white">
          <?= lang('Common.Previous') ?>
        </button>
        <button type="button" id="btnPagSiguiente" onclick="cambiarPaginaRoles(1);" <?= $pagina * $perPage >= $total ? 'disabled' : '' ?>
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white">
          <?= lang('Common.Next') ?>
        </button>
      </div>
    </div>
  </div>

  <p class="mt-3 text-xs text-gray-400"><?= lang('Roles.PermissionsNote') ?></p>
</div>

<script>
window.ROLES_PAGINA_ACTUAL = <?= (int) $pagina ?>;
</script>
