<?php
// Formato de fecha segun el idioma activo (en vez de arreglos de dias/meses
// en espanol codificados a mano) -- asi "hoy" sale en ingles o espanol solo.
$formateadorFecha = new IntlDateFormatter(service('language')->getLocale(), IntlDateFormatter::FULL, IntlDateFormatter::NONE);
$hoy = $formateadorFecha->format(time());

if (!function_exists('tiempoRelativo')) {
    function tiempoRelativo($fecha)
    {
        $diff = time() - strtotime($fecha);
        if ($diff < 60) return lang('Dashboard.JustNow');
        if ($diff < 3600) return str_replace('{n}', (string) floor($diff / 60), lang('Dashboard.MinAgo'));
        if ($diff < 86400) return str_replace('{n}', (string) floor($diff / 3600), lang('Dashboard.HoursAgo'));
        return str_replace('{n}', (string) floor($diff / 86400), lang('Dashboard.DaysAgo'));
    }
}
?>
<div class="p-4 md:p-6">

  <!-- Banner de bienvenida -->
  <div class="rounded-2xl bg-gradient-to-br from-brand-900 via-brand-800 to-brand-600 p-6 text-white shadow-lg md:p-8">
    <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-start">
      <div>
        <h1 class="text-2xl font-semibold"><?= lang('Dashboard.Welcome') ?> <?= esc($nombre) ?></h1>
        <p class="mt-1 text-sm text-brand-200"><?= lang('Dashboard.Subtitle') ?></p>
      </div>
      <span class="inline-flex w-fit items-center rounded-full bg-white/10 px-3 py-1 text-xs font-medium backdrop-blur"><?= esc($hoy) ?></span>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-xl bg-white/10 p-4 backdrop-blur">
        <div class="flex items-center gap-2 text-brand-200">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
          <span class="text-xs font-medium uppercase tracking-wide"><?= lang('Dashboard.CertificatesToday') ?></span>
        </div>
        <p class="mt-2 text-2xl font-semibold"><?= number_format($certificadosHoy) ?></p>
      </div>
      <div class="rounded-xl bg-white/10 p-4 backdrop-blur">
        <div class="flex items-center gap-2 text-brand-200">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
          <span class="text-xs font-medium uppercase tracking-wide"><?= lang('Dashboard.NewStudents') ?></span>
        </div>
        <p class="mt-2 text-2xl font-semibold"><?= number_format($estudiantesNuevosMes) ?></p>
      </div>
      <div class="rounded-xl bg-white/10 p-4 backdrop-blur">
        <div class="flex items-center gap-2 text-brand-200">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <span class="text-xs font-medium uppercase tracking-wide"><?= lang('Dashboard.ThisMonth') ?></span>
        </div>
        <p class="mt-2 text-2xl font-semibold"><?= number_format($certificadosMes) ?></p>
      </div>
      <div class="rounded-xl bg-white/10 p-4 backdrop-blur">
        <div class="flex items-center gap-2 text-brand-200">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/></svg>
          <span class="text-xs font-medium uppercase tracking-wide"><?= lang('Dashboard.ExpiringSoon30') ?></span>
        </div>
        <p class="mt-2 text-2xl font-semibold"><?= number_format($certificadosPorVencer) ?></p>
      </div>
    </div>
  </div>

  <!-- Tarjetas KPI -->
  <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
      <div class="flex items-center justify-between">
        <span class="rounded-lg bg-sky-50 p-2 text-sky-600">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        </span>
        <?php if ($crecimientoCertificados !== null) { ?>
        <span class="inline-flex items-center gap-1 text-xs font-medium <?= $crecimientoCertificados >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
          <?= $crecimientoCertificados >= 0 ? '↑' : '↓' ?> <?= abs($crecimientoCertificados) ?>%
        </span>
        <?php } ?>
      </div>
      <p class="mt-3 text-2xl font-semibold text-gray-900"><?= number_format($totalCertificados) ?></p>
      <p class="text-xs text-gray-500"><?= lang('Dashboard.CertificatesIssued') ?></p>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
      <span class="inline-flex rounded-lg bg-violet-50 p-2 text-violet-600">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-5.13a4 4 0 100-8 4 4 0 000 8zm6 3a4 4 0 100-8 4 4 0 000 8z"/></svg>
      </span>
      <p class="mt-3 text-2xl font-semibold text-gray-900"><?= number_format($totalEstudiantes) ?></p>
      <p class="text-xs text-gray-500"><?= lang('Dashboard.RegisteredStudents') ?></p>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
      <span class="inline-flex rounded-lg bg-cyan-50 p-2 text-cyan-600">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
      </span>
      <p class="mt-3 text-2xl font-semibold text-gray-900"><?= number_format($totalCursos) ?></p>
      <p class="text-xs text-gray-500"><?= lang('Dashboard.ActiveCourses') ?></p>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
      <span class="inline-flex rounded-lg bg-amber-50 p-2 text-amber-600">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/></svg>
      </span>
      <p class="mt-3 text-2xl font-semibold text-gray-900"><?= number_format($certificadosPorVencer) ?></p>
      <p class="text-xs text-gray-500"><?= lang('Dashboard.ExpiringIn30Days') ?></p>
    </div>
  </div>

  <!-- Certificados por mes + Actividad reciente -->
  <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200 lg:col-span-2">
      <h2 class="text-sm font-semibold text-gray-800"><?= lang('Dashboard.CertificatesByMonth') ?></h2>
      <p class="text-xs text-gray-500"><?= lang('Dashboard.Last') ?> <?= count($porMes) ?> <?= lang('Dashboard.Months') ?></p>

      <div class="mt-5 space-y-4">
        <?php $max = max(1, ...array_map(fn ($m) => $m->total, $porMes)); ?>
        <?php foreach ($porMes as $m) { ?>
        <div>
          <div class="mb-1 flex items-center justify-between text-sm">
            <span class="font-medium text-gray-700"><?= esc($m->label) ?></span>
            <span class="text-gray-500"><?= number_format($m->total) ?> <?= $m->total === 1 ? lang('Dashboard.Certificate') : lang('Dashboard.CertificatesPlural') ?></span>
          </div>
          <div class="h-2.5 w-full rounded-full bg-gray-100">
            <div class="h-2.5 rounded-full bg-brand-600" style="width: <?= (int) round(($m->total / $max) * 100) ?>%"></div>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>

    <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
      <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <div>
          <h2 class="text-sm font-semibold text-gray-800"><?= lang('Dashboard.RecentActivity') ?></h2>
        </div>
        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600">
          <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> <?= lang('Dashboard.Live') ?>
        </span>
      </div>
      <div class="max-h-[360px] divide-y divide-gray-100 overflow-y-auto">
        <?php if (empty($ultimos)) { ?>
        <p class="px-5 py-6 text-center text-sm text-gray-400"><?= lang('Dashboard.NoCertificatesYet') ?></p>
        <?php } ?>
        <?php foreach ($ultimos as $u) { ?>
        <a href="/certificados/<?= (int) $u->idCertificado ?>/ver" target="_blank" class="flex items-start gap-3 px-5 py-3 hover:bg-gray-50">
          <span class="mt-0.5 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
          </span>
          <div class="min-w-0 flex-1">
            <p class="text-sm text-gray-700">
              <?= lang('Dashboard.CertificateIssuedTo') ?> <span class="font-medium"><?= esc($u->nombre . ' ' . $u->apellido) ?></span>
            </p>
            <p class="truncate text-xs text-gray-500"><?= esc($u->nombreCursoEmitido) ?> · <?= esc($u->oficina) ?></p>
          </div>
          <span class="flex-shrink-0 text-xs text-gray-400"><?= tiempoRelativo($u->created_at ?? $u->fechaEmision) ?></span>
        </a>
        <?php } ?>
      </div>
    </div>
  </div>

  <!-- Acciones rapidas + Top oficinas -->
  <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200 <?= $porOficina === null ? 'lg:col-span-3' : 'lg:col-span-2' ?>">
      <h2 class="text-sm font-semibold text-gray-800"><?= lang('Dashboard.QuickActions') ?></h2>
      <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <a href="/certificados" class="rounded-lg bg-brand-700 p-4 text-white hover:bg-brand-800">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
          <p class="mt-2 text-sm font-medium"><?= lang('Dashboard.CreateCertificate') ?></p>
        </a>
        <a href="/students" class="rounded-lg bg-emerald-600 p-4 text-white hover:bg-emerald-700">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
          <p class="mt-2 text-sm font-medium"><?= lang('Dashboard.AddStudent') ?></p>
        </a>
        <a href="/reportes" class="rounded-lg bg-amber-500 p-4 text-white hover:bg-amber-600">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 15l4-6 4 3 4-7"/></svg>
          <p class="mt-2 text-sm font-medium"><?= lang('Dashboard.ViewReports') ?></p>
        </a>
        <a href="/cursos" class="rounded-lg bg-violet-600 p-4 text-white hover:bg-violet-700">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
          <p class="mt-2 text-sm font-medium"><?= lang('Dashboard.ViewCourses') ?></p>
        </a>
      </div>
    </div>

    <?php if ($porOficina !== null) { ?>
    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
      <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-800"><?= lang('Dashboard.TopOffices') ?></h2>
        <a href="/reportes" class="text-xs font-medium text-brand-600 hover:text-brand-800"><?= lang('Dashboard.ViewAll') ?> →</a>
      </div>
      <div class="mt-4 space-y-3">
        <?php $colores = ['bg-amber-400', 'bg-gray-300', 'bg-orange-400']; ?>
        <?php foreach (array_slice($porOficina, 0, 5) as $i => $o) { ?>
        <div class="flex items-center gap-3">
          <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full <?= $colores[$i] ?? 'bg-gray-100' ?> text-xs font-semibold text-white">
            <?= $i + 1 ?>
          </span>
          <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-medium text-gray-700"><?= esc($o->nombre . ' (' . $o->descripcion . ')') ?></p>
          </div>
          <span class="flex-shrink-0 text-sm font-semibold text-gray-800"><?= number_format($o->total) ?></span>
        </div>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
  </div>

  <!-- Acciones pendientes -->
  <?php if ($certificadosPorVencer > 0 || $isAdmin) { ?>
  <div class="mt-6 rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
    <h2 class="text-sm font-semibold text-gray-800"><?= lang('Dashboard.PendingActions') ?></h2>
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <?php if ($certificadosPorVencer > 0) { ?>
      <div class="flex items-center justify-between rounded-lg bg-amber-50 p-4 ring-1 ring-amber-100">
        <div>
          <p class="text-2xl font-semibold text-amber-700"><?= number_format($certificadosPorVencer) ?></p>
          <p class="text-sm font-medium text-gray-700"><?= lang('Dashboard.CertificatesExpiring') ?></p>
          <p class="text-xs text-gray-500"><?= lang('Dashboard.Next30Days') ?></p>
        </div>
        <a href="/reportes/vencer" class="rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-600"><?= lang('Dashboard.Review') ?></a>
      </div>
      <?php } ?>

      <?php if ($isAdmin && $oficinasSinPlantilla > 0) { ?>
      <div class="flex items-center justify-between rounded-lg bg-sky-50 p-4 ring-1 ring-sky-100">
        <div>
          <p class="text-2xl font-semibold text-sky-700"><?= number_format($oficinasSinPlantilla) ?></p>
          <p class="text-sm font-medium text-gray-700"><?= lang('Dashboard.OfficesWithoutTemplate') ?></p>
          <p class="text-xs text-gray-500"><?= lang('Dashboard.WontIssuePdfs') ?></p>
        </div>
        <a href="/plantillas" class="rounded-md bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-700"><?= lang('Dashboard.Configure') ?></a>
      </div>
      <?php } ?>

      <?php if ($isAdmin && $usuariosInactivos > 0) { ?>
      <div class="flex items-center justify-between rounded-lg bg-rose-50 p-4 ring-1 ring-rose-100">
        <div>
          <p class="text-2xl font-semibold text-rose-700"><?= number_format($usuariosInactivos) ?></p>
          <p class="text-sm font-medium text-gray-700"><?= lang('Dashboard.InactiveUsers') ?></p>
          <p class="text-xs text-gray-500"><?= lang('Dashboard.InactiveOrBlocked') ?></p>
        </div>
        <a href="/usuarios" class="rounded-md bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700"><?= lang('Dashboard.Review') ?></a>
      </div>
      <?php } ?>
    </div>
  </div>
  <?php } ?>

</div>
