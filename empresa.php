<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

require_permission('empresas', 'view');

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
    'logo_path' => '',
    'logo_topbar_height' => '',
    'logo_sidenav_height' => '',
    'logo_sidenav_height_sm' => '',
    'logo_auth_height' => '',
];

function normalize_rut(string $value): string
{
    $clean = preg_replace('/[^0-9kK]/', '', $value);
    return strtoupper($clean ?? '');
}

function format_rut(string $value): string
{
    $normalized = normalize_rut($value);
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
            $fields['ruc'] = format_rut((string) ($record['ruc'] ?? ''));
            $fields['telefono'] = (string) ($record['telefono'] ?? '');
            $fields['correo'] = (string) ($record['correo'] ?? '');
            $fields['direccion'] = (string) ($record['direccion'] ?? '');
            $fields['logo_path'] = (string) ($record['logo_path'] ?? '');
            $fields['logo_topbar_height'] = (string) ($record['logo_topbar_height'] ?? '');
            $fields['logo_sidenav_height'] = (string) ($record['logo_sidenav_height'] ?? '');
            $fields['logo_sidenav_height_sm'] = (string) ($record['logo_sidenav_height_sm'] ?? '');
            $fields['logo_auth_height'] = (string) ($record['logo_auth_height'] ?? '');
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
            if (!has_permission('empresas', 'delete')) {
                $errors[] = 'No tienes permisos para eliminar empresas.';
            } else {
                try {
                    $stmt = db()->prepare('DELETE FROM empresas WHERE id = ?');
                    $stmt->execute([$recordId]);
                    $_SESSION['empresa_flash'] = 'Empresa eliminada correctamente.';
                    redirect('empresa.php');
                } catch (Exception $e) {
                    $errors[] = 'No se pudo eliminar la empresa.';
                }
            }
        } else {
            $rutInput = $_POST['rut'] ?? $_POST['ruc'] ?? '';
            foreach ($fields as $key => $value) {
                if (str_starts_with($key, 'logo_')) {
                    $fields[$key] = trim((string) ($_POST[$key] ?? $value));
                    continue;
                }
                if ($key === 'ruc') {
                    $fields[$key] = trim((string) $rutInput);
                    continue;
                }
                $fields[$key] = trim((string) ($_POST[$key] ?? ''));
            }

            if ($fields['nombre'] === '') {
                $errors[] = 'El nombre es obligatorio.';
            }

            $rutForDb = '';
            if ($fields['ruc'] === '') {
                $errors[] = 'El RUT es obligatorio.';
            } else {
                $rutForDb = normalize_rut($fields['ruc']);
                if ($rutForDb === '' || strlen($rutForDb) < 2) {
                    $errors[] = 'El RUT no es válido.';
                } else {
                    $fields['ruc'] = format_rut($rutForDb);
                }
            }

            if ($fields['correo'] !== '' && !filter_var($fields['correo'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Debes ingresar un correo válido.';
            }

            if (!$errors) {
                $params = [$fields['nombre'], $rutForDb];
                $duplicateSql = 'SELECT id FROM empresas WHERE (nombre = ? OR REPLACE(REPLACE(UPPER(ruc), \'.\', \'\'), \'-\', \'\') = UPPER(?))';
                if ($action === 'update' && $recordId > 0) {
                    $duplicateSql .= ' AND id <> ?';
                    $params[] = $recordId;
                }
                $stmt = db()->prepare($duplicateSql . ' LIMIT 1');
                $stmt->execute($params);
                if ($stmt->fetchColumn()) {
                    $errors[] = 'Ya existe una empresa con el mismo nombre o RUT.';
                }
            }

            if (!$errors) {
                try {
                    $logoPath = $fields['logo_path'] !== '' ? $fields['logo_path'] : null;
                    $logoTopbarHeight = (int) ($fields['logo_topbar_height'] !== '' ? $fields['logo_topbar_height'] : 0);
                    $logoSidenavHeight = (int) ($fields['logo_sidenav_height'] !== '' ? $fields['logo_sidenav_height'] : 0);
                    $logoSidenavHeightSm = (int) ($fields['logo_sidenav_height_sm'] !== '' ? $fields['logo_sidenav_height_sm'] : 0);
                    $logoAuthHeight = (int) ($fields['logo_auth_height'] !== '' ? $fields['logo_auth_height'] : 0);

                    $logoTopbarHeight = $logoTopbarHeight > 0 ? $logoTopbarHeight : null;
                    $logoSidenavHeight = $logoSidenavHeight > 0 ? $logoSidenavHeight : null;
                    $logoSidenavHeightSm = $logoSidenavHeightSm > 0 ? $logoSidenavHeightSm : null;
                    $logoAuthHeight = $logoAuthHeight > 0 ? $logoAuthHeight : null;

                    if ($action === 'update' && $recordId > 0) {
                        if (!has_permission('empresas', 'edit')) {
                            throw new RuntimeException('Sin permisos.');
                        }
                        $stmt = db()->prepare(
                            'UPDATE empresas SET nombre = ?, razon_social = ?, ruc = ?, telefono = ?, correo = ?, direccion = ?, logo_path = ?, logo_topbar_height = ?, logo_sidenav_height = ?, logo_sidenav_height_sm = ?, logo_auth_height = ? WHERE id = ?'
                        );
                        $stmt->execute([
                            $fields['nombre'],
                            $fields['razon_social'] !== '' ? $fields['razon_social'] : null,
                            $rutForDb,
                            $fields['telefono'] !== '' ? $fields['telefono'] : null,
                            $fields['correo'] !== '' ? $fields['correo'] : null,
                            $fields['direccion'] !== '' ? $fields['direccion'] : null,
                            $logoPath,
                            $logoTopbarHeight,
                            $logoSidenavHeight,
                            $logoSidenavHeightSm,
                            $logoAuthHeight,
                            $recordId,
                        ]);
                        $empresaId = $recordId;
                        $_SESSION['empresa_flash'] = 'Empresa actualizada correctamente.';
                    } else {
                        if (!has_permission('empresas', 'create')) {
                            throw new RuntimeException('Sin permisos.');
                        }
                        $stmt = db()->prepare(
                            'INSERT INTO empresas (nombre, razon_social, ruc, telefono, correo, direccion, logo_path, logo_topbar_height, logo_sidenav_height, logo_sidenav_height_sm, logo_auth_height) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                        );
                        $stmt->execute([
                            $fields['nombre'],
                            $fields['razon_social'] !== '' ? $fields['razon_social'] : null,
                            $rutForDb,
                            $fields['telefono'] !== '' ? $fields['telefono'] : null,
                            $fields['correo'] !== '' ? $fields['correo'] : null,
                            $fields['direccion'] !== '' ? $fields['direccion'] : null,
                            $logoPath,
                            $logoTopbarHeight,
                            $logoSidenavHeight,
                            $logoSidenavHeightSm,
                            $logoAuthHeight,
                        ]);
                        $empresaId = (int) db()->lastInsertId();
                        $_SESSION['empresa_flash'] = 'Empresa registrada correctamente.';
                    }

                    if (!empty($_FILES['logo']['name'] ?? '') && $empresaId > 0) {
                        if (!isset($_FILES['logo']['error']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
                            throw new RuntimeException('No se pudo subir el logo.');
                        }

                        $tmpName = (string) $_FILES['logo']['tmp_name'];
                        $info = @getimagesize($tmpName);
                        if (!$info || !isset($info['mime'])) {
                            throw new RuntimeException('El archivo de logo no es una imagen válida.');
                        }

                        $allowedMimes = ['image/png', 'image/jpeg', 'image/webp'];
                        if (!in_array($info['mime'], $allowedMimes, true)) {
                            throw new RuntimeException('El logo debe ser PNG, JPG o WEBP.');
                        }

                        $extension = strtolower(pathinfo((string) ($_FILES['logo']['name'] ?? ''), PATHINFO_EXTENSION));
                        if ($extension === '' || !in_array($extension, ['png', 'jpg', 'jpeg', 'webp'], true)) {
                            $extension = $info['mime'] === 'image/png' ? 'png' : ($info['mime'] === 'image/webp' ? 'webp' : 'jpg');
                        }

                        $uploadDir = __DIR__ . '/uploads/empresas';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        $fileName = sprintf('empresa_%d_%s.%s', $empresaId, date('YmdHis'), $extension);
                        $destination = $uploadDir . '/' . $fileName;
                        if (!move_uploaded_file($tmpName, $destination)) {
                            throw new RuntimeException('No se pudo guardar el logo.');
                        }

                        $relativePath = 'uploads/empresas/' . $fileName;
                        $stmt = db()->prepare('UPDATE empresas SET logo_path = ? WHERE id = ?');
                        $stmt->execute([$relativePath, $empresaId]);
                    }

                    redirect('empresa.php');
                } catch (PDOException $e) {
                    if ((string) $e->getCode() === '23000') {
                        $errors[] = 'Ya existe una empresa con el mismo nombre o RUT.';
                    } else {
                        $errors[] = 'No se pudo guardar la empresa. Intenta nuevamente.';
                    }
                } catch (Exception $e) {
                    $errors[] = 'No se pudo guardar la empresa. Intenta nuevamente.';
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
                                <h5 class="card-title mb-0">Registro de empresa</h5>
                                <span class="badge bg-primary-subtle text-primary">Gestión</span>
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
                                            <div class="col-md-4"><span class="text-muted">RUT:</span> <?php echo htmlspecialchars(format_rut((string) ($viewRecord['ruc'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Correo:</span> <?php echo htmlspecialchars($viewRecord['correo'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Teléfono:</span> <?php echo htmlspecialchars($viewRecord['telefono'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-8"><span class="text-muted">Dirección:</span> <?php echo htmlspecialchars($viewRecord['direccion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Logo:</span>
                                                <?php if (!empty($viewRecord['logo_path'])) : ?>
                                                    <img src="<?php echo htmlspecialchars($viewRecord['logo_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="Logo empresa" style="height: 40px;">
                                                <?php else : ?>
                                                    —
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="empresa.php" enctype="multipart/form-data">
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
                                            <input type="text" name="ruc" class="form-control" value="<?php echo htmlspecialchars($fields['ruc'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="12.345.678-9" required>
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

                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Logo de la empresa</label>
                                            <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg,image/webp">
                                            <?php if ($fields['logo_path'] !== '') : ?>
                                                <small class="text-muted d-block mt-1">Actual: <?php echo htmlspecialchars($fields['logo_path'], ENT_QUOTES, 'UTF-8'); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Altura logo (Topbar)</label>
                                            <input type="number" name="logo_topbar_height" class="form-control" value="<?php echo htmlspecialchars($fields['logo_topbar_height'], ENT_QUOTES, 'UTF-8'); ?>" min="20" max="200" placeholder="56">
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Altura logo (Menú)</label>
                                            <input type="number" name="logo_sidenav_height" class="form-control" value="<?php echo htmlspecialchars($fields['logo_sidenav_height'], ENT_QUOTES, 'UTF-8'); ?>" min="20" max="200" placeholder="48">
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Altura logo (Menú compacto)</label>
                                            <input type="number" name="logo_sidenav_height_sm" class="form-control" value="<?php echo htmlspecialchars($fields['logo_sidenav_height_sm'], ENT_QUOTES, 'UTF-8'); ?>" min="16" max="120" placeholder="36">
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <label class="form-label">Altura logo (Login)</label>
                                            <input type="number" name="logo_auth_height" class="form-control" value="<?php echo htmlspecialchars($fields['logo_auth_height'], ENT_QUOTES, 'UTF-8'); ?>" min="20" max="200" placeholder="48">
                                        </div>

                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary" <?php echo !has_permission('empresas', $editingId ? 'edit' : 'create') ? 'disabled' : ''; ?>>
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
                                                <th>RUT</th>
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
                                                        <td><?php echo htmlspecialchars(format_rut((string) ($empresa['ruc'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($empresa['telefono'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($empresa['correo'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($empresa['direccion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($empresa['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <a class="btn btn-sm btn-outline-primary" href="empresa.php?view=<?php echo (int) $empresa['id']; ?>">Ver</a>
                                                            <?php if (has_permission('empresas', 'edit')) : ?>
                                                                <a class="btn btn-sm btn-outline-secondary" href="empresa.php?edit=<?php echo (int) $empresa['id']; ?>">Editar</a>
                                                            <?php endif; ?>
                                                            <?php if (has_permission('empresas', 'delete')) : ?>
                                                                <form method="post" action="empresa.php" class="d-inline">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="id" value="<?php echo (int) $empresa['id']; ?>">
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta empresa?');">Eliminar</button>
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
        <!-- ============================================================== -->
        <!-- End Main Content -->
        <!-- ============================================================== -->

        <?php include('partials/footer.php'); ?>
    </div>
    <!-- End page -->

    <?php include('partials/footer-scripts.php'); ?>
</body>
</html>
