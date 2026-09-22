function abrirModalUsuario() {
  document.getElementById('modalUsuario').classList.remove('hidden');
}

function cerrarModalUsuario() {
  document.getElementById('modalUsuario').classList.add('hidden');
}

//-------------------------------------------------------------------------------------------------------
function resetFormUsuario() {
  let csrfInput = document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`);
  let csrfValue = csrfInput ? csrfInput.value : null;
  document.getElementById('formUsuario').reset();
  if (csrfInput && csrfValue) {
    csrfInput.value = csrfValue;
  }
  document.getElementById('idUser').value = '';
  document.querySelectorAll('.rol-checkbox').forEach(function (cb) { cb.checked = false; });
  actualizarOficinaRequerida();
}

//-------------------------------------------------------------------------------------------------------
function esAdministradorSeleccionado() {
  return Array.from(document.querySelectorAll('.rol-checkbox'))
    .some((cb) => cb.checked && ['Owner', 'Administrator', 'Administrador'].includes(cb.dataset.nombre));
}

//-------------------------------------------------------------------------------------------------------
function actualizarOficinaRequerida() {
  document.getElementById('oficinaRequerida').classList.toggle('hidden', esAdministradorSeleccionado());
}

//-------------------------------------------------------------------------------------------------------
function filterUsuarios(page) {
  let pagina = page || window.USUARIOS_PAGINA_ACTUAL || 1;
  let params = new URLSearchParams({
    busqueda: document.getElementById('busqueda').value,
    idOficina: document.getElementById('idOficinaFiltro').value,
    idRol: document.getElementById('idRolFiltro').value,
    estado: document.getElementById('estadoFiltro').value,
    perPage: document.getElementById('perPageFiltro').value,
    page: pagina,
  });

  fetch('/usuarios/filter?' + params.toString(), {
    headers: { Accept: 'application/json' },
  })
    .then((response) => response.json())
    .then((result) => {
      window.USUARIOS_PAGINA_ACTUAL = result.pagina;

      let htm = '';
      if (result.datos.length === 0) {
        htm = `<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">${escapeHtml(I18N.common.noResults)}</td></tr>`;
      }
      result.datos.forEach(function (u) {
        let roles = (u.roles || []).map((r) => `<span class="mr-1 inline-flex rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-800">${escapeHtml(r.nombre)}</span>`).join('');
        let estadoBadge = u.estado === 'A'
          ? '<span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Activo</span>'
          : `<span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">${escapeHtml(u.estado)}</span>`;
        let botonEditar = result.puedeEditar
          ? `<button type="button" onclick="editarUsuario(${u.idUser});" class="text-brand-600 hover:text-brand-900" title="Editar">
               <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 19.5H4.5"/></svg>
             </button>`
          : '';
        let botonEliminar = result.puedeEliminar
          ? `<button type="button" onclick="eliminarUsuario(${u.idUser}, ${escapeHtml(JSON.stringify(u.usuario ?? ''))});" class="text-rose-600 hover:text-rose-800" title="Eliminar">
               <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
             </button>`
          : '';
        htm += `<tr>
                  <td class="px-4 py-3 font-medium text-gray-700">${escapeHtml(u.usuario)}</td>
                  <td class="px-4 py-3">${escapeHtml((u.nombre ?? '') + ' ' + (u.apellido ?? ''))}</td>
                  <td class="px-4 py-3 text-gray-500">${escapeHtml(u.correo)}</td>
                  <td class="px-4 py-3">${u.oficina ? escapeHtml(u.oficina + ' (' + u.oficinaDescripcion + ')') : '-'}</td>
                  <td class="px-4 py-3">${roles}</td>
                  <td class="px-4 py-3">${estadoBadge}</td>
                  <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-3">${botonEditar}${botonEliminar}</div>
                  </td>
                </tr>`;
      });
      document.getElementById('tbUsuarios').innerHTML = htm;

      let inicio = result.total ? (result.pagina - 1) * result.perPage + 1 : 0;
      let fin = Math.min(result.pagina * result.perPage, result.total);
      document.getElementById('paginadorInfo').textContent = `${I18N.common.showing} ${inicio}-${fin} ${I18N.common.of} ${result.total}`;
      document.getElementById('btnPagAnterior').disabled = result.pagina <= 1;
      document.getElementById('btnPagSiguiente').disabled = result.pagina >= result.ultimaPagina;
    });
}

//-------------------------------------------------------------------------------------------------------
function cambiarPaginaUsuarios(delta) {
  filterUsuarios((window.USUARIOS_PAGINA_ACTUAL || 1) + delta);
}

//-------------------------------------------------------------------------------------------------------
function eliminarUsuario(id, usuario) {
  Swal.fire({
    icon: 'warning',
    title: I18N.usuarios.deleteConfirmTitle,
    text: `${I18N.usuarios.deleteConfirmMsg} "${usuario}"? ${I18N.usuarios.deleteConfirmSuffix}`,
    showCancelButton: true,
    confirmButtonText: I18N.common.delete,
    cancelButtonText: I18N.common.cancel,
    confirmButtonColor: '#e11d48',
  }).then((result) => {
    if (!result.isConfirmed) return;

    let csrfInput = document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`);
    let formData = new FormData();
    formData.append(window.CSRF_TOKEN_NAME, csrfInput.value);

    fetch('/usuarios/eliminar/' + id, { method: 'POST', body: formData })
      .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
      .then(({ status, body }) => {
        if (body.csrf) {
          csrfInput.value = body.csrf;
        }
        if (status === 200 && body.ok) {
          Swal.fire({ icon: 'success', title: I18N.usuarios.deleted, text: I18N.usuarios.deleteSuccess, confirmButtonColor: '#17548a' })
            .then(() => filterUsuarios());
        } else {
          Swal.fire({ icon: 'error', title: I18N.common.error, text: body.error ?? I18N.usuarios.deleteError, confirmButtonColor: '#f90f00' });
        }
      });
  });
}

//-------------------------------------------------------------------------------------------------------
function nuevoUsuario() {
  resetFormUsuario();
  document.getElementById('tituloModalUsuario').innerText = I18N.usuarios.addUser;
  document.getElementById('passwordHint').innerText = I18N.usuarios.passwordRequiredOnCreate;
  abrirModalUsuario();
}

//-------------------------------------------------------------------------------------------------------
function editarUsuario(id) {
  fetch('/usuarios/get/' + id, { headers: { Accept: 'application/json' } })
    .then((response) => response.json())
    .then((u) => {
      resetFormUsuario();
      document.getElementById('idUser').value = u.idUser;
      document.getElementById('nombre').value = u.nombre ?? '';
      document.getElementById('apellido').value = u.apellido ?? '';
      document.getElementById('usuario').value = u.usuario ?? '';
      document.getElementById('correo').value = u.correo ?? '';
      document.getElementById('idOficina').value = u.idOficina ?? '';
      document.getElementById('estado').value = u.estado ?? 'A';

      (u.idRoles || []).forEach(function (idRol) {
        let cb = document.querySelector(`.rol-checkbox[value="${idRol}"]`);
        if (cb) cb.checked = true;
      });
      actualizarOficinaRequerida();

      document.getElementById('tituloModalUsuario').innerText = I18N.usuarios.editUser;
      document.getElementById('passwordHint').innerText = I18N.usuarios.passwordBlankToKeep;
      abrirModalUsuario();
    });
}

//-------------------------------------------------------------------------------------------------------
function guardarUsuario() {
  let nombre = document.getElementById('nombre').value;
  let usuario = document.getElementById('usuario').value;
  let idUser = document.getElementById('idUser').value;
  let password = document.getElementById('password').value;

  if (nombre === '' || usuario === '') {
    Swal.fire({ icon: 'warning', title: I18N.usuarios.requiredFields, text: I18N.usuarios.requiredFieldsMsg, confirmButtonColor: '#17548a' });
    return false;
  }
  if (!idUser && password === '') {
    Swal.fire({ icon: 'warning', title: I18N.usuarios.passwordRequiredField, text: I18N.usuarios.passwordRequiredMsg, confirmButtonColor: '#17548a' });
    return false;
  }
  if (!esAdministradorSeleccionado() && document.getElementById('idOficina').value === '') {
    Swal.fire({ icon: 'warning', title: I18N.usuarios.passwordRequiredField, text: I18N.usuarios.officeRequiredMsg, confirmButtonColor: '#17548a' });
    return false;
  }

  let formData = new FormData(document.getElementById('formUsuario'));

  fetch('/usuarios/save', { method: 'POST', body: formData })
    .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
      if (body.csrf) {
        document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`).value = body.csrf;
      }
      if (status === 200 && body.ok) {
        cerrarModalUsuario();
        Swal.fire({ icon: 'success', title: I18N.common.saved, text: I18N.usuarios.saveSuccess, confirmButtonColor: '#17548a' })
          .then(() => filterUsuarios());
      } else {
        Swal.fire({ icon: 'error', title: I18N.common.error, text: body.error ?? I18N.usuarios.saveError, confirmButtonColor: '#f90f00' });
      }
    });
}
