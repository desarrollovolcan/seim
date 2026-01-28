<?php
require __DIR__ . '/app/bootstrap.php';

$pageTitle = 'Datos de la empresa';
$pageSubtitle = 'Configuración';
$pageDescription = 'Configuración de datos corporativos.';
$moduleKey = 'config-empresa';
$moduleFields = [
    [
        'name' => 'nombre',
        'label' => 'Nombre de la empresa',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'razon_social',
        'label' => 'Razón social',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'ruc',
        'label' => 'RUT / RUC',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'telefono',
        'label' => 'Teléfono',
        'type' => 'tel',
        'required' => false,
    ],
    [
        'name' => 'correo',
        'label' => 'Correo',
        'type' => 'email',
        'required' => false,
    ],
    [
        'name' => 'direccion',
        'label' => 'Dirección',
        'type' => 'text',
        'required' => false,
    ],
    [
        'name' => 'moneda',
        'label' => 'Moneda',
        'type' => 'select',
        'required' => true,
        'options' => [
            'CLP' => 'Peso chileno (CLP)',
            'USD' => 'Dólar (USD)',
            'EUR' => 'Euro (EUR)',
        ],
    ],
];
$moduleListColumns = [
    ['key' => 'nombre', 'label' => 'Empresa'],
    ['key' => 'ruc', 'label' => 'RUT/RUC'],
    ['key' => 'correo', 'label' => 'Correo'],
    ['key' => 'moneda', 'label' => 'Moneda'],
];

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
    $logoTopbarHeight = trim($_POST['logo_topbar_height'] ?? '');
    $logoSidenavHeight = trim($_POST['logo_sidenav_height'] ?? '');
    $logoSidenavHeightSm = trim($_POST['logo_sidenav_height_sm'] ?? '');
    $logoAuthHeight = trim($_POST['logo_auth_height'] ?? '');
    $colorPrimary = trim($_POST['color_primary'] ?? '#6658dd');
    $colorSecondary = trim($_POST['color_secondary'] ?? '#4a81d4');

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

    if ($colorPrimary !== '' && !preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $colorPrimary)) {
        $errors[] = 'El color primario debe ser un valor hexadecimal válido.';
    }

    if ($colorSecondary !== '' && !preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $colorSecondary)) {
        $errors[] = 'El color secundario debe ser un valor hexadecimal válido.';
    }

    if ($logoTopbarHeight !== '' && (!ctype_digit($logoTopbarHeight) || (int) $logoTopbarHeight < 16 || (int) $logoTopbarHeight > 120)) {
        $errors[] = 'El alto del logo en la barra superior debe ser un número entre 16 y 120.';
    }

    if ($logoSidenavHeight !== '' && (!ctype_digit($logoSidenavHeight) || (int) $logoSidenavHeight < 16 || (int) $logoSidenavHeight > 120)) {
        $errors[] = 'El alto del logo en el menú lateral debe ser un número entre 16 y 120.';
    }

    if ($logoSidenavHeightSm !== '' && (!ctype_digit($logoSidenavHeightSm) || (int) $logoSidenavHeightSm < 16 || (int) $logoSidenavHeightSm > 120)) {
        $errors[] = 'El alto del logo en el menú lateral compacto debe ser un número entre 16 y 120.';
    }

    if ($logoAuthHeight !== '' && (!ctype_digit($logoAuthHeight) || (int) $logoAuthHeight < 16 || (int) $logoAuthHeight > 120)) {
        $errors[] = 'El alto del logo en las vistas de acceso debe ser un número entre 16 y 120.';
    }

    $logoPath = $empresa['logo_path'] ?? 'assets/images/logo.png';
    $logoUpload = $_FILES['logo'] ?? null;
    if (is_array($logoUpload) && ($logoUpload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $uploadDir = __DIR__ . '/assets/images/municipalidad/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        if (($logoUpload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $errors[] = 'No se pudo cargar el logo. Intenta nuevamente.';
        } else {
            $extension = strtolower(pathinfo($logoUpload['name'], PATHINFO_EXTENSION));
        }
        $allowed = ['png', 'jpg', 'jpeg', 'svg'];
        $allowedMime = [
            'png' => ['image/png'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'svg' => ['image/svg+xml', 'text/plain'],
        ];
        if (empty($errors) && is_uploaded_file($logoUpload['tmp_name'])) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($logoUpload['tmp_name']);
            if (!in_array($mimeType, $allowedMime[$extension] ?? [], true)) {
                $errors[] = 'El archivo no corresponde a un formato permitido (PNG, JPG o SVG).';
            }
        }
        if (empty($errors) && !in_array($extension, $allowed, true)) {
            $errors[] = 'Formato de logo no permitido. Usa PNG, JPG o SVG.';
        } elseif (empty($errors)) {
            $fileName = 'logo-empresa-' . date('YmdHis') . '.' . $extension;
            $targetPath = $uploadDir . $fileName;
            if (!move_uploaded_file($logoUpload['tmp_name'], $targetPath)) {
                $errors[] = 'No se pudo cargar el logo.';
            } else {
                $logoPath = 'assets/images/municipalidad/' . $fileName;
            }
        }
    }

    if (empty($errors)) {
        $stmt = db()->query('SELECT id FROM municipalidad LIMIT 1');
        $id = $stmt->fetchColumn();

        if ($id) {
            $stmtUpdate = db()->prepare(
                'UPDATE municipalidad
                 SET nombre = ?, razon_social = ?, rut = ?, direccion = ?, telefono = ?, correo = ?, moneda = ?,
                     logo_path = ?, logo_topbar_height = ?, logo_sidenav_height = ?, logo_sidenav_height_sm = ?,
                     logo_auth_height = ?, color_primary = ?, color_secondary = ?
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
                $logoPath,
                $logoTopbarHeight !== '' ? (int) $logoTopbarHeight : null,
                $logoSidenavHeight !== '' ? (int) $logoSidenavHeight : null,
                $logoSidenavHeightSm !== '' ? (int) $logoSidenavHeightSm : null,
                $logoAuthHeight !== '' ? (int) $logoAuthHeight : null,
                $colorPrimary,
                $colorSecondary,
                $id,
            ]);
        } else {
            $stmtInsert = db()->prepare(
                'INSERT INTO municipalidad (nombre, razon_social, rut, direccion, telefono, correo, moneda, logo_path, logo_topbar_height, logo_sidenav_height, logo_sidenav_height_sm, logo_auth_height, color_primary, color_secondary)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmtInsert->execute([
                $nombre,
                $razonSocial,
                $rut,
                $direccion !== '' ? $direccion : null,
                $telefono !== '' ? $telefono : null,
                $correo !== '' ? $correo : null,
                $moneda,
                $logoPath,
                $logoTopbarHeight !== '' ? (int) $logoTopbarHeight : null,
                $logoSidenavHeight !== '' ? (int) $logoSidenavHeight : null,
                $logoSidenavHeightSm !== '' ? (int) $logoSidenavHeightSm : null,
                $logoAuthHeight !== '' ? (int) $logoAuthHeight : null,
                $colorPrimary,
                $colorSecondary,
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
        'logo_path' => $logoPath,
        'logo_topbar_height' => $logoTopbarHeight,
        'logo_sidenav_height' => $logoSidenavHeight,
        'logo_sidenav_height_sm' => $logoSidenavHeightSm,
        'logo_auth_height' => $logoAuthHeight,
        'color_primary' => $colorPrimary !== '' ? $colorPrimary : '#6658dd',
        'color_secondary' => $colorSecondary !== '' ? $colorSecondary : '#4a81d4',
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

                                <form id="empresa-form" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row">
                                        <div class="col-lg-7">
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
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="empresa-color-primary">Color primario</label>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <input type="color" id="empresa-color-primary" name="color_primary" class="form-control form-control-color" value="<?php echo htmlspecialchars($empresa['color_primary'] ?? '#6658dd', ENT_QUOTES, 'UTF-8'); ?>">
                                                        <span class="badge rounded-pill" style="background-color: <?php echo htmlspecialchars($empresa['color_primary'] ?? '#6658dd', ENT_QUOTES, 'UTF-8'); ?>;">Primario</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="empresa-color-secondary">Color secundario</label>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <input type="color" id="empresa-color-secondary" name="color_secondary" class="form-control form-control-color" value="<?php echo htmlspecialchars($empresa['color_secondary'] ?? '#4a81d4', ENT_QUOTES, 'UTF-8'); ?>">
                                                        <span class="badge rounded-pill" style="background-color: <?php echo htmlspecialchars($empresa['color_secondary'] ?? '#4a81d4', ENT_QUOTES, 'UTF-8'); ?>;">Secundario</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="bg-light border rounded-3 p-4 h-100">
                                                <h6 class="mb-3">Identidad visual</h6>
                                                <div class="mb-3">
                                                    <label class="form-label" for="empresa-logo">Logo institucional</label>
                                                    <input type="file" id="empresa-logo" name="logo" class="form-control" accept=".png,.jpg,.jpeg,.svg">
                                                    <small class="text-muted">PNG, JPG o SVG. Se usará como logo del sistema.</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Tamaños del logo por sección</label>
                                                    <div class="row g-2">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <span class="input-group-text">Barra superior (px)</span>
                                                                <input type="number" min="16" max="120" id="empresa-logo-topbar" name="logo_topbar_height" class="form-control" value="<?php echo htmlspecialchars($empresa['logo_topbar_height'] ?? '56', ENT_QUOTES, 'UTF-8'); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <span class="input-group-text">Menú lateral (px)</span>
                                                                <input type="number" min="16" max="120" id="empresa-logo-sidenav" name="logo_sidenav_height" class="form-control" value="<?php echo htmlspecialchars($empresa['logo_sidenav_height'] ?? '48', ENT_QUOTES, 'UTF-8'); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <span class="input-group-text">Menú lateral compacto (px)</span>
                                                                <input type="number" min="16" max="120" id="empresa-logo-sidenav-sm" name="logo_sidenav_height_sm" class="form-control" value="<?php echo htmlspecialchars($empresa['logo_sidenav_height_sm'] ?? '36', ENT_QUOTES, 'UTF-8'); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <span class="input-group-text">Pantallas de acceso (px)</span>
                                                                <input type="number" min="16" max="120" id="empresa-logo-auth" name="logo_auth_height" class="form-control" value="<?php echo htmlspecialchars($empresa['logo_auth_height'] ?? '48', ENT_QUOTES, 'UTF-8'); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?php echo htmlspecialchars($empresa['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="Logo empresa" style="max-height: 64px;">
                                                    <span class="text-muted small">Vista previa del logo actual.</span>
                                                </div>
                                            </div>
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
