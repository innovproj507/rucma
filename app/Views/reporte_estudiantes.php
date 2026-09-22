<div class="p-4 md:p-6">
  <h1 class="text-xl font-semibold text-gray-800"><?= lang('Reportes.Title') ?></h1>

  <div class="mt-4"><?= view('reportes_tabs', ['tab' => $tab]) ?></div>

  <form action="/reportes/estudiantes" method="get" class="mt-4">
    <input type="search" name="busqueda" value="<?= esc($busqueda ?? '') ?>" placeholder="<?= lang('Reportes.SearchByNameLastNameDni') ?>"
           class="w-full max-w-md rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
  </form>

  <div class="mt-4 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-brand-50">
          <tr class="text-left text-xs font-medium uppercase tracking-wide text-brand-800">
            <th class="px-4 py-3"><?= lang('Common.Name') ?></th>
            <th class="px-4 py-3"><?= lang('Students.LastName') ?></th>
            <th class="px-4 py-3">DNI</th>
            <th class="px-4 py-3"><?= lang('Students.Country') ?></th>
            <th class="px-4 py-3 text-right"><?= lang('Reportes.Certificates') ?></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php if (empty($datos)) { ?>
          <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400"><?= lang('Common.NoResults') ?></td></tr>
          <?php } foreach ($datos as $d) { ?>
          <tr>
            <td class="px-4 py-3"><?= esc($d->nombre) ?></td>
            <td class="px-4 py-3"><?= esc($d->apellido) ?></td>
            <td class="px-4 py-3"><?= esc($d->dni) ?></td>
            <td class="px-4 py-3"><?= esc($d->pais) ?></td>
            <td class="px-4 py-3 text-right font-medium text-gray-900"><?= (int) $d->totalCertificados ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
