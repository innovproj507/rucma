<!DOCTYPE html>
<html lang="<?= esc(service('language')->getLocale()) ?>" class="h-full">
<head>
  <meta charset="UTF-8">
  <title><?=$title?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
  <meta name="color-scheme" content="light only">
  <link rel="icon" href="<?=base_url();?>/favicon.png?v=<?=VERSION?>">
  <link href="<?=base_url();?>/css/tailwind.css?v=<?=VERSION?>" rel="stylesheet">
</head>
<body class="h-full bg-gray-50 text-gray-900">
<div class="flex h-screen overflow-hidden">

  <!-- Sidebar (desktop) -->
  <aside class="hidden md:flex md:w-64 md:flex-shrink-0 md:flex-col bg-brand-900">
    <div class="flex items-center gap-3 px-4 py-5 border-b border-brand-800/60">
      <img src="<?=base_url();?>/img/logo.png" class="h-10 w-10 object-contain" alt="RUCMA">
      <div>
        <p class="text-white font-semibold leading-tight">RUCMA</p>
        <p class="text-brand-300 text-xs">v<?=VERSION?></p>
      </div>
    </div>
    <nav class="flex-1 space-y-1 px-2 py-4">
      <?php $permisos = session()->get('permisos') ?? []; ?>
      <a href="/dashboard" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white">
        <svg class="h-5 w-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"/></svg>
        <?php echo lang('Translate.Home'); ?>
      </a>
      <?php if (in_array('certificados.ver', $permisos, true)) { ?>
      <a href="/certificados" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white">
        <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <?php echo lang('Translate.CreateCertificates'); ?>
      </a>
      <?php } ?>
      <?php if (in_array('estudiantes.ver', $permisos, true)) { ?>
      <a href="/students" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white">
        <svg class="h-5 w-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-5.13a4 4 0 100-8 4 4 0 000 8zm6 3a4 4 0 100-8 4 4 0 000 8z"/></svg>
        <?php echo lang('Translate.Students'); ?>
      </a>
      <?php } ?>
      <?php if (in_array('certificados.ver_listado', $permisos, true)) { ?>
      <a href="/certificates" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white">
        <svg class="h-5 w-5 text-lime-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        <?php echo lang('Translate.Certificates'); ?>
      </a>
      <?php } ?>
      <?php if (in_array('reportes.ver_emitidos', $permisos, true)) { ?>
      <a href="/reportes" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white">
        <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 15l4-6 4 3 4-7"/></svg>
        <?php echo lang('Translate.Reports'); ?>
      </a>
      <?php } ?>
      <?php if (in_array('cursos.ver', $permisos, true)) { ?>
      <a href="/cursos" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white">
        <svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
        <?php echo lang('Translate.Courses'); ?>
      </a>
      <?php } ?>
      <?php if (in_array('plantillas.ver', $permisos, true)) { ?>
      <a href="/plantillas" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white">
        <svg class="h-5 w-5 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
        <?php echo lang('Translate.Templates'); ?>
      </a>
      <?php } ?>
      <?php if (in_array('usuarios.ver', $permisos, true)) { ?>
      <a href="/usuarios" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white">
        <svg class="h-5 w-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
        <?php echo lang('Translate.Users'); ?>
      </a>
      <?php } ?>
      <?php if (in_array('roles.ver', $permisos, true)) { ?>
      <a href="/roles" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white">
        <svg class="h-5 w-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        <?php echo lang('Translate.Roles'); ?>
      </a>
      <?php } ?>
    </nav>
    <div class="px-2 py-4 border-t border-brand-800/60">
      <a href="/logout" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white">
        <svg class="h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 5v1a3 3 0 01-3 3H6a3 3 0 01-3-3V6a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        <?php echo lang('Translate.Logout'); ?>
      </a>
    </div>
  </aside>

  <!-- Sidebar (mobile) -->
  <div id="mobileSidebar" class="fixed inset-0 z-40 hidden md:hidden">
    <div class="absolute inset-0 bg-black/50" onclick="toggleSidebar(false)"></div>
    <div class="relative flex w-64 flex-col bg-brand-900 h-full">
      <div class="flex items-center gap-3 px-4 py-5 border-b border-brand-800/60">
        <img src="<?=base_url();?>/img/logo.png" class="h-10 w-10 object-contain" alt="RUCMA">
        <p class="text-white font-semibold">RUCMA</p>
      </div>
      <nav class="flex-1 space-y-1 px-2 py-4">
        <a href="/dashboard" class="block rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white"><?php echo lang('Translate.Home'); ?></a>
        <?php if (in_array('certificados.ver', $permisos, true)) { ?>
        <a href="/certificados" class="block rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white"><?php echo lang('Translate.CreateCertificates'); ?></a>
        <?php } ?>
        <?php if (in_array('estudiantes.ver', $permisos, true)) { ?>
        <a href="/students" class="block rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white"><?php echo lang('Translate.Students'); ?></a>
        <?php } ?>
        <?php if (in_array('certificados.ver_listado', $permisos, true)) { ?>
        <a href="/certificates" class="block rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white"><?php echo lang('Translate.Certificates'); ?></a>
        <?php } ?>
        <?php if (in_array('reportes.ver_emitidos', $permisos, true)) { ?>
        <a href="/reportes" class="block rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white"><?php echo lang('Translate.Reports'); ?></a>
        <?php } ?>
        <?php if (in_array('cursos.ver', $permisos, true)) { ?>
        <a href="/cursos" class="block rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white"><?php echo lang('Translate.Courses'); ?></a>
        <?php } ?>
        <?php if (in_array('plantillas.ver', $permisos, true)) { ?>
        <a href="/plantillas" class="block rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white"><?php echo lang('Translate.Templates'); ?></a>
        <?php } ?>
        <?php if (in_array('usuarios.ver', $permisos, true)) { ?>
        <a href="/usuarios" class="block rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white"><?php echo lang('Translate.Users'); ?></a>
        <?php } ?>
        <?php if (in_array('roles.ver', $permisos, true)) { ?>
        <a href="/roles" class="block rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white"><?php echo lang('Translate.Roles'); ?></a>
        <?php } ?>
        <a href="/logout" class="block rounded-md px-3 py-2 text-sm font-medium text-brand-100 hover:bg-brand-800 hover:text-white"><?php echo lang('Translate.Logout'); ?></a>
      </nav>
    </div>
  </div>

  <div class="flex flex-1 flex-col overflow-hidden">
    <header class="flex items-center justify-between border-b-2 border-brand-600 bg-white px-4 py-3 md:px-6">
      <button type="button" class="md:hidden text-gray-500" onclick="toggleSidebar(true)">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
      </button>
      <div class="ml-auto flex items-center gap-3">
        <?php $idiomaActual = service('language')->getLocale(); ?>
        <div class="flex overflow-hidden rounded-md border border-gray-300 text-xs font-medium">
          <a href="/idioma/en" class="px-2 py-1 <?= $idiomaActual === 'en' ? 'bg-brand-900 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' ?>">EN</a>
          <a href="/idioma/es" class="px-2 py-1 <?= $idiomaActual === 'es' ? 'bg-brand-900 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' ?>">ES</a>
        </div>
        <?php if (session()->get('isAdmin')) { ?>
        <select onchange="window.location='/oficina-vista/' + this.value;"
                class="rounded-md border border-gray-300 px-2 py-1 text-sm text-gray-700 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
          <option value="0" <?= session()->get('oficinaVista') === null ? 'selected' : '' ?>><?php echo lang('Translate.AllOffices'); ?></option>
          <?php foreach (session()->get('oficinasDisponibles') ?? [] as $o) { ?>
          <option value="<?= (int) $o->idOficina ?>" <?= (int) session()->get('oficinaVista') === (int) $o->idOficina ? 'selected' : '' ?>>
            <?= esc($o->nombre . ' (' . $o->descripcion . ')') ?>
          </option>
          <?php } ?>
        </select>
        <?php } ?>
        <span class="text-sm font-medium text-gray-700"><?=esc($nombre ?? '')?></span>
        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto">
