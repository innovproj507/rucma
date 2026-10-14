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

-- certificados.editar -> Owner, Administrator (mismo criterio que cancelar)
INSERT IGNORE INTO tbl_rol_permisos (idRol, idPermiso)
SELECT r.idRol, p.idPermiso
FROM tbl_roles r
JOIN tbl_permisos p ON p.codigo = 'certificados.editar'
WHERE r.nombre IN ('Owner', 'Administrator');

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
