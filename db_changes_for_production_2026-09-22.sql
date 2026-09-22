-- ============================================================================
-- Cambios de base de datos pendientes de aplicar en produccion (rucma2)
-- Generado: 2026-09-22
--
-- Contexto: estos son los UNICOS cambios de datos/esquema hechos en la base
-- de desarrollo durante esta sesion, DESPUES del despliegue inicial a
-- produccion. No incluye nada del esquema normalizado original (tbl_oficina,
-- tbl_certificados, tbl_roles, etc.) porque eso ya deberia estar en produccion
-- desde el cutover inicial.
--
-- Es seguro correr este script mas de una vez (usa INSERT ... ON DUPLICATE
-- KEY UPDATE / IGNORE en las partes de permisos).
--
-- IMPORTANTE: haz un mysqldump de la base de produccion ANTES de correr esto.
-- ============================================================================

START TRANSACTION;

-- ----------------------------------------------------------------------------
-- 1) REQUERIDO: nuevos permisos para las funciones de esta sesion
--    (eliminar cursos, cancelar/reactivar certificados, eliminar certificados)
-- ----------------------------------------------------------------------------

INSERT INTO tbl_permisos (codigo, modulo, descripcion) VALUES
    ('cursos.eliminar',        'Cursos',        'Eliminar cursos'),
    ('certificados.cancelar',  'Certificados',  'Cancelar y reactivar certificados'),
    ('certificados.eliminar',  'Certificados',  'Eliminar certificados')
ON DUPLICATE KEY UPDATE
    modulo = VALUES(modulo),
    descripcion = VALUES(descripcion);

-- Asignacion de esos permisos a roles (por nombre, no por ID, para que
-- funcione sin importar el AUTO_INCREMENT que tenga produccion):
--   cursos.eliminar        -> Owner, Administrator
--   certificados.cancelar  -> Owner, Administrator
--   certificados.eliminar  -> Owner unicamente (accion mas destructiva)

INSERT IGNORE INTO tbl_rol_permisos (idRol, idPermiso)
SELECT r.idRol, p.idPermiso
FROM tbl_roles r
JOIN tbl_permisos p ON p.codigo = 'cursos.eliminar'
WHERE r.nombre IN ('Owner', 'Administrator');

INSERT IGNORE INTO tbl_rol_permisos (idRol, idPermiso)
SELECT r.idRol, p.idPermiso
FROM tbl_roles r
JOIN tbl_permisos p ON p.codigo = 'certificados.cancelar'
WHERE r.nombre IN ('Owner', 'Administrator');

INSERT IGNORE INTO tbl_rol_permisos (idRol, idPermiso)
SELECT r.idRol, p.idPermiso
FROM tbl_roles r
JOIN tbl_permisos p ON p.codigo = 'certificados.eliminar'
WHERE r.nombre = 'Owner';

-- ----------------------------------------------------------------------------
-- 2) RECOMENDADO (opcional): corrige cursos marcados inactivos por error
--    durante la migracion, que ya tienen certificados emitidos. Sin esto,
--    el Dashboard puede seguir mostrando "0 cursos activos" en oficinas que
--    si tienen certificados reales (bug que encontramos con Panama/Greece
--    en el ambiente local). Comenta este bloque si prefieres revisar
--    manualmente cuales cursos activar en produccion en vez de hacerlo en bloque.
-- ----------------------------------------------------------------------------

UPDATE tbl_cursos
SET estado = 'A'
WHERE estado <> 'A'
  AND idCurso IN (SELECT DISTINCT idCurso FROM tbl_certificados);

COMMIT;

-- ============================================================================
-- Verificacion sugerida despues de correr esto:
--
--   SELECT codigo, modulo, descripcion FROM tbl_permisos
--     WHERE codigo IN ('cursos.eliminar','certificados.cancelar','certificados.eliminar');
--
--   SELECT r.nombre AS rol, p.codigo AS permiso
--     FROM tbl_rol_permisos rp
--     JOIN tbl_roles r ON r.idRol = rp.idRol
--     JOIN tbl_permisos p ON p.idPermiso = rp.idPermiso
--     WHERE p.codigo IN ('cursos.eliminar','certificados.cancelar','certificados.eliminar')
--     ORDER BY p.codigo, r.nombre;
--
--   (deberia mostrar: cursos.eliminar->Owner,Administrator |
--    certificados.cancelar->Owner,Administrator | certificados.eliminar->Owner)
-- ============================================================================
