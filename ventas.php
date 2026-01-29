<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

require_permission('ventas', 'view');

$municipalidad = get_municipalidad();
$errors = [];
$successMessage = '';
$empresaId = current_empresa_id();

$fields = [
    'cliente_id' => '',
    'fecha' => date('Y-m-d'),
    'nota' => '',
];

$clientes = [];
$productos = [];
$lineItems = [['producto_id' => '', 'cantidad' => '', 'precio_unitario' => '']];
$lineErrors = [];
$hasClienteNombreColumn = column_exists('ventas', 'cliente_nombre');
$hasClienteIdColumn = column_exists('ventas', 'cliente_id');
$hasClienteLegacyColumn = column_exists('ventas', 'cliente');
$hasBodegaColumn = column_exists('ventas', 'bodega_id');
$hasEmpresaColumn = column_exists('ventas', 'empresa_id');
$hasNotaColumn = column_exists('ventas', 'nota');
$hasObservacionColumn = column_exists('ventas', 'observacion');
try {
    $stmt = db()->prepare('SELECT id, nombre, documento FROM clientes WHERE empresa_id = ? OR empresa_id IS NULL ORDER BY nombre');
    $stmt->execute([$empresaId]);
    $clientes = $stmt->fetchAll();
    $stmt = db()->prepare('SELECT id, nombre, sku, precio_venta, stock_actual FROM inventario_productos WHERE empresa_id = ? OR empresa_id IS NULL ORDER BY nombre');
    $stmt->execute([$empresaId]);
    $productos = $stmt->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar los catálogos de venta.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Tu sesión expiró. Vuelve a intentar.';
    } else {
        foreach ($fields as $key => $value) {
            $fields[$key] = trim((string) ($_POST[$key] ?? ''));
        }

        $productoIds = $_POST['producto_id'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];
        $precios = $_POST['precio_unitario'] ?? [];

        $lineItems = [];
        foreach ($productoIds as $index => $productoId) {
            $lineItems[] = [
                'producto_id' => trim((string) $productoId),
                'cantidad' => trim((string) ($cantidades[$index] ?? '')),
                'precio_unitario' => trim((string) ($precios[$index] ?? '')),
            ];
        }

        if ($fields['cliente_id'] === '') {
            $errors[] = 'El cliente es obligatorio.';
        }

        $lineTotal = 0;
        $validLines = 0;
        foreach ($lineItems as $index => $line) {
            $hasAny = $line['producto_id'] !== '' || $line['cantidad'] !== '' || $line['precio_unitario'] !== '';
            if (!$hasAny) {
                continue;
            }

            if ($line['producto_id'] === '') {
                $lineErrors[$index] = 'Selecciona un producto.';
                continue;
            }
            if ($line['cantidad'] === '' || !is_numeric($line['cantidad'])) {
                $lineErrors[$index] = 'Cantidad inválida.';
                continue;
            }
            if ($line['precio_unitario'] === '' || !is_numeric($line['precio_unitario'])) {
                $lineErrors[$index] = 'Precio inválido.';
                continue;
            }

            $validLines++;
            $lineTotal += (float) $line['cantidad'] * (float) $line['precio_unitario'];
        }

        if ($validLines === 0) {
            $errors[] = 'Debes ingresar al menos un producto.';
        }

        if (!$errors) {
            try {
                if (!has_permission('ventas', 'create')) {
                    throw new RuntimeException('Sin permisos.');
                }
                $total = $lineTotal;

                db()->beginTransaction();

                $stmt = db()->prepare('SELECT nombre FROM clientes WHERE id = ? AND (empresa_id = ? OR empresa_id IS NULL) LIMIT 1');
                $stmt->execute([(int) $fields['cliente_id'], $empresaId]);
                $clienteNombre = $stmt->fetchColumn();
                if (!$clienteNombre) {
                    throw new RuntimeException('Cliente no encontrado.');
                }

                $columns = [];
                $values = [];

                if ($hasClienteIdColumn) {
                    $columns[] = 'cliente_id';
                    $values[] = (int) $fields['cliente_id'];
                }
                if ($hasClienteNombreColumn) {
                    $columns[] = 'cliente_nombre';
                    $values[] = $clienteNombre;
                }
                if ($hasClienteLegacyColumn) {
                    $columns[] = 'cliente';
                    $values[] = $clienteNombre;
                }

                if ($hasBodegaColumn) {
                    $bodegaId = get_default_bodega_id();
                    if (!$bodegaId) {
                        throw new RuntimeException('No se encontró una bodega configurada.');
                    }
                    $columns[] = 'bodega_id';
                    $values[] = $bodegaId;
                }

                if ($hasEmpresaColumn) {
                    $columns[] = 'empresa_id';
                    $values[] = $empresaId;
                }

                $columns[] = 'fecha';
                $values[] = $fields['fecha'];
                $columns[] = 'total';
                $values[] = $total;

                if ($hasNotaColumn) {
                    $columns[] = 'nota';
                    $values[] = $fields['nota'] !== '' ? $fields['nota'] : null;
                } elseif ($hasObservacionColumn) {
                    $columns[] = 'observacion';
                    $values[] = $fields['nota'] !== '' ? $fields['nota'] : null;
                }

                $placeholders = implode(', ', array_fill(0, count($columns), '?'));
                $stmt = db()->prepare(
                    sprintf('INSERT INTO ventas (%s) VALUES (%s)', implode(', ', $columns), $placeholders)
                );
                $stmt->execute($values);
                $ventaId = (int) db()->lastInsertId();

                $insertItem = db()->prepare(
                    'INSERT INTO venta_items (venta_id, producto_id, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?)'
                );
                $updateStock = db()->prepare('UPDATE inventario_productos SET stock_actual = stock_actual - ? WHERE id = ?');
                $insertMovimiento = db()->prepare(
                    'INSERT INTO inventario_movimientos (producto_id, tipo, cantidad, descripcion, empresa_id) VALUES (?, ?, ?, ?, ?)'
                );

                foreach ($lineItems as $line) {
                    $hasAny = $line['producto_id'] !== '' || $line['cantidad'] !== '' || $line['precio_unitario'] !== '';
                    if (!$hasAny) {
                        continue;
                    }
                    if ($line['producto_id'] === '' || $line['cantidad'] === '' || $line['precio_unitario'] === '') {
                        continue;
                    }

                    $cantidad = (float) $line['cantidad'];
                    $precioUnitario = (float) $line['precio_unitario'];
                    $lineTotalValue = $cantidad * $precioUnitario;

                    $insertItem->execute([
                        $ventaId,
                        (int) $line['producto_id'],
                        $cantidad,
                        $precioUnitario,
                        $lineTotalValue,
                    ]);

                    $updateStock->execute([$cantidad, (int) $line['producto_id']]);

                    $insertMovimiento->execute([
                        (int) $line['producto_id'],
                        'salida',
                        $cantidad,
                        'Salida por venta',
                        $empresaId,
                    ]);
                }

                db()->commit();

                $_SESSION['venta_flash'] = 'Venta registrada correctamente.';
                redirect('ventas.php');
            } catch (Exception $e) {
                if (db()->inTransaction()) {
                    db()->rollBack();
                }
                $errors[] = 'No se pudo registrar la venta.';
            }
        }
    }
}

if (isset($_SESSION['venta_flash'])) {
    $successMessage = (string) $_SESSION['venta_flash'];
    unset($_SESSION['venta_flash']);
}

$ventas = [];
try {
    if ($hasClienteIdColumn) {
        if ($hasClienteNombreColumn) {
            $clienteNombreSelect = 'COALESCE(c.nombre, v.cliente_nombre) AS cliente_nombre';
        } elseif ($hasClienteLegacyColumn) {
            $clienteNombreSelect = 'COALESCE(c.nombre, v.cliente) AS cliente_nombre';
        } else {
            $clienteNombreSelect = 'c.nombre AS cliente_nombre';
        }

        $stmt = db()->prepare(
            sprintf(
                'SELECT v.*, %s
                 FROM ventas v
                 LEFT JOIN clientes c ON c.id = v.cliente_id
                 WHERE v.empresa_id = ? OR v.empresa_id IS NULL
                 ORDER BY v.created_at DESC',
                $clienteNombreSelect
            )
        );
        $stmt->execute([$empresaId]);
        $ventas = $stmt->fetchAll();
    } else {
        $clienteNombreSelect = $hasClienteNombreColumn ? 'v.cliente_nombre AS cliente_nombre' : 'v.cliente AS cliente_nombre';
        $stmt = db()->prepare(
            sprintf(
                'SELECT v.*, %s FROM ventas v WHERE v.empresa_id = ? OR v.empresa_id IS NULL ORDER BY v.created_at DESC',
                $clienteNombreSelect
            )
        );
        $stmt->execute([$empresaId]);
        $ventas = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el listado de ventas.';
}

include('partials/html.php');
?>

<head>
    <?php $title = 'Ventas'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Comercial'; $title = 'Ventas'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Control de ventas</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Show Code</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showCodeSwitchVentas">
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

                                <form method="post" action="ventas.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

                                    <div class="row g-3">
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">Cliente</label>
                                            <select name="cliente_id" class="form-select" required>
                                                <option value="">Selecciona</option>
                                                <?php foreach ($clientes as $cliente) : ?>
                                                    <option value="<?php echo (int) $cliente['id']; ?>" <?php echo $fields['cliente_id'] === (string) $cliente['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($cliente['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                        <?php if (!empty($cliente['documento'])) : ?>
                                                            (<?php echo htmlspecialchars($cliente['documento'], ENT_QUOTES, 'UTF-8'); ?>)
                                                        <?php endif; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="form-text">¿No aparece? <a href="clientes.php">Registra un cliente</a>.</div>
                                        </div>
                                        <div class="col-md-6 col-xl-2">
                                            <label class="form-label">Fecha</label>
                                            <input type="date" name="fecha" class="form-control" value="<?php echo htmlspecialchars($fields['fecha'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Detalle de productos</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered align-middle mb-2" id="ventasDetalleTable">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 45%;">Producto</th>
                                                            <th style="width: 15%;">Cantidad</th>
                                                            <th style="width: 20%;">Precio unitario</th>
                                                            <th style="width: 20%;">Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($lineItems as $index => $line) : ?>
                                                            <tr>
                                                                <td>
                                                                    <select name="producto_id[]" class="form-select">
                                                                        <option value="">Selecciona</option>
                                                                        <?php foreach ($productos as $producto) : ?>
                                                                            <option value="<?php echo (int) $producto['id']; ?>" <?php echo $line['producto_id'] === (string) $producto['id'] ? 'selected' : ''; ?>>
                                                                                <?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($producto['sku'], ENT_QUOTES, 'UTF-8'); ?>)
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    <?php if (isset($lineErrors[$index])) : ?>
                                                                        <div class="text-danger small mt-1"><?php echo htmlspecialchars($lineErrors[$index], ENT_QUOTES, 'UTF-8'); ?></div>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.01" name="cantidad[]" class="form-control" value="<?php echo htmlspecialchars($line['cantidad'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.01" name="precio_unitario[]" class="form-control" value="<?php echo htmlspecialchars($line['precio_unitario'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-line">Quitar</button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="addVentaLine">Agregar línea</button>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Nota</label>
                                            <input type="text" name="nota" class="form-control" value="<?php echo htmlspecialchars($fields['nota'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Observaciones de la venta">
                                        </div>

                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary" <?php echo !$clientes || !has_permission('ventas', 'create') ? 'disabled' : ''; ?>>Guardar venta</button>
                                            <?php if (!$clientes) : ?>
                                                <span class="text-muted align-self-center">Necesitas registrar al menos un cliente.</span>
                                            <?php elseif (!has_permission('ventas', 'create')) : ?>
                                                <span class="text-muted align-self-center">No tienes permisos para registrar ventas.</span>
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
                                <h5 class="card-title mb-0">Ventas registradas</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>Fecha</th>
                                                <th>Total</th>
                                                <th>Nota</th>
                                                <th>Creado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$ventas) : ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Sin registros aún.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($ventas as $venta) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($venta['cliente_nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($venta['fecha'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($venta['total'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($venta['nota'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($venta['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
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
    <script>
        (() => {
            const addLineButton = document.getElementById('addVentaLine');
            const tableBody = document.querySelector('#ventasDetalleTable tbody');

            if (!addLineButton || !tableBody) {
                return;
            }

            addLineButton.addEventListener('click', () => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <select name="producto_id[]" class="form-select">
                            <option value="">Selecciona</option>
                            ${<?php echo json_encode($productos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>.map((producto) => {
                                const label = `${producto.nombre} (${producto.sku})`;
                                return `<option value="${producto.id}">${label}</option>`;
                            }).join('')}
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="cantidad[]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="precio_unitario[]" class="form-control"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-line">Quitar</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            tableBody.addEventListener('click', (event) => {
                const target = event.target;
                if (target instanceof HTMLElement && target.classList.contains('remove-line')) {
                    const row = target.closest('tr');
                    if (row && tableBody.rows.length > 1) {
                        row.remove();
                    }
                }
            });
        })();
    </script>
</body>
</html>
