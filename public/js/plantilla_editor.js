let layout = window.PLANTILLA_LAYOUT || {};

document.addEventListener('DOMContentLoaded', function () {
  const lienzo = document.getElementById('lienzo');

  document.querySelectorAll('.draggable').forEach(function (el) {
    el.addEventListener('pointerdown', function (e) {
      e.preventDefault();
      const key = el.dataset.key;
      const rect = lienzo.getBoundingClientRect();

      function onMove(ev) {
        let x = ((ev.clientX - rect.left) / rect.width) * 100;
        let y = ((ev.clientY - rect.top) / rect.height) * 100;
        x = Math.max(0, Math.min(100, Math.round(x * 10) / 10));
        y = Math.max(0, Math.min(100, Math.round(y * 10) / 10));
        layout[key].x = x;
        layout[key].y = y;
        el.style.left = x + '%';
        el.style.top = y + '%';
        if (document.getElementById('elementoSeleccionado').value === key) {
          document.getElementById('propX').value = x;
          document.getElementById('propY').value = y;
        }
      }
      function onUp() {
        document.removeEventListener('pointermove', onMove);
        document.removeEventListener('pointerup', onUp);
      }
      document.addEventListener('pointermove', onMove);
      document.addEventListener('pointerup', onUp);

      document.getElementById('elementoSeleccionado').value = key;
      cargarPropiedades();
    });
  });

  cargarPropiedades();
});

// El lienzo (max 560px) representa una hoja carta de 215.9mm de ancho; esta
// es la equivalencia aproximada entre pt (tamano real en el PDF) y px en el
// lienzo, solo para que el cambio de tamano se note en la vista previa.
const PT_A_PX_LIENZO = (560 / 215.9) * 0.352778;

//-------------------------------------------------------------------------------------------------------
function tieneTamano(key) {
  let opcion = document.querySelector(`#elementoSeleccionado option[value="${key}"]`);
  return opcion && opcion.dataset.tieneTamano === '1';
}

//-------------------------------------------------------------------------------------------------------
function cargarPropiedades() {
  let key = document.getElementById('elementoSeleccionado').value;
  let pos = layout[key];
  document.getElementById('propX').value = pos.x;
  document.getElementById('propY').value = pos.y;
  document.getElementById('propW').value = pos.w;

  let wrap = document.getElementById('propTamanoWrap');
  if (tieneTamano(key)) {
    wrap.classList.remove('hidden');
    document.getElementById('propTamano').value = pos.size ?? '';
  } else {
    wrap.classList.add('hidden');
  }
}

//-------------------------------------------------------------------------------------------------------
function aplicarPropiedades() {
  let key = document.getElementById('elementoSeleccionado').value;
  layout[key].x = parseFloat(document.getElementById('propX').value) || 0;
  layout[key].y = parseFloat(document.getElementById('propY').value) || 0;
  layout[key].w = parseFloat(document.getElementById('propW').value) || 0;

  let el = document.querySelector(`.draggable[data-key="${key}"]`);
  el.style.left = layout[key].x + '%';
  el.style.top = layout[key].y + '%';
  el.style.width = layout[key].w + '%';

  if (tieneTamano(key)) {
    let tamano = parseFloat(document.getElementById('propTamano').value);
    if (tamano > 0) {
      layout[key].size = tamano;
      el.style.fontSize = (tamano * PT_A_PX_LIENZO) + 'px';
    }
  }
}

//-------------------------------------------------------------------------------------------------------
function previsualizarImagen(input, campo) {
  if (!input.files || !input.files[0]) {
    return;
  }
  let archivo = input.files[0];
  let img = document.getElementById('preview_' + campo);
  let vacio = document.getElementById('previewVacio_' + campo);
  let nombre = document.getElementById('nombreArchivo_' + campo);

  nombre.textContent = archivo.name;

  let lector = new FileReader();
  lector.onload = function (e) {
    img.src = e.target.result;
    img.classList.remove('hidden');
    vacio.classList.add('hidden');
  };
  lector.readAsDataURL(archivo);

  if (campo === 'marco') {
    let lector2 = new FileReader();
    lector2.onload = function (e) {
      document.getElementById('lienzo').style.backgroundImage = `url('${e.target.result}')`;
    };
    lector2.readAsDataURL(archivo);
    return;
  }

  // Si es el logo/firma que ya esta en el lienzo, refleja el cambio ahi tambien.
  let elLienzo = document.querySelector(`.draggable[data-key="${campo}"] img`);
  if (elLienzo) {
    let lector2 = new FileReader();
    lector2.onload = function (e) {
      elLienzo.src = e.target.result;
    };
    lector2.readAsDataURL(archivo);
  }
}

//-------------------------------------------------------------------------------------------------------
function guardarEditor() {
  document.getElementById('layout').value = JSON.stringify(layout);
  let formData = new FormData(document.getElementById('formEditor'));

  fetch('/plantillas/save', { method: 'POST', body: formData })
    .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
      if (body.csrf) {
        document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`).value = body.csrf;
      }
      if (status === 200 && body.ok) {
        Swal.fire({ icon: 'success', title: I18N.common.saved, text: I18N.plantillas.saveSuccess, confirmButtonColor: '#17548a' })
          .then(() => window.location.reload());
      } else {
        Swal.fire({ icon: 'error', title: I18N.common.error, text: body.error ?? I18N.plantillas.couldNotSave, confirmButtonColor: '#f90f00' });
      }
    });
}

//-------------------------------------------------------------------------------------------------------
function vistaPrevia() {
  document.getElementById('layout').value = JSON.stringify(layout);
  let formData = new FormData(document.getElementById('formEditor'));

  fetch('/plantillas/save', { method: 'POST', body: formData })
    .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
      if (body.csrf) {
        document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`).value = body.csrf;
      }
      if (status === 200 && body.ok) {
        window.open('/plantillas/' + window.PLANTILLA_ID + '/preview', '_blank');
      } else {
        Swal.fire({ icon: 'error', title: I18N.common.error, text: body.error ?? I18N.plantillas.couldNotSaveBeforePreview, confirmButtonColor: '#f90f00' });
      }
    });
}
