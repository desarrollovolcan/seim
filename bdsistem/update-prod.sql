-- Script de actualización para producción
-- Ejecutar en el orden indicado.

-- 1) Tabla empresas (si aún no existe)
CREATE TABLE IF NOT EXISTS `empresas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `razon_social` VARCHAR(200) DEFAULT NULL,
  `ruc` VARCHAR(30) DEFAULT NULL,
  `telefono` VARCHAR(30) DEFAULT NULL,
  `correo` VARCHAR(150) DEFAULT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresas_nombre_unique` (`nombre`),
  UNIQUE KEY `empresas_ruc_unique` (`ruc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Agregar columnas a municipalidad para empresa
ALTER TABLE `municipalidad`
  ADD COLUMN IF NOT EXISTS `razon_social` VARCHAR(200) DEFAULT NULL AFTER `nombre`,
  ADD COLUMN IF NOT EXISTS `moneda` VARCHAR(10) DEFAULT 'CLP' AFTER `rut`;

-- 3) Agregar columna data a module_records
ALTER TABLE `module_records`
  ADD COLUMN IF NOT EXISTS `data` JSON DEFAULT NULL AFTER `descripcion`;
