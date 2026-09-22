-- Fase 1: Maestras (tbl_pais, tbl_oficina, tbl_categoria, tbl_modalidad) en rucma2
-- tbl_pais / tbl_categoria / tbl_modalidad ya existian con la estructura correcta
-- (creadas en una sesion anterior). Aqui se agrega tbl_oficina y se siembran los
-- datos reales desde `rucma`, preservando los IDs para que las FKs de fases
-- posteriores (cursos, certificados) sigan apuntando a los mismos registros.

USE rucma2;

CREATE TABLE IF NOT EXISTS `tbl_oficina` (
  `idOficina` int NOT NULL AUTO_INCREMENT,
  `idPais` int NOT NULL,
  `code` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `flag` varchar(100) DEFAULT NULL,
  `estado` enum('A','I') NOT NULL DEFAULT 'A',
  PRIMARY KEY (`idOficina`),
  KEY `fk_oficina_pais` (`idPais`),
  CONSTRAINT `fk_oficina_pais` FOREIGN KEY (`idPais`) REFERENCES `tbl_pais` (`idPais`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- tbl_pais: mismos IDs que en `rucma` (1=PA, 2=GRE, 3=IND), estado S -> A
INSERT INTO tbl_pais (idPais, code, nombre, flag, estado) VALUES
  (1, 'PA',  'Panamá', NULL, 'A'),
  (2, 'GRE', 'Greece', NULL, 'A'),
  (3, 'IND', 'India',  NULL, 'A')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- tbl_oficina: mismos IDs que en `rucma`
INSERT INTO tbl_oficina (idOficina, idPais, code, nombre, descripcion, flag, estado) VALUES
  (1, 1, 'PA',  'Panamá', 'OMI',               NULL, 'A'),
  (2, 2, 'GRE', 'Greece', 'Greece',            NULL, 'A'),
  (3, 3, 'IND', 'India',  'India',             NULL, 'A'),
  (4, 1, 'PA',  'Panamá', 'No OMI',            NULL, 'A'),
  (5, 3, 'IND', 'India',  'India Assessment',  NULL, 'A'),
  (6, 3, 'IND', 'India',  'India PDE',         NULL, 'A')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- tbl_categoria: mismos IDs que en `rucma` (1=New, 2=Refresher)
INSERT INTO tbl_categoria (idCategoria, codigo, descripcion, estado) VALUES
  (1, 'NEW', 'New',       'A'),
  (2, 'REF', 'Refresher', 'A')
ON DUPLICATE KEY UPDATE codigo = VALUES(codigo), descripcion = VALUES(descripcion);

-- tbl_modalidad: mismos IDs que en `rucma` (1=In Classroom, 2=E-Learning)
INSERT INTO tbl_modalidad (idModalidad, codigo, descripcion, estado) VALUES
  (1, 'CLASSROOM',  'In Classroom', 'A'),
  (2, 'ELEARNING',  'E-Learning',   'A')
ON DUPLICATE KEY UPDATE codigo = VALUES(codigo), descripcion = VALUES(descripcion);
