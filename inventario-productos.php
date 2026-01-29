<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

$municipalidad = get_municipalidad();
$errors = [];
$successMessage = '';
$editingId = null;
$viewRecord = null;

$fields = [
    'nombre' => '',
    'sku' => '',
    'categoria_id' => '',
    'subfamilia_id' => '',
    'unidad_id' => '',
    'precio_compra' => '',
    'precio_venta' => '',
    'stock_minimo' => '',
    'stock_actual' => '0',
    'descripcion' => '',
];

$categorias = [];
$subfamilias = [];
$unidades = [];
try {
    $categorias = db()->query('SELECT id, nombre FROM inventario_categorias ORDER BY nombre')->fetchAll();
    $subfamilias = db()->query('SELECT id, categoria_id, nombre FROM inventario_subfamilias ORDER BY nombre')->fetchAll();
    $unidades = db()->query('SELECT id, nombre, abreviatura FROM inventario_unidades ORDER BY nombre')->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar catálogos de inventario.';
}

if (isset($_GET['view'])) {
    $viewId = (int) $_GET['view'];
    if ($viewId > 0) {
        $stmt = db()->prepare('SELECT * FROM inventario_productos WHERE id = ? LIMIT 1');
        $stmt->execute([$viewId]);
        $viewRecord = $stmt->fetch() ?: null;
    }
}

if (isset($_GET['edit'])) {
    $editingId = (int) $_GET['edit'];
    if ($editingId > 0) {
        $stmt = db()->prepare('SELECT * FROM inventario_productos WHERE id = ? LIMIT 1');
        $stmt->execute([$editingId]);
        $record = $stmt->fetch();
        if ($record) {
            $fields['nombre'] = (string) ($record['nombre'] ?? '');
            $fields['sku'] = (string) ($record['sku'] ?? '');
            $fields['categoria_id'] = (string) ($record['categoria_id'] ?? '');
            $fields['subfamilia_id'] = (string) ($record['subfamilia_id'] ?? '');
            $fields['unidad_id'] = (string) ($record['unidad_id'] ?? '');
            $fields['precio_compra'] = (string) ($record['precio_compra'] ?? '');
            $fields['precio_venta'] = (string) ($record['precio_venta'] ?? '');
            $fields['stock_minimo'] = (string) ($record['stock_minimo'] ?? '');
            $fields['stock_actual'] = (string) ($record['stock_actual'] ?? '0');
            $fields['descripcion'] = (string) ($record['descripcion'] ?? '');
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Tu sesión expiró. Vuelve a intentar.';
    } else {
        $action = (string) ($_POST['action'] ?? 'create');
        $recordId = (int) ($_POST['id'] ?? 0);

        if ($action === 'delete' && $recordId > 0) {
            try {
                $stmt = db()->prepare('DELETE FROM inventario_productos WHERE id = ?');
                $stmt->execute([$recordId]);
                $_SESSION['producto_flash'] = 'Producto eliminado correctamente.';
                redirect('inventario-productos.php');
            } catch (Exception $e) {
                $errors[] = 'No se pudo eliminar el producto.';
            }
        } else {
            foreach ($fields as $key => $value) {
                $fields[$key] = trim((string) ($_POST[$key] ?? ''));
            }

            if ($fields['nombre'] === '') {
                $errors[] = 'El nombre es obligatorio.';
            }
            if ($fields['sku'] === '') {
                $errors[] = 'El SKU es obligatorio.';
            }
            if ($fields['categoria_id'] === '') {
                $errors[] = 'La familia es obligatoria.';
            }
            if ($fields['subfamilia_id'] === '') {
                $errors[] = 'La subfamilia es obligatoria.';
            }
            if ($fields['unidad_id'] === '') {
                $errors[] = 'La unidad es obligatoria.';
            }

            if (!$errors) {
                try {
                    if ($action === 'update' && $recordId > 0) {
                        $stmt = db()->prepare(
                            'UPDATE inventario_productos SET nombre = ?, sku = ?, categoria_id = ?, subfamilia_id = ?, unidad_id = ?, precio_compra = ?, precio_venta = ?, stock_minimo = ?, stock_actual = ?, descripcion = ? WHERE id = ?'
                        );
                        $stmt->execute([
                            $fields['nombre'],
                            $fields['sku'],
                            (int) $fields['categoria_id'],
                            (int) $fields['subfamilia_id'],
                            (int) $fields['unidad_id'],
                            $fields['precio_compra'] !== '' ? (float) $fields['precio_compra'] : null,
                            $fields['precio_venta'] !== '' ? (float) $fields['precio_venta'] : null,
                            $fields['stock_minimo'] !== '' ? (float) $fields['stock_minimo'] : null,
                            (float) ($fields['stock_actual'] !== '' ? $fields['stock_actual'] : 0),
                            $fields['descripcion'] !== '' ? $fields['descripcion'] : null,
                            $recordId,
                        ]);
                        $_SESSION['producto_flash'] = 'Producto actualizado correctamente.';
                    } else {
                        $stmt = db()->prepare(
                            'INSERT INTO inventario_productos (nombre, sku, categoria_id, subfamilia_id, unidad_id, precio_compra, precio_venta, stock_minimo, stock_actual, descripcion)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                        );
                        $stmt->execute([
                            $fields['nombre'],
                            $fields['sku'],
                            (int) $fields['categoria_id'],
                            (int) $fields['subfamilia_id'],
                            (int) $fields['unidad_id'],
                            $fields['precio_compra'] !== '' ? (float) $fields['precio_compra'] : null,
                            $fields['precio_venta'] !== '' ? (float) $fields['precio_venta'] : null,
                            $fields['stock_minimo'] !== '' ? (float) $fields['stock_minimo'] : null,
                            (float) ($fields['stock_actual'] !== '' ? $fields['stock_actual'] : 0),
                            $fields['descripcion'] !== '' ? $fields['descripcion'] : null,
                        ]);
                        $_SESSION['producto_flash'] = 'Producto registrado correctamente.';
                    }
                    redirect('inventario-productos.php');
                } catch (Exception $e) {
                    $errors[] = 'No se pudo guardar el producto. Revisa que el SKU no esté duplicado.';
                }
            }
        }
    }
}

if (isset($_SESSION['producto_flash'])) {
    $successMessage = (string) $_SESSION['producto_flash'];
    unset($_SESSION['producto_flash']);
}

$productos = [];
try {
    $stmt = db()->query(
        'SELECT p.*, c.nombre AS categoria_nombre, s.nombre AS subfamilia_nombre, u.nombre AS unidad_nombre, u.abreviatura AS unidad_abreviatura
         FROM inventario_productos p
         LEFT JOIN inventario_categorias c ON c.id = p.categoria_id
         LEFT JOIN inventario_subfamilias s ON s.id = p.subfamilia_id
         LEFT JOIN inventario_unidades u ON u.id = p.unidad_id
         ORDER BY p.created_at DESC'
    );
    $productos = $stmt->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el listado de productos.';
}

include('partials/html.php');
?>

<head>
    <?php $title = 'Productos'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Inventario'; $title = 'Productos'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Productos</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Show Code</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showCodeSwitchProductos">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if ($successMessage !== '') : ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <?php if ($errors) : ?>
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            <?php foreach ($errors as $error) : ?>
                                                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <?php if ($viewRecord) : ?>
                                    <div class="border rounded-3 p-3 mb-4 bg-light-subtle">
                                        <h6 class="fw-semibold mb-2">Detalle de producto</h6>
                                        <div class="row g-2">
                                            <div class="col-md-4"><span class="text-muted">Nombre:</span> <?php echo htmlspecialchars($viewRecord['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">SKU:</span> <?php echo htmlspecialchars($viewRecord['sku'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Stock:</span> <?php echo htmlspecialchars((string) ($viewRecord['stock_actual'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="inventario-productos.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="<?php echo $editingId ? 'update' : 'create'; ?>">
                                    <?php if ($editingId) : ?>
                                        <input type="hidden" name="id" value="<?php echo (int) $editingId; ?>">
                                    <?php endif; ?>

                                    <div class="row g-3">
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($fields['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">SKU</label>
                                            <input type="text" name="sku" class="form-control" value="<?php echo htmlspecialchars($fields['sku'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Familia</label>
                                            <select name="categoria_id" class="form-select" required>
                                                <option value="">Selecciona</option>
                                                <?php foreach ($categorias as $categoria) : ?>
                                                    <option value="<?php echo (int) $categoria['id']; ?>" <?php echo $fields['categoria_id'] === (string) $categoria['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Subfamilia</label>
                                            <select name="subfamilia_id" class="form-select" required>
                                                <option value="">Selecciona</option>
                                                <?php foreach ($subfamilias as $subfamilia) : ?>
                                                    <option value="<?php echo (int) $subfamilia['id']; ?>" <?php echo $fields['subfamilia_id'] === (string) $subfamilia['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($subfamilia['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Unidad</label>
                                            <select name="unidad_id" class="form-select" required>
                                                <option value="">Selecciona</option>
                                                <?php foreach ($unidades as $unidad) : ?>
                                                    <option value="<?php echo (int) $unidad['id']; ?>" <?php echo $fields['unidad_id'] === (string) $unidad['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($unidad['nombre'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($unidad['abreviatura'], ENT_QUOTES, 'UTF-8'); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Precio compra</label>
                                            <input type="number" step="0.01" name="precio_compra" class="form-control" value="<?php echo htmlspecialchars($fields['precio_compra'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Precio venta</label>
                                            <input type="number" step="0.01" name="precio_venta" class="form-control" value="<?php echo htmlspecialchars($fields['precio_venta'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Stock mínimo</label>
                                            <input type="number" step="0.01" name="stock_minimo" class="form-control" value="<?php echo htmlspecialchars($fields['stock_minimo'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="0">
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Stock actual</label>
                                            <input type="number" step="0.01" name="stock_actual" class="form-control" value="<?php echo htmlspecialchars($fields['stock_actual'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label">Descripción</label>
                                            <input type="text" name="descripcion" class="form-control" value="<?php echo htmlspecialchars($fields['descripcion'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Detalle del producto">
                                        </div>

                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <?php echo $editingId ? 'Actualizar producto' : 'Guardar producto'; ?>
                                            </button>
                                            <?php if ($editingId) : ?>
                                                <a href="inventario-productos.php" class="btn btn-outline-secondary">Cancelar edición</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Productos registrados</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>SKU</th>
                                                <th>Familia</th>
                                                <th>Subfamilia</th>
                                                <th>Unidad</th>
                                                <th>Stock</th>
                                                <th>Precio venta</th>
                                                <th>Creado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$productos) : ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">Sin registros aún.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($productos as $producto) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($producto['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($producto['sku'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($producto['categoria_nombre'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($producto['subfamilia_nombre'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($producto['unidad_abreviatura'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($producto['stock_actual'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($producto['precio_venta'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($producto['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <a class="btn btn-sm btn-outline-primary" href="inventario-productos.php?view=<?php echo (int) $producto['id']; ?>">Ver</a>
                                                            <a class="btn btn-sm btn-outline-secondary" href="inventario-productos.php?edit=<?php echo (int) $producto['id']; ?>">Editar</a>
                                                            <form method="post" action="inventario-productos.php" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?php echo (int) $producto['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este producto?');">Eliminar</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include('partials/footer.php'); ?>
    </div>

    <?php include('partials/footer-scripts.php'); ?>
</body>
</html>
