<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$moduleKey = $moduleKey ?? pathinfo(basename($_SERVER['SCRIPT_NAME'] ?? ''), PATHINFO_FILENAME);
$pageTitle = $pageTitle ?? 'Módulo';
$pageSubtitle = $pageSubtitle ?? 'Módulo';
$pageDescription = $pageDescription ?? 'Vista informativa del módulo.';
$hideModuleCrud = $hideModuleCrud ?? false;
$moduleMode = $moduleMode ?? 'crud';
$showTableFilters = $showTableFilters ?? true;
$showTablePagination = $showTablePagination ?? true;
$formAppendHtml = $formAppendHtml ?? '';
$pageSummaryCards = $pageSummaryCards ?? [];
$pageActivityRows = $pageActivityRows ?? [];
$pageActivityTitle = $pageActivityTitle ?? 'Últimos movimientos';
$hideModuleIntro = $hideModuleIntro ?? false;
$hideModuleTable = $hideModuleTable ?? false;

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

            <div class="container-fluid erp-page">

                <?php $subtitle = $pageSubtitle; $title = $pageTitle; include('partials/page-title.php'); ?>

                <?php if (!empty($pageSummaryCards)) : ?>
                    <div class="row g-3 mb-4">
                        <?php foreach ($pageSummaryCards as $card) : ?>
                            <?php
                                $cardTitle = $card['title'] ?? '';
                                $cardValue = $card['value'] ?? '';
                                $cardMeta = $card['meta'] ?? '';
                                $cardIcon = $card['icon'] ?? 'ti ti-coin';
                            ?>
                            <div class="col-sm-6 col-xl-3">
                                <div class="erp-summary-card h-100">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="text-muted small"><?php echo htmlspecialchars($cardTitle, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <h4 class="mb-1"><?php echo htmlspecialchars($cardValue, ENT_QUOTES, 'UTF-8'); ?></h4>
                                            <?php if ($cardMeta !== '') : ?>
                                                <div class="trend"><?php echo htmlspecialchars($cardMeta, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="icon-circle">
                                            <i class="<?php echo htmlspecialchars($cardIcon, ENT_QUOTES, 'UTF-8'); ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!$hideModuleIntro) : ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="erp-section">
                                <div class="erp-section-header">
                                    <h5 class="card-title mb-1"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="erp-section-body">
                                    <p class="mb-0">Gestiona registros con formularios estructurados, filtros y acciones rápidas.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pageActivityRows)) : ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="erp-section">
                                <div class="erp-section-header">
                                    <div class="erp-toolbar">
                                        <div>
                                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($pageActivityTitle, ENT_QUOTES, 'UTF-8'); ?></h5>
                                            <p class="text-muted mb-0">Resumen de movimientos recientes registrados en el módulo.</p>
                                        </div>
                                        <span class="erp-status-pill"><?php echo count($pageActivityRows); ?> registros</span>
                                    </div>
                                </div>
                                <div class="erp-section-body">
                                    <div class="table-responsive">
                                        <table class="table erp-table table-striped table-centered align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Referencia</th>
                                                    <th>Descripción</th>
                                                    <th class="text-end">Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pageActivityRows as $row) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['fecha'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($row['referencia'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($row['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end"><?php echo htmlspecialchars($row['monto'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!$hideModuleCrud) : ?>
                <div class="row">
                    <div class="col-12">
                        <div class="erp-section">
                            <div class="erp-section-header">
                                <div class="erp-toolbar">
                                    <div>
                                        <h5 class="card-title mb-0">
                                            <?php echo $moduleMode === 'report' ? 'Filtros del reporte' : ($editingRecord ? 'Editar registro' : 'Nuevo registro'); ?>
                                        </h5>
                                        <p class="text-muted mb-0">
                                            <?php echo $moduleMode === 'report' ? 'Configura los filtros y genera el resultado.' : 'Completa los datos solicitados en el formulario.'; ?>
                                        </p>
                                    </div>
                                    <button type="submit" form="module-form" class="btn btn-primary">
                                        <?php
                                            if ($moduleMode === 'report') {
                                                echo 'Aplicar filtros';
                                            } else {
                                                echo $editingRecord ? 'Guardar cambios' : 'Crear registro';
                                            }
                                        ?>
                                    </button>
                                </div>
                            </div>
                            <div class="erp-section-body">
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
                                    <div class="erp-form-grid">
                                        <?php $groupOpen = false; ?>
                                        <?php foreach ($moduleFields as $field) : ?>
                                            <?php
                                                $fieldName = $field['name'] ?? '';
                                                if ($fieldName === '') {
                                                    $fieldType = $field['type'] ?? '';
                                                    if ($fieldType === 'group_start') {
                                                        $groupTitle = $field['label'] ?? '';
                                                        $groupDescription = $field['description'] ?? '';
                                                        if ($groupOpen) {
                                                            echo '</div></div></div></div>';
                                                        }
                                                        $groupOpen = true;
                                                        ?>
                                                        <div class="erp-field erp-field--full">
                                                            <div class="erp-form-card">
                                                                <div class="erp-form-card__header">
                                                                    <div>
                                                                        <div class="fw-semibold"><?php echo htmlspecialchars($groupTitle, ENT_QUOTES, 'UTF-8'); ?></div>
                                                                        <?php if ($groupDescription !== '') : ?>
                                                                            <div class="text-muted small"><?php echo htmlspecialchars($groupDescription, ENT_QUOTES, 'UTF-8'); ?></div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                                <div class="erp-form-card__body">
                                                                    <div class="erp-form-grid erp-form-grid--nested">
                                                        <?php
                                                        continue;
                                                    }
                                                    if ($fieldType === 'group_end') {
                                                        if ($groupOpen) {
                                                            echo '</div></div></div></div>';
                                                            $groupOpen = false;
                                                        }
                                                        continue;
                                                    }
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
                                                $fieldColClass = $field['col'] ?? '';
                                                if ($fieldColClass === '') {
                                                    $fieldColClass = $fieldType === 'textarea' ? 'erp-field erp-field--full' : 'erp-field';
                                                }
                                            ?>
                                            <?php if ($fieldType === 'section') : ?>
                                                <div class="erp-field erp-field--full">
                                                    <div class="border rounded-3 bg-light-subtle px-3 py-2">
                                                        <div class="fw-semibold"><?php echo htmlspecialchars($sectionTitle, ENT_QUOTES, 'UTF-8'); ?></div>
                                                        <?php if ($sectionDescription !== '') : ?>
                                                            <div class="text-muted small"><?php echo htmlspecialchars($sectionDescription, ENT_QUOTES, 'UTF-8'); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php continue; ?>
                                            <?php endif; ?>
                                            <div class="<?php echo htmlspecialchars($fieldColClass, ENT_QUOTES, 'UTF-8'); ?>">
                                                <div class="mb-0">
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
                                            </div>
                                        <?php endforeach; ?>
                                        <?php if ($groupOpen) : ?>
                                            </div></div></div></div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($formAppendHtml !== '') : ?>
                                        <div class="mt-3">
                                            <?php echo $formAppendHtml; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                                        <?php if ($editingRecord && $moduleMode !== 'report') : ?>
                                            <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">Cancelar edición</a>
                                        <?php endif; ?>
                                        <?php if ($moduleMode === 'report') : ?>
                                            <span class="text-muted small">Los filtros se guardan como referencia para futuras consultas.</span>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php if (!$hideModuleTable) : ?>
                        <div class="col-12">
                            <div class="erp-section">
                                <div class="erp-section-header">
                                    <div class="erp-toolbar">
                                        <div>
                                            <h5 class="card-title mb-0">
                                                <?php echo $moduleMode === 'report' ? 'Resultados del reporte' : 'Registros disponibles'; ?>
                                            </h5>
                                            <p class="text-muted mb-0">
                                                <?php echo $moduleMode === 'report' ? 'Vista consolidada según los filtros aplicados.' : 'Listado de elementos registrados en el módulo.'; ?>
                                            </p>
                                        </div>
                                        <span class="erp-status-pill"><?php echo count($records); ?> elemento(s)</span>
                                    </div>
                                </div>
                                <div class="erp-section-body">
                                    <?php if ($showTableFilters) : ?>
                                        <div class="erp-filters mb-3">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                                <input type="text" class="form-control" placeholder="Buscar por nombre, código o referencia">
                                            </div>
                                            <select class="form-select">
                                                <option value="">Estado</option>
                                                <option>Activo</option>
                                                <option>Inactivo</option>
                                            </select>
                                            <select class="form-select">
                                                <option value="">Categoría</option>
                                                <option>Operativo</option>
                                                <option>Administrativo</option>
                                                <option>Contable</option>
                                            </select>
                                            <button class="btn btn-outline-secondary" type="button">Limpiar</button>
                                        </div>
                                    <?php endif; ?>
                                    <div class="table-responsive">
                                        <table class="table erp-table table-striped table-centered align-middle mb-0">
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
                                                        <td colspan="<?php echo count($moduleListColumns) + 1; ?>">
                                                            <div class="erp-empty">No hay registros para mostrar todavía.</div>
                                                        </td>
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
                                                            <div class="d-inline-flex gap-2 erp-table-actions">
                                                                <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars(basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>?edit=<?php echo (int) $record['id']; ?>">Editar</a>
                                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-record-id="<?php echo (int) $record['id']; ?>" data-record-label="<?php echo htmlspecialchars((string) ($record[$moduleTitleField] ?? $record['nombre'] ?? 'registro'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                    Eliminar
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php if ($showTablePagination) : ?>
                                        <div class="erp-pagination">
                                            <div class="text-muted small">Mostrando <?php echo count($records); ?> de <?php echo count($records); ?> registros</div>
                                            <nav>
                                                <ul class="pagination pagination-sm mb-0">
                                                    <li class="page-item disabled"><span class="page-link">Anterior</span></li>
                                                    <li class="page-item active"><span class="page-link">1</span></li>
                                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                    <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
                                                </ul>
                                            </nav>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

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
                            <p class="mb-0">¿Deseas eliminar <strong data-delete-label>este registro</strong>? Esta acción no se puede deshacer.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form method="post" action="<?php echo htmlspecialchars(basename((string) ($_SERVER['SCRIPT_NAME'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
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
    <?php if (!empty($pageInlineScript)) : ?>
        <?php echo $pageInlineScript; ?>
    <?php endif; ?>

</body>

</html>
