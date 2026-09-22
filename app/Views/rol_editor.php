<div class="p-4 md:p-6">
  <a href="/roles" class="text-sm text-brand-600 hover:text-brand-900">&larr; <?= lang('Roles.BackToRoles') ?></a>
  <h1 class="mt-1 text-xl font-semibold text-gray-800"><?= $rol->idRol ? lang('Roles.EditRole') : lang('Roles.AddRole') ?></h1>

  <form id="formRol" class="mt-4 space-y-6">
    <?= csrf_field() ?>
    <input type="hidden" id="idRol" name="idRol" value="<?= (int) ($rol->idRol ?? 0) ?>">

    <div class="max-w-3xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="sm:col-span-2">
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Roles.NameReq') ?></label>
          <input type="text" id="nombre" name="nombre" value="<?= esc($rol->nombre) ?>" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Common.Status') ?></label>
          <select id="estado" name="estado" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="A" <?= $rol->estado === 'A' ? 'selected' : '' ?>><?= lang('Common.Active') ?></option>
            <option value="I" <?= $rol->estado === 'I' ? 'selected' : '' ?>><?= lang('Common.Inactive') ?></option>
          </select>
        </div>
      </div>
      <div class="mt-4">
        <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Roles.Description') ?></label>
        <input type="text" id="descripcion" name="descripcion" value="<?= esc($rol->descripcion ?? '') ?>" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
      </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
      <h2 class="mb-4 text-sm font-semibold text-gray-800"><?= lang('Roles.Permissions') ?></h2>

      <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
        <?php foreach ($permisosPorModulo as $modulo => $permisos) { ?>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
          <div class="mb-3 flex items-center justify-between border-b border-gray-200 pb-2">
            <p class="text-sm font-semibold text-gray-700"><?= esc($modulo) ?></p>
            <label class="flex items-center gap-1.5 text-xs text-gray-500">
              <input type="checkbox" class="modulo-toggle rounded border-gray-300 text-brand-600 focus:ring-brand-500" data-modulo="<?= esc($modulo) ?>" onchange="toggleModulo(this);">
              <?= lang('Roles.MarkAll') ?>
            </label>
          </div>
          <div class="space-y-2.5">
            <?php foreach ($permisos as $p) { ?>
            <label class="flex items-start gap-2 text-sm text-gray-700">
              <input type="checkbox" name="idPermisos[]" value="<?= (int) $p->idPermiso ?>"
                     class="permiso-checkbox mt-0.5 rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                     data-modulo="<?= esc($modulo) ?>"
                     <?= in_array((int) $p->idPermiso, $rol->idPermisos, true) ? 'checked' : '' ?>>
              <span><?= esc($p->descripcion) ?> <span class="text-gray-400">(<?= esc($p->codigo) ?>)</span></span>
            </label>
            <?php } ?>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>

    <div class="flex justify-end gap-3">
      <a href="/roles" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><?= lang('Common.Cancel') ?></a>
      <button type="button" onclick="guardarRol();" class="rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800"><?= lang('Common.Save') ?></button>
    </div>
  </form>
</div>

<script>window.CSRF_TOKEN_NAME = '<?= csrf_token() ?>';</script>
