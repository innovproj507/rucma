<div class="p-4 md:p-6">
  <h1 class="text-xl font-semibold text-gray-800"><?= lang('Reportes.Title') ?></h1>

  <div class="mt-4"><?= view('reportes_tabs', ['tab' => $tab]) ?></div>

  <form class="mt-4 flex items-end gap-3">
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Reportes.ExpirationWindow') ?></label>
      <select onchange="window.location='/reportes/vencer?dias='+this.value;"
              class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <option value="30" <?= $dias == 30 ? 'selected' : '' ?>><?= lang('Reportes.Next30Days') ?></option>
        <option value="60" <?= $dias == 60 ? 'selected' : '' ?>><?= lang('Reportes.Next60Days') ?></option>
        <option value="90" <?= $dias == 90 ? 'selected' : '' ?>><?= lang('Reportes.Next90Days') ?></option>
      </select>
    </div>
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
            <th class="px-4 py-3"><?= lang('Reportes.Expires') ?></th>
            <th class="px-4 py-3"><?= lang('Reportes.DaysRemaining') ?></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php if (empty($datos)) { ?>
          <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400"><?= lang('Reportes.NoneExpiring') ?></td></tr>
          <?php } foreach ($datos as $d) { ?>
          <tr>
            <td class="px-4 py-3 font-medium text-gray-700"><?= esc($d->codigo) ?></td>
            <td class="px-4 py-3"><?= esc($d->nombre . ' ' . $d->apellido) ?></td>
            <td class="px-4 py-3"><?= esc($d->dni) ?></td>
            <td class="px-4 py-3 text-gray-500"><?= esc($d->nombreCursoEmitido) ?></td>
            <td class="px-4 py-3"><?= esc($d->oficina) ?></td>
            <td class="px-4 py-3"><?= date('d/m/Y', strtotime($d->fechaExpiracion)) ?></td>
            <td class="px-4 py-3">
              <?php $dias = (int) $d->diasRestantes; ?>
              <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?= $dias <= 7 ? 'bg-rose-50 text-rose-700' : ($dias <= 30 ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-600') ?>">
                <?= $dias ?> <?= lang('Reportes.Days') ?>
              </span>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
