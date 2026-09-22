let cursosCache = [];

//-------------------------------------------------------------------------------------------------------
// Fecha Emision = hoy (dia en que se crea el certificado) y Fecha
// Expiracion = 5 anos despues, recalculada automaticamente si el usuario
// cambia la fecha de emision (p.ej. para emitir con fecha retroactiva).
function formatoFechaInput(fecha) {
  return fecha.getFullYear() + '-' + String(fecha.getMonth() + 1).padStart(2, '0') + '-' + String(fecha.getDate()).padStart(2, '0');
}

function actualizarFechaExpiracion() {
  let inputEmision = document.getElementById('fechaEmision');
  let inputExpiracion = document.getElementById('fechaExpiracion');
  if (!inputEmision.value) return;

  let [anio, mes, dia] = inputEmision.value.split('-').map(Number);
  let expiracion = new Date(anio + 5, mes - 1, dia);
  inputExpiracion.value = formatoFechaInput(expiracion);
}

document.addEventListener('DOMContentLoaded', function () {
  let inputEmision = document.getElementById('fechaEmision');
  if (!inputEmision) return;

  inputEmision.value = formatoFechaInput(new Date());
  actualizarFechaExpiracion();
  inputEmision.addEventListener('change', actualizarFechaExpiracion);
});

function limpiarCamposEstudiante() {
  document.getElementById('nombre').value = '';
  document.getElementById('apellido').value = '';
  document.getElementById('idPais').value = '';
  document.getElementById('nacionalidad').value = '';
  document.getElementById('fechaNac').value = '';
  document.getElementById('lugarNac').value = '';
  document.getElementById('sexo').value = '';
}

//-------------------------------------------------------------------------------------------------------
function buscarEstudiante() {
  let dni = document.getElementById('dni').value.trim();
  let estado = document.getElementById('estudianteEstado');
  let btnAgregar = document.getElementById('btnAgregarEstudiante');

  if (dni === '') {
    estado.textContent = '';
    btnAgregar.classList.add('hidden');
    return;
  }

  fetch('/certificados/estudiante?dni=' + encodeURIComponent(dni), {
    headers: { Accept: 'application/json' },
  })
    .then((response) => response.json())
    .then((value) => {
      if (value) {
        document.getElementById('nombre').value = value.nombre ?? '';
        document.getElementById('apellido').value = value.apellido ?? '';
        document.getElementById('idPais').value = value.idPais ?? '';
        document.getElementById('nacionalidad').value = value.nacionalidad ?? '';
        document.getElementById('fechaNac').value = value.fechaNac ?? '';
        document.getElementById('lugarNac').value = value.lugarNac ?? '';
        document.getElementById('sexo').value = value.sexo ?? '';
        estado.textContent = I18N.certificadoForm.existingStudentFound;
        estado.className = 'mt-1 text-xs text-emerald-600';
        btnAgregar.classList.add('hidden');
      } else {
        limpiarCamposEstudiante();
        estado.textContent = I18N.certificadoForm.studentNotFound;
        estado.className = 'mt-1 text-xs text-amber-600';
        btnAgregar.classList.remove('hidden');
      }
    });
}

//-------------------------------------------------------------------------------------------------------
function abrirModalNuevoEstudiante() {
  let dni = document.getElementById('dni').value.trim();
  if (dni === '') {
    Swal.fire({ icon: 'warning', title: I18N.certificadoForm.missingDni, text: I18N.certificadoForm.typeDniFirst, confirmButtonColor: '#17548a' });
    return;
  }

  document.getElementById('formNuevoEstudianteCert').reset();
  document.getElementById('nuevoDni').value = dni;
  document.getElementById('modalNuevoEstudianteCert').classList.remove('hidden');
}

//-------------------------------------------------------------------------------------------------------
function cerrarModalNuevoEstudiante() {
  document.getElementById('modalNuevoEstudianteCert').classList.add('hidden');
}

//-------------------------------------------------------------------------------------------------------
function guardarNuevoEstudianteDesdeCertificado() {
  let nombre = document.getElementById('nuevoNombre').value.trim();
  let apellido = document.getElementById('nuevoApellido').value.trim();
  let dni = document.getElementById('nuevoDni').value.trim();

  if (nombre === '' || apellido === '') {
    Swal.fire({ icon: 'warning', title: I18N.certificadoForm.requiredFields, text: I18N.certificadoForm.requiredFieldsMsg, confirmButtonColor: '#17548a' });
    return;
  }

  let csrfInput = document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`);
  let formData = new FormData();
  formData.append(window.CSRF_TOKEN_NAME, csrfInput.value);
  formData.append('nombre', nombre);
  formData.append('apellido', apellido);
  formData.append('dni', dni);
  formData.append('idPais', document.getElementById('nuevoIdPais').value);
  formData.append('nacionalidad', document.getElementById('nuevoNacionalidad').value);
  formData.append('fechaNac', document.getElementById('nuevoFechaNac').value);
  formData.append('sexo', document.getElementById('nuevoSexo').value);
  formData.append('lugarNac', document.getElementById('nuevoLugarNac').value);

  fetch('/students/save', { method: 'POST', body: formData })
    .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
      if (body.csrf) {
        csrfInput.value = body.csrf;
      }
      if (status === 200 && body.ok) {
        document.getElementById('nombre').value = nombre;
        document.getElementById('apellido').value = apellido;
        document.getElementById('idPais').value = document.getElementById('nuevoIdPais').value;
        document.getElementById('nacionalidad').value = document.getElementById('nuevoNacionalidad').value;
        document.getElementById('fechaNac').value = document.getElementById('nuevoFechaNac').value;
        document.getElementById('sexo').value = document.getElementById('nuevoSexo').value;
        document.getElementById('lugarNac').value = document.getElementById('nuevoLugarNac').value;

        let estado = document.getElementById('estudianteEstado');
        estado.textContent = I18N.certificadoForm.studentAddedSaved;
        estado.className = 'mt-1 text-xs text-emerald-600';
        document.getElementById('btnAgregarEstudiante').classList.add('hidden');

        cerrarModalNuevoEstudiante();
      } else {
        Swal.fire({ icon: 'error', title: I18N.common.error, text: body.error ?? I18N.certificadoForm.studentSaveError, confirmButtonColor: '#f90f00' });
      }
    });
}

//-------------------------------------------------------------------------------------------------------
function cargarCursos() {
  let idCategoria = document.getElementById('idCategoria').value;
  let select = document.getElementById('idCurso');

  if (idCategoria === '') {
    select.innerHTML = `<option value="">${escapeHtml(I18N.certificadoForm.selectCategoryFirst)}</option>`;
    return;
  }

  fetch('/certificados/cursos?idCategoria=' + idCategoria, {
    headers: { Accept: 'application/json' },
  })
    .then((response) => response.json())
    .then((result) => {
      cursosCache = result;
      let htm = `<option value="">${escapeHtml(I18N.certificadoForm.selectPlaceholder)}</option>`;
      result.forEach(function (curso) {
        htm += `<option value="${curso.idCurso}">${escapeHtml(curso.nombreIngles ?? curso.nombre)}</option>`;
      });
      select.innerHTML = htm;
    });
}

//-------------------------------------------------------------------------------------------------------
function cursoSeleccionado() {
  let idCurso = document.getElementById('idCurso').value;
  let curso = cursosCache.find((c) => String(c.idCurso) === String(idCurso));
  if (curso && curso.horas) {
    document.getElementById('duracion').value = curso.horas;
  }
}

//-------------------------------------------------------------------------------------------------------
function guardarCertificado() {
  let form = document.getElementById('formCertificado');
  if (!form.reportValidity()) {
    return;
  }

  let formData = new FormData(form);

  fetch('/certificados/guardar', {
    method: 'POST',
    body: formData,
  })
    .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
      if (body.csrf) {
        document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`).value = body.csrf;
      }
      if (status === 200 && body.ok) {
        Swal.fire({
          icon: 'success',
          title: I18N.certificadoForm.certificateRegistered,
          html: `${escapeHtml(I18N.certificadoForm.generatedCode)} <strong>${escapeHtml(body.codigo)}</strong>`,
          confirmButtonText: I18N.certificadoForm.viewCertificate,
          confirmButtonColor: '#17548a',
          showCancelButton: true,
          cancelButtonText: I18N.certificadoForm.close,
        }).then((result) => {
          if (result.isConfirmed) {
            window.open('/certificados/' + body.idCertificado + '/ver', '_blank');
          }
          form.reset();
          document.getElementById('idCurso').innerHTML = `<option value="">${escapeHtml(I18N.certificadoForm.selectCategoryFirst)}</option>`;
          document.getElementById('estudianteEstado').textContent = '';
          document.getElementById('btnAgregarEstudiante').classList.add('hidden');
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: I18N.common.error,
          text: body.error ?? I18N.certificadoForm.couldNotGenerate,
          confirmButtonColor: '#f90f00',
        });
      }
    });
}
