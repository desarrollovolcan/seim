<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

$municipalidad = get_municipalidad();
$errors = [];
$successMessage = '';
$editingId = null;
$viewRecord = null;

$fields = [
    'categoria_id' => '',
    'nombre' => '',
    'descripcion' => '',
];

$familias = [];
try {
    $familias = db()->query('SELECT id, nombre FROM inventario_categorias ORDER BY nombre')->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar la lista de familias.';
}

if (isset($_GET['view'])) {
    $viewId = (int) $_GET['view'];
    if ($viewId > 0) {
        $stmt = db()->prepare(
            'SELECT s.*, c.nombre AS familia_nombre
             FROM inventario_subfamilias s
             LEFT JOIN inventario_categorias c ON c.id = s.categoria_id
             WHERE s.id = ? LIMIT 1'
        );
        $stmt->execute([$viewId]);
        $viewRecord = $stmt->fetch() ?: null;
    }
}

if (isset($_GET['edit'])) {
    $editingId = (int) $_GET['edit'];
    if ($editingId > 0) {
        $stmt = db()->prepare('SELECT * FROM inventario_subfamilias WHERE id = ? LIMIT 1');
        $stmt->execute([$editingId]);
        $record = $stmt->fetch();
        if ($record) {
            $fields['categoria_id'] = (string) ($record['categoria_id'] ?? '');
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
                $stmt = db()->prepare('DELETE FROM inventario_subfamilias WHERE id = ?');
                $stmt->execute([$recordId]);
                $_SESSION['subfamilia_flash'] = 'Subfamilia eliminada correctamente.';
                redirect('inventario-subfamilias.php');
            } catch (Exception $e) {
                $errors[] = 'No se pudo eliminar la subfamilia.';
            }
        } else {
            foreach ($fields as $key => $value) {
                $fields[$key] = trim((string) ($_POST[$key] ?? ''));
            }

            if ($fields['nombre'] === '') {
                $errors[] = 'El nombre es obligatorio.';
            }
            if ($fields['categoria_id'] === '') {
                $errors[] = 'La familia es obligatoria.';
            }

            if (!$errors) {
                try {
                    if ($action === 'update' && $recordId > 0) {
                        $stmt = db()->prepare(
                            'UPDATE inventario_subfamilias SET categoria_id = ?, nombre = ?, descripcion = ? WHERE id = ?'
                        );
                        $stmt->execute([
                            (int) $fields['categoria_id'],
                            $fields['nombre'],
                            $fields['descripcion'] !== '' ? $fields['descripcion'] : null,
                            $recordId,
                        ]);
                        $_SESSION['subfamilia_flash'] = 'Subfamilia actualizada correctamente.';
                    } else {
                        $stmt = db()->prepare(
                            'INSERT INTO inventario_subfamilias (categoria_id, nombre, descripcion) VALUES (?, ?, ?)'
                        );
                        $stmt->execute([
                            (int) $fields['categoria_id'],
                            $fields['nombre'],
                            $fields['descripcion'] !== '' ? $fields['descripcion'] : null,
                        ]);
                        $_SESSION['subfamilia_flash'] = 'Subfamilia creada correctamente.';
                    }
                    redirect('inventario-subfamilias.php');
                } catch (Exception $e) {
                    $errors[] = 'No se pudo guardar la subfamilia. Revisa que el nombre no esté duplicado.';
                }
            }
        }
    }
}

if (isset($_SESSION['subfamilia_flash'])) {
    $successMessage = (string) $_SESSION['subfamilia_flash'];
    unset($_SESSION['subfamilia_flash']);
}

$subfamilias = [];
try {
    $subfamilias = db()->query(
        'SELECT s.*, c.nombre AS familia_nombre
         FROM inventario_subfamilias s
         LEFT JOIN inventario_categorias c ON c.id = s.categoria_id
         ORDER BY s.created_at DESC'
    )->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el listado de subfamilias.';
}

include('partials/html.php');
?>

<head>
    <?php $title = 'Subfamilias'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Productos'; $title = 'Subfamilias'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Subfamilias de productos</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Show Code</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showCodeSwitchSubfamilias">
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
                                        <h6 class="fw-semibold mb-2">Detalle de subfamilia</h6>
                                        <div class="row g-2">
                                            <div class="col-md-4"><span class="text-muted">Familia:</span> <?php echo htmlspecialchars($viewRecord['familia_nombre'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Nombre:</span> <?php echo htmlspecialchars($viewRecord['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="col-md-4"><span class="text-muted">Descripción:</span> <?php echo htmlspecialchars($viewRecord['descripcion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="inventario-subfamilias.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="<?php echo $editingId ? 'update' : 'create'; ?>">
                                    <?php if ($editingId) : ?>
                                        <input type="hidden" name="id" value="<?php echo (int) $editingId; ?>">
                                    <?php endif; ?>

                                    <div class="row g-3">
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">Familia</label>
                                            <select name="categoria_id" class="form-select" required>
                                                <option value="">Selecciona</option>
                                                <?php foreach ($familias as $familia) : ?>
                                                    <option value="<?php echo (int) $familia['id']; ?>" <?php echo $fields['categoria_id'] === (string) $familia['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($familia['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($fields['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 col-xl-4">
                                            <label class="form-label">Descripción</label>
                                            <input type="text" name="descripcion" class="form-control" value="<?php echo htmlspecialchars($fields['descripcion'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Descripción opcional">
                                        </div>

                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <?php echo $editingId ? 'Actualizar subfamilia' : 'Guardar subfamilia'; ?>
                                            </button>
                                            <?php if ($editingId) : ?>
                                                <a href="inventario-subfamilias.php" class="btn btn-outline-secondary">Cancelar edición</a>
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
                                <h5 class="card-title mb-0">Subfamilias registradas</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Familia</th>
                                                <th>Subfamilia</th>
                                                <th>Descripción</th>
                                                <th>Creado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$subfamilias) : ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Sin registros aún.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($subfamilias as $subfamilia) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($subfamilia['familia_nombre'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($subfamilia['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($subfamilia['descripcion'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($subfamilia['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <a class="btn btn-sm btn-outline-primary" href="inventario-subfamilias.php?view=<?php echo (int) $subfamilia['id']; ?>">Ver</a>
                                                            <a class="btn btn-sm btn-outline-secondary" href="inventario-subfamilias.php?edit=<?php echo (int) $subfamilia['id']; ?>">Editar</a>
                                                            <form method="post" action="inventario-subfamilias.php" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?php echo (int) $subfamilia['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta subfamilia?');">Eliminar</button>
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
