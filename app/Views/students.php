<div class="p-4 md:p-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="text-xl font-semibold text-gray-800"><?php echo lang('Translate.Students'); ?></h1>
    <?php if ($puedeCrear) { ?>
    <button type="button" onclick="nuevoEstudiante();"
            class="inline-flex items-center gap-2 rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800">
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
      <?= lang('Students.AddStudent') ?>
    </button>
    <?php } ?>
  </div>

  <div class="mt-4 flex flex-wrap items-end gap-3">
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Search') ?></label>
      <input type="search" id="txtbusqueda" oninput="filterStudents(1);" placeholder="<?= lang('Students.SearchPlaceholder') ?>"
             class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Students.Country') ?></label>
      <select id="idPaisFiltro" onchange="filterStudents(1);"
              class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <option value=""><?= lang('Common.All') ?></option>
        <?php if (isset($paises)) { foreach ($paises as $p) { ?>
        <option value="<?= (int) $p->idPais ?>"><?= esc($p->nombre) ?></option>
        <?php } } ?>
      </select>
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Show') ?></label>
      <select id="perPageFiltro" onchange="filterStudents(1);"
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
            <th class="px-4 py-3"><?= lang('Common.Name') ?></th>
            <th class="px-4 py-3"><?= lang('Students.LastName') ?></th>
            <th class="px-4 py-3"><?= lang('Students.Dni') ?></th>
            <th class="px-4 py-3"><?= lang('Students.Country') ?></th>
            <th class="px-4 py-3"><?= lang('Students.Email') ?></th>
            <th class="px-4 py-3"><?= lang('Students.Phone') ?></th>
            <th class="px-4 py-3 text-right"><?= lang('Common.Edit') ?></th>
          </tr>
        </thead>
        <tbody id="tbEstudiantes" class="divide-y divide-gray-100">
          <?php if (empty($datos)) { ?>
          <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400"><?= lang('Common.NoResults') ?></td></tr>
          <?php } ?>
          <?php if (isset($datos)) { foreach ($datos as &$value) { ?>
          <tr>
            <td class="px-4 py-3"><?= esc($value->nombre) ?></td>
            <td class="px-4 py-3"><?= esc($value->apellido) ?></td>
            <td class="px-4 py-3"><?= esc($value->dni) ?></td>
            <td class="px-4 py-3"><?= esc($value->pais) ?></td>
            <td class="px-4 py-3"><?= esc($value->email) ?></td>
            <td class="px-4 py-3"><?= esc($value->telefono) ?></td>
            <td class="px-4 py-3 text-right">
              <?php if ($puedeEditar) { ?>
              <button type="button" onclick="editarEstudiante(<?= (int) $value->idEstudiante ?>);" class="text-brand-600 hover:text-brand-900">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 19.5H4.5"/></svg>
              </button>
              <?php } ?>
            </td>
          </tr>
          <?php } } ?>
        </tbody>
      </table>
    </div>
    <div class="flex items-center justify-between border-t border-gray-100 px-4 py-3 text-sm text-gray-500">
      <span id="paginadorInfo">
        <?= lang('Common.Showing') ?> <?= count($datos) ? (($pagina - 1) * $perPage + 1) : 0 ?>-<?= min($pagina * $perPage, $total) ?> <?= lang('Common.Of') ?> <?= $total ?>
      </span>
      <div class="flex gap-2">
        <button type="button" id="btnPagAnterior" onclick="cambiarPaginaStudents(-1);" <?= $pagina <= 1 ? 'disabled' : '' ?>
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white">
          <?= lang('Common.Previous') ?>
        </button>
        <button type="button" id="btnPagSiguiente" onclick="cambiarPaginaStudents(1);" <?= $pagina * $perPage >= $total ? 'disabled' : '' ?>
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white">
          <?= lang('Common.Next') ?>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Agregar/Editar Estudiante -->
<div id="modalEstudiante" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/50" onclick="cerrarModal();"></div>
  <div class="relative flex min-h-full items-center justify-center p-4">
    <div class="w-full max-w-lg rounded-lg bg-white shadow-xl">
      <form id="formEstudiante">
        <?= csrf_field() ?>
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
          <h3 class="text-base font-semibold text-gray-800" id="tituloModalEstudiante"><?= lang('Students.AddStudent') ?></h3>
          <button type="button" onclick="cerrarModal();" class="text-gray-400 hover:text-gray-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="max-h-[70vh] space-y-4 overflow-y-auto px-6 py-4">
          <input type="hidden" id="idEstudiante" name="idEstudiante">

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Students.FirstNameReq') ?></label>
              <input type="text" id="nombre" name="nombre" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Students.LastNameReq') ?></label>
              <input type="text" id="apellido" name="apellido" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Students.DniReq') ?></label>
            <input type="text" id="dni" name="dni" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Students.CountryDocument') ?></label>
              <select id="idPais" name="idPais" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <option value=""><?= lang('Common.SelectPlaceholder') ?></option>
                <?php if (isset($paises)) { foreach ($paises as &$p) { ?>
                <option value="<?= (int) $p->idPais ?>"><?= esc($p->nombre) ?></option>
                <?php } } ?>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Students.Nationality') ?></label>
              <input type="text" id="nacionalidad" name="nacionalidad" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Students.DateOfBirth') ?></label>
              <input type="date" id="fechaNac" name="fechaNac" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Students.Sex') ?></label>
              <select id="sexo" name="sexo" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <option value=""><?= lang('Common.SelectPlaceholder') ?></option>
                <option value="M">M</option>
                <option value="F">F</option>
              </select>
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Students.PlaceOfBirth') ?></label>
            <input type="text" id="lugarNac" name="lugarNac" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Students.Email') ?></label>
              <input type="email" id="email" name="email" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Students.Phone') ?></label>
              <input type="text" id="telefono" name="telefono" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
          </div>
        </div>
        <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">
          <button type="button" onclick="cerrarModal();" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><?= lang('Common.Cancel') ?></button>
          <button type="button" onclick="guardarEstudiante();" class="rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800"><?= lang('Common.Save') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
window.CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
window.STUDENTS_PAGINA_ACTUAL = <?= (int) $pagina ?>;
</script>
