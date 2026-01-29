<?php
    $lowStockTop = array_slice($lowStock, 0, 5);
    $ventasTop = array_slice($ventasProductos, 0, 6);
?>
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
                <?php $subtitle = 'Resumen de ventas y rentabilidad'; $title = 'Dashboard'; include('partials/page-title.php'); ?>

                <?php if (!empty($_SESSION['permission_error'])) : ?>
                    <div class="alert alert-warning">
                        <?php echo htmlspecialchars((string) $_SESSION['permission_error'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <?php unset($_SESSION['permission_error']); ?>
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted text-uppercase fs-12 mb-1">Ventas totales</p>
                                <h3 class="mb-0">$<?php echo number_format((float) ($resumen['ventas_total'] ?? 0), 2); ?></h3>
                                <small class="text-muted">Ingresos registrados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted text-uppercase fs-12 mb-1">Ganancia total</p>
                                <h3 class="mb-0">$<?php echo number_format((float) ($resumen['ganancia_total'] ?? 0), 2); ?></h3>
                                <small class="text-muted">
                                    Costo: $<?php echo number_format((float) ($resumen['costo_total'] ?? 0), 2); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted text-uppercase fs-12 mb-1">Margen promedio</p>
                                <h3 class="mb-0"><?php echo number_format((float) ($resumen['margen_total'] ?? 0), 2); ?>%</h3>
                                <small class="text-muted">Rentabilidad global</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted text-uppercase fs-12 mb-1">Productos bajo stock</p>
                                <h3 class="mb-0"><?php echo (int) ($resumen['productos_bajo_stock'] ?? 0); ?></h3>
                                <small class="text-muted">Alertas críticas</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-xl-8">
                        <div class="card shadow-sm h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Ventas vs precio por producto</h5>
                                <span class="text-muted fs-12">Top productos vendidos</span>
                            </div>
                            <div class="card-body">
                                <div id="ventasPrecioChart" class="apex-charts" style="min-height: 240px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Ganancia acumulada</h5>
                                <span class="text-muted fs-12">Evolución mensual</span>
                            </div>
                            <div class="card-body">
                                <div id="gananciaAcumuladaChart" class="apex-charts" style="min-height: 240px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-xl-4">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Productos con stock bajo</h5>
                                <span class="text-muted fs-12">Inventario crítico</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Stock actual</th>
                                                <th>Mínimo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$lowStockTop) : ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">Sin alertas de stock.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($lowStockTop as $item) : ?>
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
                    <div class="col-xl-8">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Top productos rentables</h5>
                                <span class="text-muted fs-12">Ventas y margen</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Unidades</th>
                                                <th>Ventas</th>
                                                <th>Margen %</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$ventasTop) : ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Sin ventas registradas.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($ventasTop as $producto) : ?>
                                                    <?php
                                                        $precioCompra = (float) ($producto['precio_compra'] ?? 0);
                                                        $precioVenta = (float) ($producto['precio_venta'] ?? 0);
                                                        $margen = $precioCompra > 0
                                                            ? round((($precioVenta - $precioCompra) / $precioCompra) * 100, 2)
                                                            : 0;
                                                        $unidades = (float) ($producto['unidades'] ?? 0);
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($producto['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo number_format($unidades, 2); ?></td>
                                                        <td>$<?php echo number_format((float) ($producto['ventas_total'] ?? 0), 2); ?></td>
                                                        <td><?php echo number_format($margen, 2); ?>%</td>
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

            const productosLabels = <?php echo json_encode(array_column($ventasProductos, 'nombre'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
            const ventasProductoData = <?php echo json_encode(array_map(static fn($item) => (float) ($item['ventas_total'] ?? 0), $ventasProductos), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
            const precioProductoData = <?php echo json_encode(array_map(static fn($item) => (float) (($item['precio_venta'] ?? $item['precio_promedio'] ?? 0)), $ventasProductos), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

            const ventasPrecioChart = new ApexCharts(document.querySelector('#ventasPrecioChart'), {
                chart: {
                    type: 'line',
                    height: 240,
                    toolbar: { show: false },
                },
                series: [
                    { name: 'Ventas ($)', type: 'column', data: ventasProductoData },
                    { name: 'Precio venta ($)', type: 'line', data: precioProductoData },
                ],
                stroke: {
                    width: [0, 3],
                    curve: 'smooth',
                },
                xaxis: {
                    categories: productosLabels,
                },
                yaxis: [
                    {
                        title: { text: 'Ventas' },
                    },
                    {
                        opposite: true,
                        title: { text: 'Precio venta' },
                    },
                ],
                colors: ['#4f46e5', '#22c55e'],
                dataLabels: { enabled: false },
                tooltip: {
                    shared: true,
                    intersect: false,
                },
            });
            ventasPrecioChart.render();

            const gananciaLabels = <?php echo json_encode(array_column($gananciaAcumulada, 'periodo'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
            const gananciaData = <?php echo json_encode(array_map(static fn($item) => (float) ($item['ganancia_acumulada'] ?? 0), $gananciaAcumulada), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

            const gananciaChart = new ApexCharts(document.querySelector('#gananciaAcumuladaChart'), {
                chart: {
                    type: 'area',
                    height: 240,
                    toolbar: { show: false },
                },
                series: [{ name: 'Ganancia acumulada', data: gananciaData }],
                xaxis: { categories: gananciaLabels },
                colors: ['#f97316'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                        stops: [0, 90, 100],
                    },
                },
            });
            gananciaChart.render();
        })();
    </script>
</body>
</html>
