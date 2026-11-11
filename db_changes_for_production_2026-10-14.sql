-- ============================================================================
-- Cambios de base de datos pendientes de aplicar en produccion (rucma2)
-- Generado: 2026-10-14
--
-- Contexto: edicion de certificados ya emitidos (modal "Editar certificado"
-- en el listado de Certificados). Solo agrega el permiso nuevo y lo asigna.
--
-- Es seguro correr este script mas de una vez.
--
-- IMPORTANTE: haz un mysqldump de la base de produccion ANTES de correr esto.
-- ============================================================================

START TRANSACTION;

INSERT INTO tbl_permisos (codigo, modulo, descripcion) VALUES
    ('certificados.editar', 'Certificados', 'Editar certificados emitidos (fechas, modalidad, lugar, duracion)')
ON DUPLICATE KEY UPDATE
    modulo = VALUES(modulo),
    descripcion = VALUES(descripcion);

-- certificados.editar -> los roles admin (por nombre) y ademas cualquier rol
-- que ya pueda cancelar certificados, para no depender de como se llamen los
-- roles en produccion.
INSERT IGNORE INTO tbl_rol_permisos (idRol, idPermiso)
SELECT DISTINCT r.idRol, p.idPermiso
FROM tbl_roles r
JOIN tbl_permisos p ON p.codigo = 'certificados.editar'
WHERE r.nombre IN ('Owner', 'Administrator', 'Administrador')
   OR r.idRol IN (
        SELECT rp.idRol
        FROM tbl_rol_permisos rp
        JOIN tbl_permisos pc ON pc.idPermiso = rp.idPermiso
        WHERE pc.codigo = 'certificados.cancelar'
   )
  -- Por si en produccion falta el UNIQUE uq_rol_permiso(idRol, idPermiso):
  -- sin el, INSERT IGNORE por si solo no evitaria duplicados al re-correrlo.
  AND NOT EXISTS (
        SELECT 1 FROM tbl_rol_permisos x
        WHERE x.idRol = r.idRol AND x.idPermiso = p.idPermiso
  );

COMMIT;

-- ============================================================================
-- Verificacion sugerida:
--
--   SELECT r.nombre AS rol, p.codigo AS permiso
--     FROM tbl_rol_permisos rp
--     JOIN tbl_roles r ON r.idRol = rp.idRol
--     JOIN tbl_permisos p ON p.idPermiso = rp.idPermiso
--     WHERE p.codigo = 'certificados.editar';
--
--   (deberia mostrar: Owner, Administrator)
-- ============================================================================
