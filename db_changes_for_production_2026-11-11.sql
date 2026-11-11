-- ============================================================================
-- Cambios de base de datos pendientes de aplicar en produccion (rucma2)
-- Generado: 2026-11-11
--
-- Contexto: nueva linea "(IMO Model Course X.XX)" debajo del nombre del curso
-- en el PDF del certificado (solo si el curso tiene numero IMO asignado).
-- Agrega la columna donde se guarda el numero IMO vigente al momento de
-- emitir el certificado (igual que nombreCursoEmitido, nivelSTCWEmitido,
-- etc. -- una foto del dato del curso al momento de emitir, no una
-- referencia que cambie despues si el curso se edita).
--
-- Ya aplicado en local (rucma2) y en produccion (rucma_DB) el 2026-11-11.
-- Este archivo queda solo como registro -- es seguro volver a correrlo
-- (ADD COLUMN falla si ya existe, no hace nada destructivo).
--
-- IMPORTANTE: haz un mysqldump de la base de produccion ANTES de correr esto.
-- ============================================================================

ALTER TABLE tbl_certificados
  ADD COLUMN imoEmitido DECIMAL(20,6) NULL AFTER nivelSTCWEmitido;
