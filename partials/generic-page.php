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
$moduleFields = $moduleFields ?? [
    [
        'name' => 'nombre',
        'label' => 'Nombre',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Ingresa un nombre',
    ],
    [
        'name' => 'descripcion',
        'label' => 'Descripción',
        'type' => 'textarea',
        'required' => false,
        'placeholder' => 'Agrega detalles relevantes',
        'rows' => 4,
    ],
];
$moduleTitleField = $moduleTitleField ?? 'nombre';
$moduleListColumns = $moduleListColumns ?? [
    ['key' => 'nombre', 'label' => 'Nombre'],
    ['key' => 'descripcion', 'label' => 'Descripción'],
    ['key' => 'created_at', 'label' => 'Creado'],
];
$fieldMap = [];
foreach ($moduleFields as $field) {
    if (!isset($field['name'])) {
        continue;
    }
    $fieldMap[$field['name']] = $field;
}

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS module_records (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            module_key VARCHAR(120) NOT NULL,
            nombre VARCHAR(150) NOT NULL,
            descripcion TEXT DEFAULT NULL,
            data JSON DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY module_records_module_idx (module_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
    try {
        $columns = db()->query('SHOW COLUMNS FROM module_records LIKE "data"')->fetchAll();
        if (!$columns) {
            db()->exec('ALTER TABLE module_records ADD COLUMN data JSON DEFAULT NULL AFTER descripcion');
        }
    } catch (Exception $e) {
    } catch (Error $e) {
    }
} catch (Exception $e) {
    $errors[] = 'No se pudo preparar el almacenamiento del módulo. Intenta nuevamente.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Tu sesión expiró. Vuelve a intentar.';
    } else {
        $action = (string) $_POST['action'];
        $recordId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $payload = [];
        foreach ($moduleFields as $field) {
            $fieldName = $field['name'] ?? null;
            if (!$fieldName) {
                continue;
            }
            $value = $_POST[$fieldName] ?? '';
            if (is_string($value)) {
                $value = trim($value);
            }
            $isRequired = (bool) ($field['required'] ?? false);
            if (in_array($action, ['create', 'update'], true) && $isRequired && $value === '') {
                $errors[] = sprintf('El campo %s es obligatorio.', $field['label'] ?? $fieldName);
            }
            $payload[$fieldName] = $value === '' ? null : $value;
        }

        $nombre = (string) ($payload['nombre'] ?? '');
        if ($nombre === '' && $moduleTitleField !== 'nombre' && isset($payload[$moduleTitleField])) {
            $nombre = (string) $payload[$moduleTitleField];
        }
        $descripcion = isset($payload['descripcion']) ? (string) $payload['descripcion'] : '';
        unset($payload['nombre'], $payload['descripcion']);
        $dataJson = $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE) : null;

        if (in_array($action, ['create', 'update'], true) && $nombre === '') {
            $errors[] = 'El nombre es obligatorio.';
        }

        if (!$errors) {
            try {
                if ($action === 'create') {
                    $stmt = db()->prepare('INSERT INTO module_records (module_key, nombre, descripcion, data) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$moduleKey, $nombre, $descripcion !== '' ? $descripcion : null, $dataJson]);
                    $successMessage = 'Registro creado correctamente.';
                }

                if ($action === 'update' && $recordId > 0) {
                    $stmt = db()->prepare('UPDATE module_records SET nombre = ?, descripcion = ?, data = ? WHERE id = ? AND module_key = ?');
                    $stmt->execute([$nombre, $descripcion !== '' ? $descripcion : null, $dataJson, $recordId, $moduleKey]);
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
        if ($editingRecord && !empty($editingRecord['data'])) {
            $decoded = json_decode((string) $editingRecord['data'], true);
            if (is_array($decoded)) {
                $editingRecord = array_merge($editingRecord, $decoded);
            }
        }
    }
}

$records = [];
try {
    $stmt = db()->prepare('SELECT * FROM module_records WHERE module_key = ? ORDER BY created_at DESC');
    $stmt->execute([$moduleKey]);
        $records = $stmt->fetchAll();
        foreach ($records as $index => $record) {
            if (!empty($record['data'])) {
                $decoded = json_decode((string) $record['data'], true);
                if (is_array($decoded)) {
                    $records[$index] = array_merge($record, $decoded);
                }
            }
        }
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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div>
                                        <h5 class="card-title mb-0">
                                            <?php echo $editingRecord ? 'Editar registro' : 'Nuevo registro'; ?>
                                        </h5>
                                        <p class="text-muted mb-0">Completa los datos solicitados en el formulario.</p>
                                    </div>
                                    <button type="submit" form="module-form" class="btn btn-primary">
                                        <?php echo $editingRecord ? 'Guardar cambios' : 'Crear registro'; ?>
                                    </button>
                                </div>
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
                                <form id="module-form" method="post" action="<?php echo htmlspecialchars(basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="<?php echo $editingRecord ? 'update' : 'create'; ?>">
                                    <?php if ($editingRecord) : ?>
                                        <input type="hidden" name="id" value="<?php echo (int) ($editingRecord['id'] ?? 0); ?>">
                                    <?php endif; ?>
                                    <?php foreach ($moduleFields as $field) : ?>
                                        <?php
                                            $fieldName = $field['name'] ?? '';
                                            if ($fieldName === '') {
                                                $fieldType = $field['type'] ?? '';
                                                if ($fieldType === 'section') {
                                                    $sectionTitle = $field['label'] ?? '';
                                                    $sectionDescription = $field['description'] ?? '';
                                                } else {
                                                    continue;
                                                }
                                            } else {
                                                $fieldType = $field['type'] ?? 'text';
                                                $sectionTitle = '';
                                                $sectionDescription = '';
                                            }
                                            $fieldId = 'module-' . $fieldName;
                                            $fieldLabel = $field['label'] ?? $fieldName;
                                            $fieldValue = $editingRecord[$fieldName] ?? ($field['default'] ?? '');
                                            $fieldRequired = !empty($field['required']);
                                            $fieldPlaceholder = $field['placeholder'] ?? '';
                                            $fieldHelp = $field['help'] ?? '';
                                            $fieldReadonly = !empty($field['readonly']);
                                            $fieldStep = $field['step'] ?? null;
                                            $fieldOptions = $field['options'] ?? [];
                                            $fieldRows = $field['rows'] ?? 4;
                                        ?>
                                        <?php if ($fieldType === 'section') : ?>
                                            <div class="border rounded-3 bg-light-subtle px-3 py-2 mb-3">
                                                <div class="fw-semibold"><?php echo htmlspecialchars($sectionTitle, ENT_QUOTES, 'UTF-8'); ?></div>
                                                <?php if ($sectionDescription !== '') : ?>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($sectionDescription, ENT_QUOTES, 'UTF-8'); ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <?php continue; ?>
                                        <?php endif; ?>
                                        <div class="mb-3">
                                            <label class="form-label" for="<?php echo htmlspecialchars($fieldId, ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php echo htmlspecialchars($fieldLabel, ENT_QUOTES, 'UTF-8'); ?>
                                            </label>
                                            <?php if ($fieldType === 'textarea') : ?>
                                                <textarea class="form-control" id="<?php echo htmlspecialchars($fieldId, ENT_QUOTES, 'UTF-8'); ?>" name="<?php echo htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8'); ?>" rows="<?php echo (int) $fieldRows; ?>" <?php echo $fieldRequired ? 'required' : ''; ?> <?php echo $fieldReadonly ? 'readonly' : ''; ?> placeholder="<?php echo htmlspecialchars($fieldPlaceholder, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) $fieldValue, ENT_QUOTES, 'UTF-8'); ?></textarea>
                                            <?php elseif ($fieldType === 'select') : ?>
                                                <select class="form-select" id="<?php echo htmlspecialchars($fieldId, ENT_QUOTES, 'UTF-8'); ?>" name="<?php echo htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $fieldRequired ? 'required' : ''; ?> <?php echo $fieldReadonly ? 'disabled' : ''; ?>>
                                                    <option value="">Selecciona</option>
                                                    <?php foreach ($fieldOptions as $optionValue => $optionLabel) : ?>
                                                        <?php $selected = (string) $fieldValue === (string) $optionValue ? 'selected' : ''; ?>
                                                        <option value="<?php echo htmlspecialchars((string) $optionValue, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected; ?>>
                                                            <?php echo htmlspecialchars((string) $optionLabel, ENT_QUOTES, 'UTF-8'); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if ($fieldReadonly) : ?>
                                                    <input type="hidden" name="<?php echo htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars((string) $fieldValue, ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <input type="<?php echo htmlspecialchars($fieldType, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="<?php echo htmlspecialchars($fieldId, ENT_QUOTES, 'UTF-8'); ?>" name="<?php echo htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars((string) $fieldValue, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $fieldRequired ? 'required' : ''; ?> <?php echo $fieldReadonly ? 'readonly' : ''; ?> <?php echo $fieldStep ? 'step="' . htmlspecialchars((string) $fieldStep, ENT_QUOTES, 'UTF-8') . '"' : ''; ?> placeholder="<?php echo htmlspecialchars($fieldPlaceholder, ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php endif; ?>
                                            <?php if ($fieldHelp !== '') : ?>
                                                <div class="form-text"><?php echo htmlspecialchars($fieldHelp, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if ($editingRecord) : ?>
                                        <a class="btn btn-light" href="<?php echo htmlspecialchars(basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">Cancelar</a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
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
                                                <?php foreach ($moduleListColumns as $column) : ?>
                                                    <th><?php echo htmlspecialchars($column['label'] ?? (string) $column['key'], ENT_QUOTES, 'UTF-8'); ?></th>
                                                <?php endforeach; ?>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$records) : ?>
                                                <tr>
                                                    <td colspan="<?php echo count($moduleListColumns) + 1; ?>" class="text-center text-muted">Sin registros todavía.</td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php foreach ($records as $record) : ?>
                                                <tr>
                                                    <?php foreach ($moduleListColumns as $column) : ?>
                                                        <?php
                                                            $columnKey = $column['key'] ?? '';
                                                            $value = $columnKey !== '' ? ($record[$columnKey] ?? '') : '';
                                                            $fieldConfig = $fieldMap[$columnKey] ?? null;
                                                            if ($fieldConfig && ($fieldConfig['type'] ?? '') === 'select') {
                                                                $options = $fieldConfig['options'] ?? [];
                                                                if ($value !== '' && isset($options[$value])) {
                                                                    $value = $options[$value];
                                                                }
                                                            }
                                                        ?>
                                                        <td><?php echo htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <?php endforeach; ?>
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
