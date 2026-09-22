<div class="border-b border-gray-200">
  <nav class="-mb-px flex flex-wrap gap-4 text-sm">
    <a href="/reportes"
       class="border-b-2 px-1 py-3 font-medium <?= $tab === 'emitidos' ? 'border-brand-600 text-brand-700' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
      <?= lang('Reportes.TabIssued') ?>
    </a>
    <a href="/reportes/vencer"
       class="border-b-2 px-1 py-3 font-medium <?= $tab === 'vencer' ? 'border-brand-600 text-brand-700' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
      <?= lang('Reportes.TabExpiring') ?>
    </a>
    <a href="/reportes/cursos"
       class="border-b-2 px-1 py-3 font-medium <?= $tab === 'cursos' ? 'border-brand-600 text-brand-700' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
      <?= lang('Reportes.TabByCourse') ?>
    </a>
    <a href="/reportes/estudiantes"
       class="border-b-2 px-1 py-3 font-medium <?= $tab === 'estudiantes' ? 'border-brand-600 text-brand-700' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
      <?= lang('Reportes.TabStudents') ?>
    </a>
  </nav>
</div>
