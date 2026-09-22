<div class="p-4 md:p-6 max-w-4xl">
  <h1 class="text-xl font-semibold text-gray-800"><?php echo lang('Translate.CreateCertificates'); ?></h1>

  <?php if ($sinOficina) { ?>
  <div class="mt-4 rounded-md bg-amber-50 px-4 py-3 text-sm text-amber-800 ring-1 ring-amber-200">
    <?= lang('CertificadoForm.NoOfficeSelected') ?>
  </div>
  <?php } ?>

  <form id="formCertificado" class="mt-4 space-y-6">
    <?= csrf_field() ?>

    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
      <h2 class="mb-1 text-sm font-semibold uppercase tracking-wide text-brand-800"><?= lang('CertificadoForm.StudentSection') ?></h2>
      <p class="mb-4 text-sm text-gray-500"><?= lang('CertificadoForm.StudentSectionHelp') ?></p>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.DniReq') ?></label>
          <div class="flex gap-2">
            <input type="text" id="dni" name="dni" required onblur="buscarEstudiante();" placeholder="<?= lang('CertificadoForm.DniPlaceholder') ?>"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            <button type="button" id="btnAgregarEstudiante" onclick="abrirModalNuevoEstudiante();" title="<?= lang('CertificadoForm.AddNewStudent') ?>"
                    class="flex flex-shrink-0 items-center justify-center rounded-md bg-emerald-600 px-3 text-white hover:bg-emerald-700">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            </button>
          </div>
          <p id="estudianteEstado" class="mt-1 text-xs text-gray-500"></p>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.FirstNameReq') ?></label>
          <input type="text" id="nombre" name="nombre" required readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600 focus:outline-none">
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.LastNameReq') ?></label>
          <input type="text" id="apellido" name="apellido" required readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600 focus:outline-none">
        </div>
      </div>

      <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.CountryDocument') ?></label>
          <select id="idPais" name="idPais" tabindex="-1" class="pointer-events-none w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600 focus:outline-none">
            <option value=""><?= lang('Common.SelectPlaceholder') ?></option>
            <?php foreach ($paises as $p) { ?>
            <option value="<?= (int) $p->idPais ?>"><?= esc($p->nombre) ?></option>
            <?php } ?>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.Nationality') ?></label>
          <input type="text" id="nacionalidad" name="nacionalidad" readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600 focus:outline-none">
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.DateOfBirth') ?></label>
          <input type="date" id="fechaNac" name="fechaNac" readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600 focus:outline-none">
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.Sex') ?></label>
          <select id="sexo" name="sexo" tabindex="-1" class="pointer-events-none w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600 focus:outline-none">
            <option value=""><?= lang('Common.SelectPlaceholder') ?></option>
            <option value="M">M</option>
            <option value="F">F</option>
          </select>
        </div>
      </div>
      <div class="mt-4">
        <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.PlaceOfBirth') ?></label>
        <input type="text" id="lugarNac" name="lugarNac" readonly class="w-full max-w-md rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600 focus:outline-none">
      </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
      <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-brand-800"><?= lang('CertificadoForm.CertificateSection') ?></h2>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.CategoryReq') ?></label>
          <select id="idCategoria" name="idCategoria" required onchange="cargarCursos();" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value=""><?= lang('Common.SelectPlaceholder') ?></option>
            <?php foreach ($categorias as $c) { ?>
            <option value="<?= (int) $c->idCategoria ?>"><?= esc($c->descripcion) ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="sm:col-span-2">
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.CourseReq') ?></label>
          <select id="idCurso" name="idCurso" required onchange="cursoSeleccionado();" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value=""><?= lang('CertificadoForm.SelectCategoryFirst') ?></option>
          </select>
        </div>
      </div>

      <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.ModalityReq') ?></label>
          <select id="idModalidad" name="idModalidad" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value=""><?= lang('Common.SelectPlaceholder') ?></option>
            <?php foreach ($modalidades as $m) { ?>
            <option value="<?= (int) $m->idModalidad ?>"><?= esc($m->descripcion) ?></option>
            <?php } ?>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.DeliveryPlace') ?></label>
          <input type="text" id="lugarEntrega" name="lugarEntrega" value="<?= esc($lugarEntregaDefault) ?>" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.DurationHours') ?></label>
          <input type="text" id="duracion" name="duracion" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        </div>
      </div>

      <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.StartDateReq') ?></label>
          <input type="date" id="fechaInicio" name="fechaInicio" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.EndDateReq') ?></label>
          <input type="date" id="fechaFinal" name="fechaFinal" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.IssueDateReq') ?></label>
          <input type="date" id="fechaEmision" name="fechaEmision" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.ExpirationDateReq') ?></label>
          <input type="date" id="fechaExpiracion" name="fechaExpiracion" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        </div>
      </div>
    </div>

    <div class="flex justify-end">
      <button type="button" onclick="guardarCertificado();" class="rounded-md bg-brand-900 px-6 py-2 text-sm font-medium text-white hover:bg-brand-800">
        <?= lang('CertificadoForm.GenerateCertificate') ?>
      </button>
    </div>
  </form>
</div>

<!-- Modal Agregar Estudiante (desde Create Certificates) -->
<div id="modalNuevoEstudianteCert" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/50" onclick="cerrarModalNuevoEstudiante();"></div>
  <div class="relative flex min-h-full items-center justify-center p-4">
    <div class="w-full max-w-lg rounded-lg bg-white shadow-xl">
      <form id="formNuevoEstudianteCert">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
          <h3 class="text-base font-semibold text-gray-800"><?= lang('CertificadoForm.AddStudent') ?></h3>
          <button type="button" onclick="cerrarModalNuevoEstudiante();" class="text-gray-400 hover:text-gray-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="max-h-[70vh] space-y-4 overflow-y-auto px-6 py-4">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.DniReq') ?></label>
            <input type="text" id="nuevoDni" readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600 focus:outline-none">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.FirstNameReq') ?></label>
              <input type="text" id="nuevoNombre" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.LastNameReq') ?></label>
              <input type="text" id="nuevoApellido" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.CountryDocument') ?></label>
              <select id="nuevoIdPais" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <option value=""><?= lang('Common.SelectPlaceholder') ?></option>
                <?php foreach ($paises as $p) { ?>
                <option value="<?= (int) $p->idPais ?>"><?= esc($p->nombre) ?></option>
                <?php } ?>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.Nationality') ?></label>
              <input type="text" id="nuevoNacionalidad" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.DateOfBirth') ?></label>
              <input type="date" id="nuevoFechaNac" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.Sex') ?></label>
              <select id="nuevoSexo" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <option value=""><?= lang('Common.SelectPlaceholder') ?></option>
                <option value="M">M</option>
                <option value="F">F</option>
              </select>
            </div>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700"><?= lang('CertificadoForm.PlaceOfBirth') ?></label>
            <input type="text" id="nuevoLugarNac" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
          </div>
        </div>
        <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">
          <button type="button" onclick="cerrarModalNuevoEstudiante();" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><?= lang('Common.Cancel') ?></button>
          <button type="button" onclick="guardarNuevoEstudianteDesdeCertificado();" class="rounded-md bg-brand-900 px-4 py-2 text-sm font-medium text-white hover:bg-brand-800"><?= lang('CertificadoForm.SaveStudent') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>window.CSRF_TOKEN_NAME = '<?= csrf_token() ?>';</script>
