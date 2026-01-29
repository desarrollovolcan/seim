<?php
require __DIR__ . '/app/bootstrap.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($_POST['action'] === 'delete' && $id > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM roles WHERE id = ?');
            $stmt->execute([$id]);
            redirect('roles-lista.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el rol. Verifica dependencias asociadas.';
        }
    }
}

$roles = db()->query('SELECT id, nombre, descripcion, estado FROM roles ORDER BY nombre')->fetchAll();
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Listar roles"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Roles y Permisos"; $title = "Listar roles"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="erp-section">
                            <div class="erp-section-header">
                                <div class="erp-toolbar">
                                    <div>
                                        <h5 class="card-title mb-0">Roles</h5>
                                        <p class="text-muted mb-0">Configuración de roles y permisos.</p>
                                    </div>
                                    <a href="roles-editar.php" class="btn btn-primary">Nuevo rol</a>
                                </div>
                            </div>
                            <div class="erp-section-body">
                                <?php if ($errorMessage !== '') : ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <div class="erp-filters mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                                        <input type="text" class="form-control" placeholder="Buscar rol">
                                    </div>
                                    <select class="form-select">
                                        <option value="">Estado</option>
                                        <option>Activo</option>
                                        <option>Inactivo</option>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button">Limpiar</button>
                                </div>
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
                                                <?php foreach ($roles as $rol) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($rol['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($rol['descripcion'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ((int) $rol['estado'] === 1) : ?>
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
                                                                    <li><a class="dropdown-item" href="roles-editar.php?id=<?php echo (int) $rol['id']; ?>">Ver</a></li>
                                                                    <li><a class="dropdown-item" href="roles-editar.php?id=<?php echo (int) $rol['id']; ?>">Editar</a></li>
                                                                    <li><a class="dropdown-item" href="roles-permisos.php?rol_id=<?php echo (int) $rol['id']; ?>">Permisos</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-record-id="<?php echo (int) $rol['id']; ?>" data-record-label="<?php echo htmlspecialchars($rol['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
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
                                <div class="erp-pagination">
                                    <div class="text-muted small">Mostrando <?php echo count($roles); ?> roles</div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0">
                                            <li class="page-item disabled"><span class="page-link">Anterior</span></li>
                                            <li class="page-item active"><span class="page-link">1</span></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
                                        </ul>
                                    </nav>
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
