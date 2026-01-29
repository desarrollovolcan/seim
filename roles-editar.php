<?php
require __DIR__ . '/app/bootstrap.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$rol = null;
$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';
$roles = db()->query('SELECT id, nombre, descripcion, estado FROM roles ORDER BY nombre')->fetchAll();

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM roles WHERE id = ?');
    $stmt->execute([$id]);
    $rol = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $deleteId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($deleteId > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM roles WHERE id = ?');
            $stmt->execute([$deleteId]);
            redirect('roles-editar.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el rol. Verifica dependencias asociadas.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado = isset($_POST['estado']) && $_POST['estado'] === '0' ? 0 : 1;

    if ($nombre === '') {
        $errors[] = 'El nombre del rol es obligatorio.';
    }

    if (empty($errors)) {
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE roles SET nombre = ?, descripcion = ?, estado = ? WHERE id = ?');
            $stmt->execute([$nombre, $descripcion !== '' ? $descripcion : null, $estado, $id]);
            redirect('roles-editar.php?id=' . $id . '&success=1');
        } else {
            $stmt = db()->prepare('INSERT INTO roles (nombre, descripcion, estado) VALUES (?, ?, ?)');
            $stmt->execute([$nombre, $descripcion !== '' ? $descripcion : null, $estado]);
            $newId = (int) db()->lastInsertId();
            redirect('roles-editar.php?id=' . $newId . '&success=1');
        }
    }
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Crear/editar rol"; include('partials/title-meta.php'); ?>

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

            <div class="container-fluid erp-page">

                <?php $subtitle = "Roles y Permisos"; $title = "Crear/editar rol"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="erp-section">
                            <div class="erp-section-header">
                                <div class="erp-toolbar">
                                    <div>
                                        <h5 class="card-title mb-0">Detalles del rol</h5>
                                        <p class="text-muted mb-0">Define la información base y el estado del rol.</p>
                                    </div>
                                    <a href="roles-permisos.php" class="btn btn-outline-secondary">Configurar permisos</a>
                                </div>
                            </div>
                            <div class="erp-section-body">
                                <?php if ($errorMessage !== '') : ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($errors)) : ?>
                                    <div class="alert alert-danger">
                                        <?php foreach ($errors as $error) : ?>
                                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($success === '1') : ?>
                                    <div class="alert alert-success">Rol guardado correctamente.</div>
                                <?php endif; ?>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="erp-form-grid">
                                        <div class="erp-field">
                                            <label class="form-label" for="rol-nombre">Nombre del rol</label>
                                            <input type="text" id="rol-nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($rol['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="erp-field">
                                            <label class="form-label" for="rol-estado">Estado</label>
                                            <select id="rol-estado" name="estado" class="form-select">
                                                <option value="1" <?php echo !$rol || (int) ($rol['estado'] ?? 1) === 1 ? 'selected' : ''; ?>>Activo</option>
                                                <option value="0" <?php echo $rol && (int) $rol['estado'] === 0 ? 'selected' : ''; ?>>Inactivo</option>
                                            </select>
                                        </div>
                                        <div class="erp-field erp-field--full">
                                            <label class="form-label" for="rol-descripcion">Descripción</label>
                                            <textarea id="rol-descripcion" name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($rol['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary">Guardar rol</button>
                                        <a href="roles-lista.php" class="btn btn-outline-secondary">Volver al listado</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="erp-section">
                            <div class="erp-section-header">
                                <div class="erp-toolbar">
                                    <div>
                                        <h5 class="card-title mb-0">Listado de roles</h5>
                                        <p class="text-muted mb-0">Revisa los roles existentes y accede a acciones rápidas.</p>
                                    </div>
                                    <span class="erp-status-pill"><?php echo count($roles); ?> roles</span>
                                </div>
                            </div>
                            <div class="erp-section-body">
                                <div class="table-responsive">
                                    <table class="table erp-table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Rol</th>
                                                <th>Descripción</th>
                                                <th>Estado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($roles)) : ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No hay roles registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($roles as $rolItem) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($rolItem['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($rolItem['descripcion'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ((int) $rolItem['estado'] === 1) : ?>
                                                                <span class="badge text-bg-success">Activo</span>
                                                            <?php else : ?>
                                                                <span class="badge text-bg-secondary">Inactivo</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Acciones
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="roles-editar.php?id=<?php echo (int) $rolItem['id']; ?>">Ver</a></li>
                                                                    <li><a class="dropdown-item" href="roles-editar.php?id=<?php echo (int) $rolItem['id']; ?>">Editar</a></li>
                                                                    <li><a class="dropdown-item" href="roles-permisos.php?rol_id=<?php echo (int) $rolItem['id']; ?>">Permisos</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-record-id="<?php echo (int) $rolItem['id']; ?>" data-record-label="<?php echo htmlspecialchars($rolItem['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                            Eliminar
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
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
            <!-- container -->

            <div class="modal fade erp-modal" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmar eliminación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">¿Deseas eliminar <strong data-delete-label>este rol</strong>? Esta acción no se puede deshacer.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" id="deleteRecordId" value="">
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
