    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Textos usados por los .js de cada modulo (SweetAlert, tablas armadas por
// JS, etc.) -- panel bilingue: se arma en el idioma activo de la sesion.
window.I18N = {
  common: {
    noResults: <?= json_encode(lang('Common.NoResults')) ?>,
    showing: <?= json_encode(lang('Common.Showing')) ?>,
    of: <?= json_encode(lang('Common.Of')) ?>,
    active: <?= json_encode(lang('Common.Active')) ?>,
    inactive: <?= json_encode(lang('Common.Inactive')) ?>,
    closed: <?= json_encode(lang('Common.Closed')) ?>,
    saved: <?= json_encode(lang('Common.Saved')) ?>,
    error: <?= json_encode(lang('Common.Error')) ?>,
    delete: <?= json_encode(lang('Common.Delete')) ?>,
    cancel: <?= json_encode(lang('Common.Cancel')) ?>,
  },
  cursos: {
    addCourse: <?= json_encode(lang('Cursos.AddCourse')) ?>,
    editCourse: <?= json_encode(lang('Cursos.EditCourse')) ?>,
    deleteTitle: <?= json_encode(lang('Cursos.DeleteConfirmTitle')) ?>,
    deleteMsg: <?= json_encode(lang('Cursos.DeleteConfirmMsg')) ?>,
    deleteSuffix: <?= json_encode(lang('Cursos.DeleteConfirmSuffix')) ?>,
    deleted: <?= json_encode(lang('Cursos.CourseDeleted')) ?>,
    deleteError: <?= json_encode(lang('Cursos.CourseDeleteError')) ?>,
    requiredField: <?= json_encode(lang('Validacion.RequiredField')) ?>,
    nameRequired: <?= json_encode(lang('Validacion.CourseNameRequired')) ?>,
    saveSuccess: <?= json_encode(lang('Validacion.CourseSaved')) ?>,
    saveError: <?= json_encode(lang('Validacion.CourseSaveError')) ?>,
    activateAction: <?= json_encode(lang('Cursos.ActivateAction')) ?>,
    deactivateAction: <?= json_encode(lang('Cursos.DeactivateAction')) ?>,
    activateConfirmTitle: <?= json_encode(lang('Cursos.ActivateConfirmTitle')) ?>,
    deactivateConfirmTitle: <?= json_encode(lang('Cursos.DeactivateConfirmTitle')) ?>,
    activateConfirmMsg: <?= json_encode(lang('Cursos.ActivateConfirmMsg')) ?>,
    deactivateConfirmMsg: <?= json_encode(lang('Cursos.DeactivateConfirmMsg')) ?>,
    activateConfirmSuffix: <?= json_encode(lang('Cursos.ActivateConfirmSuffix')) ?>,
    deactivateConfirmSuffix: <?= json_encode(lang('Cursos.DeactivateConfirmSuffix')) ?>,
    courseActivated: <?= json_encode(lang('Cursos.CourseActivated')) ?>,
    courseDeactivated: <?= json_encode(lang('Cursos.CourseDeactivated')) ?>,
    changeStatusError: <?= json_encode(lang('Cursos.ChangeStatusError')) ?>,
  },
  students: {
    addStudent: <?= json_encode(lang('Students.AddStudent')) ?>,
    editStudent: <?= json_encode(lang('Students.EditStudent')) ?>,
    requiredFields: <?= json_encode(lang('Students.RequiredFields')) ?>,
    requiredFieldsMsg: <?= json_encode(lang('Students.RequiredFieldsMsg')) ?>,
    saveSuccess: <?= json_encode(lang('Students.StudentSaved')) ?>,
    saveError: <?= json_encode(lang('Students.StudentSaveError')) ?>,
  },
  certificatesList: {
    issued: <?= json_encode(lang('CertificatesList.Issued')) ?>,
    cancelled: <?= json_encode(lang('CertificatesList.Cancelled')) ?>,
    viewPdf: <?= json_encode(lang('CertificatesList.ViewPdf')) ?>,
    downloadPdf: <?= json_encode(lang('CertificatesList.DownloadPdf')) ?>,
    cancelAction: <?= json_encode(lang('CertificatesList.CancelAction')) ?>,
    reactivateAction: <?= json_encode(lang('CertificatesList.ReactivateAction')) ?>,
    deleteAction: <?= json_encode(lang('CertificatesList.DeleteAction')) ?>,
    cancelConfirmTitle: <?= json_encode(lang('CertificatesList.CancelConfirmTitle')) ?>,
    cancelConfirmMsg: <?= json_encode(lang('CertificatesList.CancelConfirmMsg')) ?>,
    cancelConfirmSuffix: <?= json_encode(lang('CertificatesList.CancelConfirmSuffix')) ?>,
    reactivateConfirmTitle: <?= json_encode(lang('CertificatesList.ReactivateConfirmTitle')) ?>,
    reactivateConfirmMsg: <?= json_encode(lang('CertificatesList.ReactivateConfirmMsg')) ?>,
    reactivateConfirmSuffix: <?= json_encode(lang('CertificatesList.ReactivateConfirmSuffix')) ?>,
    deleteConfirmTitle: <?= json_encode(lang('CertificatesList.DeleteConfirmTitle')) ?>,
    deleteConfirmMsg: <?= json_encode(lang('CertificatesList.DeleteConfirmMsg')) ?>,
    deleteConfirmSuffix: <?= json_encode(lang('CertificatesList.DeleteConfirmSuffix')) ?>,
    cancelled_: <?= json_encode(lang('CertificatesList.Cancelled_')) ?>,
    reactivated: <?= json_encode(lang('CertificatesList.Reactivated')) ?>,
    certificateDeleted: <?= json_encode(lang('CertificatesList.CertificateDeleted')) ?>,
    cancelError: <?= json_encode(lang('CertificatesList.CancelError')) ?>,
    deleteError: <?= json_encode(lang('CertificatesList.DeleteError')) ?>,
  },
  certificadoForm: {
    existingStudentFound: <?= json_encode(lang('CertificadoForm.ExistingStudentFound')) ?>,
    studentNotFound: <?= json_encode(lang('CertificadoForm.StudentNotFound')) ?>,
    requiredFields: <?= json_encode(lang('CertificadoForm.RequiredFields')) ?>,
    requiredFieldsMsg: <?= json_encode(lang('CertificadoForm.RequiredFieldsMsg')) ?>,
    studentAddedSaved: <?= json_encode(lang('CertificadoForm.StudentAddedSaved')) ?>,
    studentSaveError: <?= json_encode(lang('CertificadoForm.StudentSaveError')) ?>,
    certificateGenerated: <?= json_encode(lang('CertificadoForm.CertificateGenerated')) ?>,
    couldNotGenerate: <?= json_encode(lang('CertificadoForm.CouldNotGenerate')) ?>,
    missingDni: <?= json_encode(lang('CertificadoForm.MissingDni')) ?>,
    typeDniFirst: <?= json_encode(lang('CertificadoForm.TypeDniFirst')) ?>,
    certificateRegistered: <?= json_encode(lang('CertificadoForm.CertificateRegistered')) ?>,
    generatedCode: <?= json_encode(lang('CertificadoForm.GeneratedCode')) ?>,
    viewCertificate: <?= json_encode(lang('CertificadoForm.ViewCertificate')) ?>,
    close: <?= json_encode(lang('CertificadoForm.Close')) ?>,
    selectCategoryFirst: <?= json_encode(lang('CertificadoForm.SelectCategoryFirst')) ?>,
    selectPlaceholder: <?= json_encode(lang('Common.SelectPlaceholder')) ?>,
  },
  usuarios: {
    addUser: <?= json_encode(lang('Usuarios.AddUser')) ?>,
    editUser: <?= json_encode(lang('Usuarios.EditUser')) ?>,
    passwordRequiredOnCreate: <?= json_encode(lang('Usuarios.PasswordRequiredOnCreate')) ?>,
    passwordBlankToKeep: <?= json_encode(lang('Usuarios.PasswordBlankToKeep')) ?>,
    requiredFields: <?= json_encode(lang('Usuarios.RequiredFields')) ?>,
    requiredFieldsMsg: <?= json_encode(lang('Usuarios.RequiredFieldsMsg')) ?>,
    passwordRequiredField: <?= json_encode(lang('Usuarios.PasswordRequiredField')) ?>,
    passwordRequiredMsg: <?= json_encode(lang('Usuarios.PasswordRequiredMsg')) ?>,
    officeRequiredMsg: <?= json_encode(lang('Usuarios.OfficeRequiredMsg')) ?>,
    saveSuccess: <?= json_encode(lang('Usuarios.UserSaved')) ?>,
    saveError: <?= json_encode(lang('Usuarios.UserSaveError')) ?>,
    deleted: <?= json_encode(lang('Usuarios.Deleted')) ?>,
    deleteConfirmTitle: <?= json_encode(lang('Usuarios.DeleteUserConfirm')) ?>,
    deleteConfirmMsg: <?= json_encode(lang('Usuarios.DeleteUserConfirmMsg')) ?>,
    deleteConfirmSuffix: <?= json_encode(lang('Usuarios.DeleteUserConfirmSuffix')) ?>,
    deleteSuccess: <?= json_encode(lang('Usuarios.UserDeleted')) ?>,
    deleteError: <?= json_encode(lang('Usuarios.UserDeleteError')) ?>,
  },
  roles: {
    requiredField: <?= json_encode(lang('Validacion.RequiredField')) ?>,
    nameRequiredMsg: <?= json_encode(lang('Roles.NameRequiredMsg')) ?>,
    saveSuccess: <?= json_encode(lang('Roles.RoleSaved')) ?>,
    saveError: <?= json_encode(lang('Roles.RoleSaveError')) ?>,
  },
  dashboard: {
    certificate: <?= json_encode(lang('Dashboard.Certificate')) ?>,
    certificatesPlural: <?= json_encode(lang('Dashboard.CertificatesPlural')) ?>,
  },
  plantillas: {
    addTemplate: <?= json_encode(lang('Plantillas.AddTemplate')) ?>,
    editTemplate: <?= json_encode(lang('Plantillas.EditTemplate')) ?>,
    requiredField: <?= json_encode(lang('Validacion.RequiredField')) ?>,
    nameRequiredMsg: <?= json_encode(lang('Plantillas.NameRequiredMsg')) ?>,
    backgroundRequiredMsg: <?= json_encode(lang('Plantillas.BackgroundRequiredMsg')) ?>,
    saveSuccess: <?= json_encode(lang('Plantillas.TemplateSaved')) ?>,
    saveError: <?= json_encode(lang('Plantillas.TemplateSaveError')) ?>,
    couldNotSave: <?= json_encode(lang('Plantillas.CouldNotSave')) ?>,
    couldNotSaveBeforePreview: <?= json_encode(lang('Plantillas.CouldNotSaveBeforePreview')) ?>,
  },
};

function toggleSidebar(open) {
  document.getElementById('mobileSidebar').classList.toggle('hidden', !open);
}

// Escapa texto antes de insertarlo en HTML armado con template literals +
// innerHTML -- sin esto, un campo de texto guardado por un usuario (nombre,
// curso, correo, etc.) con algo como <img src=x onerror=...> ejecutaria
// JavaScript arbitrario en el navegador de cualquiera que vea esa lista.
function escapeHtml(valor) {
  if (valor === null || valor === undefined) {
    return '';
  }
  return String(valor)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}
</script>

<?php  if(isset($page)  && $page=='students')   {   ?>
  <script src="<?=base_url();?>/js/students.js?v=<?=@filemtime(FCPATH . 'js/students.js')?>"></script>
<?php   }  ?>

<?php  if(isset($page)  && $page=='certificado')   {   ?>
  <script src="<?=base_url();?>/js/certificado_form.js?v=<?=@filemtime(FCPATH . 'js/certificado_form.js')?>"></script>
<?php   }  ?>

<?php  if(isset($page)  && $page=='reportes' && $tab=='emitidos')   {   ?>
  <script src="<?=base_url();?>/js/reporte_emitidos.js?v=<?=@filemtime(FCPATH . 'js/reporte_emitidos.js')?>"></script>
<?php   }  ?>

<?php  if(isset($page)  && $page=='reportes' && $tab=='cursos')   {   ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <script src="<?=base_url();?>/js/reporte_cursos.js?v=<?=@filemtime(FCPATH . 'js/reporte_cursos.js')?>"></script>
<?php   }  ?>

<?php  if(isset($page)  && $page=='cursos')   {   ?>
  <script src="<?=base_url();?>/js/cursos.js?v=<?=@filemtime(FCPATH . 'js/cursos.js')?>"></script>
<?php   }  ?>

<?php  if(isset($page)  && $page=='plantillas')   {   ?>
  <script src="<?=base_url();?>/js/plantillas.js?v=<?=@filemtime(FCPATH . 'js/plantillas.js')?>"></script>
<?php   }  ?>

<?php  if(isset($page)  && $page=='plantilla_editor')   {   ?>
  <script src="<?=base_url();?>/js/plantilla_editor.js?v=<?=@filemtime(FCPATH . 'js/plantilla_editor.js')?>"></script>
<?php   }  ?>

<?php  if(isset($page)  && $page=='usuarios')   {   ?>
  <script src="<?=base_url();?>/js/usuarios.js?v=<?=@filemtime(FCPATH . 'js/usuarios.js')?>"></script>
<?php   }  ?>

<?php  if(isset($page)  && $page=='rol_editor')   {   ?>
  <script src="<?=base_url();?>/js/rol_editor.js?v=<?=@filemtime(FCPATH . 'js/rol_editor.js')?>"></script>
<?php   }  ?>

<?php  if(isset($page)  && $page=='roles')   {   ?>
  <script src="<?=base_url();?>/js/roles.js?v=<?=@filemtime(FCPATH . 'js/roles.js')?>"></script>
<?php   }  ?>

<?php  if(isset($page)  && $page=='certificates')   {   ?>
  <script src="<?=base_url();?>/js/certificates.js?v=<?=@filemtime(FCPATH . 'js/certificates.js')?>"></script>
<?php   }  ?>

</body>
</html>
