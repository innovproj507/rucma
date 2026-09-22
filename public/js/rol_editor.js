function toggleModulo(checkbox) {
  let modulo = checkbox.dataset.modulo;
  document.querySelectorAll(`.permiso-checkbox[data-modulo="${modulo}"]`).forEach(function (cb) {
    cb.checked = checkbox.checked;
  });
}

//-------------------------------------------------------------------------------------------------------
function guardarRol() {
  let nombre = document.getElementById('nombre').value;
  if (nombre === '') {
    Swal.fire({ icon: 'warning', title: I18N.roles.requiredField, text: I18N.roles.nameRequiredMsg, confirmButtonColor: '#17548a' });
    return false;
  }

  let formData = new FormData(document.getElementById('formRol'));

  fetch('/roles/save', { method: 'POST', body: formData })
    .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
      if (status === 200 && body.ok) {
        Swal.fire({ icon: 'success', title: I18N.common.saved, text: I18N.roles.saveSuccess, confirmButtonColor: '#17548a' })
          .then(() => { window.location = '/roles'; });
      } else {
        Swal.fire({ icon: 'error', title: I18N.common.error, text: body.error ?? I18N.roles.saveError, confirmButtonColor: '#f90f00' });
      }
    });
}
