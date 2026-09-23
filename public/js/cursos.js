function abrirModalCurso() {
  document.getElementById('modalCurso').classList.remove('hidden');
}

function cerrarModalCurso() {
  document.getElementById('modalCurso').classList.add('hidden');
}

//-------------------------------------------------------------------------------------------------------
function resetFormCurso() {
  let csrfInput = document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`);
  let csrfValue = csrfInput ? csrfInput.value : null;
  document.getElementById('formCurso').reset();
  if (csrfInput && csrfValue) {
    csrfInput.value = csrfValue;
  }
  document.getElementById('idCurso').value = '';
}

//-------------------------------------------------------------------------------------------------------
function filterCursos(page) {
  let pagina = page || window.CURSOS_PAGINA_ACTUAL || 1;
  let idOficinaFiltro = document.getElementById('idOficinaFiltro');
  let params = new URLSearchParams({
    busqueda: document.getElementById('busqueda').value,
    idModalidad: document.getElementById('idModalidadFiltro').value,
    idCategoria: document.getElementById('idCategoriaFiltro').value,
    estado: document.getElementById('estadoFiltro').value,
    perPage: document.getElementById('perPageFiltro').value,
    page: pagina,
  });
  if (idOficinaFiltro) {
    params.set('idOficina', idOficinaFiltro.value);
  }

  fetch('/cursos/filter?' + params.toString(), {
    headers: { Accept: 'application/json' },
  })
    .then((response) => response.json())
    .then((result) => {
      window.CURSOS_PAGINA_ACTUAL = result.pagina;

      let htm = '';
      if (result.datos.length === 0) {
        htm = `<tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">${escapeHtml(I18N.common.noResults)}</td></tr>`;
      }
      result.datos.forEach(function (c) {
        let estadoBadge = c.estado === 'A'
          ? `<span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">${escapeHtml(I18N.common.active)}</span>`
          : `<span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">${escapeHtml(c.estado)}</span>`;
        htm += `<tr>
                  <td class="px-4 py-3 font-medium text-gray-700">${escapeHtml(c.codigo)}</td>
                  <td class="px-4 py-3">${escapeHtml(c.nombre)}</td>
                  <td class="px-4 py-3 text-gray-500">${escapeHtml(c.oficina)}</td>
                  <td class="px-4 py-3 text-gray-500">${escapeHtml(c.modalidad)}</td>
                  <td class="px-4 py-3">${estadoBadge}</td>
                  <td class="px-4 py-3 text-right">
                    <button type="button" onclick="editarCurso(${c.idCurso});" class="text-brand-600 hover:text-brand-900">
                      <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 19.5H4.5"/></svg>
                    </button>
                    ${result.puedeEliminar ? `<button type="button" onclick="eliminarCurso(${c.idCurso}, ${escapeHtml(JSON.stringify(c.nombre ?? ''))});" class="ml-2 text-rose-600 hover:text-rose-800" title="${escapeHtml(I18N.common.delete)}"><svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg></button>` : ''}
                  </td>
                </tr>`;
      });
      document.getElementById('tbCursos').innerHTML = htm;

      let inicio = result.total ? (result.pagina - 1) * result.perPage + 1 : 0;
      let fin = Math.min(result.pagina * result.perPage, result.total);
      document.getElementById('paginadorInfo').textContent = `${I18N.common.showing} ${inicio}-${fin} ${I18N.common.of} ${result.total}`;
      document.getElementById('btnPagAnterior').disabled = result.pagina <= 1;
      document.getElementById('btnPagSiguiente').disabled = result.pagina >= result.ultimaPagina;
    });
}

//-------------------------------------------------------------------------------------------------------
function cambiarPagina(delta) {
  filterCursos((window.CURSOS_PAGINA_ACTUAL || 1) + delta);
}

//-------------------------------------------------------------------------------------------------------
function nuevoCurso() {
  resetFormCurso();
  document.getElementById('tituloModalCurso').innerText = I18N.cursos.addCourse;
  abrirModalCurso();
}

//-------------------------------------------------------------------------------------------------------
function editarCurso(id) {
  fetch('/cursos/get/' + id, { headers: { Accept: 'application/json' } })
    .then((response) => response.json())
    .then((c) => {
      resetFormCurso();
      document.getElementById('idCurso').value = c.idCurso;
      document.getElementById('codigo').value = c.codigo ?? '';
      document.getElementById('idModalidad').value = c.idModalidad ?? '';
      document.getElementById('nombre').value = c.nombre ?? '';
      document.getElementById('nombreIngles').value = c.nombreIngles ?? '';
      document.getElementById('nivelSTCW').value = c.nivelSTCW ?? '';
      document.getElementById('reglas').value = c.reglas ?? '';
      document.getElementById('reglasIngles').value = c.reglasIngles ?? '';
      document.getElementById('imo').value = c.imo ?? '';
      document.getElementById('estado').value = c.estado ?? 'A';
      if (document.getElementById('idOficinaCurso')) document.getElementById('idOficinaCurso').value = c.idOficina ?? '';

      (c.horas || []).forEach(function (h) {
        let input = document.getElementById('horas_' + h.idCategoria);
        if (input) input.value = h.horas;
      });

      document.getElementById('tituloModalCurso').innerText = I18N.cursos.editCourse;
      abrirModalCurso();
    });
}

//-------------------------------------------------------------------------------------------------------
function guardarCurso() {
  let nombre = document.getElementById('nombre').value;

  if (nombre === '') {
    Swal.fire({
      icon: 'warning',
      title: I18N.cursos.requiredField,
      text: I18N.cursos.nameRequired,
      confirmButtonColor: '#17548a',
    });
    return false;
  }

  let formData = new FormData(document.getElementById('formCurso'));

  fetch('/cursos/save', { method: 'POST', body: formData })
    .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
      if (body.csrf) {
        document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`).value = body.csrf;
      }
      if (status === 200 && body.ok) {
        cerrarModalCurso();
        Swal.fire({
          icon: 'success',
          title: I18N.common.saved,
          text: I18N.cursos.saveSuccess,
          confirmButtonColor: '#17548a',
        }).then(() => filterCursos());
      } else {
        Swal.fire({
          icon: 'error',
          title: I18N.common.error,
          text: body.error ?? I18N.cursos.saveError,
          confirmButtonColor: '#f90f00',
        });
      }
    });
}

//-------------------------------------------------------------------------------------------------------
function eliminarCurso(id, nombre) {
  Swal.fire({
    icon: 'warning',
    title: I18N.cursos.deleteTitle,
    text: `${I18N.cursos.deleteMsg} "${nombre}"? ${I18N.cursos.deleteSuffix}`,
    showCancelButton: true,
    confirmButtonText: I18N.common.delete,
    cancelButtonText: I18N.common.cancel,
    confirmButtonColor: '#e11d48',
  }).then((result) => {
    if (!result.isConfirmed) return;

    let csrfInput = document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`);
    let formData = new FormData();
    formData.append(window.CSRF_TOKEN_NAME, csrfInput.value);

    fetch('/cursos/eliminar/' + id, { method: 'POST', body: formData })
      .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
      .then(({ status, body }) => {
        if (body.csrf) {
          csrfInput.value = body.csrf;
        }
        if (status === 200 && body.ok) {
          Swal.fire({ icon: 'success', title: I18N.common.saved, text: I18N.cursos.deleted, confirmButtonColor: '#17548a' }).then(() => filterCursos());
        } else {
          Swal.fire({ icon: 'error', title: I18N.common.error, text: body.error ?? I18N.cursos.deleteError, confirmButtonColor: '#f90f00' });
        }
      });
  });
}
