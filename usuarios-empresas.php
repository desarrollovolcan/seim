<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

require_permission('usuarios_empresas', 'view');

$errors = [];
$successMessage = '';

$usuarios = [];
$empresas = load_empresas();
$selectedUserId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;

try {
    $usuarios = db()->query('SELECT id, nombre, apellido, correo FROM users ORDER BY nombre')->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el listado de usuarios.';
}

if ($selectedUserId === 0 && $usuarios) {
    $selectedUserId = (int) $usuarios[0]['id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Tu sesión expiró. Vuelve a intentar.';
    } elseif (!has_permission('usuarios_empresas', 'edit')) {
        $errors[] = 'No tienes permisos para asignar empresas.';
    } else {
        $selectedUserId = (int) ($_POST['user_id'] ?? 0);
        $empresaIds = $_POST['empresa_ids'] ?? [];
        $empresaIds = array_values(array_unique(array_filter(array_map('intval', (array) $empresaIds))));

        if ($selectedUserId <= 0) {
            $errors[] = 'Debes seleccionar un usuario.';
        } elseif (!$empresaIds) {
            $errors[] = 'Selecciona al menos una empresa.';
        } else {
            try {
                db()->beginTransaction();
                $stmtDelete = db()->prepare('DELETE FROM user_empresas WHERE user_id = ?');
                $stmtDelete->execute([$selectedUserId]);

                $stmtInsert = db()->prepare('INSERT INTO user_empresas (user_id, empresa_id) VALUES (?, ?)');
                foreach ($empresaIds as $empresaId) {
                    $stmtInsert->execute([$selectedUserId, $empresaId]);
                }
                db()->commit();

                $_SESSION['usuarios_empresas_flash'] = 'Empresas asignadas correctamente.';
                redirect('usuarios-empresas.php?user_id=' . $selectedUserId);
            } catch (Exception $e) {
                if (db()->inTransaction()) {
                    db()->rollBack();
                }
                $errors[] = 'No se pudo guardar la asignación.';
            }
        }
    }
}

if (isset($_SESSION['usuarios_empresas_flash'])) {
    $successMessage = (string) $_SESSION['usuarios_empresas_flash'];
    unset($_SESSION['usuarios_empresas_flash']);
}

$userEmpresas = [];
if ($selectedUserId > 0) {
    try {
        $stmt = db()->prepare('SELECT empresa_id FROM user_empresas WHERE user_id = ?');
        $stmt->execute([$selectedUserId]);
        $userEmpresas = array_map('intval', array_column($stmt->fetchAll(), 'empresa_id'));
    } catch (Exception $e) {
        $errors[] = 'No se pudo cargar la asignación actual.';
    }
}

include('partials/html.php');
?>

<head>
    <?php $title = 'Usuarios por empresa'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Accesos'; $title = 'Usuarios por empresa'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Asignación de empresas</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Show Code</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showCodeSwitchUsuariosEmpresas">
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

                                <form method="post" action="usuarios-empresas.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Usuario</label>
                                            <select name="user_id" class="form-select" onchange="window.location='usuarios-empresas.php?user_id=' + this.value">
                                                <?php foreach ($usuarios as $usuario) : ?>
                                                    <?php $selected = $selectedUserId === (int) $usuario['id'] ? 'selected' : ''; ?>
                                                    <option value="<?php echo (int) $usuario['id']; ?>" <?php echo $selected; ?>>
                                                        <?php echo htmlspecialchars(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Empresas asociadas</label>
                                            <div class="row g-2">
                                                <?php foreach ($empresas as $empresa) : ?>
                                                    <?php
                                                        $empresaId = (int) ($empresa['id'] ?? 0);
                                                        $empresaNombre = $empresa['razon_social'] ?: $empresa['nombre'];
                                                        $checked = in_array($empresaId, $userEmpresas, true);
                                                    ?>
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="empresa_ids[]" value="<?php echo $empresaId; ?>" id="empresa-<?php echo $empresaId; ?>" <?php echo $checked ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="empresa-<?php echo $empresaId; ?>">
                                                                <?php echo htmlspecialchars($empresaNombre, ENT_QUOTES, 'UTF-8'); ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary" <?php echo has_permission('usuarios_empresas', 'edit') ? '' : 'disabled'; ?>>Guardar asignación</button>
                                        </div>
                                    </div>
                                </form>
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
