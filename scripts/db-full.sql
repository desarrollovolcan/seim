-- Base de datos mínima para iniciar desde cero con usuarios, empresas e inventario.

CREATE TABLE empresas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    razon_social VARCHAR(200) DEFAULT NULL,
    ruc VARCHAR(30) DEFAULT NULL,
    telefono VARCHAR(30) DEFAULT NULL,
    correo VARCHAR(150) DEFAULT NULL,
    direccion VARCHAR(200) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY empresas_nombre_unique (nombre),
    UNIQUE KEY empresas_ruc_unique (ruc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    empresa_id INT UNSIGNED DEFAULT NULL,
    rut VARCHAR(20) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    cargo VARCHAR(100) DEFAULT NULL,
    fecha_nacimiento DATE DEFAULT NULL,
    correo VARCHAR(150) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    direccion VARCHAR(200) DEFAULT NULL,
    username VARCHAR(60) NOT NULL,
    rol VARCHAR(60) DEFAULT NULL,
    unidad_id INT UNSIGNED DEFAULT NULL,
    avatar_path VARCHAR(255) DEFAULT NULL,
    password_hash VARCHAR(255) NOT NULL,
    password_locked TINYINT(1) NOT NULL DEFAULT 0,
    is_superadmin TINYINT(1) NOT NULL DEFAULT 0,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY users_rut_unique (rut),
    UNIQUE KEY users_username_unique (username),
    UNIQUE KEY users_correo_unique (correo),
    KEY users_empresa_id_idx (empresa_id),
    CONSTRAINT users_empresa_fk FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inventario_categorias (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    descripcion VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY inventario_categorias_nombre_unique (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inventario_unidades (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    abreviatura VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY inventario_unidades_nombre_unique (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inventario_productos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    sku VARCHAR(60) NOT NULL,
    categoria_id INT UNSIGNED NOT NULL,
    unidad_id INT UNSIGNED NOT NULL,
    precio_compra DECIMAL(12,2) DEFAULT NULL,
    precio_venta DECIMAL(12,2) DEFAULT NULL,
    stock_minimo DECIMAL(12,2) DEFAULT 0,
    stock_actual DECIMAL(12,2) DEFAULT 0,
    descripcion VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY inventario_productos_sku_unique (sku),
    KEY inventario_productos_categoria_idx (categoria_id),
    KEY inventario_productos_unidad_idx (unidad_id),
    CONSTRAINT inventario_productos_categoria_fk FOREIGN KEY (categoria_id) REFERENCES inventario_categorias (id) ON DELETE RESTRICT,
    CONSTRAINT inventario_productos_unidad_fk FOREIGN KEY (unidad_id) REFERENCES inventario_unidades (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inventario_movimientos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    producto_id INT UNSIGNED NOT NULL,
    tipo VARCHAR(20) NOT NULL,
    cantidad DECIMAL(12,2) NOT NULL,
    descripcion VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY inventario_movimientos_producto_idx (producto_id),
    CONSTRAINT inventario_movimientos_producto_fk FOREIGN KEY (producto_id) REFERENCES inventario_productos (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
