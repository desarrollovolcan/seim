<?php

declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

$moduleKey = $moduleKey ?? pathinfo(basename($_SERVER['SCRIPT_NAME'] ?? ''), PATHINFO_FILENAME);
$pageTitle = $pageTitle ?? 'Módulo';
$pageSubtitle = $pageSubtitle ?? 'Módulo';
$pageDescription = $pageDescription ?? 'Vista informativa del módulo.';

$errors = [];
$successMessage = '';
$editingRecord = null;

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS module_records (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            module_key VARCHAR(120) NOT NULL,
            nombre VARCHAR(150) NOT NULL,
            descripcion TEXT DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY module_records_module_idx (module_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
    $errors[] = 'No se pudo preparar el almacenamiento del módulo. Intenta nuevamente.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Tu sesión expiró. Vuelve a intentar.';
    } else {
        $action = (string) $_POST['action'];
        $recordId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if (in_array($action, ['create', 'update'], true) && $nombre === '') {
            $errors[] = 'El nombre es obligatorio.';
        }

        if (!$errors) {
            try {
                if ($action === 'create') {
                    $stmt = db()->prepare('INSERT INTO module_records (module_key, nombre, descripcion) VALUES (?, ?, ?)');
                    $stmt->execute([$moduleKey, $nombre, $descripcion !== '' ? $descripcion : null]);
                    $successMessage = 'Registro creado correctamente.';
                }

                if ($action === 'update' && $recordId > 0) {
                    $stmt = db()->prepare('UPDATE module_records SET nombre = ?, descripcion = ? WHERE id = ? AND module_key = ?');
                    $stmt->execute([$nombre, $descripcion !== '' ? $descripcion : null, $recordId, $moduleKey]);
                    $successMessage = 'Registro actualizado correctamente.';
                }

                if ($action === 'delete' && $recordId > 0) {
                    $stmt = db()->prepare('DELETE FROM module_records WHERE id = ? AND module_key = ?');
                    $stmt->execute([$recordId, $moduleKey]);
                    $successMessage = 'Registro eliminado correctamente.';
                }

                if ($successMessage !== '') {
                    $_SESSION['module_flash'] = $successMessage;
                    redirect(basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')));
                }
            } catch (Exception $e) {
                $errors[] = 'No se pudo guardar la información. Revisa la base de datos.';
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    if ($editId > 0) {
        $stmt = db()->prepare('SELECT * FROM module_records WHERE id = ? AND module_key = ? LIMIT 1');
        $stmt->execute([$editId, $moduleKey]);
        $editingRecord = $stmt->fetch();
    }
}

$records = [];
try {
    $stmt = db()->prepare('SELECT * FROM module_records WHERE module_key = ? ORDER BY created_at DESC');
    $stmt->execute([$moduleKey]);
    $records = $stmt->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar la información del módulo.';
}

if (isset($_SESSION['module_flash'])) {
    $successMessage = (string) $_SESSION['module_flash'];
    unset($_SESSION['module_flash']);
}

include('partials/html.php');
?>

<head>
    <?php $title = $pageTitle; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = $pageSubtitle; $title = $pageTitle; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h5>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">Gestiona registros de este módulo con operaciones de creación, edición y eliminación.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <?php echo $editingRecord ? 'Editar registro' : 'Nuevo registro'; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($successMessage !== '') : ?>
                                    <div class="alert alert-success"> <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?> </div>
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
                                <form method="post" action="<?php echo htmlspecialchars(basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="<?php echo $editingRecord ? 'update' : 'create'; ?>">
                                    <?php if ($editingRecord) : ?>
                                        <input type="hidden" name="id" value="<?php echo (int) ($editingRecord['id'] ?? 0); ?>">
                                    <?php endif; ?>
                                    <div class="mb-3">
                                        <label class="form-label" for="module-name">Nombre</label>
                                        <input type="text" class="form-control" id="module-name" name="nombre" value="<?php echo htmlspecialchars((string) ($editingRecord['nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="module-description">Descripción</label>
                                        <textarea class="form-control" id="module-description" name="descripcion" rows="4"><?php echo htmlspecialchars((string) ($editingRecord['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="submit" class="btn btn-primary"><?php echo $editingRecord ? 'Guardar cambios' : 'Crear registro'; ?></button>
                                        <?php if ($editingRecord) : ?>
                                            <a class="btn btn-light" href="<?php echo htmlspecialchars(basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">Cancelar</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Registros disponibles</h5>
                                <span class="text-muted small"><?php echo count($records); ?> elemento(s)</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th>Creado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$records) : ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Sin registros todavía.</td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php foreach ($records as $record) : ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars((string) $record['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars((string) ($record['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars((string) $record['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td class="text-end">
                                                        <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars(basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>?edit=<?php echo (int) $record['id']; ?>">Editar</a>
                                                        <form method="post" action="<?php echo htmlspecialchars(basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="id" value="<?php echo (int) $record['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este registro?');">Eliminar</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- container -->

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
