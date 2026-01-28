<?php

if (isset($crudTable)) {
    require_once __DIR__ . '/../app/bootstrap.php';

    $crudMessage = null;
    $crudError = null;
    $crudReadOnly = $crudReadOnly ?? false;

    $table = $crudTable;
    $fields = $crudFields ?? [];

    try {
        $columns = db()->query(sprintf('DESCRIBE `%s`', $table))->fetchAll();
        $columnNames = array_column($columns, 'Field');
        $hasIdColumn = in_array('id', $columnNames, true);

        if (!$fields) {
            foreach ($columns as $column) {
                $name = $column['Field'];
                if (in_array($name, ['id', 'created_at', 'updated_at'], true)) {
                    continue;
                }
                $fields[$name] = ucwords(str_replace('_', ' ', $name));
            }
        }

        if (!$hasIdColumn) {
            $crudReadOnly = true;
        }
    } catch (Exception $e) {
        $crudError = 'No se pudo cargar la estructura de la tabla.';
        $fields = $fields ?: [];
        $columnNames = [];
        $hasIdColumn = false;
    } catch (Error $e) {
        $crudError = 'No se pudo cargar la estructura de la tabla.';
        $fields = $fields ?: [];
        $columnNames = [];
        $hasIdColumn = false;
    }

    $crudEditId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
    $crudDeleteId = isset($_POST['delete_id']) ? (int) $_POST['delete_id'] : null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$crudReadOnly) {
        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            $crudError = 'Tu sesión expiró. Vuelve a intentar.';
        } elseif ($crudDeleteId) {
            try {
                $stmt = db()->prepare(sprintf('DELETE FROM `%s` WHERE id = :id', $table));
                $stmt->execute(['id' => $crudDeleteId]);
                $crudMessage = 'Registro eliminado correctamente.';
            } catch (Exception $e) {
                $crudError = 'No se pudo eliminar el registro.';
            } catch (Error $e) {
                $crudError = 'No se pudo eliminar el registro.';
            }
        } else {
            $payload = [];
            foreach ($fields as $name => $label) {
                $payload[$name] = $_POST[$name] ?? null;
            }

            try {
                if ($crudEditId) {
                    $setParts = [];
                    foreach ($payload as $name => $value) {
                        $setParts[] = sprintf('`%s` = :%s', $name, $name);
                    }
                    $payload['id'] = $crudEditId;
                    $sql = sprintf('UPDATE `%s` SET %s WHERE id = :id', $table, implode(', ', $setParts));
                    $stmt = db()->prepare($sql);
                    $stmt->execute($payload);
                    $crudMessage = 'Registro actualizado correctamente.';
                } else {
                    $columnsList = implode('`, `', array_keys($payload));
                    $paramsList = ':' . implode(', :', array_keys($payload));
                    $sql = sprintf('INSERT INTO `%s` (`%s`) VALUES (%s)', $table, $columnsList, $paramsList);
                    $stmt = db()->prepare($sql);
                    $stmt->execute($payload);
                    $crudMessage = 'Registro creado correctamente.';
                }
            } catch (Exception $e) {
                $crudError = 'No se pudo guardar el registro.';
            } catch (Error $e) {
                $crudError = 'No se pudo guardar el registro.';
            }
        }
    }

    $crudRecord = null;
    if ($crudEditId) {
        try {
            $stmt = db()->prepare(sprintf('SELECT * FROM `%s` WHERE id = :id', $table));
            $stmt->execute(['id' => $crudEditId]);
            $crudRecord = $stmt->fetch();
        } catch (Exception $e) {
            $crudError = 'No se pudo cargar el registro para edición.';
        } catch (Error $e) {
            $crudError = 'No se pudo cargar el registro para edición.';
        }
    }

    $crudRows = [];
    try {
        $stmt = db()->query(sprintf('SELECT * FROM `%s` ORDER BY %s DESC LIMIT 200', $table, $hasIdColumn ? 'id' : '1'));
        $crudRows = $stmt->fetchAll();
    } catch (Exception $e) {
        $crudError = $crudError ?? 'No se pudo cargar la lista de registros.';
    } catch (Error $e) {
        $crudError = $crudError ?? 'No se pudo cargar la lista de registros.';
    }
}

include('partials/html.php');
?>

<head>
    <?php $title = $pageTitle ?? 'Módulo'; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = $pageSubtitle ?? 'Módulo'; $title = $pageTitle ?? 'Módulo'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($pageTitle ?? 'Módulo', ENT_QUOTES, 'UTF-8'); ?></h5>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($pageDescription ?? 'Vista informativa del módulo.', ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <div class="card-body">
                                <?php if (isset($crudTable)) { ?>
                                    <?php if ($crudMessage) { ?>
                                        <div class="alert alert-success" role="alert">
                                            <?php echo htmlspecialchars($crudMessage, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($crudError) { ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo htmlspecialchars($crudError, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    <?php } ?>

                                    <?php if (!$crudReadOnly && $fields) { ?>
                                        <form method="post" class="row g-3 mb-4">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php foreach ($fields as $name => $label) { ?>
                                                <div class="col-md-6">
                                                    <label class="form-label" for="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                                    </label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                                                        name="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                                                        value="<?php echo htmlspecialchars($crudRecord[$name] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    >
                                                </div>
                                            <?php } ?>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <?php echo $crudEditId ? 'Actualizar' : 'Guardar'; ?>
                                                </button>
                                                <?php if ($crudEditId) { ?>
                                                    <a href="<?php echo htmlspecialchars(basename($_SERVER['PHP_SELF']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-secondary ms-2">Cancelar</a>
                                                <?php } ?>
                                            </div>
                                        </form>
                                    <?php } ?>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-centered mb-0">
                                            <thead>
                                                <tr>
                                                    <?php if (!empty($crudRows)) { ?>
                                                        <?php foreach (array_keys($crudRows[0]) as $column) { ?>
                                                            <th><?php echo htmlspecialchars((string) $column, ENT_QUOTES, 'UTF-8'); ?></th>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <th>Listado</th>
                                                    <?php } ?>
                                                    <?php if (!$crudReadOnly && $hasIdColumn) { ?>
                                                        <th>Acciones</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($crudRows)) { ?>
                                                    <tr>
                                                        <td colspan="100%" class="text-muted">Sin registros por ahora.</td>
                                                    </tr>
                                                <?php } ?>
                                                <?php foreach ($crudRows as $row) { ?>
                                                    <tr>
                                                        <?php foreach ($row as $value) { ?>
                                                            <td><?php echo htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <?php } ?>
                                                        <?php if (!$crudReadOnly && $hasIdColumn) { ?>
                                                            <td>
                                                                <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars(basename($_SERVER['PHP_SELF']) . '?edit=' . (int) $row['id'], ENT_QUOTES, 'UTF-8'); ?>">Editar</a>
                                                                <form method="post" class="d-inline">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <input type="hidden" name="delete_id" value="<?php echo (int) $row['id']; ?>">
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar registro?');">Eliminar</button>
                                                                </form>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } else { ?>
                                    <p class="mb-0">Esta vista está lista para conectar con los flujos y datos reales del sistema.</p>
                                <?php } ?>
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
