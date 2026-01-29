<?php

declare(strict_types=1);

session_start();

$config = require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user'])) {
    $currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $publicScripts = [
        'auth-2-sign-in.php',
        'logout.php',
    ];

    if (!in_array($currentScript, $publicScripts, true) && strncmp($currentScript, 'auth-', 5) !== 0) {
        header('Location: auth-2-sign-in.php');
        exit;
    }
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $settings = $GLOBALS['config']['db'];
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $settings['host'],
        $settings['name'],
        $settings['charset']
    );

    $pdo = new PDO($dsn, $settings['user'], $settings['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

    return $scheme . '://' . $host . ($basePath !== '' ? $basePath : '');
}

function ensure_event_validation_token(int $eventId, ?string $currentToken): string
{
    if (!empty($currentToken)) {
        return $currentToken;
    }

    $token = bin2hex(random_bytes(16));
    $stmt = db()->prepare('UPDATE events SET validation_token = ? WHERE id = ?');
    $stmt->execute([$token, $eventId]);

    return $token;
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function current_empresa_id(): ?int
{
    if (isset($_SESSION['empresa_id'])) {
        return (int) $_SESSION['empresa_id'];
    }

    if (!isset($_SESSION['user']['id'])) {
        return get_default_empresa_id();
    }

    try {
        $stmt = db()->prepare('SELECT empresa_id FROM user_empresas WHERE user_id = ? ORDER BY empresa_id LIMIT 1');
        $stmt->execute([(int) $_SESSION['user']['id']]);
        $empresaId = $stmt->fetchColumn();
        if ($empresaId) {
            $_SESSION['empresa_id'] = (int) $empresaId;
            return (int) $empresaId;
        }
    } catch (Exception $e) {
    } catch (Error $e) {
    }

    $defaultEmpresaId = get_default_empresa_id();
    if ($defaultEmpresaId) {
        $_SESSION['empresa_id'] = $defaultEmpresaId;
    }

    return $defaultEmpresaId;
}

function load_user_empresas(int $userId): array
{
    try {
        $stmt = db()->prepare(
            'SELECT e.id, e.nombre, e.razon_social
             FROM user_empresas ue
             INNER JOIN empresas e ON e.id = ue.empresa_id
             WHERE ue.user_id = ?
             ORDER BY e.nombre'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
    } catch (Error $e) {
    }

    return [];
}

function load_empresas(): array
{
    try {
        return db()->query('SELECT id, nombre, razon_social FROM empresas ORDER BY nombre')->fetchAll();
    } catch (Exception $e) {
    } catch (Error $e) {
    }

    return [];
}

function validate_rut(string $rut): bool
{
    $clean = strtoupper((string) preg_replace('/[^0-9K]/', '', $rut));
    if (strlen($clean) < 2) {
        return false;
    }

    $body = substr($clean, 0, -1);
    $dv = substr($clean, -1);
    if ($body === '' || !ctype_digit($body)) {
        return false;
    }

    $sum = 0;
    $factor = 2;
    for ($i = strlen($body) - 1; $i >= 0; $i--) {
        $sum += ((int) $body[$i]) * $factor;
        $factor = $factor === 7 ? 2 : $factor + 1;
    }

    $mod = 11 - ($sum % 11);
    $expectedDv = match ($mod) {
        11 => '0',
        10 => 'K',
        default => (string) $mod,
    };

    return $dv === $expectedDv;
}

function normalize_rut(string $rut): string
{
    $clean = strtoupper((string) preg_replace('/[^0-9K]/', '', $rut));
    return $clean;
}

function format_rut(string $rut): string
{
    $normalized = normalize_rut($rut);
    if ($normalized === '' || strlen($normalized) < 2) {
        return $normalized;
    }

    $body = substr($normalized, 0, -1);
    $dv = substr($normalized, -1);
    $reversed = strrev($body);
    $chunks = str_split($reversed, 3);
    $bodyWithDots = strrev(implode('.', $chunks));

    return $bodyWithDots . '-' . $dv;
}

function get_municipalidad(): array
{
    $defaults = [
        'nombre' => 'Go Muni',
        'razon_social' => '',
        'moneda' => 'CLP',
        'logo_path' => 'assets/images/logo.png',
        'logo_topbar_height' => 56,
        'logo_sidenav_height' => 48,
        'logo_sidenav_height_sm' => 36,
        'logo_auth_height' => 48,
        'color_primary' => '#6658dd',
        'color_secondary' => '#4a81d4',
    ];

    try {
        $stmt = db()->query('SELECT * FROM municipalidad LIMIT 1');
        $municipalidad = $stmt->fetch();
        if (is_array($municipalidad)) {
            $defaults = array_merge($defaults, $municipalidad);
        }
    } catch (Exception $e) {
    } catch (Error $e) {
    }

    try {
        $empresaId = current_empresa_id();
        if ($empresaId) {
            $stmt = db()->prepare(
                'SELECT nombre, razon_social, logo_path, logo_topbar_height, logo_sidenav_height, logo_sidenav_height_sm, logo_auth_height
                 FROM empresas
                 WHERE id = ?
                 LIMIT 1'
            );
            $stmt->execute([$empresaId]);
            $empresa = $stmt->fetch();
            if (is_array($empresa)) {
                foreach (['nombre', 'razon_social', 'logo_path'] as $field) {
                    if (array_key_exists($field, $empresa) && $empresa[$field] !== null && $empresa[$field] !== '') {
                        $defaults[$field] = $empresa[$field];
                    }
                }
                foreach (['logo_topbar_height', 'logo_sidenav_height', 'logo_sidenav_height_sm', 'logo_auth_height'] as $field) {
                    if (array_key_exists($field, $empresa) && is_numeric($empresa[$field]) && (int) $empresa[$field] > 0) {
                        $defaults[$field] = (int) $empresa[$field];
                    }
                }
            }
        }
    } catch (Exception $e) {
    } catch (Error $e) {
    }

    return $defaults;
}

function hex_to_rgb(string $hex): ?array
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
        return null;
    }
    return [
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2)),
    ];
}

function ensure_event_types(): array
{
    $defaults = [
        ['nombre' => 'Reunión', 'color_class' => 'bg-primary-subtle text-primary'],
        ['nombre' => 'Operativo', 'color_class' => 'bg-secondary-subtle text-secondary'],
        ['nombre' => 'Ceremonia', 'color_class' => 'bg-success-subtle text-success'],
        ['nombre' => 'Actividad cultural', 'color_class' => 'bg-warning-subtle text-warning'],
    ];

    try {
        db()->exec(
            'CREATE TABLE IF NOT EXISTS event_types (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(120) NOT NULL,
                color_class VARCHAR(120) NOT NULL DEFAULT "bg-primary-subtle text-primary",
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $count = (int) db()->query('SELECT COUNT(*) FROM event_types')->fetchColumn();
        if ($count === 0) {
            $stmt = db()->prepare('INSERT INTO event_types (nombre, color_class) VALUES (?, ?)');
            foreach ($defaults as $default) {
                $stmt->execute([$default['nombre'], $default['color_class']]);
            }
        }

        return db()->query('SELECT id, nombre, color_class FROM event_types ORDER BY nombre')->fetchAll();
    } catch (Exception $e) {
    } catch (Error $e) {
    }

    return $defaults;
}

function table_exists(string $table): bool
{
    try {
        $stmt = db()->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?'
        );
        $stmt->execute([$GLOBALS['config']['db']['name'], $table]);
        return (int) $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    } catch (Error $e) {
        return false;
    }
}

function column_exists(string $table, string $column): bool
{
    try {
        $stmt = db()->prepare(
            'SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ?'
        );
        $stmt->execute([$GLOBALS['config']['db']['name'], $table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    } catch (Error $e) {
        return false;
    }
}

function get_default_empresa_id(): ?int
{
    try {
        $empresaId = db()->query('SELECT id FROM empresas ORDER BY id LIMIT 1')->fetchColumn();
        return $empresaId ? (int) $empresaId : null;
    } catch (Exception $e) {
        return null;
    } catch (Error $e) {
        return null;
    }
}

function get_default_bodega_id(): ?int
{
    if (!table_exists('bodegas')) {
        return null;
    }

    try {
        $bodegaId = db()->query('SELECT id FROM bodegas ORDER BY id LIMIT 1')->fetchColumn();
        if ($bodegaId) {
            return (int) $bodegaId;
        }

        $empresaId = get_default_empresa_id();
        $stmt = db()->prepare('INSERT INTO bodegas (nombre, empresa_id) VALUES (?, ?)');
        $stmt->execute(['Bodega principal', $empresaId]);
        return (int) db()->lastInsertId();
    } catch (Exception $e) {
        return null;
    } catch (Error $e) {
        return null;
    }
}

function ensure_comercial_tables(): void
{
    try {
        db()->exec(
            'CREATE TABLE IF NOT EXISTS empresas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(150) NOT NULL,
                razon_social VARCHAR(200) DEFAULT NULL,
                ruc VARCHAR(20) NOT NULL,
                telefono VARCHAR(40) DEFAULT NULL,
                correo VARCHAR(150) DEFAULT NULL,
                direccion VARCHAR(200) DEFAULT NULL,
                logo_path VARCHAR(255) DEFAULT NULL,
                logo_topbar_height INT DEFAULT NULL,
                logo_sidenav_height INT DEFAULT NULL,
                logo_sidenav_height_sm INT DEFAULT NULL,
                logo_auth_height INT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY empresas_nombre_unique (nombre),
                UNIQUE KEY empresas_ruc_unique (ruc)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS inventario_categorias (
                id INT AUTO_INCREMENT PRIMARY KEY,
                empresa_id INT NULL,
                nombre VARCHAR(150) NOT NULL,
                descripcion VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY inventario_categorias_empresa_idx (empresa_id),
                UNIQUE KEY inventario_categorias_nombre_unique (nombre)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS inventario_subfamilias (
                id INT AUTO_INCREMENT PRIMARY KEY,
                empresa_id INT NULL,
                categoria_id INT NULL,
                nombre VARCHAR(150) NOT NULL,
                descripcion VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY inventario_subfamilias_nombre_unique (nombre),
                KEY inventario_subfamilias_empresa_idx (empresa_id),
                KEY inventario_subfamilias_categoria_idx (categoria_id),
                CONSTRAINT inventario_subfamilias_categoria_fk FOREIGN KEY (categoria_id) REFERENCES inventario_categorias (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS inventario_unidades (
                id INT AUTO_INCREMENT PRIMARY KEY,
                empresa_id INT NULL,
                nombre VARCHAR(150) NOT NULL,
                abreviatura VARCHAR(30) NOT NULL,
                descripcion VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY inventario_unidades_empresa_idx (empresa_id),
                UNIQUE KEY inventario_unidades_nombre_unique (nombre)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS inventario_productos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                empresa_id INT NULL,
                nombre VARCHAR(150) NOT NULL,
                sku VARCHAR(80) NOT NULL,
                categoria_id INT NULL,
                subfamilia_id INT NULL,
                unidad_id INT NULL,
                precio_compra DECIMAL(12,2) DEFAULT NULL,
                precio_venta DECIMAL(12,2) DEFAULT NULL,
                stock_minimo DECIMAL(12,2) DEFAULT NULL,
                stock_actual DECIMAL(12,2) NOT NULL DEFAULT 0,
                descripcion VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY inventario_productos_sku_unique (sku),
                KEY inventario_productos_empresa_idx (empresa_id),
                KEY inventario_productos_categoria_idx (categoria_id),
                KEY inventario_productos_subfamilia_idx (subfamilia_id),
                KEY inventario_productos_unidad_idx (unidad_id),
                CONSTRAINT inventario_productos_categoria_fk FOREIGN KEY (categoria_id) REFERENCES inventario_categorias (id) ON DELETE SET NULL,
                CONSTRAINT inventario_productos_subfamilia_fk FOREIGN KEY (subfamilia_id) REFERENCES inventario_subfamilias (id) ON DELETE SET NULL,
                CONSTRAINT inventario_productos_unidad_fk FOREIGN KEY (unidad_id) REFERENCES inventario_unidades (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS inventario_movimientos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                empresa_id INT NULL,
                producto_id INT NOT NULL,
                tipo VARCHAR(20) NOT NULL,
                cantidad DECIMAL(12,2) NOT NULL,
                descripcion VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY inventario_movimientos_empresa_idx (empresa_id),
                KEY inventario_movimientos_producto_idx (producto_id),
                CONSTRAINT inventario_movimientos_producto_fk FOREIGN KEY (producto_id) REFERENCES inventario_productos (id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS clientes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                empresa_id INT NULL,
                nombre VARCHAR(150) NOT NULL,
                documento VARCHAR(60) DEFAULT NULL,
                correo VARCHAR(150) DEFAULT NULL,
                telefono VARCHAR(40) DEFAULT NULL,
                direccion VARCHAR(200) DEFAULT NULL,
                notas VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY clientes_empresa_idx (empresa_id),
                KEY clientes_nombre_idx (nombre)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS proveedores (
                id INT AUTO_INCREMENT PRIMARY KEY,
                empresa_id INT NULL,
                nombre VARCHAR(150) NOT NULL,
                razon_social VARCHAR(200) DEFAULT NULL,
                rut VARCHAR(20) NOT NULL,
                telefono VARCHAR(40) DEFAULT NULL,
                correo VARCHAR(150) DEFAULT NULL,
                direccion VARCHAR(200) DEFAULT NULL,
                contacto_nombre VARCHAR(150) DEFAULT NULL,
                contacto_cargo VARCHAR(120) DEFAULT NULL,
                contacto_telefono VARCHAR(30) DEFAULT NULL,
                contacto_correo VARCHAR(150) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY proveedores_empresa_idx (empresa_id),
                UNIQUE KEY proveedores_rut_unique (rut),
                KEY proveedores_nombre_idx (nombre)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS ventas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                empresa_id INT NULL,
                cliente_id INT NULL,
                cliente_nombre VARCHAR(150) DEFAULT NULL,
                fecha DATE NOT NULL,
                total DECIMAL(12,2) NOT NULL DEFAULT 0,
                nota VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY ventas_empresa_idx (empresa_id),
                KEY ventas_cliente_idx (cliente_id),
                CONSTRAINT ventas_cliente_fk FOREIGN KEY (cliente_id) REFERENCES clientes (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS venta_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                venta_id INT NOT NULL,
                producto_id INT NOT NULL,
                cantidad DECIMAL(12,2) NOT NULL,
                precio_unitario DECIMAL(12,2) NOT NULL,
                total DECIMAL(12,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY venta_items_venta_idx (venta_id),
                KEY venta_items_producto_idx (producto_id),
                CONSTRAINT venta_items_venta_fk FOREIGN KEY (venta_id) REFERENCES ventas (id) ON DELETE CASCADE,
                CONSTRAINT venta_items_producto_fk FOREIGN KEY (producto_id) REFERENCES inventario_productos (id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS inventario_compras (
                id INT AUTO_INCREMENT PRIMARY KEY,
                empresa_id INT NULL,
                proveedor VARCHAR(150) NOT NULL,
                tipo_documento VARCHAR(30) DEFAULT NULL,
                numero_documento VARCHAR(60) DEFAULT NULL,
                fecha DATE NOT NULL,
                total DECIMAL(12,2) NOT NULL DEFAULT 0,
                nota VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS inventario_compra_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                compra_id INT NOT NULL,
                empresa_id INT NULL,
                producto_id INT NOT NULL,
                cantidad DECIMAL(12,2) NOT NULL,
                precio_unitario DECIMAL(12,2) NOT NULL,
                total DECIMAL(12,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY inventario_compra_items_empresa_idx (empresa_id),
                KEY inventario_compra_items_compra_idx (compra_id),
                KEY inventario_compra_items_producto_idx (producto_id),
                CONSTRAINT inventario_compra_items_compra_fk FOREIGN KEY (compra_id) REFERENCES inventario_compras (id) ON DELETE CASCADE,
                CONSTRAINT inventario_compra_items_producto_fk FOREIGN KEY (producto_id) REFERENCES inventario_productos (id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(80) NOT NULL,
                descripcion VARCHAR(200) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY roles_nombre_unique (nombre)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS permissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                modulo VARCHAR(60) NOT NULL,
                accion VARCHAR(30) NOT NULL,
                descripcion VARCHAR(200) DEFAULT NULL,
                UNIQUE KEY permissions_modulo_accion_unique (modulo, accion)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS role_permissions (
                role_id INT NOT NULL,
                permission_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (role_id, permission_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS user_empresas (
                user_id INT NOT NULL,
                empresa_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (user_id, empresa_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    } catch (Exception $e) {
    } catch (Error $e) {
    }

    try {
        db()->exec('ALTER TABLE inventario_productos ADD COLUMN IF NOT EXISTS subfamilia_id INT NULL');
        db()->exec('ALTER TABLE inventario_categorias ADD COLUMN IF NOT EXISTS empresa_id INT NULL');
        db()->exec('ALTER TABLE inventario_subfamilias ADD COLUMN IF NOT EXISTS empresa_id INT NULL');
        db()->exec('ALTER TABLE inventario_unidades ADD COLUMN IF NOT EXISTS empresa_id INT NULL');
        db()->exec('ALTER TABLE inventario_productos ADD COLUMN IF NOT EXISTS empresa_id INT NULL');
        db()->exec('ALTER TABLE inventario_movimientos ADD COLUMN IF NOT EXISTS empresa_id INT NULL');
        db()->exec('ALTER TABLE clientes ADD COLUMN IF NOT EXISTS empresa_id INT NULL');
        db()->exec('ALTER TABLE proveedores ADD COLUMN IF NOT EXISTS empresa_id INT NULL');
        db()->exec('ALTER TABLE proveedores ADD COLUMN IF NOT EXISTS contacto_nombre VARCHAR(150) DEFAULT NULL');
        db()->exec('ALTER TABLE proveedores ADD COLUMN IF NOT EXISTS contacto_cargo VARCHAR(120) DEFAULT NULL');
        db()->exec('ALTER TABLE proveedores ADD COLUMN IF NOT EXISTS contacto_telefono VARCHAR(30) DEFAULT NULL');
        db()->exec('ALTER TABLE proveedores ADD COLUMN IF NOT EXISTS contacto_correo VARCHAR(150) DEFAULT NULL');
        db()->exec('ALTER TABLE ventas ADD COLUMN IF NOT EXISTS empresa_id INT NULL');
        db()->exec('ALTER TABLE inventario_compras ADD COLUMN IF NOT EXISTS empresa_id INT NULL');
        db()->exec('ALTER TABLE inventario_compra_items ADD COLUMN IF NOT EXISTS empresa_id INT NULL');
        db()->exec('ALTER TABLE inventario_compras ADD COLUMN IF NOT EXISTS tipo_documento VARCHAR(30) DEFAULT NULL');
        db()->exec('ALTER TABLE inventario_compras ADD COLUMN IF NOT EXISTS numero_documento VARCHAR(60) DEFAULT NULL');
        db()->exec('ALTER TABLE ventas ADD COLUMN IF NOT EXISTS cliente_id INT NULL');
        db()->exec('ALTER TABLE ventas ADD COLUMN IF NOT EXISTS cliente_nombre VARCHAR(150) DEFAULT NULL');
        db()->exec('ALTER TABLE ventas ADD COLUMN IF NOT EXISTS fecha DATE NOT NULL DEFAULT "1970-01-01"');
        db()->exec('ALTER TABLE ventas ADD COLUMN IF NOT EXISTS total DECIMAL(12,2) NOT NULL DEFAULT 0');
        db()->exec('ALTER TABLE ventas ADD COLUMN IF NOT EXISTS nota VARCHAR(255) DEFAULT NULL');
        db()->exec('ALTER TABLE ventas ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
        db()->exec('ALTER TABLE empresas ADD COLUMN IF NOT EXISTS logo_path VARCHAR(255) DEFAULT NULL');
        db()->exec('ALTER TABLE empresas ADD COLUMN IF NOT EXISTS logo_topbar_height INT DEFAULT NULL');
        db()->exec('ALTER TABLE empresas ADD COLUMN IF NOT EXISTS logo_sidenav_height INT DEFAULT NULL');
        db()->exec('ALTER TABLE empresas ADD COLUMN IF NOT EXISTS logo_sidenav_height_sm INT DEFAULT NULL');
        db()->exec('ALTER TABLE empresas ADD COLUMN IF NOT EXISTS logo_auth_height INT DEFAULT NULL');
    } catch (Exception $e) {
    } catch (Error $e) {
    }

    try {
        $hasRoles = (int) db()->query('SELECT COUNT(*) FROM roles')->fetchColumn();
        if ($hasRoles === 0) {
            $stmt = db()->prepare('INSERT INTO roles (nombre, descripcion) VALUES (?, ?)');
            $stmt->execute(['Super Administrador', 'Acceso total al sistema']);
            $stmt->execute(['Administrador', 'Acceso total al sistema']);
            $stmt->execute(['Operador', 'Gestión operativa']);
            $stmt->execute(['Consulta', 'Solo lectura']);
        }
    } catch (Exception $e) {
    } catch (Error $e) {
    }
}

function current_role_id(): ?int
{
    if (!isset($_SESSION['user']['rol'])) {
        return null;
    }

    static $roleIdCache = [];
    $roleName = (string) $_SESSION['user']['rol'];
    if ($roleName === '') {
        return null;
    }
    if (array_key_exists($roleName, $roleIdCache)) {
        return $roleIdCache[$roleName];
    }

    try {
        $stmt = db()->prepare('SELECT id FROM roles WHERE nombre = ? LIMIT 1');
        $stmt->execute([$roleName]);
        $roleId = $stmt->fetchColumn();
        $roleIdCache[$roleName] = $roleId ? (int) $roleId : null;
    } catch (Exception $e) {
        $roleIdCache[$roleName] = null;
    } catch (Error $e) {
        $roleIdCache[$roleName] = null;
    }

    return $roleIdCache[$roleName];
}

ensure_comercial_tables();

function is_superuser(): bool
{
    if (!isset($_SESSION['user']['rol'])) {
        return false;
    }

    $roleName = strtolower((string) $_SESSION['user']['rol']);
    return $roleName !== '' && (str_contains($roleName, 'super') || str_contains($roleName, 'admin'));
}

function has_permission(string $module, string $action = 'view'): bool
{
    if (!isset($_SESSION['user'])) {
        return true;
    }

    if (is_superuser()) {
        return true;
    }

    $roleId = current_role_id();
    if (!$roleId) {
        return true;
    }

    try {
        $stmt = db()->prepare('SELECT COUNT(*) FROM role_permissions WHERE role_id = ?');
        $stmt->execute([$roleId]);
        $hasAssignments = (int) $stmt->fetchColumn() > 0;
        if (!$hasAssignments) {
            return true;
        }

        $stmt = db()->prepare('SELECT id FROM permissions WHERE modulo = ? AND accion = ? LIMIT 1');
        $stmt->execute([$module, $action]);
        $permissionId = $stmt->fetchColumn();
        if (!$permissionId) {
            return true;
        }

        $stmt = db()->prepare('SELECT 1 FROM role_permissions WHERE role_id = ? AND permission_id = ?');
        $stmt->execute([$roleId, (int) $permissionId]);
        return (bool) $stmt->fetchColumn();
    } catch (Exception $e) {
        return true;
    } catch (Error $e) {
        return true;
    }
}

function require_permission(string $module, string $action = 'view'): void
{
    if (has_permission($module, $action)) {
        return;
    }

    $_SESSION['permission_error'] = 'No tienes permisos para acceder a este módulo.';
    redirect('dashboard.php');
}
