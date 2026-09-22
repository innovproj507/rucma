function filterCertificates(page) {
  let pagina = page || window.CERTIFICATES_PAGINA_ACTUAL || 1;
  let params = new URLSearchParams({
    busqueda: document.getElementById('busqueda').value,
    estado: document.getElementById('estadoFiltro').value,
    desde: document.getElementById('desdeFiltro').value,
    hasta: document.getElementById('hastaFiltro').value,
    perPage: document.getElementById('perPageFiltro').value,
    page: pagina,
  });

  fetch('/certificates/filter?' + params.toString(), {
    headers: { Accept: 'application/json' },
  })
    .then((response) => response.json())
    .then((result) => {
      window.CERTIFICATES_PAGINA_ACTUAL = result.pagina;

      let htm = '';
      if (result.datos.length === 0) {
        htm = `<tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">${escapeHtml(I18N.common.noResults)}</td></tr>`;
      }
      result.datos.forEach(function (c) {
        let estadoBadge;
        if (result.puedeCancelar) {
          let esIssued = c.estado === 'E';
          let clase = esIssued ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100';
          let titulo = esIssued ? I18N.certificatesList.cancelAction : I18N.certificatesList.reactivateAction;
          let texto = esIssued ? I18N.certificatesList.issued : I18N.certificatesList.cancelled;
          estadoBadge = `<button type="button" onclick="cancelarCertificado(${c.idCertificado}, ${escapeHtml(JSON.stringify(c.codigo ?? ''))}, ${escapeHtml(JSON.stringify(c.estado ?? ''))});" title="${escapeHtml(titulo)}" class="inline-flex items-center rounded-md border px-3 py-1.5 text-sm font-medium shadow-sm transition hover:shadow ${clase}">${escapeHtml(texto)}</button>`;
        } else {
          estadoBadge = c.estado === 'E'
            ? `<span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">${escapeHtml(I18N.certificatesList.issued)}</span>`
            : `<span class="inline-flex rounded-full bg-rose-50 px-2 py-0.5 text-xs font-medium text-rose-600">${escapeHtml(I18N.certificatesList.cancelled)}</span>`;
        }
        let botonesPdf = result.puedeVerPdf
          ? `<a href="/certificados/${c.idCertificado}/ver" target="_blank" class="text-brand-600 hover:text-brand-900" title="${escapeHtml(I18N.certificatesList.viewPdf)}">
               <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
             </a>
             <a href="/certificados/${c.idCertificado}/descargar" class="ml-2 text-brand-600 hover:text-brand-900" title="${escapeHtml(I18N.certificatesList.downloadPdf)}">
               <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
             </a>`
          : '';
        let botonesEstado = '';
        if (result.puedeEliminar) {
          botonesEstado += `<button type="button" onclick="eliminarCertificado(${c.idCertificado}, ${escapeHtml(JSON.stringify(c.codigo ?? ''))});" class="ml-2 text-rose-600 hover:text-rose-800" title="${escapeHtml(I18N.certificatesList.deleteAction)}"><svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg></button>`;
        }
        htm += `<tr>
                  <td class="px-4 py-3 font-medium text-gray-700">${escapeHtml(c.codigo)}</td>
                  <td class="px-4 py-3">${escapeHtml((c.nombre ?? '') + ' ' + (c.apellido ?? ''))}</td>
                  <td class="px-4 py-3 text-gray-500">${escapeHtml(c.dni)}</td>
                  <td class="px-4 py-3">${escapeHtml(c.nombreCursoEmitido)}</td>
                  <td class="px-4 py-3">${escapeHtml(c.oficina)}</td>
                  <td class="px-4 py-3 text-gray-500">${escapeHtml(c.fechaEmision)}</td>
                  <td class="px-4 py-3 text-gray-500">${escapeHtml(c.fechaExpiracion)}</td>
                  <td class="px-4 py-3">${estadoBadge}</td>
                  <td class="px-4 py-3 text-right">${botonesPdf}${botonesEstado}</td>
                </tr>`;
      });
      document.getElementById('tbCertificates').innerHTML = htm;

      let inicio = result.total ? (result.pagina - 1) * result.perPage + 1 : 0;
      let fin = Math.min(result.pagina * result.perPage, result.total);
      document.getElementById('paginadorInfo').textContent = `${I18N.common.showing} ${inicio}-${fin} ${I18N.common.of} ${result.total}`;
      document.getElementById('btnPagAnterior').disabled = result.pagina <= 1;
      document.getElementById('btnPagSiguiente').disabled = result.pagina >= result.ultimaPagina;
    });
}

//-------------------------------------------------------------------------------------------------------
function cambiarPaginaCertificates(delta) {
  filterCertificates((window.CERTIFICATES_PAGINA_ACTUAL || 1) + delta);
}

//-------------------------------------------------------------------------------------------------------
function cancelarCertificado(id, codigo, estadoActual) {
  let esCancelar = estadoActual !== 'C';
  Swal.fire({
    icon: 'warning',
    title: esCancelar ? I18N.certificatesList.cancelConfirmTitle : I18N.certificatesList.reactivateConfirmTitle,
    text: `${esCancelar ? I18N.certificatesList.cancelConfirmMsg : I18N.certificatesList.reactivateConfirmMsg} "${codigo}"? ${esCancelar ? I18N.certificatesList.cancelConfirmSuffix : I18N.certificatesList.reactivateConfirmSuffix}`,
    showCancelButton: true,
    confirmButtonText: esCancelar ? I18N.certificatesList.cancelAction : I18N.certificatesList.reactivateAction,
    cancelButtonText: I18N.common.cancel,
    confirmButtonColor: '#d97706',
  }).then((result) => {
    if (!result.isConfirmed) return;

    let csrfInput = document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`);
    let formData = new FormData();
    formData.append(window.CSRF_TOKEN_NAME, csrfInput.value);

    fetch(`/certificados/${id}/cancelar`, { method: 'POST', body: formData })
      .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
      .then(({ status, body }) => {
        if (body.csrf) {
          csrfInput.value = body.csrf;
        }
        if (status === 200 && body.ok) {
          Swal.fire({ icon: 'success', title: I18N.common.saved, text: esCancelar ? I18N.certificatesList.cancelled_ : I18N.certificatesList.reactivated, confirmButtonColor: '#17548a' }).then(() => filterCertificates());
        } else {
          Swal.fire({ icon: 'error', title: I18N.common.error, text: body.error ?? I18N.certificatesList.cancelError, confirmButtonColor: '#f90f00' });
        }
      });
  });
}

//-------------------------------------------------------------------------------------------------------
function eliminarCertificado(id, codigo) {
  Swal.fire({
    icon: 'warning',
    title: I18N.certificatesList.deleteConfirmTitle,
    text: `${I18N.certificatesList.deleteConfirmMsg} "${codigo}"? ${I18N.certificatesList.deleteConfirmSuffix}`,
    showCancelButton: true,
    confirmButtonText: I18N.common.delete,
    cancelButtonText: I18N.common.cancel,
    confirmButtonColor: '#e11d48',
  }).then((result) => {
    if (!result.isConfirmed) return;

    let csrfInput = document.querySelector(`input[name="${window.CSRF_TOKEN_NAME}"]`);
    let formData = new FormData();
    formData.append(window.CSRF_TOKEN_NAME, csrfInput.value);

    fetch(`/certificados/${id}/eliminar`, { method: 'POST', body: formData })
      .then((response) => response.json().then((data) => ({ status: response.status, body: data })))
      .then(({ status, body }) => {
        if (body.csrf) {
          csrfInput.value = body.csrf;
        }
        if (status === 200 && body.ok) {
          Swal.fire({ icon: 'success', title: I18N.common.saved, text: I18N.certificatesList.certificateDeleted, confirmButtonColor: '#17548a' }).then(() => filterCertificates());
        } else {
          Swal.fire({ icon: 'error', title: I18N.common.error, text: body.error ?? I18N.certificatesList.deleteError, confirmButtonColor: '#f90f00' });
        }
      });
  });
}
