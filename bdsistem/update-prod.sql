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
  ADD COLUMN IF NOT EXISTS `razon_social` VARCHAR(200) DEFAULT NULL AFTER `nombre`;

ALTER TABLE `municipalidad`
  ADD COLUMN IF NOT EXISTS `moneda` VARCHAR(10) DEFAULT 'CLP' AFTER `rut`;

-- 3) Agregar columna data a module_records
ALTER TABLE `module_records`
  ADD COLUMN IF NOT EXISTS `data` JSON DEFAULT NULL AFTER `descripcion`;

-- 4) Tablas de familias, subfamilias y compras (inventario ligero)
CREATE TABLE IF NOT EXISTS `inventario_categorias` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventario_categorias_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `inventario_subfamilias` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `categoria_id` INT DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventario_subfamilias_nombre_unique` (`nombre`),
  KEY `inventario_subfamilias_categoria_idx` (`categoria_id`),
  CONSTRAINT `inventario_subfamilias_categoria_fk` FOREIGN KEY (`categoria_id`) REFERENCES `inventario_categorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `inventario_unidades` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `abreviatura` VARCHAR(30) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventario_unidades_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `inventario_productos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `sku` VARCHAR(80) NOT NULL,
  `categoria_id` INT DEFAULT NULL,
  `subfamilia_id` INT DEFAULT NULL,
  `unidad_id` INT DEFAULT NULL,
  `precio_compra` DECIMAL(12,2) DEFAULT NULL,
  `precio_venta` DECIMAL(12,2) DEFAULT NULL,
  `stock_minimo` DECIMAL(12,2) DEFAULT NULL,
  `stock_actual` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventario_productos_sku_unique` (`sku`),
  KEY `inventario_productos_categoria_idx` (`categoria_id`),
  KEY `inventario_productos_subfamilia_idx` (`subfamilia_id`),
  KEY `inventario_productos_unidad_idx` (`unidad_id`),
  CONSTRAINT `inventario_productos_categoria_fk` FOREIGN KEY (`categoria_id`) REFERENCES `inventario_categorias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventario_productos_subfamilia_fk` FOREIGN KEY (`subfamilia_id`) REFERENCES `inventario_subfamilias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventario_productos_unidad_fk` FOREIGN KEY (`unidad_id`) REFERENCES `inventario_unidades` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `inventario_productos`
  ADD COLUMN IF NOT EXISTS `subfamilia_id` INT DEFAULT NULL AFTER `categoria_id`;

CREATE TABLE IF NOT EXISTS `inventario_movimientos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `producto_id` INT NOT NULL,
  `tipo` VARCHAR(20) NOT NULL,
  `cantidad` DECIMAL(12,2) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inventario_movimientos_producto_idx` (`producto_id`),
  CONSTRAINT `inventario_movimientos_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `inventario_productos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `inventario_compras` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `proveedor` VARCHAR(150) NOT NULL,
  `fecha` DATE NOT NULL,
  `total` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `nota` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `inventario_compra_items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `compra_id` INT NOT NULL,
  `producto_id` INT NOT NULL,
  `cantidad` DECIMAL(12,2) NOT NULL,
  `precio_unitario` DECIMAL(12,2) NOT NULL,
  `total` DECIMAL(12,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inventario_compra_items_compra_idx` (`compra_id`),
  KEY `inventario_compra_items_producto_idx` (`producto_id`),
  CONSTRAINT `inventario_compra_items_compra_fk` FOREIGN KEY (`compra_id`) REFERENCES `inventario_compras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventario_compra_items_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `inventario_productos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
