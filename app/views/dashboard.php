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

                <?php if (!empty($_SESSION['permission_error'])) : ?>
                    <div class="alert alert-warning">
                        <?php echo htmlspecialchars((string) $_SESSION['permission_error'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <?php unset($_SESSION['permission_error']); ?>
                <?php endif; ?>

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
                    <div class="col-xl-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Ventas por mes</h5>
                            </div>
                            <div class="card-body">
                                <div id="ventasMensualesChart" class="apex-charts" style="min-height: 240px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Margen de ganancia por producto</h5>
                            </div>
                            <div class="card-body">
                                <div id="margenProductosChart" class="apex-charts" style="min-height: 240px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-xl-6">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Stock bajo</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Stock actual</th>
                                                <th>Stock mínimo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$lowStock) : ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">Sin alertas críticas.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($lowStock as $item) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($item['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($item['stock_actual'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($item['stock_minimo'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
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
                </div>

                <div class="row g-3 mt-1">
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
                    <div class="col-xl-8">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Top margen por producto</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Compra</th>
                                                <th>Venta</th>
                                                <th>Margen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$profitMargins) : ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Sin datos de margen aún.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($profitMargins as $margin) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($margin['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($margin['precio_compra'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($margin['precio_venta'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($margin['margen'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?>%</td>
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
    <script>
        (() => {
            if (typeof ApexCharts === 'undefined') {
                return;
            }

            const ventasLabels = <?php echo json_encode(array_column($ventasMensuales, 'periodo'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
            const ventasData = <?php echo json_encode(array_map(static fn($item) => (float) ($item['total'] ?? 0), $ventasMensuales), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

            const ventasChart = new ApexCharts(document.querySelector('#ventasMensualesChart'), {
                chart: {
                    type: 'bar',
                    height: 240,
                    toolbar: { show: false },
                },
                series: [{ name: 'Ventas', data: ventasData }],
                xaxis: { categories: ventasLabels },
                colors: ['#4f46e5'],
                dataLabels: { enabled: false },
            });
            ventasChart.render();

            const margenLabels = <?php echo json_encode(array_column($profitMargins, 'nombre'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
            const margenData = <?php echo json_encode(array_map(static fn($item) => (float) ($item['margen'] ?? 0), $profitMargins), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

            const margenChart = new ApexCharts(document.querySelector('#margenProductosChart'), {
                chart: {
                    type: 'bar',
                    height: 240,
                    toolbar: { show: false },
                },
                series: [{ name: 'Margen %', data: margenData }],
                xaxis: { categories: margenLabels },
                colors: ['#22c55e'],
                dataLabels: { enabled: false },
            });
            margenChart.render();
        })();
    </script>
</body>
</html>
