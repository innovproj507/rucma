function abrirModal() {
  document.getElementById('modalEstudiante').classList.remove('hidden');
}

function cerrarModal() {
  document.getElementById('modalEstudiante').classList.add('hidden');
}

//-------------------------------------------------------------------------------------------------------
function filterStudents(page) {
  let pagina = page || window.STUDENTS_PAGINA_ACTUAL || 1;
  let params = new URLSearchParams({
    txtbusqueda: document.getElementById('txtbusqueda').value,
    idPais: document.getElementById('idPaisFiltro').value,
    perPage: document.getElementById('perPageFiltro').value,
    page: pagina,
  });

  fetch('/students/filter?' + params.toString(), {
    method: 'GET',
    cache: 'no-cache',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    },
  })
    .then((response) => response.json())
    .then((result) => {
      window.STUDENTS_PAGINA_ACTUAL = result.pagina;

      let htm = '';
      if (result.datos.length === 0) {
        htm = `<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">${escapeHtml(I18N.common.noResults)}</td></tr>`;
      }
      result.datos.forEach(function (value) {
        htm += `<tr>
                  <td class="px-4 py-3">${escapeHtml(value.nombre)}</td>
                  <td class="px-4 py-3">${escapeHtml(value.apellido)}</td>
                  <td class="px-4 py-3">${escapeHtml(value.dni)}</td>
                  <td class="px-4 py-3">${escapeHtml(value.pais)}</td>
                  <td class="px-4 py-3">${escapeHtml(value.email)}</td>
                  <td class="px-4 py-3">${escapeHtml(value.telefono)}</td>
                  <td class="px-4 py-3 text-right">
                    ${result.puedeEditar ? `<button type="button" onclick="editarEstudiante(${value.idEstudiante});" class="text-slate-500 hover:text-slate-900">
                      <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 19.5H4.5"/></svg>
                    </button>` : ''}
                  </td>
                </tr>`;
      });
      document.getElementById('tbEstudiantes').innerHTML = htm;

      let inicio = result.total ? (result.pagina - 1) * result.perPage + 1 : 0;
      let fin = Math.min(result.pagina * result.perPage, result.total);
      document.getElementById('paginadorInfo').textContent = `${I18N.common.showing} ${inicio}-${fin} ${I18N.common.of} ${result.total}`;
      document.getElementById('btnPagAnterior').disabled = result.pagina <= 1;
      document.getElementById('btnPagSiguiente').disabled = result.pagina >= result.ultimaPagina;
    });
}

//-------------------------------------------------------------------------------------------------------
function cambiarPaginaStudents(delta) {
  filterStudents((window.STUDENTS_PAGINA_ACTUAL || 1) + delta);
}

//-------------------------------------------------------------------------------------------------------
function nuevoEstudiante() {
  resetFormEstudiante();
  document.getElementById('tituloModalEstudiante').innerText = I18N.students.addStudent;
  abrirModal();
}

//-------------------------------------------------------------------------------------------------------
// form.reset() reverts every field, including the hidden CSRF input, back to
// the value it had when the page loaded -- which is stale once a save has
// already rotated the token. Preserve the current CSRF value across reset().
function resetFormEstudiante() {
  let csrfInput = document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`);
  let csrfValue = csrfInput ? csrfInput.value : null;
  document.getElementById('formEstudiante').reset();
  if (csrfInput && csrfValue) {
    csrfInput.value = csrfValue;
  }
  document.getElementById('idEstudiante').value = '';
}

//-------------------------------------------------------------------------------------------------------
function editarEstudiante(id) {
  fetch('/students/get/' + id, {
    method: 'GET',
    cache: 'no-cache',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    },
  })
    .then((response) => response.json())
    .then((value) => {
      resetFormEstudiante();
      document.getElementById('idEstudiante').value = value.idEstudiante;
      document.getElementById('nombre').value = value.nombre ?? '';
      document.getElementById('apellido').value = value.apellido ?? '';
      document.getElementById('dni').value = value.dni ?? '';
      document.getElementById('idPais').value = value.idPais ?? '';
      document.getElementById('nacionalidad').value = value.nacionalidad ?? '';
      document.getElementById('fechaNac').value = value.fechaNac ?? '';
      document.getElementById('lugarNac').value = value.lugarNac ?? '';
      document.getElementById('sexo').value = value.sexo ?? '';
      document.getElementById('email').value = value.email ?? '';
      document.getElementById('telefono').value = value.telefono ?? '';
      document.getElementById('tituloModalEstudiante').innerText = I18N.students.editStudent;
      abrirModal();
    });
}

//-------------------------------------------------------------------------------------------------------
function guardarEstudiante() {
  let nombre = document.getElementById('nombre').value;
  let apellido = document.getElementById('apellido').value;
  let dni = document.getElementById('dni').value;

  if (nombre == '' || apellido == '' || dni == '') {
    Swal.fire({
      icon: 'warning',
      title: I18N.students.requiredFields,
      text: I18N.students.requiredFieldsMsg,
      confirmButtonColor: '#0076bc',
    });
    return false;
  }

  let form = document.getElementById('formEstudiante');
  let formData = new FormData(form);

  fetch('/students/save', {
    method: 'POST',
    body: formData,
  })
    .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
      if (body.csrf) {
        document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`).value = body.csrf;
      }
      if (status == 200 && body.ok) {
        cerrarModal();
        Swal.fire({
          icon: 'success',
          title: I18N.common.saved,
          text: I18N.students.saveSuccess,
          confirmButtonColor: '#0076bc',
        }).then(() => {
          filterStudents();
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: I18N.common.error,
          text: body.error ?? I18N.students.saveError,
          confirmButtonColor: '#f90f00',
        });
      }
    });
}
