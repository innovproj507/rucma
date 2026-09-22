document.addEventListener('DOMContentLoaded', function () {
  const canvas = document.getElementById('chartCursos');
  if (!canvas || typeof Chart === 'undefined') return;

  const data = window.CURSOS_CHART_DATA || { labels: [], values: [] };
  const BRAND_600 = '#1c6bab';
  const GRID_GRAY = '#e5e7eb';
  const TEXT_MUTED = '#6b7280';

  new Chart(canvas.getContext('2d'), {
    type: 'bar',
    data: {
      labels: data.labels,
      datasets: [{
        data: data.values,
        backgroundColor: BRAND_600,
        borderRadius: 4,
        maxBarThickness: 20,
      }],
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => ctx.parsed.x + ' ' + (ctx.parsed.x === 1 ? I18N.dashboard.certificate : I18N.dashboard.certificatesPlural),
          },
        },
      },
      scales: {
        x: {
          beginAtZero: true,
          ticks: { precision: 0, color: TEXT_MUTED },
          grid: { color: GRID_GRAY, drawTicks: false },
        },
        y: {
          grid: { display: false },
          ticks: { color: TEXT_MUTED, font: { size: 11 } },
        },
      },
    },
  });
});
