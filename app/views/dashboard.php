<?php include('partials/html.php'); ?>

<head>
    <?php $title = 'Dashboard'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Resumen general'; $title = 'Dashboard'; include('partials/page-title.php'); ?>

                <div class="row g-3">
                    <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted text-uppercase fs-12 mb-1">Productos</p>
                                        <h3 class="mb-0"><?php echo (int) ($metrics['productos'] ?? 0); ?></h3>
                                        <small class="text-muted">Total registrados</small>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                        <i data-lucide="package" class="fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted text-uppercase fs-12 mb-1">Categorías</p>
                                        <h3 class="mb-0"><?php echo (int) ($metrics['categorias'] ?? 0); ?></h3>
                                        <small class="text-muted">Activas</small>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                                        <i data-lucide="list" class="fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted text-uppercase fs-12 mb-1">Unidades</p>
                                        <h3 class="mb-0"><?php echo (int) ($metrics['unidades'] ?? 0); ?></h3>
                                        <small class="text-muted">Configuradas</small>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center">
                                        <i data-lucide="ruler" class="fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted text-uppercase fs-12 mb-1">Movimientos</p>
                                        <h3 class="mb-0"><?php echo (int) ($metrics['movimientos'] ?? 0); ?></h3>
                                        <small class="text-muted">Registrados</small>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center">
                                        <i data-lucide="arrow-left-right" class="fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted text-uppercase fs-12 mb-1">Ventas</p>
                                        <h3 class="mb-0"><?php echo (int) ($metrics['ventas'] ?? 0); ?></h3>
                                        <small class="text-muted">Totales</small>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                        <i data-lucide="shopping-cart" class="fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted text-uppercase fs-12 mb-1">Clientes</p>
                                        <h3 class="mb-0"><?php echo (int) ($metrics['clientes'] ?? 0); ?></h3>
                                        <small class="text-muted">Registrados</small>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center">
                                        <i data-lucide="users" class="fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted text-uppercase fs-12 mb-1">Bajo stock</p>
                                        <h3 class="mb-0"><?php echo (int) ($metrics['bajo_stock'] ?? 0); ?></h3>
                                        <small class="text-muted">Productos críticos</small>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center">
                                        <i data-lucide="alert-triangle" class="fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-xl-8">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Ventas recientes</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>Fecha</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$recentSales) : ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">Sin ventas recientes.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($recentSales as $sale) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($sale['cliente'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($sale['fecha'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($sale['total'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Acciones rápidas</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="compras.php" class="btn btn-outline-warning">Registrar compra</a>
                                    <a href="inventario-categorias.php" class="btn btn-outline-info">Nueva familia</a>
                                    <a href="inventario-productos.php" class="btn btn-outline-primary">Nuevo producto</a>
                                    <a href="inventario-movimientos.php" class="btn btn-outline-secondary">Registrar movimiento</a>
                                    <a href="clientes.php" class="btn btn-outline-info">Nuevo cliente</a>
                                    <a href="ventas.php" class="btn btn-outline-success">Registrar venta</a>
                                    <a href="inventario-stock.php" class="btn btn-outline-danger">Revisar stock</a>
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
