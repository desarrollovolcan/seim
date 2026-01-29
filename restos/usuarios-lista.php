<?php
require __DIR__ . '/app/bootstrap.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($_POST['action'] === 'delete' && $id > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$id]);
            redirect('usuarios-lista.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el usuario. Verifica dependencias asociadas.';
        }
    }
}

$stmt = db()->query('SELECT id, rut, nombre, apellido, correo, rol, estado, ultimo_acceso FROM users ORDER BY id DESC');
$usuarios = $stmt->fetchAll();
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Listar usuarios"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Usuarios"; $title = "Listar usuarios"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="erp-section">
                            <div class="erp-section-header">
                                <div class="erp-toolbar">
                                    <div>
                                        <h5 class="card-title mb-0">Usuarios</h5>
                                        <p class="text-muted mb-0">Gestión y administración de usuarios.</p>
                                    </div>
                                    <a href="usuarios-crear.php" class="btn btn-primary">Nuevo usuario</a>
                                </div>
                            </div>
                            <div class="erp-section-body">
                                <?php if ($errorMessage !== '') : ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <div class="erp-filters mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                                        <input type="text" class="form-control" placeholder="Buscar por nombre o RUT">
                                    </div>
                                    <select class="form-select">
                                        <option value="">Estado</option>
                                        <option>Habilitado</option>
                                        <option>Deshabilitado</option>
                                    </select>
                                    <select class="form-select">
                                        <option value="">Rol</option>
                                        <option>SuperAdmin</option>
                                        <option>Admin</option>
                                        <option>Consulta</option>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button">Limpiar</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table erp-table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>RUT</th>
                                                <th>Nombre</th>
                                                <th>Correo</th>
                                                <th>Rol</th>
                                                <th>Estado</th>
                                                <th>Último acceso</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($usuarios)) : ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No hay usuarios registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($usuarios as $usuario) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($usuario['rut'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars(trim($usuario['nombre'] . ' ' . $usuario['apellido']), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($usuario['rol'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ((int) $usuario['estado'] === 1) : ?>
                                                                <span class="badge text-bg-success">Habilitado</span>
                                                            <?php else : ?>
                                                                <span class="badge text-bg-secondary">Deshabilitado</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $usuario['ultimo_acceso'] ? htmlspecialchars($usuario['ultimo_acceso'], ENT_QUOTES, 'UTF-8') : '-'; ?></td>
                                                        <td class="text-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Acciones
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="usuarios-detalle.php?id=<?php echo (int) $usuario['id']; ?>">Ver detalle</a></li>
                                                                    <li><a class="dropdown-item" href="usuarios-editar.php?id=<?php echo (int) $usuario['id']; ?>">Editar</a></li>
                                                                    <li><a class="dropdown-item" href="usuarios-asignar-roles.php?id=<?php echo (int) $usuario['id']; ?>">Asignar roles</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-record-id="<?php echo (int) $usuario['id']; ?>" data-record-label="<?php echo htmlspecialchars(trim($usuario['nombre'] . ' ' . $usuario['apellido']), ENT_QUOTES, 'UTF-8'); ?>">
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
                                    <div class="text-muted small">Mostrando <?php echo count($usuarios); ?> usuarios</div>
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
                            <p class="mb-0">¿Deseas eliminar a <strong data-delete-label>este usuario</strong>? Esta acción no se puede deshacer.</p>
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
