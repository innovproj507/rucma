function paramsFiltro() {
  let params = new URLSearchParams({
    busqueda: document.getElementById('busqueda').value,
    estado: document.getElementById('estado').value,
    desde: document.getElementById('desde').value,
    hasta: document.getElementById('hasta').value,
  });
  return params;
}

function filtrarEmitidos() {
  fetch('/reportes/filtrar?' + paramsFiltro().toString(), {
    headers: { Accept: 'application/json' },
  })
    .then((response) => response.json())
    .then((result) => {
      let htm = '';
      if (result.length === 0) {
        htm = `<tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">${escapeHtml(I18N.common.noResults)}</td></tr>`;
      }
      result.forEach(function (d) {
        htm += `<tr>
                  <td class="px-4 py-3 font-medium text-gray-700">${escapeHtml(d.codigo)}</td>
                  <td class="px-4 py-3">${escapeHtml((d.nombre ?? '') + ' ' + (d.apellido ?? ''))}</td>
                  <td class="px-4 py-3">${escapeHtml(d.dni)}</td>
                  <td class="px-4 py-3 text-gray-500">${escapeHtml(d.nombreCursoEmitido)}</td>
                  <td class="px-4 py-3">${escapeHtml(d.oficina)}</td>
                  <td class="px-4 py-3">${escapeHtml(formatoFecha(d.fechaEmision))}</td>
                  <td class="px-4 py-3">${escapeHtml(formatoFecha(d.fechaExpiracion))}</td>
                  <td class="px-4 py-3">${escapeHtml(d.estado)}</td>
                  <td class="px-4 py-3 text-right">
                    <a href="/certificados/${d.idCertificado}/ver" target="_blank" class="text-brand-600 hover:text-brand-900">
                      <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>
                  </td>
                </tr>`;
      });
      document.getElementById('tbReporte').innerHTML = htm;
    });
}

function exportarEmitidos() {
  window.location = '/reportes/exportar?' + paramsFiltro().toString();
}

function formatoFecha(fecha) {
  if (!fecha) return '-';
  let [y, m, d] = fecha.split('-');
  return `${d}/${m}/${y}`;
}
