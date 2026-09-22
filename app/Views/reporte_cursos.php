<div class="p-4 md:p-6">
  <h1 class="text-xl font-semibold text-gray-800"><?= lang('Reportes.Title') ?></h1>

  <div class="mt-4"><?= view('reportes_tabs', ['tab' => $tab]) ?></div>

  <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
      <h2 class="text-sm font-semibold text-gray-800"><?= lang('Reportes.Top10Courses') ?></h2>
      <div class="mt-4">
        <canvas id="chartCursos" height="280"></canvas>
      </div>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
      <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-brand-50 sticky top-0">
            <tr class="text-left text-xs font-medium uppercase tracking-wide text-brand-800">
              <th class="px-4 py-3"><?= lang('Reportes.Course') ?></th>
              <th class="px-4 py-3 text-right"><?= lang('Reportes.Certificates') ?></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php if (empty($datos)) { ?>
            <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400"><?= lang('Reportes.NoCertificatesYet') ?></td></tr>
            <?php } foreach ($datos as $d) { ?>
            <tr>
              <td class="px-4 py-3 text-gray-700"><?= esc($d->curso) ?></td>
              <td class="px-4 py-3 text-right font-medium text-gray-900"><?= number_format($d->total) ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
window.CURSOS_CHART_DATA = {
  labels: <?= json_encode(array_map(fn($d) => mb_strimwidth($d->curso, 0, 40, '…'), array_slice($datos, 0, 10))) ?>,
  values: <?= json_encode(array_map(fn($d) => (int) $d->total, array_slice($datos, 0, 10))) ?>
};
</script>
