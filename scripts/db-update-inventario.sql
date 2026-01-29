-- Agrega tablas para gestión simple de inventario de productos.

CREATE TABLE IF NOT EXISTS inventario_categorias (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    descripcion VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY inventario_categorias_nombre_unique (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS inventario_unidades (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    abreviatura VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY inventario_unidades_nombre_unique (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS inventario_productos (
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

CREATE TABLE IF NOT EXISTS inventario_movimientos (
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

CREATE TABLE IF NOT EXISTS ventas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    cliente VARCHAR(150) NOT NULL,
    fecha DATE NOT NULL,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    nota VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS venta_items (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    venta_id INT UNSIGNED NOT NULL,
    producto_id INT UNSIGNED NOT NULL,
    cantidad DECIMAL(12,2) NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY venta_items_venta_idx (venta_id),
    KEY venta_items_producto_idx (producto_id),
    CONSTRAINT venta_items_venta_fk FOREIGN KEY (venta_id) REFERENCES ventas (id) ON DELETE CASCADE,
    CONSTRAINT venta_items_producto_fk FOREIGN KEY (producto_id) REFERENCES inventario_productos (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
