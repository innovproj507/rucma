<div class="p-4 md:p-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="text-xl font-semibold text-gray-800"><?= lang('Cursos.Title') ?></h1>
    <?php if ($puedeCrear) { ?>
    <button type="button" onclick="nuevoCurso();"
            class="inline-flex items-center gap-2 rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800">
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
      <?= lang('Cursos.AddCourse') ?>
    </button>
    <?php } ?>
  </div>

  <div class="mt-4 flex flex-wrap items-end gap-3">
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Search') ?></label>
      <input type="search" id="busqueda" oninput="filterCursos(1);" placeholder="<?= lang('Common.Name') ?>"
             class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Modality') ?></label>
      <select id="idModalidadFiltro" onchange="filterCursos(1);"
              class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <option value=""><?= lang('Cursos.AllModalities') ?></option>
        <?php foreach ($modalidades as $m) { ?>
        <option value="<?= (int) $m->idModalidad ?>"><?= esc($m->descripcion) ?></option>
        <?php } ?>
      </select>
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Category') ?></label>
      <select id="idCategoriaFiltro" onchange="filterCursos(1);"
              class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <option value=""><?= lang('Cursos.AllModalities') ?></option>
        <?php foreach ($categorias as $cat) { ?>
        <option value="<?= (int) $cat->idCategoria ?>"><?= esc($cat->descripcion) ?></option>
        <?php } ?>
      </select>
    </div>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Status') ?></label>
      <select id="estadoFiltro" onchange="filterCursos(1);"
              class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <option value=""><?= lang('Common.All') ?></option>
        <option value="A"><?= lang('Common.Active') ?></option>
        <option value="I"><?= lang('Common.Inactive') ?></option>
        <option value="C"><?= lang('Common.Closed') ?></option>
      </select>
    </div>
    <?php if (session()->get('isAdmin')) { ?>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('CertificatesList.Office') ?></label>
      <select id="idOficinaFiltro" onchange="filterCursos(1);"
              class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        <option value="" <?= empty($idOficinaFiltro) ? 'selected' : '' ?>><?= lang('Translate.AllOffices') ?></option>
        <?php foreach ($oficinas as $o) { ?>
        <option value="<?= (int) $o->idOficina ?>" <?= (int) ($idOficinaFiltro ?? 0) === (int) $o->idOficina ? 'selected' : '' ?>>
          <?= esc($o->nombre . ' (' . $o->descripcion . ')') ?>
        </option>
        <?php } ?>
      </select>
    </div>
    <?php } ?>
    <div>
      <label class="mb-1 block text-xs font-medium text-gray-600"><?= lang('Common.Show') ?></label>
      <select id="perPageFiltro" onchange="filterCursos(1);"
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
            <th class="px-4 py-3"><?= lang('Common.Code') ?></th>
            <th class="px-4 py-3"><?= lang('Common.Name') ?></th>
            <th class="px-4 py-3"><?= lang('CertificatesList.Office') ?></th>
            <th class="px-4 py-3"><?= lang('Common.Modality') ?></th>
            <th class="px-4 py-3"><?= lang('Common.Status') ?></th>
            <th class="px-4 py-3 text-right"><?= lang('Common.Edit') ?></th>
          </tr>
        </thead>
        <tbody id="tbCursos" class="divide-y divide-gray-100">
          <?php $idiomaActualCursos = service('language')->getLocale(); ?>
          <?php foreach ($datos as $c) { ?>
          <tr>
            <td class="px-4 py-3 font-medium text-gray-700"><?= esc($c->codigo) ?></td>
            <td class="px-4 py-3"><?= esc($idiomaActualCursos === 'en' ? ($c->nombreIngles ?: $c->nombre) : $c->nombre) ?></td>
            <td class="px-4 py-3 text-gray-500"><?= esc($c->oficinaDescripcion ? $c->oficina . ' (' . $c->oficinaDescripcion . ')' : $c->oficina) ?></td>
            <td class="px-4 py-3 text-gray-500"><?= esc($c->modalidad) ?></td>
            <td class="px-4 py-3">
              <?php
                $estadoLabel = $c->estado === 'A' ? lang('Common.Active') : ($c->estado === 'I' ? lang('Common.Inactive') : lang('Common.Closed'));
              ?>
              <?php if ($puedeEditar) { ?>
              <button type="button" onclick="cambiarEstadoCurso(<?= (int) $c->idCurso ?>, <?= esc(json_encode($c->nombre), 'attr') ?>, <?= esc(json_encode($c->estado), 'attr') ?>);"
                      title="<?= $c->estado === 'A' ? esc(lang('Cursos.DeactivateAction')) : esc(lang('Cursos.ActivateAction')) ?>"
                      class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-medium shadow-sm transition hover:shadow <?= $c->estado === 'A' ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'border-gray-200 bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                <?= $estadoLabel ?>
              </button>
              <?php } else { ?>
              <span class="inline-flex rounded-full <?= $c->estado === 'A' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' ?> px-2 py-0.5 text-xs font-medium"><?= $estadoLabel ?></span>
              <?php } ?>
            </td>
            <td class="px-4 py-3 text-right">
              <?php if ($puedeEditar) { ?>
              <button type="button" onclick="editarCurso(<?= (int) $c->idCurso ?>);" class="text-brand-600 hover:text-brand-900">
                <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 19.5H4.5"/></svg>
              </button>
              <?php } ?>
              <?php if (!empty($puedeEliminar)) { ?>
              <button type="button" onclick="eliminarCurso(<?= (int) $c->idCurso ?>, <?= esc(json_encode($c->nombre), 'attr') ?>);" class="ml-2 text-rose-600 hover:text-rose-800" title="<?= esc(lang('Common.Delete')) ?>">
                <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
              </button>
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
        <button type="button" id="btnPagAnterior" onclick="cambiarPagina(-1);" <?= $pagina <= 1 ? 'disabled' : '' ?>
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white">
          <?= lang('Common.Previous') ?>
        </button>
        <button type="button" id="btnPagSiguiente" onclick="cambiarPagina(1);" <?= $pagina * $perPage >= $total ? 'disabled' : '' ?>
                class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-white">
          <?= lang('Common.Next') ?>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Agregar/Editar Curso -->
<div id="modalCurso" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/50" onclick="cerrarModalCurso();"></div>
  <div class="relative flex min-h-full items-center justify-center p-4">
    <div class="w-full max-w-2xl rounded-lg bg-white shadow-xl">
      <form id="formCurso">
        <?= csrf_field() ?>
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
          <h3 class="text-base font-semibold text-gray-800" id="tituloModalCurso"><?= lang('Cursos.AddCourse') ?></h3>
          <button type="button" onclick="cerrarModalCurso();" class="text-gray-400 hover:text-gray-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="max-h-[70vh] space-y-4 overflow-y-auto px-6 py-4">
          <input type="hidden" id="idCurso" name="idCurso">

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Common.Code') ?></label>
              <input type="text" id="codigo" name="codigo" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Common.Modality') ?></label>
              <select id="idModalidad" name="idModalidad" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <option value=""><?= lang('Common.SelectPlaceholder') ?></option>
                <?php foreach ($modalidades as $m) { ?>
                <option value="<?= (int) $m->idModalidad ?>"><?= esc($m->descripcion) ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Cursos.NameSpanish') ?></label>
            <input type="text" id="nombre" name="nombre" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Cursos.NameEnglish') ?></label>
            <input type="text" id="nombreIngles" name="nombreIngles" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Cursos.StcwLevel') ?></label>
            <input type="text" id="nivelSTCW" name="nivelSTCW" placeholder="<?= lang('Cursos.StcwLevelPlaceholder') ?>" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Cursos.RulesSpanish') ?></label>
            <textarea id="reglas" name="reglas" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Cursos.RulesEnglish') ?></label>
            <textarea id="reglasIngles" name="reglasIngles" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
          </div>

          <?php if (session()->get('isAdmin')) { ?>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificatesList.Office') ?> *</label>
            <select id="idOficinaCurso" name="idOficina" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
              <?php foreach ($oficinas as $o) { ?>
              <option value="<?= (int) $o->idOficina ?>"><?= esc($o->nombre . ' (' . $o->descripcion . ')') ?></option>
              <?php } ?>
            </select>
          </div>
          <?php } ?>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700">IMO</label>
              <input type="text" id="imo" name="imo" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('Common.Status') ?></label>
              <select id="estado" name="estado" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <option value="A"><?= lang('Common.Active') ?></option>
                <option value="I"><?= lang('Common.Inactive') ?></option>
                <option value="C"><?= lang('Common.Closed') ?></option>
              </select>
            </div>
          </div>

          <div class="rounded-md bg-gray-50 p-3">
            <p class="mb-2 text-sm font-medium text-gray-700"><?= lang('Cursos.HoursByCategory') ?></p>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
              <?php foreach ($categorias as $cat) { ?>
              <div>
                <label class="mb-1 block text-xs text-gray-600"><?= esc($cat->descripcion) ?></label>
                <input type="number" step="0.5" min="0" name="horas[<?= (int) $cat->idCategoria ?>]" id="horas_<?= (int) $cat->idCategoria ?>"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
        <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">
          <button type="button" onclick="cerrarModalCurso();" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><?= lang('Common.Cancel') ?></button>
          <button type="button" onclick="guardarCurso();" class="rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800"><?= lang('Common.Save') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
window.CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
window.CURSOS_PAGINA_ACTUAL = <?= (int) $pagina ?>;
window.PUEDE_EDITAR_CURSO = <?= $puedeEditar ? 'true' : 'false' ?>;
</script>
