<?php
require __DIR__ . '/app/bootstrap.php';

$pageTitle = 'Datos de la empresa';
$pageSubtitle = 'Configuración';
$pageDescription = 'Configuración de datos corporativos.';

$empresa = get_municipalidad();
$errors = [];
$success = $_GET['success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $nombre = trim($_POST['nombre'] ?? '');
    $razonSocial = trim($_POST['razon_social'] ?? '');
    $rut = trim($_POST['rut'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $moneda = trim($_POST['moneda'] ?? 'CLP');

    if ($nombre === '') {
        $errors[] = 'El nombre de la empresa es obligatorio.';
    }

    if ($razonSocial === '') {
        $errors[] = 'La razón social es obligatoria.';
    }

    if ($rut === '') {
        $errors[] = 'El RUT/RUC es obligatorio.';
    }

    if (!in_array($moneda, ['CLP', 'USD', 'EUR'], true)) {
        $errors[] = 'Selecciona una moneda válida.';
    }

    if (empty($errors)) {
        $stmt = db()->query('SELECT id FROM municipalidad LIMIT 1');
        $id = $stmt->fetchColumn();

        if ($id) {
            $stmtUpdate = db()->prepare(
                'UPDATE municipalidad
                 SET nombre = ?, razon_social = ?, rut = ?, direccion = ?, telefono = ?, correo = ?, moneda = ?
                 WHERE id = ?'
            );
            $stmtUpdate->execute([
                $nombre,
                $razonSocial,
                $rut,
                $direccion !== '' ? $direccion : null,
                $telefono !== '' ? $telefono : null,
                $correo !== '' ? $correo : null,
                $moneda,
                $id,
            ]);
        } else {
            $stmtInsert = db()->prepare(
                'INSERT INTO municipalidad (nombre, razon_social, rut, direccion, telefono, correo, moneda)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmtInsert->execute([
                $nombre,
                $razonSocial,
                $rut,
                $direccion !== '' ? $direccion : null,
                $telefono !== '' ? $telefono : null,
                $correo !== '' ? $correo : null,
                $moneda,
            ]);
        }

        redirect('config-empresa.php?success=1');
    }

    $empresa = array_merge($empresa, [
        'nombre' => $nombre,
        'razon_social' => $razonSocial,
        'rut' => $rut,
        'direccion' => $direccion,
        'telefono' => $telefono,
        'correo' => $correo,
        'moneda' => $moneda,
    ]);
}

$registros = [];
try {
    $registros = db()->query(
        'SELECT nombre, razon_social, rut, correo, telefono, moneda, created_at
         FROM municipalidad
         ORDER BY created_at DESC'
    )->fetchAll();
} catch (Exception $e) {
} catch (Error $e) {
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

                <?php if ($success === '1') : ?>
                    <div class="alert alert-success">Información actualizada correctamente.</div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Datos de empresa</h5>
                                    <p class="text-muted mb-0">Actualiza la información corporativa base.</p>
                                </div>
                                <button type="submit" form="empresa-form" class="btn btn-primary">Guardar cambios</button>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($errors)) : ?>
                                    <div class="alert alert-danger">
                                        <?php foreach ($errors as $error) : ?>
                                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <form id="empresa-form" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="empresa-nombre">Nombre</label>
                                            <input type="text" id="empresa-nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($empresa['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="empresa-razon">Razón social</label>
                                            <input type="text" id="empresa-razon" name="razon_social" class="form-control" value="<?php echo htmlspecialchars($empresa['razon_social'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="empresa-rut">RUT / RUC</label>
                                            <input type="text" id="empresa-rut" name="rut" class="form-control" value="<?php echo htmlspecialchars($empresa['rut'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="empresa-moneda">Moneda</label>
                                            <?php $monedaActual = $empresa['moneda'] ?? 'CLP'; ?>
                                            <select id="empresa-moneda" name="moneda" class="form-select" required>
                                                <option value="CLP" <?php echo $monedaActual === 'CLP' ? 'selected' : ''; ?>>Peso chileno (CLP)</option>
                                                <option value="USD" <?php echo $monedaActual === 'USD' ? 'selected' : ''; ?>>Dólar (USD)</option>
                                                <option value="EUR" <?php echo $monedaActual === 'EUR' ? 'selected' : ''; ?>>Euro (EUR)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="empresa-correo">Correo</label>
                                            <input type="email" id="empresa-correo" name="correo" class="form-control" value="<?php echo htmlspecialchars($empresa['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="empresa-telefono">Teléfono</label>
                                            <input type="text" id="empresa-telefono" name="telefono" class="form-control" value="<?php echo htmlspecialchars($empresa['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label" for="empresa-direccion">Dirección</label>
                                            <input type="text" id="empresa-direccion" name="direccion" class="form-control" value="<?php echo htmlspecialchars($empresa['direccion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
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
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Registros existentes</h5>
                                <span class="text-muted small"><?php echo count($registros); ?> registro(s)</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Empresa</th>
                                                <th>Razón social</th>
                                                <th>RUT/RUC</th>
                                                <th>Correo</th>
                                                <th>Teléfono</th>
                                                <th>Moneda</th>
                                                <th>Creado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($registros)) : ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No hay registros todavía.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($registros as $registro) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($registro['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($registro['razon_social'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($registro['rut'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($registro['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($registro['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($registro['moneda'] ?? 'CLP', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($registro['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
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
