<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

require_permission('movimientos', 'view');

$municipalidad = get_municipalidad();
$errors = [];
$successMessage = '';
$viewRecord = null;
$empresaId = current_empresa_id();

$fields = [
    'producto_id' => '',
    'tipo' => 'entrada',
    'cantidad' => '',
    'descripcion' => '',
];

$productos = [];
try {
    $stmt = db()->prepare('SELECT id, nombre, sku FROM inventario_productos WHERE empresa_id = ? OR empresa_id IS NULL ORDER BY nombre');
    $stmt->execute([$empresaId]);
    $productos = $stmt->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el listado de productos.';
}

if (isset($_GET['view'])) {
    $viewId = (int) $_GET['view'];
    if ($viewId > 0) {
        $stmt = db()->prepare(
            'SELECT m.*, p.nombre AS producto_nombre, p.sku AS producto_sku
             FROM inventario_movimientos m
             JOIN inventario_productos p ON p.id = m.producto_id
             WHERE m.id = ? AND (m.empresa_id = ? OR m.empresa_id IS NULL) LIMIT 1'
        );
        $stmt->execute([$viewId, $empresaId]);
        $viewRecord = $stmt->fetch() ?: null;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Tu sesión expiró. Vuelve a intentar.';
    } else {
        $action = (string) ($_POST['action'] ?? 'create');
        $recordId = (int) ($_POST['id'] ?? 0);

        if ($action === 'delete' && $recordId > 0) {
            $errors[] = 'No se permite eliminar movimientos para mantener el historial.';
        } else {
            foreach ($fields as $key => $value) {
                $fields[$key] = trim((string) ($_POST[$key] ?? ''));
            }

            if ($fields['producto_id'] === '') {
                $errors[] = 'El producto es obligatorio.';
            }
            if (!in_array($fields['tipo'], ['entrada', 'salida', 'ajuste'], true)) {
                $errors[] = 'El tipo de movimiento es inválido.';
            }
            if ($fields['cantidad'] === '' || !is_numeric($fields['cantidad'])) {
                $errors[] = 'La cantidad es obligatoria.';
            }

            if (!$errors) {
                try {
                    if (!has_permission('movimientos', 'create')) {
                        throw new RuntimeException('Sin permisos.');
                    }
                    $cantidad = (float) $fields['cantidad'];
                    if ($fields['tipo'] === 'salida') {
                        $cantidad = -abs($cantidad);
                    }

                    $stmt = db()->prepare(
                        'INSERT INTO inventario_movimientos (producto_id, tipo, cantidad, descripcion, empresa_id)
                         VALUES (?, ?, ?, ?, ?)'
                    );
                    $stmt->execute([
                        (int) $fields['producto_id'],
                        $fields['tipo'],
                        $cantidad,
                        $fields['descripcion'] !== '' ? $fields['descripcion'] : null,
                        $empresaId,
                    ]);

                    $stmt = db()->prepare('UPDATE inventario_productos SET stock_actual = stock_actual + ? WHERE id = ?');
                    $stmt->execute([$cantidad, (int) $fields['producto_id']]);

                    $_SESSION['movimiento_flash'] = 'Movimiento registrado correctamente.';
                    redirect('inventario-movimientos.php');
                } catch (Exception $e) {
                    $errors[] = 'No se pudo guardar el movimiento.';
                }
            }
        }
    }
}

if (isset($_SESSION['movimiento_flash'])) {
    $successMessage = (string) $_SESSION['movimiento_flash'];
    unset($_SESSION['movimiento_flash']);
}

$movimientos = [];
try {
    $stmt = db()->prepare(
        'SELECT m.*, p.nombre AS producto_nombre, p.sku AS producto_sku
         FROM inventario_movimientos m
         JOIN inventario_productos p ON p.id = m.producto_id
         WHERE m.empresa_id = ? OR m.empresa_id IS NULL
         ORDER BY m.created_at DESC'
    );
    $stmt->execute([$empresaId]);
    $movimientos = $stmt->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el historial de movimientos.';
}

include('partials/html.php');
?>

<head>
    <?php $title = 'Movimientos'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Inventario'; $title = 'Movimientos'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Movimientos de inventario</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Show Code</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showCodeSwitchMovimientos">
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
                                        <h6 class="fw-semibold mb-2">Detalle de movimiento</h6>
                                        <div class="row g-2">
                                            <div class="col-md-4"><span class="text-muted">Producto:</span> <?php echo htmlspecialchars($viewRecord['producto_nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Tipo:</span> <?php echo htmlspecialchars($viewRecord['tipo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Cantidad:</span> <?php echo htmlspecialchars((string) ($viewRecord['cantidad'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="inventario-movimientos.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="create">

                                    <div class="row g-3">
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">Producto</label>
                                            <select name="producto_id" class="form-select" required>
                                                <option value="">Selecciona</option>
                                                <?php foreach ($productos as $producto) : ?>
                                                    <option value="<?php echo (int) $producto['id']; ?>" <?php echo $fields['producto_id'] === (string) $producto['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($producto['sku'], ENT_QUOTES, 'UTF-8'); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xl-2">
                                            <label class="form-label">Tipo</label>
                                            <select name="tipo" class="form-select">
                                                <option value="entrada" <?php echo $fields['tipo'] === 'entrada' ? 'selected' : ''; ?>>Entrada</option>
                                                <option value="salida" <?php echo $fields['tipo'] === 'salida' ? 'selected' : ''; ?>>Salida</option>
                                                <option value="ajuste" <?php echo $fields['tipo'] === 'ajuste' ? 'selected' : ''; ?>>Ajuste</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xl-2">
                                            <label class="form-label">Cantidad</label>
                                            <input type="number" step="0.01" name="cantidad" class="form-control" value="<?php echo htmlspecialchars($fields['cantidad'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">Descripción</label>
                                            <input type="text" name="descripcion" class="form-control" value="<?php echo htmlspecialchars($fields['descripcion'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Motivo del movimiento">
                                        </div>

                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary" <?php echo !has_permission('movimientos', 'create') ? 'disabled' : ''; ?>>Guardar movimiento</button>
                                            <?php if (!has_permission('movimientos', 'create')) : ?>
                                                <span class="text-muted align-self-center">No tienes permisos para registrar movimientos.</span>
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
                                <h5 class="card-title mb-0">Historial de movimientos</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Tipo</th>
                                                <th>Cantidad</th>
                                                <th>Descripción</th>
                                                <th>Fecha</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$movimientos) : ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Sin registros aún.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($movimientos as $movimiento) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($movimiento['producto_nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($movimiento['tipo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($movimiento['cantidad'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($movimiento['descripcion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($movimiento['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <a class="btn btn-sm btn-outline-primary" href="inventario-movimientos.php?view=<?php echo (int) $movimiento['id']; ?>">Ver</a>
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
