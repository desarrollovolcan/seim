CREATE TABLE `empresas` (
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

CREATE TABLE `municipalidad` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `razon_social` VARCHAR(200) DEFAULT NULL,
  `rut` VARCHAR(20) DEFAULT NULL,
  `moneda` VARCHAR(10) DEFAULT 'CLP',
  `telefono` VARCHAR(30) DEFAULT NULL,
  `correo` VARCHAR(150) DEFAULT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `logo_path` VARCHAR(255) DEFAULT NULL,
  `logo_topbar_height` INT DEFAULT 56,
  `logo_sidenav_height` INT DEFAULT 48,
  `logo_sidenav_height_sm` INT DEFAULT 36,
  `logo_auth_height` INT DEFAULT 48,
  `color_primary` VARCHAR(20) DEFAULT '#6658dd',
  `color_secondary` VARCHAR(20) DEFAULT '#4a81d4',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `rut` VARCHAR(20) NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NOT NULL,
  `cargo` VARCHAR(100) DEFAULT NULL,
  `fecha_nacimiento` DATE DEFAULT NULL,
  `correo` VARCHAR(150) NOT NULL,
  `telefono` VARCHAR(30) NOT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `username` VARCHAR(60) NOT NULL,
  `rol` VARCHAR(60) DEFAULT NULL,
  `unidad_id` INT UNSIGNED DEFAULT NULL,
  `avatar_path` VARCHAR(255) DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `password_locked` TINYINT(1) NOT NULL DEFAULT 0,
  `is_superadmin` TINYINT(1) NOT NULL DEFAULT 0,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_rut_unique` (`rut`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_correo_unique` (`correo`),
  KEY `users_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `users_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario_categorias` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inventario_categorias_empresa_idx` (`empresa_id`),
  UNIQUE KEY `inventario_categorias_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario_subfamilias` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT DEFAULT NULL,
  `categoria_id` INT DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventario_subfamilias_nombre_unique` (`nombre`),
  KEY `inventario_subfamilias_empresa_idx` (`empresa_id`),
  KEY `inventario_subfamilias_categoria_idx` (`categoria_id`),
  CONSTRAINT `inventario_subfamilias_categoria_fk` FOREIGN KEY (`categoria_id`) REFERENCES `inventario_categorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario_unidades` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `abreviatura` VARCHAR(30) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inventario_unidades_empresa_idx` (`empresa_id`),
  UNIQUE KEY `inventario_unidades_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario_productos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT DEFAULT NULL,
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
  KEY `inventario_productos_empresa_idx` (`empresa_id`),
  KEY `inventario_productos_categoria_idx` (`categoria_id`),
  KEY `inventario_productos_subfamilia_idx` (`subfamilia_id`),
  KEY `inventario_productos_unidad_idx` (`unidad_id`),
  CONSTRAINT `inventario_productos_categoria_fk` FOREIGN KEY (`categoria_id`) REFERENCES `inventario_categorias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventario_productos_subfamilia_fk` FOREIGN KEY (`subfamilia_id`) REFERENCES `inventario_subfamilias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventario_productos_unidad_fk` FOREIGN KEY (`unidad_id`) REFERENCES `inventario_unidades` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario_movimientos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT DEFAULT NULL,
  `producto_id` INT NOT NULL,
  `tipo` VARCHAR(20) NOT NULL,
  `cantidad` DECIMAL(12,2) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inventario_movimientos_empresa_idx` (`empresa_id`),
  KEY `inventario_movimientos_producto_idx` (`producto_id`),
  CONSTRAINT `inventario_movimientos_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `inventario_productos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `clientes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `documento` VARCHAR(60) DEFAULT NULL,
  `telefono` VARCHAR(30) DEFAULT NULL,
  `correo` VARCHAR(150) DEFAULT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `notas` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `clientes_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `clientes_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `proveedores` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `razon_social` VARCHAR(200) DEFAULT NULL,
  `rut` VARCHAR(20) NOT NULL,
  `telefono` VARCHAR(30) DEFAULT NULL,
  `correo` VARCHAR(150) DEFAULT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `contacto_nombre` VARCHAR(150) DEFAULT NULL,
  `contacto_cargo` VARCHAR(120) DEFAULT NULL,
  `contacto_telefono` VARCHAR(30) DEFAULT NULL,
  `contacto_correo` VARCHAR(150) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `proveedores_empresa_id_idx` (`empresa_id`),
  UNIQUE KEY `proveedores_rut_unique` (`rut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ventas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT DEFAULT NULL,
  `cliente_id` INT UNSIGNED DEFAULT NULL,
  `cliente_nombre` VARCHAR(150) DEFAULT NULL,
  `fecha` DATE NOT NULL,
  `total` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `nota` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ventas_empresa_idx` (`empresa_id`),
  KEY `ventas_cliente_idx` (`cliente_id`),
  CONSTRAINT `ventas_cliente_fk` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `venta_items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `venta_id` INT NOT NULL,
  `producto_id` INT NOT NULL,
  `cantidad` DECIMAL(12,2) NOT NULL,
  `precio_unitario` DECIMAL(12,2) NOT NULL,
  `total` DECIMAL(12,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `venta_items_venta_idx` (`venta_id`),
  KEY `venta_items_producto_idx` (`producto_id`),
  CONSTRAINT `venta_items_venta_fk` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `venta_items_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `inventario_productos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario_compras` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT DEFAULT NULL,
  `proveedor` VARCHAR(150) NOT NULL,
  `tipo_documento` VARCHAR(30) DEFAULT NULL,
  `numero_documento` VARCHAR(60) DEFAULT NULL,
  `fecha` DATE NOT NULL,
  `total` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `nota` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inventario_compras_empresa_idx` (`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario_compra_items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `compra_id` INT NOT NULL,
  `empresa_id` INT DEFAULT NULL,
  `producto_id` INT NOT NULL,
  `cantidad` DECIMAL(12,2) NOT NULL,
  `precio_unitario` DECIMAL(12,2) NOT NULL,
  `total` DECIMAL(12,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inventario_compra_items_empresa_idx` (`empresa_id`),
  KEY `inventario_compra_items_compra_idx` (`compra_id`),
  KEY `inventario_compra_items_producto_idx` (`producto_id`),
  CONSTRAINT `inventario_compra_items_compra_fk` FOREIGN KEY (`compra_id`) REFERENCES `inventario_compras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventario_compra_items_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `inventario_productos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(80) NOT NULL,
  `descripcion` VARCHAR(200) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `permissions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `modulo` VARCHAR(60) NOT NULL,
  `accion` VARCHAR(30) NOT NULL,
  `descripcion` VARCHAR(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_modulo_accion_unique` (`modulo`, `accion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `role_permissions` (
  `role_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`, `permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user_empresas` (
  `user_id` INT NOT NULL,
  `empresa_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `empresa_id`),
  KEY `user_empresas_empresa_idx` (`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos base mínimos
-- Sección: empresa
INSERT INTO empresas (nombre, razon_social, ruc, telefono, correo, direccion)
VALUES ('Acquaperla', 'Acquaperla SpA', '99999999-9', '+56 9 6000 0000', 'contacto@acquaperla.cl', 'Av. Principal 123');

-- Sección: roles
INSERT INTO roles (nombre, descripcion)
VALUES
  ('Super Administrador', 'Acceso total al sistema'),
  ('Administrador', 'Acceso total al sistema'),
  ('Operador', 'Gestión operativa'),
  ('Consulta', 'Solo lectura');

-- Sección: super usuario
INSERT INTO users (empresa_id, rut, nombre, apellido, correo, telefono, direccion, username, rol, password_hash, password_locked, is_superadmin, estado)
VALUES (1, '100.000.000-0', 'Super', 'Administrador', 'superadmin@acquaperla.cl', '+56 9 6000 0001', 'Av. Principal 123', 'superadmin', 'Super Administrador', '$2y$12$ORBoGynOWX2s.zGl65ScX.GZioqCrSJ8Ona5p3T3FmZUMn6KBccpa', 1, 1, 1);

-- Sección: usuario-empresa
INSERT INTO user_empresas (user_id, empresa_id)
VALUES (1, 1);
