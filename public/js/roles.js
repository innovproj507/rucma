function filterRoles(page) {
  let pagina = page || window.ROLES_PAGINA_ACTUAL || 1;
  let params = new URLSearchParams({
    busqueda: document.getElementById('busqueda').value,
    estado: document.getElementById('estadoFiltro').value,
    perPage: document.getElementById('perPageFiltro').value,
    page: pagina,
  });

  fetch('/roles/filter?' + params.toString(), {
    headers: { Accept: 'application/json' },
  })
    .then((response) => response.json())
    .then((result) => {
      window.ROLES_PAGINA_ACTUAL = result.pagina;

      let htm = '';
      if (result.datos.length === 0) {
        htm = `<tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">${escapeHtml(I18N.common.noResults)}</td></tr>`;
      }
      result.datos.forEach(function (r) {
        let estadoBadge = r.estado === 'A'
          ? '<span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Activo</span>'
          : `<span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">${escapeHtml(r.estado)}</span>`;
        let botonEditar = result.puedeEditar
          ? `<a href="/roles/${r.idRol}/editar" class="text-brand-600 hover:text-brand-900">
               <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 19.5H4.5"/></svg>
             </a>`
          : '';
        htm += `<tr>
                  <td class="px-4 py-3 font-medium text-gray-700">${escapeHtml(r.nombre)}</td>
                  <td class="px-4 py-3 text-gray-500">${escapeHtml(r.descripcion)}</td>
                  <td class="px-4 py-3 text-center">${r.totalUsuarios ?? 0}</td>
                  <td class="px-4 py-3 text-center">${r.totalPermisos ?? 0} / ${result.totalPermisosDisponibles ?? 0}</td>
                  <td class="px-4 py-3">${estadoBadge}</td>
                  <td class="px-4 py-3 text-right">${botonEditar}</td>
                </tr>`;
      });
      document.getElementById('tbRoles').innerHTML = htm;

      let inicio = result.total ? (result.pagina - 1) * result.perPage + 1 : 0;
      let fin = Math.min(result.pagina * result.perPage, result.total);
      document.getElementById('paginadorInfo').textContent = `${I18N.common.showing} ${inicio}-${fin} ${I18N.common.of} ${result.total}`;
      document.getElementById('btnPagAnterior').disabled = result.pagina <= 1;
      document.getElementById('btnPagSiguiente').disabled = result.pagina >= result.ultimaPagina;
    });
}

//-------------------------------------------------------------------------------------------------------
function cambiarPaginaRoles(delta) {
  filterRoles((window.ROLES_PAGINA_ACTUAL || 1) + delta);
}
