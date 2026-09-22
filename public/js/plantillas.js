function abrirModalPlantilla() {
  document.getElementById('modalPlantilla').classList.remove('hidden');
}

function cerrarModalPlantilla() {
  document.getElementById('modalPlantilla').classList.add('hidden');
}

//-------------------------------------------------------------------------------------------------------
function resetFormPlantilla() {
  let csrfInput = document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`);
  let csrfValue = csrfInput ? csrfInput.value : null;
  document.getElementById('formPlantilla').reset();
  if (csrfInput && csrfValue) {
    csrfInput.value = csrfValue;
  }
  document.getElementById('idPlantilla').value = '';
  ['marco', 'logoIzq', 'logoDer', 'firma'].forEach(function (campo) {
    document.getElementById('preview_' + campo).classList.add('hidden');
  });
  document.querySelectorAll('.oficina-checkbox').forEach(function (cb) { cb.checked = false; });
}

//-------------------------------------------------------------------------------------------------------
function previsualizar(campo) {
  let input = document.getElementById(campo);
  let preview = document.getElementById('preview_' + campo);
  if (input.files && input.files[0]) {
    preview.src = URL.createObjectURL(input.files[0]);
    preview.classList.remove('hidden');
  }
}

//-------------------------------------------------------------------------------------------------------
function nuevaPlantilla() {
  resetFormPlantilla();
  document.getElementById('tituloModalPlantilla').innerText = I18N.plantillas.addTemplate;
  document.getElementById('marco').required = true;
  abrirModalPlantilla();
}

//-------------------------------------------------------------------------------------------------------
function editarPlantilla(id) {
  fetch('/plantillas/get/' + id, { headers: { Accept: 'application/json' } })
    .then((response) => response.json())
    .then((p) => {
      resetFormPlantilla();
      document.getElementById('idPlantilla').value = p.idPlantilla;
      document.getElementById('nombre').value = p.nombre ?? '';
      document.getElementById('estado').value = p.estado ?? 'A';
      document.getElementById('marco').required = false;

      ['marco', 'logoIzq', 'logoDer', 'firma'].forEach(function (campo) {
        if (p[campo]) {
          let preview = document.getElementById('preview_' + campo);
          preview.src = '/img/' + p[campo];
          preview.classList.remove('hidden');
        }
      });

      (p.idOficinas || []).forEach(function (idOficina) {
        let cb = document.querySelector(`.oficina-checkbox[value="${idOficina}"]`);
        if (cb) cb.checked = true;
      });

      document.getElementById('tituloModalPlantilla').innerText = I18N.plantillas.editTemplate;
      abrirModalPlantilla();
    });
}

//-------------------------------------------------------------------------------------------------------
function guardarPlantilla() {
  let nombre = document.getElementById('nombre').value;
  let idPlantilla = document.getElementById('idPlantilla').value;
  let marcoFile = document.getElementById('marco').files.length;

  if (nombre === '') {
    Swal.fire({ icon: 'warning', title: I18N.plantillas.requiredField, text: I18N.plantillas.nameRequiredMsg, confirmButtonColor: '#17548a' });
    return false;
  }
  if (!idPlantilla && marcoFile === 0) {
    Swal.fire({ icon: 'warning', title: I18N.plantillas.requiredField, text: I18N.plantillas.backgroundRequiredMsg, confirmButtonColor: '#17548a' });
    return false;
  }

  let formData = new FormData(document.getElementById('formPlantilla'));

  fetch('/plantillas/save', { method: 'POST', body: formData })
    .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
      if (body.csrf) {
        document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`).value = body.csrf;
      }
      if (status === 200 && body.ok) {
        Swal.fire({
          icon: 'success',
          title: I18N.common.saved,
          text: I18N.plantillas.saveSuccess,
          confirmButtonColor: '#17548a',
        }).then(() => window.location.reload());
      } else {
        Swal.fire({ icon: 'error', title: I18N.common.error, text: body.error ?? I18N.plantillas.saveError, confirmButtonColor: '#f90f00' });
      }
    });
}
