<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

$municipalidad = get_municipalidad();
$errors = [];
$successMessage = '';

$fields = [
    'proveedor' => '',
    'tipo_documento' => '',
    'numero_documento' => '',
    'fecha' => date('Y-m-d'),
    'nota' => '',
];

$productos = [];
[$lineItems, $lineErrors] = [[['producto_id' => '', 'cantidad' => '', 'precio_unitario' => '']], []];
try {
    $productos = db()->query('SELECT id, nombre, sku, stock_actual FROM inventario_productos ORDER BY nombre')->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar la lista de productos.';
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

        if ($fields['proveedor'] === '') {
            $errors[] = 'El proveedor es obligatorio.';
        }
        if ($fields['tipo_documento'] === '') {
            $errors[] = 'El tipo de documento es obligatorio.';
        }
        if ($fields['numero_documento'] === '') {
            $errors[] = 'El número de documento es obligatorio.';
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
                $total = $lineTotal;

                db()->beginTransaction();

                $stmt = db()->prepare(
                    'INSERT INTO inventario_compras (proveedor, tipo_documento, numero_documento, fecha, total, nota) VALUES (?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $fields['proveedor'],
                    $fields['tipo_documento'],
                    $fields['numero_documento'],
                    $fields['fecha'],
                    $total,
                    $fields['nota'] !== '' ? $fields['nota'] : null,
                ]);
                $compraId = (int) db()->lastInsertId();

                $insertItem = db()->prepare(
                    'INSERT INTO inventario_compra_items (compra_id, producto_id, cantidad, precio_unitario, total)
                     VALUES (?, ?, ?, ?, ?)'
                );
                $updateStock = db()->prepare('UPDATE inventario_productos SET stock_actual = stock_actual + ? WHERE id = ?');
                $insertMovimiento = db()->prepare(
                    'INSERT INTO inventario_movimientos (producto_id, tipo, cantidad, descripcion) VALUES (?, ?, ?, ?)'
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
                        $compraId,
                        (int) $line['producto_id'],
                        $cantidad,
                        $precioUnitario,
                        $lineTotalValue,
                    ]);

                    $updateStock->execute([$cantidad, (int) $line['producto_id']]);

                    $insertMovimiento->execute([
                        (int) $line['producto_id'],
                        'entrada',
                        $cantidad,
                        'Ingreso por compra',
                    ]);
                }

                db()->commit();

                $_SESSION['compra_flash'] = 'Compra registrada correctamente.';
                redirect('compras.php');
            } catch (Exception $e) {
                if (db()->inTransaction()) {
                    db()->rollBack();
                }
                $errors[] = 'No se pudo registrar la compra.';
            }
        }
    }
}

if (isset($_SESSION['compra_flash'])) {
    $successMessage = (string) $_SESSION['compra_flash'];
    unset($_SESSION['compra_flash']);
}

$compras = [];
try {
    $compras = db()->query('SELECT * FROM inventario_compras ORDER BY created_at DESC')->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el listado de compras.';
}

include('partials/html.php');
?>

<head>
    <?php $title = 'Compras'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Entradas'; $title = 'Compras'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Compras</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Show Code</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showCodeSwitchCompras">
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

                                <form method="post" action="compras.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

                                    <div class="row g-3">
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">Proveedor</label>
                                            <input type="text" name="proveedor" class="form-control" value="<?php echo htmlspecialchars($fields['proveedor'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 col-xl-2">
                                            <label class="form-label">Tipo documento</label>
                                            <select name="tipo_documento" class="form-select" required>
                                                <option value="">Selecciona</option>
                                                <?php
                                                    $tipoOptions = ['factura' => 'Factura', 'guia' => 'Guía', 'boleta' => 'Boleta'];
                                                    foreach ($tipoOptions as $value => $label) :
                                                ?>
                                                    <option value="<?php echo $value; ?>" <?php echo $fields['tipo_documento'] === $value ? 'selected' : ''; ?>>
                                                        <?php echo $label; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xl-2">
                                            <label class="form-label">N° documento</label>
                                            <input type="text" name="numero_documento" class="form-control" value="<?php echo htmlspecialchars($fields['numero_documento'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 col-xl-2">
                                            <label class="form-label">Fecha recepción</label>
                                            <input type="date" name="fecha" class="form-control" value="<?php echo htmlspecialchars($fields['fecha'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Detalle de productos</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered align-middle mb-2" id="comprasDetalleTable">
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
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="addCompraLine">Agregar línea</button>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Nota</label>
                                            <input type="text" name="nota" class="form-control" value="<?php echo htmlspecialchars($fields['nota'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Observaciones de la compra">
                                        </div>

                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">Guardar compra</button>
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
                                <h5 class="card-title mb-0">Compras registradas</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Proveedor</th>
                                                <th>Documento</th>
                                                <th>Fecha recepción</th>
                                                <th>Total</th>
                                                <th>Nota</th>
                                                <th>Creado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$compras) : ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Sin registros aún.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($compras as $compra) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($compra['proveedor'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php echo htmlspecialchars(($compra['tipo_documento'] ?? '—') . ' ' . ($compra['numero_documento'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($compra['fecha'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($compra['total'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($compra['nota'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($compra['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
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
            const addLineButton = document.getElementById('addCompraLine');
            const tableBody = document.querySelector('#comprasDetalleTable tbody');

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
