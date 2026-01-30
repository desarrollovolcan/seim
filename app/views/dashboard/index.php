<div class="dashboard-compact">
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
                        <table class="table table-sm align-middle">
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
                                            <td><?php echo e($item['name'] ?? ''); ?></td>
                                            <td class="text-end"><?php echo (int)($item['produced_quantity'] ?? 0); ?></td>
                                            <td class="text-end"><?php echo e(format_currency((float)($item['cost'] ?? 0))); ?></td>
                                            <td class="text-end"><?php echo (int)($item['stock'] ?? 0); ?></td>
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
                        <table class="table table-sm align-middle">
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
                                            <td><?php echo e($item['name'] ?? ''); ?></td>
                                            <td class="text-end"><?php echo (int)($item['quantity'] ?? 0); ?></td>
                                            <td class="text-end"><?php echo e(format_currency((float)($item['total'] ?? 0))); ?></td>
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
                        <table class="table table-sm align-middle">
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
                                            <td><?php echo e($item['name'] ?? ''); ?></td>
                                            <td class="text-end"><?php echo e(format_currency((float)($item['total'] ?? 0))); ?></td>
                                            <td class="text-end"><?php echo e(format_currency((float)($item['total_cost'] ?? 0))); ?></td>
                                            <td class="text-end"><?php echo e(format_currency((float)($item['profit'] ?? 0))); ?></td>
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
                        <table class="table table-sm align-middle">
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
                                            <td><?php echo e($item['name'] ?? ''); ?></td>
                                            <td class="text-end"><?php echo (int)($item['stock'] ?? 0); ?></td>
                                            <td class="text-end"><?php echo (int)($item['stock_min'] ?? 0); ?></td>
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
        const productCostCtx = document.getElementById('productCostChart');
        if (productCostCtx) {
            new Chart(productCostCtx, {
                type: 'bar',
                data: {
                    labels: productCostLabels,
                    datasets: [{
                        label: 'Costo unitario',
                        data: productCostTotals,
                        backgroundColor: '#5a4de1',
                        borderRadius: 6,
                        maxBarThickness: 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { grid: { color: 'rgba(148, 163, 184, 0.25)' }, beginAtZero: true }
                    }
                }
            });
        }

        const salesCtx = document.getElementById('salesByProductChart');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: salesLabels,
                    datasets: [{
                        label: 'Ventas',
                        data: salesTotals,
                        borderColor: '#22b59a',
                        backgroundColor: 'rgba(34, 181, 154, 0.18)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: '#22b59a'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { grid: { color: 'rgba(148, 163, 184, 0.25)' }, beginAtZero: true }
                    }
                }
            });
        }

        const profitCtx = document.getElementById('profitByProductChart');
        if (profitCtx) {
            new Chart(profitCtx, {
                type: 'bar',
                data: {
                    labels: profitLabels,
                    datasets: [{
                        label: 'Ganancia',
                        data: profitTotals,
                        backgroundColor: '#f3a257',
                        borderRadius: 6,
                        maxBarThickness: 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { grid: { color: 'rgba(148, 163, 184, 0.25)' }, beginAtZero: true }
                    }
                }
            });
        }

        const lowStockCtx = document.getElementById('lowStockChart');
        if (lowStockCtx) {
            new Chart(lowStockCtx, {
                type: 'bar',
                data: {
                    labels: lowStockLabels,
                    datasets: [
                        {
                            label: 'Stock actual',
                            data: lowStockValues,
                            backgroundColor: '#f06c6c',
                            borderRadius: 6,
                            maxBarThickness: 36
                        },
                        {
                            label: 'Stock mínimo',
                            data: lowStockMinimums,
                            backgroundColor: '#94a3b8',
                            borderRadius: 6,
                            maxBarThickness: 36
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10 } } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { grid: { color: 'rgba(148, 163, 184, 0.25)' }, beginAtZero: true }
                    }
                }
            });
        }
    }
</script>
