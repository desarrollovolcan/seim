<?php
$totalProduced = 0;
$totalSales = 0.0;
$totalProfit = 0.0;
$lowStockCount = 0;

foreach (($producedProducts ?? []) as $item) {
    $totalProduced += (int)($item['produced_quantity'] ?? 0);
}
foreach (($salesByProduct ?? []) as $item) {
    $totalSales += (float)($item['total'] ?? 0);
}
foreach (($profitByProduct ?? []) as $item) {
    $totalProfit += (float)($item['profit'] ?? 0);
}
foreach (($lowStockProducts ?? []) as $item) {
    if ((int)($item['stock'] ?? 0) <= (int)($item['stock_min'] ?? 0)) {
        $lowStockCount++;
    }
}
?>

<div class="dashboard-compact">
    <div class="row g-2 mt-2 dashboard-metrics">
        <div class="col-6 col-lg-3">
            <div class="card h-100 dashboard-metric-card">
                <div class="card-body">
                    <div class="dashboard-metric-title">Unidades producidas</div>
                    <div class="dashboard-metric-value"><?php echo (int)$totalProduced; ?></div>
                    <div class="dashboard-metric-subtitle text-muted">Total acumulado</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 dashboard-metric-card">
                <div class="card-body">
                    <div class="dashboard-metric-title">Ventas totales</div>
                    <div class="dashboard-metric-value"><?php echo e(format_currency($totalSales)); ?></div>
                    <div class="dashboard-metric-subtitle text-muted">Ingresos por producto</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 dashboard-metric-card">
                <div class="card-body">
                    <div class="dashboard-metric-title">Ganancia estimada</div>
                    <div class="dashboard-metric-value"><?php echo e(format_currency($totalProfit)); ?></div>
                    <div class="dashboard-metric-subtitle text-muted">Margen neto</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 dashboard-metric-card">
                <div class="card-body">
                    <div class="dashboard-metric-title">Stock en riesgo</div>
                    <div class="dashboard-metric-value"><?php echo (int)$lowStockCount; ?></div>
                    <div class="dashboard-metric-subtitle text-muted">Productos bajo mínimo</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-2 mt-2">
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Costos de productos</h4>
                        <small class="text-muted">Costos unitarios y unidades producidas.</small>
                    </div>
                    <a href="index.php?route=production/stock" class="btn btn-outline-primary btn-sm">Ver stock producido</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle dashboard-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Unidades producidas</th>
                                    <th class="text-end">Costo unitario</th>
                                    <th class="text-end">Stock actual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($producedProducts)): ?>
                                    <tr>
                                        <td colspan="4" class="text-muted text-center">Sin producción registrada.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($producedProducts as $item): ?>
                                        <tr>
                                            <td data-label="Producto"><?php echo e($item['name'] ?? ''); ?></td>
                                            <td class="text-end" data-label="Unidades producidas"><?php echo (int)($item['produced_quantity'] ?? 0); ?></td>
                                            <td class="text-end" data-label="Costo unitario"><?php echo e(format_currency((float)($item['cost'] ?? 0))); ?></td>
                                            <td class="text-end" data-label="Stock actual"><?php echo (int)($item['stock'] ?? 0); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="dashboard-chart mt-3">
                        <canvas id="productCostChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0">Ventas por producto</h4>
                    <small class="text-muted">Totales vendidos y unidades.</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle dashboard-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Unidades</th>
                                    <th class="text-end">Ventas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($salesByProduct)): ?>
                                    <tr>
                                        <td colspan="3" class="text-muted text-center">Sin ventas registradas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($salesByProduct as $item): ?>
                                        <tr>
                                            <td data-label="Producto"><?php echo e($item['name'] ?? ''); ?></td>
                                            <td class="text-end" data-label="Unidades"><?php echo (int)($item['quantity'] ?? 0); ?></td>
                                            <td class="text-end" data-label="Ventas"><?php echo e(format_currency((float)($item['total'] ?? 0))); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="dashboard-chart dashboard-chart-sm mt-3">
                        <canvas id="salesByProductChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2 mt-2">
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0">Ganancias por producto</h4>
                    <small class="text-muted">Margen estimado según costo y ventas.</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle dashboard-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Ventas</th>
                                    <th class="text-end">Costo</th>
                                    <th class="text-end">Ganancia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($profitByProduct)): ?>
                                    <tr>
                                        <td colspan="4" class="text-muted text-center">Sin ganancias registradas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($profitByProduct as $item): ?>
                                        <tr>
                                            <td data-label="Producto"><?php echo e($item['name'] ?? ''); ?></td>
                                            <td class="text-end" data-label="Ventas"><?php echo e(format_currency((float)($item['total'] ?? 0))); ?></td>
                                            <td class="text-end" data-label="Costo"><?php echo e(format_currency((float)($item['total_cost'] ?? 0))); ?></td>
                                            <td class="text-end" data-label="Ganancia"><?php echo e(format_currency((float)($item['profit'] ?? 0))); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="dashboard-chart dashboard-chart-sm mt-3">
                        <canvas id="profitByProductChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0">Stock bajo</h4>
                    <small class="text-muted">Productos bajo su stock mínimo.</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle dashboard-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Stock actual</th>
                                    <th class="text-end">Stock mínimo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($lowStockProducts)): ?>
                                    <tr>
                                        <td colspan="3" class="text-muted text-center">Sin productos con stock bajo.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($lowStockProducts as $item): ?>
                                        <tr>
                                            <td data-label="Producto"><?php echo e($item['name'] ?? ''); ?></td>
                                            <td class="text-end" data-label="Stock actual"><?php echo (int)($item['stock'] ?? 0); ?></td>
                                            <td class="text-end" data-label="Stock mínimo"><?php echo (int)($item['stock_min'] ?? 0); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="dashboard-chart dashboard-chart-sm mt-3">
                        <canvas id="lowStockChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$productCostLabels = [];
$productCostTotals = [];
foreach (($producedProducts ?? []) as $item) {
    $productCostLabels[] = $item['name'] ?? '';
    $productCostTotals[] = (float)($item['cost'] ?? 0);
}
$salesLabels = [];
$salesTotals = [];
foreach (($salesByProduct ?? []) as $item) {
    $salesLabels[] = $item['name'] ?? '';
    $salesTotals[] = (float)($item['total'] ?? 0);
}
$profitLabels = [];
$profitTotals = [];
foreach (($profitByProduct ?? []) as $item) {
    $profitLabels[] = $item['name'] ?? '';
    $profitTotals[] = (float)($item['profit'] ?? 0);
}
$lowStockLabels = [];
$lowStockValues = [];
$lowStockMinimums = [];
foreach (($lowStockProducts ?? []) as $item) {
    $lowStockLabels[] = $item['name'] ?? '';
    $lowStockValues[] = (int)($item['stock'] ?? 0);
    $lowStockMinimums[] = (int)($item['stock_min'] ?? 0);
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const productCostLabels = <?php echo json_encode($productCostLabels, JSON_UNESCAPED_UNICODE); ?>;
    const productCostTotals = <?php echo json_encode($productCostTotals, JSON_UNESCAPED_UNICODE); ?>;
    const salesLabels = <?php echo json_encode($salesLabels, JSON_UNESCAPED_UNICODE); ?>;
    const salesTotals = <?php echo json_encode($salesTotals, JSON_UNESCAPED_UNICODE); ?>;
    const profitLabels = <?php echo json_encode($profitLabels, JSON_UNESCAPED_UNICODE); ?>;
    const profitTotals = <?php echo json_encode($profitTotals, JSON_UNESCAPED_UNICODE); ?>;
    const lowStockLabels = <?php echo json_encode($lowStockLabels, JSON_UNESCAPED_UNICODE); ?>;
    const lowStockValues = <?php echo json_encode($lowStockValues, JSON_UNESCAPED_UNICODE); ?>;
    const lowStockMinimums = <?php echo json_encode($lowStockMinimums, JSON_UNESCAPED_UNICODE); ?>;

    if (window.Chart) {
        const isMobile = window.innerWidth <= 576;
        const buildGradient = (ctx, start, end) => {
            const gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, start);
            gradient.addColorStop(1, end);
            return gradient;
        };
        const baseGrid = { color: 'rgba(148, 163, 184, 0.25)' };
        const axisFont = { size: isMobile ? 10 : 12 };

        const productCostCtx = document.getElementById('productCostChart');
        if (productCostCtx) {
            const productCostGradient = buildGradient(productCostCtx.getContext('2d'), 'rgba(90, 77, 225, 0.65)', 'rgba(90, 77, 225, 0.15)');
            new Chart(productCostCtx, {
                type: 'bar',
                data: {
                    labels: productCostLabels,
                    datasets: [{
                        label: 'Costo unitario',
                        data: productCostTotals,
                        backgroundColor: productCostGradient,
                        borderColor: '#5a4de1',
                        borderWidth: 1,
                        borderRadius: 8,
                        maxBarThickness: isMobile ? 18 : 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    indexAxis: isMobile ? 'y' : 'x',
                    scales: {
                        x: { grid: { display: false }, ticks: { font: axisFont } },
                        y: { grid: baseGrid, beginAtZero: true, ticks: { font: axisFont } }
                    }
                }
            });
        }

        const salesCtx = document.getElementById('salesByProductChart');
        if (salesCtx) {
            const salesPalette = ['#22b59a', '#5a4de1', '#f3a257', '#4aa3ff', '#f06c6c', '#7c8bff'];
            new Chart(salesCtx, {
                type: 'doughnut',
                data: {
                    labels: salesLabels,
                    datasets: [{
                        label: 'Ventas',
                        data: salesTotals,
                        backgroundColor: salesPalette,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: isMobile ? '60%' : '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, boxHeight: 10, font: axisFont }
                        }
                    }
                }
            });
        }

        const profitCtx = document.getElementById('profitByProductChart');
        if (profitCtx) {
            const profitGradient = buildGradient(profitCtx.getContext('2d'), 'rgba(243, 162, 87, 0.45)', 'rgba(243, 162, 87, 0.05)');
            new Chart(profitCtx, {
                type: 'line',
                data: {
                    labels: profitLabels,
                    datasets: [{
                        label: 'Ganancia',
                        data: profitTotals,
                        borderColor: '#f3a257',
                        backgroundColor: profitGradient,
                        fill: true,
                        tension: 0.35,
                        pointRadius: isMobile ? 2 : 3,
                        pointHoverRadius: isMobile ? 3 : 4,
                        pointBackgroundColor: '#f3a257'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: axisFont, maxTicksLimit: isMobile ? 4 : undefined } },
                        y: { grid: baseGrid, beginAtZero: true, ticks: { font: axisFont } }
                    }
                }
            });
        }

        const lowStockCtx = document.getElementById('lowStockChart');
        if (lowStockCtx) {
            const lowStockGradient = buildGradient(lowStockCtx.getContext('2d'), 'rgba(240, 108, 108, 0.65)', 'rgba(240, 108, 108, 0.2)');
            const lowStockMinGradient = buildGradient(lowStockCtx.getContext('2d'), 'rgba(148, 163, 184, 0.7)', 'rgba(148, 163, 184, 0.25)');
            new Chart(lowStockCtx, {
                type: 'bar',
                data: {
                    labels: lowStockLabels,
                    datasets: [
                        {
                            label: 'Stock actual',
                            data: lowStockValues,
                            backgroundColor: lowStockGradient,
                            borderColor: '#f06c6c',
                            borderWidth: 1,
                            borderRadius: 8,
                            maxBarThickness: isMobile ? 18 : 36
                        },
                        {
                            label: 'Stock mínimo',
                            data: lowStockMinimums,
                            backgroundColor: lowStockMinGradient,
                            borderColor: '#94a3b8',
                            borderWidth: 1,
                            borderRadius: 8,
                            maxBarThickness: isMobile ? 18 : 36
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, font: axisFont } } },
                    indexAxis: isMobile ? 'y' : 'x',
                    scales: {
                        x: { grid: { display: false }, ticks: { font: axisFont } },
                        y: { grid: baseGrid, beginAtZero: true, ticks: { font: axisFont } }
                    }
                }
            });
        }
    }
</script>
