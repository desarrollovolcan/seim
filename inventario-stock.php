<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

$municipalidad = get_municipalidad();
$errors = [];

$productos = [];
try {
    $productos = db()->query(
        'SELECT p.*, c.nombre AS categoria_nombre, s.nombre AS subfamilia_nombre, u.abreviatura AS unidad_abreviatura
         FROM inventario_productos p
         LEFT JOIN inventario_categorias c ON c.id = p.categoria_id
         LEFT JOIN inventario_subfamilias s ON s.id = p.subfamilia_id
         LEFT JOIN inventario_unidades u ON u.id = p.unidad_id
         ORDER BY p.nombre'
    )->fetchAll();
} catch (Exception $e) {
    $errors[] = 'No se pudo cargar el stock actual.';
}

include('partials/html.php');
?>

<head>
    <?php $title = 'Stock actual'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Inventario'; $title = 'Stock actual'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Stock actual</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($errors) : ?>
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            <?php foreach ($errors as $error) : ?>
                                                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Familia</th>
                                                <th>Subfamilia</th>
                                                <th>Unidad</th>
                                                <th>Stock actual</th>
                                                <th>Stock mínimo</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$productos) : ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">Sin registros aún.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($productos as $producto) : ?>
                                                    <?php
                                                        $stockActual = (float) ($producto['stock_actual'] ?? 0);
                                                        $stockMin = (float) ($producto['stock_minimo'] ?? 0);
                                                        $estado = $stockActual <= $stockMin ? 'Bajo stock' : 'Disponible';
                                                        $badgeClass = $stockActual <= $stockMin ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success';
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($producto['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($producto['categoria_nombre'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($producto['subfamilia_nombre'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($producto['unidad_abreviatura'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) $stockActual, ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) $stockMin, ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $estado; ?></span></td>
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
