-- ============================================================================
-- Cambios de base de datos (rucma2 / produccion)
-- Generado: 2026-11-25
--
-- La duracion del certificado ("Duration (hours)") era INT y redondeaba
-- cursos con horas fraccionarias (17.5 -> 18). Pasa a DECIMAL(5,1), igual que
-- tbl_curso_categoria_horas.horas.
--
-- Ya aplicado en local (rucma2) y en produccion (rucma_DB) el 2026-11-25,
-- junto con la correccion de horas "New" de Panama OMI segun
-- cursos_unificados_OMI.xlsx (PCHT 17.5, PSAS 10, RADR 80.2, ASD 382).
-- ============================================================================

ALTER TABLE tbl_certificados MODIFY duracion DECIMAL(5,1) NULL;
