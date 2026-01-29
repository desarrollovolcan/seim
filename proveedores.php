<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

require_permission('proveedores', 'view');

$municipalidad = get_municipalidad();
$errors = [];
$successMessage = '';
$editingId = null;
$viewRecord = null;
$empresaId = current_empresa_id();

$fields = [
    'nombre' => '',
    'razon_social' => '',
    'rut' => '',
    'telefono' => '',
    'correo' => '',
    'direccion' => '',
];

if (isset($_GET['view'])) {
    $viewId = (int) $_GET['view'];
    if ($viewId > 0) {
        $stmt = db()->prepare('SELECT * FROM proveedores WHERE id = ? AND (empresa_id = ? OR empresa_id IS NULL) LIMIT 1');
        $stmt->execute([$viewId, $empresaId]);
        $viewRecord = $stmt->fetch() ?: null;
    }
}

if (isset($_GET['edit'])) {
    $editingId = (int) $_GET['edit'];
    if ($editingId > 0) {
        $stmt = db()->prepare('SELECT * FROM proveedores WHERE id = ? AND (empresa_id = ? OR empresa_id IS NULL) LIMIT 1');
        $stmt->execute([$editingId, $empresaId]);
        $record = $stmt->fetch();
        if ($record) {
            $fields['nombre'] = (string) ($record['nombre'] ?? '');
            $fields['razon_social'] = (string) ($record['razon_social'] ?? '');
            $fields['rut'] = format_rut((string) ($record['rut'] ?? ''));
            $fields['telefono'] = (string) ($record['telefono'] ?? '');
            $fields['correo'] = (string) ($record['correo'] ?? '');
            $fields['direccion'] = (string) ($record['direccion'] ?? '');
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
            if (!has_permission('proveedores', 'delete')) {
                $errors[] = 'No tienes permisos para eliminar proveedores.';
            } else {
                try {
                    $stmt = db()->prepare('DELETE FROM proveedores WHERE id = ? AND (empresa_id = ? OR empresa_id IS NULL)');
                    $stmt->execute([$recordId, $empresaId]);
                    $_SESSION['proveedor_flash'] = 'Proveedor eliminado correctamente.';
                    redirect('proveedores.php');
                } catch (Exception $e) {
                    $errors[] = 'No se pudo eliminar el proveedor.';
                }
            }
        } else {
            foreach ($fields as $key => $value) {
                $fields[$key] = trim((string) ($_POST[$key] ?? ''));
            }

            if ($fields['nombre'] === '') {
                $errors[] = 'El nombre es obligatorio.';
            }
            if ($fields['rut'] === '') {
                $errors[] = 'El RUT es obligatorio.';
            } elseif (!validate_rut($fields['rut'])) {
                $errors[] = 'El RUT ingresado no es válido.';
            } else {
                $fields['rut'] = format_rut($fields['rut']);
            }
            if ($fields['correo'] !== '' && !filter_var($fields['correo'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Debes ingresar un correo válido.';
            }

            if (!$errors) {
                try {
                    if ($action === 'update' && $recordId > 0) {
                        if (!has_permission('proveedores', 'edit')) {
                            throw new RuntimeException('Sin permisos.');
                        }
                        $stmt = db()->prepare(
                            'UPDATE proveedores SET nombre = ?, razon_social = ?, rut = ?, telefono = ?, correo = ?, direccion = ?, empresa_id = ? WHERE id = ?'
                        );
                        $stmt->execute([
                            $fields['nombre'],
                            $fields['razon_social'] !== '' ? $fields['razon_social'] : null,
                            $fields['rut'],
                            $fields['telefono'] !== '' ? $fields['telefono'] : null,
                            $fields['correo'] !== '' ? $fields['correo'] : null,
                            $fields['direccion'] !== '' ? $fields['direccion'] : null,
                            $empresaId,
                            $recordId,
                        ]);
                        $_SESSION['proveedor_flash'] = 'Proveedor actualizado correctamente.';
                    } else {
                        if (!has_permission('proveedores', 'create')) {
                            throw new RuntimeException('Sin permisos.');
                        }
                        $stmt = db()->prepare(
                            'INSERT INTO proveedores (nombre, razon_social, rut, telefono, correo, direccion, empresa_id) VALUES (?, ?, ?, ?, ?, ?, ?)'
                        );
                        $stmt->execute([
                            $fields['nombre'],
                            $fields['razon_social'] !== '' ? $fields['razon_social'] : null,
                            $fields['rut'],
                            $fields['telefono'] !== '' ? $fields['telefono'] : null,
                            $fields['correo'] !== '' ? $fields['correo'] : null,
                            $fields['direccion'] !== '' ? $fields['direccion'] : null,
                            $empresaId,
                        ]);
                        $_SESSION['proveedor_flash'] = 'Proveedor registrado correctamente.';
                    }
                    redirect('proveedores.php');
                } catch (Exception $e) {
                    $errors[] = 'No se pudo guardar el proveedor. Revisa que el RUT no esté duplicado.';
                }
            }
        }
    }
}

if (isset($_SESSION['proveedor_flash'])) {
    $successMessage = (string) $_SESSION['proveedor_flash'];
    unset($_SESSION['proveedor_flash']);
}

$proveedores = [];
try {
    $stmt = db()->prepare('SELECT * FROM proveedores WHERE empresa_id = ? OR empresa_id IS NULL ORDER BY created_at DESC');
    $stmt->execute([$empresaId]);
    $proveedores = $stmt->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el listado de proveedores.';
}

include('partials/html.php');
?>

<head>
    <?php $title = 'Proveedores'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Comercial'; $title = 'Proveedores'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Proveedores</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Show Code</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showCodeSwitchProveedores">
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
                                        <h6 class="fw-semibold mb-2">Detalle de proveedor</h6>
                                        <div class="row g-2">
                                            <div class="col-md-4"><span class="text-muted">Nombre:</span> <?php echo htmlspecialchars($viewRecord['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Razón social:</span> <?php echo htmlspecialchars($viewRecord['razon_social'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">RUT:</span> <?php echo htmlspecialchars(format_rut((string) ($viewRecord['rut'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Correo:</span> <?php echo htmlspecialchars($viewRecord['correo'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Teléfono:</span> <?php echo htmlspecialchars($viewRecord['telefono'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Dirección:</span> <?php echo htmlspecialchars($viewRecord['direccion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="proveedores.php">
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
                                            <label class="form-label">Razón social</label>
                                            <input type="text" name="razon_social" class="form-control" value="<?php echo htmlspecialchars($fields['razon_social'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">RUT</label>
                                            <input type="text" name="rut" class="form-control rut-field" value="<?php echo htmlspecialchars($fields['rut'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="12.345.678-9" required>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($fields['telefono'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>

                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Correo</label>
                                            <div class="app-search">
                                                <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($fields['correo'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="proveedor@correo.com">
                                                <i data-lucide="mail" class="app-search-icon text-muted"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-6">
                                            <label class="form-label">Dirección</label>
                                            <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($fields['direccion'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Dirección">
                                        </div>

                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary" <?php echo !has_permission('proveedores', $editingId ? 'edit' : 'create') ? 'disabled' : ''; ?>>
                                                <?php echo $editingId ? 'Actualizar proveedor' : 'Guardar proveedor'; ?>
                                            </button>
                                            <?php if ($editingId) : ?>
                                                <a href="proveedores.php" class="btn btn-outline-secondary">Cancelar edición</a>
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
                                <h5 class="card-title mb-0">Proveedores registrados</h5>
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
                                                <th>Dirección</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$proveedores) : ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Sin registros aún.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($proveedores as $proveedor) : ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo htmlspecialchars($proveedor['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                            <?php if (!empty($proveedor['razon_social'])) : ?>
                                                                <div class="text-muted small"><?php echo htmlspecialchars($proveedor['razon_social'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars(format_rut((string) ($proveedor['rut'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($proveedor['correo'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($proveedor['telefono'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($proveedor['direccion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap">
                                                            <a href="proveedores.php?view=<?php echo (int) $proveedor['id']; ?>" class="btn btn-outline-info btn-sm">Ver</a>
                                                            <?php if (has_permission('proveedores', 'edit')) : ?>
                                                                <a href="proveedores.php?edit=<?php echo (int) $proveedor['id']; ?>" class="btn btn-outline-primary btn-sm">Editar</a>
                                                            <?php endif; ?>
                                                            <?php if (has_permission('proveedores', 'delete')) : ?>
                                                                <form method="post" action="proveedores.php" class="d-inline">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="id" value="<?php echo (int) $proveedor['id']; ?>">
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar proveedor?');">Eliminar</button>
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
