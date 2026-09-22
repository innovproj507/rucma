<!DOCTYPE html>
<html lang="<?= esc(service('language')->getLocale()) ?>" class="h-full">
<head>
  <meta charset="UTF-8">
  <title>RUCMA · Acceso</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="color-scheme" content="light only">
  <link rel="icon" href="<?=base_url();?>/favicon.png?v=<?=VERSION?>">
  <link href="<?=base_url();?>/css/tailwind.css?v=<?=VERSION?>" rel="stylesheet">
</head>
<body class="h-full bg-gradient-to-br from-brand-900 via-brand-800 to-brand-600">
  <div class="flex min-h-full items-center justify-center px-4">
    <form action="/auth" method="post" class="w-full max-w-sm rounded-xl bg-white p-8 shadow-xl">
      <?= csrf_field() ?>

      <img src="<?=base_url();?>/img/logo2.png" class="mx-auto mb-4 h-40 w-40 object-contain" alt="RUCMA">
      <p class="mb-6 text-center text-lg font-semibold text-gray-800">RUCMA <span class="text-xs font-normal text-gray-400">v<?=VERSION?></span></p>

      <?php if (isset($msg) && $msg) { ?>
      <div class="mb-4 rounded-md bg-red-50 px-3 py-2 text-sm text-red-700"><?=esc($msg)?></div>
      <?php } ?>

      <div class="relative mb-4">
        <svg class="pointer-events-none absolute left-4 top-1/2 h-6 w-6 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
        </svg>
        <input type="text" name="user" placeholder="<?php echo lang('Translate.ingreseusuario'); ?>"
               class="w-full rounded-lg border border-gray-300 py-3.5 pl-12 pr-4 text-base focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
      </div>
      <div class="relative mb-6">
        <svg class="pointer-events-none absolute left-4 top-1/2 h-6 w-6 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
        </svg>
        <input type="password" name="pass" id="pass" placeholder="<?php echo lang('Translate.ingreseContreasena'); ?>"
               class="w-full rounded-lg border border-gray-300 py-3.5 pl-12 pr-12 text-base focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <button type="button" onclick="togglePass();" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-800" tabindex="-1">
          <svg id="eyeOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <svg id="eyeClosed" class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.243L9.88 9.88"/>
          </svg>
        </button>
      </div>

      <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-900 py-3.5 text-base font-medium text-white shadow-md transition hover:bg-brand-800 hover:shadow-lg">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H2.25"/>
        </svg>
        <?php echo lang('Translate.login'); ?>
      </button>
    </form>
  </div>

  <script>
    function togglePass() {
      const input = document.getElementById('pass');
      const eyeOpen = document.getElementById('eyeOpen');
      const eyeClosed = document.getElementById('eyeClosed');
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      eyeOpen.classList.toggle('hidden', isHidden);
      eyeClosed.classList.toggle('hidden', !isHidden);
    }
  </script>
</body>
</html>
