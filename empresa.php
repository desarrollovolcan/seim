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
    'razon_social' => '',
    'ruc' => '',
    'telefono' => '',
    'correo' => '',
    'direccion' => '',
];

if (isset($_GET['view'])) {
    $viewId = (int) $_GET['view'];
    if ($viewId > 0) {
        $stmt = db()->prepare('SELECT * FROM empresas WHERE id = ? LIMIT 1');
        $stmt->execute([$viewId]);
        $viewRecord = $stmt->fetch() ?: null;
    }
}

if (isset($_GET['edit'])) {
    $editingId = (int) $_GET['edit'];
    if ($editingId > 0) {
        $stmt = db()->prepare('SELECT * FROM empresas WHERE id = ? LIMIT 1');
        $stmt->execute([$editingId]);
        $record = $stmt->fetch();
        if ($record) {
            $fields['nombre'] = (string) ($record['nombre'] ?? '');
            $fields['razon_social'] = (string) ($record['razon_social'] ?? '');
            $fields['ruc'] = (string) ($record['ruc'] ?? '');
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
            try {
                $stmt = db()->prepare('DELETE FROM empresas WHERE id = ?');
                $stmt->execute([$recordId]);
                $_SESSION['empresa_flash'] = 'Empresa eliminada correctamente.';
                redirect('empresa.php');
            } catch (Exception $e) {
                $errors[] = 'No se pudo eliminar la empresa.';
            }
        } else {
            foreach ($fields as $key => $value) {
                $fields[$key] = trim((string) ($_POST[$key] ?? ''));
            }

            if ($fields['nombre'] === '') {
                $errors[] = 'El nombre es obligatorio.';
            }
            if ($fields['ruc'] === '') {
                $errors[] = 'El RUC es obligatorio.';
            }
            if ($fields['correo'] !== '' && !filter_var($fields['correo'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Debes ingresar un correo válido.';
            }

            if (!$errors) {
                try {
                    if ($action === 'update' && $recordId > 0) {
                        $stmt = db()->prepare(
                            'UPDATE empresas SET nombre = ?, razon_social = ?, ruc = ?, telefono = ?, correo = ?, direccion = ? WHERE id = ?'
                        );
                        $stmt->execute([
                            $fields['nombre'],
                            $fields['razon_social'] !== '' ? $fields['razon_social'] : null,
                            $fields['ruc'],
                            $fields['telefono'] !== '' ? $fields['telefono'] : null,
                            $fields['correo'] !== '' ? $fields['correo'] : null,
                            $fields['direccion'] !== '' ? $fields['direccion'] : null,
                            $recordId,
                        ]);
                        $_SESSION['empresa_flash'] = 'Empresa actualizada correctamente.';
                    } else {
                        $stmt = db()->prepare(
                            'INSERT INTO empresas (nombre, razon_social, ruc, telefono, correo, direccion) VALUES (?, ?, ?, ?, ?, ?)'
                        );
                        $stmt->execute([
                            $fields['nombre'],
                            $fields['razon_social'] !== '' ? $fields['razon_social'] : null,
                            $fields['ruc'],
                            $fields['telefono'] !== '' ? $fields['telefono'] : null,
                            $fields['correo'] !== '' ? $fields['correo'] : null,
                            $fields['direccion'] !== '' ? $fields['direccion'] : null,
                        ]);
                        $_SESSION['empresa_flash'] = 'Empresa registrada correctamente.';
                    }
                    redirect('empresa.php');
                } catch (Exception $e) {
                    $errors[] = 'No se pudo guardar la empresa. Revisa que el nombre o RUC no estén duplicados.';
                }
            }
        }
    }
}

if (isset($_SESSION['empresa_flash'])) {
    $successMessage = (string) $_SESSION['empresa_flash'];
    unset($_SESSION['empresa_flash']);
}

$empresas = [];
try {
    $empresas = db()->query('SELECT * FROM empresas ORDER BY created_at DESC')->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el listado de empresas.';
}

include('partials/html.php');
?>

<head>
    <?php $title = 'Empresa'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Gestión'; $title = 'Empresa'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Input Example</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Show Code</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showCodeSwitchEmpresa">
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
                                        <h6 class="fw-semibold mb-2">Detalle de empresa</h6>
                                        <div class="row g-2">
                                            <div class="col-md-4"><span class="text-muted">Nombre:</span> <?php echo htmlspecialchars($viewRecord['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">RUC:</span> <?php echo htmlspecialchars($viewRecord['ruc'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Correo:</span> <?php echo htmlspecialchars($viewRecord['correo'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Teléfono:</span> <?php echo htmlspecialchars($viewRecord['telefono'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-8"><span class="text-muted">Dirección:</span> <?php echo htmlspecialchars($viewRecord['direccion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="empresa.php">
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
                                            <label class="form-label">RUC</label>
                                            <input type="text" name="ruc" class="form-control" value="<?php echo htmlspecialchars($fields['ruc'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($fields['telefono'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>

                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Correo</label>
                                            <div class="app-search">
                                                <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($fields['correo'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="empresa@correo.com">
                                                <i data-lucide="mail" class="app-search-icon text-muted"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-6">
                                            <label class="form-label">Dirección</label>
                                            <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($fields['direccion'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Dirección">
                                        </div>

                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <?php echo $editingId ? 'Actualizar empresa' : 'Guardar empresa'; ?>
                                            </button>
                                            <?php if ($editingId) : ?>
                                                <a href="empresa.php" class="btn btn-outline-secondary">Cancelar edición</a>
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
                                <h5 class="card-title mb-0">Empresas registradas</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>RUC</th>
                                                <th>Teléfono</th>
                                                <th>Correo</th>
                                                <th>Dirección</th>
                                                <th>Creado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$empresas) : ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">Sin registros aún.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($empresas as $empresa) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($empresa['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($empresa['ruc'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($empresa['telefono'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($empresa['correo'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($empresa['direccion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($empresa['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <a class="btn btn-sm btn-outline-primary" href="empresa.php?view=<?php echo (int) $empresa['id']; ?>">Ver</a>
                                                            <a class="btn btn-sm btn-outline-secondary" href="empresa.php?edit=<?php echo (int) $empresa['id']; ?>">Editar</a>
                                                            <form method="post" action="empresa.php" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?php echo (int) $empresa['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta empresa?');">Eliminar</button>
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
        <!-- ============================================================== -->
        <!-- End Main Content -->
        <!-- ============================================================== -->

        <?php include('partials/footer.php'); ?>
    </div>
    <!-- End page -->

    <?php include('partials/footer-scripts.php'); ?>
</body>
</html>
