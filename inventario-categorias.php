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
    'descripcion' => '',
];

if (isset($_GET['view'])) {
    $viewId = (int) $_GET['view'];
    if ($viewId > 0) {
        $stmt = db()->prepare('SELECT * FROM inventario_categorias WHERE id = ? LIMIT 1');
        $stmt->execute([$viewId]);
        $viewRecord = $stmt->fetch() ?: null;
    }
}

if (isset($_GET['edit'])) {
    $editingId = (int) $_GET['edit'];
    if ($editingId > 0) {
        $stmt = db()->prepare('SELECT * FROM inventario_categorias WHERE id = ? LIMIT 1');
        $stmt->execute([$editingId]);
        $record = $stmt->fetch();
        if ($record) {
            $fields['nombre'] = (string) ($record['nombre'] ?? '');
            $fields['descripcion'] = (string) ($record['descripcion'] ?? '');
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
                $stmt = db()->prepare('DELETE FROM inventario_categorias WHERE id = ?');
                $stmt->execute([$recordId]);
                $_SESSION['categoria_flash'] = 'Categoría eliminada correctamente.';
                redirect('inventario-categorias.php');
            } catch (Exception $e) {
                $errors[] = 'No se pudo eliminar la categoría.';
            }
        } else {
            foreach ($fields as $key => $value) {
                $fields[$key] = trim((string) ($_POST[$key] ?? ''));
            }

            if ($fields['nombre'] === '') {
                $errors[] = 'El nombre es obligatorio.';
            }

            if (!$errors) {
                try {
                    if ($action === 'update' && $recordId > 0) {
                        $stmt = db()->prepare('UPDATE inventario_categorias SET nombre = ?, descripcion = ? WHERE id = ?');
                        $stmt->execute([
                            $fields['nombre'],
                            $fields['descripcion'] !== '' ? $fields['descripcion'] : null,
                            $recordId,
                        ]);
                        $_SESSION['categoria_flash'] = 'Categoría actualizada correctamente.';
                    } else {
                        $stmt = db()->prepare('INSERT INTO inventario_categorias (nombre, descripcion) VALUES (?, ?)');
                        $stmt->execute([
                            $fields['nombre'],
                            $fields['descripcion'] !== '' ? $fields['descripcion'] : null,
                        ]);
                        $_SESSION['categoria_flash'] = 'Categoría creada correctamente.';
                    }
                    redirect('inventario-categorias.php');
                } catch (Exception $e) {
                    $errors[] = 'No se pudo guardar la categoría. Revisa que el nombre no esté duplicado.';
                }
            }
        }
    }
}

if (isset($_SESSION['categoria_flash'])) {
    $successMessage = (string) $_SESSION['categoria_flash'];
    unset($_SESSION['categoria_flash']);
}

$categorias = [];
try {
    $categorias = db()->query('SELECT * FROM inventario_categorias ORDER BY created_at DESC')->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el listado de categorías.';
}

include('partials/html.php');
?>

<head>
    <?php $title = 'Categorías'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Inventario'; $title = 'Categorías'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Categorías de productos</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Show Code</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showCodeSwitchCategorias">
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
                                        <h6 class="fw-semibold mb-2">Detalle de categoría</h6>
                                        <div class="row g-2">
                                            <div class="col-md-6"><span class="text-muted">Nombre:</span> <?php echo htmlspecialchars($viewRecord['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-6"><span class="text-muted">Descripción:</span> <?php echo htmlspecialchars($viewRecord['descripcion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="inventario-categorias.php">
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
                                        <div class="col-md-6 col-xl-8">
                                            <label class="form-label">Descripción</label>
                                            <input type="text" name="descripcion" class="form-control" value="<?php echo htmlspecialchars($fields['descripcion'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Descripción opcional">
                                        </div>

                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <?php echo $editingId ? 'Actualizar categoría' : 'Guardar categoría'; ?>
                                            </button>
                                            <?php if ($editingId) : ?>
                                                <a href="inventario-categorias.php" class="btn btn-outline-secondary">Cancelar edición</a>
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
                                <h5 class="card-title mb-0">Categorías registradas</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th>Creado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$categorias) : ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Sin registros aún.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($categorias as $categoria) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($categoria['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($categoria['descripcion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($categoria['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <a class="btn btn-sm btn-outline-primary" href="inventario-categorias.php?view=<?php echo (int) $categoria['id']; ?>">Ver</a>
                                                            <a class="btn btn-sm btn-outline-secondary" href="inventario-categorias.php?edit=<?php echo (int) $categoria['id']; ?>">Editar</a>
                                                            <form method="post" action="inventario-categorias.php" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?php echo (int) $categoria['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta categoría?');">Eliminar</button>
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

        <?php include('partials/footer.php'); ?>
    </div>

    <?php include('partials/footer-scripts.php'); ?>
</body>
</html>
