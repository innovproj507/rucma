<div class="p-4 md:p-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="text-xl font-semibold text-gray-800"><?= lang('Usuarios.Title') ?></h1>
    <?php if ($puedeCrear) { ?>
    <button type="button" onclick="nuevoUsuario();"
            class="inline-flex items-center gap-2 rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800">
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
      <?= lang('Usuarios.AddUser') ?>
    </button>
    <?php } ?>
  </div>

  <div class="mt-4 flex flex-wrap items-end gap-3">
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Search') ?></label>
      <input type="search" id="busqueda" oninput="filterUsuarios(1);" placeholder="<?= lang('Usuarios.SearchPlaceholder') ?>"
             class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Usuarios.Office') ?></label>
      <select id="idOficinaFiltro" onchange="filterUsuarios(1);"
              class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <option value=""><?= lang('Common.All') ?></option>
        <?php foreach ($oficinas as $o) { ?>
        <option value="<?= (int) $o->idOficina ?>"><?= esc($o->nombre . ' (' . $o->descripcion . ')') ?></option>
        <?php } ?>
      </select>
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Usuarios.Role') ?></label>
      <select id="idRolFiltro" onchange="filterUsuarios(1);"
              class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <option value=""><?= lang('Common.All') ?></option>
        <?php foreach ($rolesDisponibles as $r) { ?>
        <option value="<?= (int) $r->idRol ?>"><?= esc($r->nombre) ?></option>
        <?php } ?>
      </select>
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Status') ?></label>
      <select id="estadoFiltro" onchange="filterUsuarios(1);"
              class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <option value=""><?= lang('Common.All') ?></option>
        <option value="A"><?= lang('Common.Active') ?></option>
        <option value="I"><?= lang('Common.Inactive') ?></option>
        <option value="B"><?= lang('Usuarios.Blocked') ?></option>
      </select>
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Show') ?></label>
      <select id="perPageFiltro" onchange="filterUsuarios(1);"
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
            <th class="px-4 py-3"><?= lang('Usuarios.Username') ?></th>
            <th class="px-4 py-3"><?= lang('Common.Name') ?></th>
            <th class="px-4 py-3"><?= lang('Usuarios.Email') ?></th>
            <th class="px-4 py-3"><?= lang('Usuarios.Office') ?></th>
            <th class="px-4 py-3"><?= lang('Usuarios.Roles') ?></th>
            <th class="px-4 py-3"><?= lang('Common.Status') ?></th>
            <th class="px-4 py-3 text-right"><?= lang('Common.Edit') ?></th>
          </tr>
        </thead>
        <tbody id="tbUsuarios" class="divide-y divide-gray-100">
          <?php if (empty($datos)) { ?>
          <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400"><?= lang('Common.NoResults') ?></td></tr>
          <?php } ?>
          <?php foreach ($datos as $u) { ?>
          <tr>
            <td class="px-4 py-3 font-medium text-gray-700"><?= esc($u->usuario) ?></td>
            <td class="px-4 py-3"><?= esc($u->nombre . ' ' . $u->apellido) ?></td>
            <td class="px-4 py-3 text-gray-500"><?= esc($u->correo) ?></td>
            <td class="px-4 py-3"><?= esc($u->oficina ? $u->oficina . ' (' . $u->oficinaDescripcion . ')' : '-') ?></td>
            <td class="px-4 py-3">
              <?php foreach ($u->roles as $r) { ?>
              <span class="mr-1 inline-flex rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-800"><?= esc($r->nombre) ?></span>
              <?php } ?>
            </td>
            <td class="px-4 py-3">
              <?php if ($u->estado === 'A') { ?>
              <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700"><?= lang('Common.Active') ?></span>
              <?php } else { ?>
              <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600"><?= esc($u->estado) ?></span>
              <?php } ?>
            </td>
            <td class="px-4 py-3 text-right">
              <div class="flex items-center justify-end gap-3">
                <?php if ($puedeEditar) { ?>
                <button type="button" onclick="editarUsuario(<?= (int) $u->idUser ?>);" class="text-brand-600 hover:text-brand-900" title="<?= lang('Common.Edit') ?>">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 19.5H4.5"/></svg>
                </button>
                <?php } ?>
                <?php if ($puedeEliminar) { ?>
                <button type="button" onclick="eliminarUsuario(<?= (int) $u->idUser ?>, '<?= esc($u->usuario, 'js') ?>');" class="text-rose-600 hover:text-rose-800" title="<?= lang('Common.Delete') ?>">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                </button>
                <?php } ?>
              </div>
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
        <button type="button" id="btnPagAnterior" onclick="cambiarPaginaUsuarios(-1);" <?= $pagina <= 1 ? 'disabled' : '' ?>
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white">
          <?= lang('Common.Previous') ?>
        </button>
        <button type="button" id="btnPagSiguiente" onclick="cambiarPaginaUsuarios(1);" <?= $pagina * $perPage >= $total ? 'disabled' : '' ?>
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white">
          <?= lang('Common.Next') ?>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Agregar/Editar Usuario -->
<div id="modalUsuario" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/50" onclick="cerrarModalUsuario();"></div>
  <div class="relative flex min-h-full items-center justify-center p-4">
    <div class="w-full max-w-lg rounded-lg bg-white shadow-xl">
      <form id="formUsuario">
        <?= csrf_field() ?>
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
          <h3 class="text-base font-semibold text-gray-800" id="tituloModalUsuario"><?= lang('Usuarios.AddUser') ?></h3>
          <button type="button" onclick="cerrarModalUsuario();" class="text-gray-400 hover:text-gray-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="max-h-[70vh] space-y-4 overflow-y-auto px-6 py-4">
          <input type="hidden" id="idUser" name="idUser">

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Students.FirstNameReq') ?></label>
              <input type="text" id="nombre" name="nombre" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Usuarios.LastNameOptional') ?></label>
              <input type="text" id="apellido" name="apellido" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Usuarios.UsernameReq') ?></label>
              <input type="text" id="usuario" name="usuario" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Usuarios.Email') ?></label>
              <input type="email" id="correo" name="correo" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Usuarios.Password') ?> <span id="passwordHint" class="font-normal text-gray-400"><?= lang('Usuarios.PasswordRequiredOnCreate') ?></span></label>
            <input type="password" id="password" name="password" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" autocomplete="new-password">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Usuarios.Office') ?> <span id="oficinaRequerida" class="text-rose-600">*</span></label>
              <select id="idOficina" name="idOficina" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <option value=""><?= lang('Usuarios.OfficeNone') ?></option>
                <?php foreach ($oficinas as $o) { ?>
                <option value="<?= (int) $o->idOficina ?>"><?= esc($o->nombre . ' (' . $o->descripcion . ')') ?></option>
                <?php } ?>
              </select>
              <p class="mt-1 text-xs text-gray-400"><?= lang('Usuarios.OfficeRequiredHint') ?></p>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Common.Status') ?></label>
              <select id="estado" name="estado" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <option value="A"><?= lang('Common.Active') ?></option>
                <option value="I"><?= lang('Common.Inactive') ?></option>
                <option value="B"><?= lang('Usuarios.Blocked') ?></option>
              </select>
            </div>
          </div>

          <div class="rounded-md bg-gray-50 p-3">
            <p class="mb-2 text-sm font-medium text-gray-700"><?= lang('Usuarios.Roles') ?></p>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
              <?php foreach ($rolesDisponibles as $r) { ?>
              <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="idRoles[]" value="<?= (int) $r->idRol ?>" data-nombre="<?= esc($r->nombre) ?>"
                       class="rol-checkbox rounded border-gray-300 text-brand-600 focus:ring-brand-500" onchange="actualizarOficinaRequerida();">
                <?= esc($r->nombre) ?>
              </label>
              <?php } ?>
            </div>
          </div>
        </div>
        <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">
          <button type="button" onclick="cerrarModalUsuario();" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><?= lang('Common.Cancel') ?></button>
          <button type="button" onclick="guardarUsuario();" class="rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800"><?= lang('Common.Save') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
window.CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
window.PUEDE_EDITAR_USUARIO = <?= $puedeEditar ? 'true' : 'false' ?>;
window.PUEDE_ELIMINAR_USUARIO = <?= $puedeEliminar ? 'true' : 'false' ?>;
window.USUARIOS_PAGINA_ACTUAL = <?= (int) $pagina ?>;
</script>
