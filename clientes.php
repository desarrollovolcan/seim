<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

require_permission('clientes', 'view');

$municipalidad = get_municipalidad();
$errors = [];
$successMessage = '';
$editingId = null;
$viewRecord = null;
$empresaId = current_empresa_id();

$fields = [
    'nombre' => '',
    'documento' => '',
    'correo' => '',
    'telefono' => '',
    'direccion' => '',
    'notas' => '',
];

if (isset($_GET['view'])) {
    $viewId = (int) $_GET['view'];
    if ($viewId > 0) {
        $stmt = db()->prepare('SELECT * FROM clientes WHERE id = ? AND (empresa_id = ? OR empresa_id IS NULL) LIMIT 1');
        $stmt->execute([$viewId, $empresaId]);
        $viewRecord = $stmt->fetch() ?: null;
    }
}

if (isset($_GET['edit'])) {
    $editingId = (int) $_GET['edit'];
    if ($editingId > 0) {
        $stmt = db()->prepare('SELECT * FROM clientes WHERE id = ? AND (empresa_id = ? OR empresa_id IS NULL) LIMIT 1');
        $stmt->execute([$editingId, $empresaId]);
        $record = $stmt->fetch();
        if ($record) {
            $fields['nombre'] = (string) ($record['nombre'] ?? '');
            $fields['documento'] = format_rut((string) ($record['documento'] ?? ''));
            $fields['correo'] = (string) ($record['correo'] ?? '');
            $fields['telefono'] = (string) ($record['telefono'] ?? '');
            $fields['direccion'] = (string) ($record['direccion'] ?? '');
            $fields['notas'] = (string) ($record['notas'] ?? '');
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
            if (!has_permission('clientes', 'delete')) {
                $errors[] = 'No tienes permisos para eliminar clientes.';
            } else {
                try {
                    $stmt = db()->prepare('DELETE FROM clientes WHERE id = ? AND (empresa_id = ? OR empresa_id IS NULL)');
                    $stmt->execute([$recordId, $empresaId]);
                    $_SESSION['cliente_flash'] = 'Cliente eliminado correctamente.';
                    redirect('clientes.php');
                } catch (Exception $e) {
                    $errors[] = 'No se pudo eliminar el cliente.';
                }
            }
        } else {
            foreach ($fields as $key => $value) {
                $fields[$key] = trim((string) ($_POST[$key] ?? ''));
            }

            if ($fields['nombre'] === '') {
                $errors[] = 'El nombre es obligatorio.';
            }
            if ($fields['documento'] === '') {
                $errors[] = 'El RUT es obligatorio.';
            } elseif (!validate_rut($fields['documento'])) {
                $errors[] = 'El RUT ingresado no es válido.';
            } else {
                $fields['documento'] = format_rut($fields['documento']);
            }
            if ($fields['correo'] !== '' && !filter_var($fields['correo'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El correo no es válido.';
            }

            if (!$errors) {
                try {
                    if ($action === 'update' && $recordId > 0) {
                        if (!has_permission('clientes', 'edit')) {
                            throw new RuntimeException('Sin permisos.');
                        }
                        $stmt = db()->prepare(
                            'UPDATE clientes SET nombre = ?, documento = ?, correo = ?, telefono = ?, direccion = ?, notas = ?, empresa_id = ? WHERE id = ?'
                        );
                        $stmt->execute([
                            $fields['nombre'],
                            $fields['documento'] !== '' ? $fields['documento'] : null,
                            $fields['correo'] !== '' ? $fields['correo'] : null,
                            $fields['telefono'] !== '' ? $fields['telefono'] : null,
                            $fields['direccion'] !== '' ? $fields['direccion'] : null,
                            $fields['notas'] !== '' ? $fields['notas'] : null,
                            $empresaId,
                            $recordId,
                        ]);
                        $_SESSION['cliente_flash'] = 'Cliente actualizado correctamente.';
                    } else {
                        if (!has_permission('clientes', 'create')) {
                            throw new RuntimeException('Sin permisos.');
                        }
                        $stmt = db()->prepare(
                            'INSERT INTO clientes (nombre, documento, correo, telefono, direccion, notas, empresa_id) VALUES (?, ?, ?, ?, ?, ?, ?)'
                        );
                        $stmt->execute([
                            $fields['nombre'],
                            $fields['documento'] !== '' ? $fields['documento'] : null,
                            $fields['correo'] !== '' ? $fields['correo'] : null,
                            $fields['telefono'] !== '' ? $fields['telefono'] : null,
                            $fields['direccion'] !== '' ? $fields['direccion'] : null,
                            $fields['notas'] !== '' ? $fields['notas'] : null,
                            $empresaId,
                        ]);
                        $_SESSION['cliente_flash'] = 'Cliente registrado correctamente.';
                    }
                    redirect('clientes.php');
                } catch (Exception $e) {
                    $errors[] = 'No se pudo guardar el cliente.';
                }
            }
        }
    }
}

if (isset($_SESSION['cliente_flash'])) {
    $successMessage = (string) $_SESSION['cliente_flash'];
    unset($_SESSION['cliente_flash']);
}

$clientes = [];
try {
    $stmt = db()->prepare('SELECT * FROM clientes WHERE empresa_id = ? OR empresa_id IS NULL ORDER BY created_at DESC');
    $stmt->execute([$empresaId]);
    $clientes = $stmt->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el listado de clientes.';
}

include('partials/html.php');
?>

<head>
    <?php $title = 'Clientes'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Comercial'; $title = 'Clientes'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Clientes</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Show Code</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showCodeSwitchClientes">
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
                                        <h6 class="fw-semibold mb-2">Detalle de cliente</h6>
                                        <div class="row g-2">
                                            <div class="col-md-4"><span class="text-muted">Nombre:</span> <?php echo htmlspecialchars($viewRecord['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">RUT:</span> <?php echo htmlspecialchars(format_rut((string) ($viewRecord['documento'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Correo:</span> <?php echo htmlspecialchars($viewRecord['correo'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Teléfono:</span> <?php echo htmlspecialchars($viewRecord['telefono'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Dirección:</span> <?php echo htmlspecialchars($viewRecord['direccion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Notas:</span> <?php echo htmlspecialchars($viewRecord['notas'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="clientes.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="<?php echo $editingId ? 'update' : 'create'; ?>">
                                    <?php if ($editingId) : ?>
                                        <input type="hidden" name="id" value="<?php echo (int) $editingId; ?>">
                                    <?php endif; ?>

                                    <div class="row g-3">
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($fields['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">RUT</label>
                                            <input type="text" name="documento" class="form-control rut-field" value="<?php echo htmlspecialchars($fields['documento'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="12.345.678-9" required>
                                        </div>
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">Correo</label>
                                            <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($fields['correo'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="cliente@correo.com">
                                        </div>
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($fields['telefono'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="+56 9 1234 5678">
                                        </div>
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">Dirección</label>
                                            <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($fields['direccion'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Dirección del cliente">
                                        </div>
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">Notas</label>
                                            <input type="text" name="notas" class="form-control" value="<?php echo htmlspecialchars($fields['notas'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Observaciones">
                                        </div>

                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary" <?php echo !has_permission('clientes', $editingId ? 'edit' : 'create') ? 'disabled' : ''; ?>>
                                                <?php echo $editingId ? 'Actualizar cliente' : 'Guardar cliente'; ?>
                                            </button>
                                            <?php if ($editingId) : ?>
                                                <a href="clientes.php" class="btn btn-outline-secondary">Cancelar edición</a>
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
                                <h5 class="card-title mb-0">Clientes registrados</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>RUT</th>
                                                <th>Correo</th>
                                                <th>Teléfono</th>
                                                <th>Creado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$clientes) : ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Sin registros aún.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($clientes as $cliente) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($cliente['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars(format_rut((string) ($cliente['documento'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($cliente['correo'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($cliente['telefono'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($cliente['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <a class="btn btn-sm btn-outline-primary" href="clientes.php?view=<?php echo (int) $cliente['id']; ?>">Ver</a>
                                                            <?php if (has_permission('clientes', 'edit')) : ?>
                                                                <a class="btn btn-sm btn-outline-secondary" href="clientes.php?edit=<?php echo (int) $cliente['id']; ?>">Editar</a>
                                                            <?php endif; ?>
                                                            <?php if (has_permission('clientes', 'delete')) : ?>
                                                                <form method="post" action="clientes.php" class="d-inline">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="id" value="<?php echo (int) $cliente['id']; ?>">
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este cliente?');">Eliminar</button>
                                                                </form>
                                                            <?php endif; ?>
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
    <script>
        function formatRut(value) {
            const clean = value.replace(/[^0-9kK]/g, '').toUpperCase();
            if (clean.length === 0) {
                return '';
            }
            const body = clean.slice(0, -1);
            const dv = clean.slice(-1);
            const reversed = body.split('').reverse();
            const grouped = [];
            for (let i = 0; i < reversed.length; i += 3) {
                grouped.push(reversed.slice(i, i + 3).reverse().join(''));
            }
            const formattedBody = grouped.reverse().join('.');
            return `${formattedBody}-${dv}`;
        }

        document.querySelectorAll('.rut-field').forEach((input) => {
            input.addEventListener('blur', (event) => {
                event.target.value = formatRut(event.target.value);
            });
        });
    </script>
</body>
</html>
