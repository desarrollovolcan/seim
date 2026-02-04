<?php
$totalProduced = 0;
$totalSales = 0.0;
$totalProfit = 0.0;
$lowStockCount = 0;
$totalProducts = max(1, count($producedProducts ?? []));

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
    <div class="row g-2 mt-2">
        <div class="col-12">
            <div class="card dashboard-hero dashboard-hero-light">
                <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <h3 class="mb-1">Resumen operativo</h3>
                        <p class="mb-0">Monitorea producción, ventas y stock con indicadores rápidos y gráficos claros.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="index.php?route=production/stock" class="btn btn-primary btn-sm">Producción</a>
                        <a href="index.php?route=sales" class="btn btn-outline-primary btn-sm">Ventas</a>
                        <a href="index.php?route=inventory/movements" class="btn btn-outline-primary btn-sm">Inventario</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-2 mt-2 dashboard-metrics">
        <div class="col-6 col-lg-3">
            <div class="card h-100 dashboard-metric-card">
                <div class="card-body">
                    <div class="dashboard-metric-title">Unidades producidas</div>
                    <div class="dashboard-metric-value"><?php echo (int)$totalProduced; ?></div>
                    <div class="dashboard-metric-meta">
                        <span class="text-muted">Productos: <?php echo (int)$totalProducts; ?></span>
                        <a href="index.php?route=production/stock" class="dashboard-metric-link">Detalle</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 dashboard-metric-card">
                <div class="card-body">
                    <div class="dashboard-metric-title">Ventas totales</div>
                    <div class="dashboard-metric-value"><?php echo e(format_currency($totalSales)); ?></div>
                    <div class="dashboard-metric-meta">
                        <span class="text-muted">Promedio: <?php echo e(format_currency($totalSales / $totalProducts)); ?></span>
                        <a href="index.php?route=sales" class="dashboard-metric-link">Detalle</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 dashboard-metric-card">
                <div class="card-body">
                    <div class="dashboard-metric-title">Ganancia estimada</div>
                    <div class="dashboard-metric-value"><?php echo e(format_currency($totalProfit)); ?></div>
                    <div class="dashboard-metric-meta">
                        <span class="text-muted">Rentabilidad</span>
                        <a href="index.php?route=accounting/financial-statements" class="dashboard-metric-link">Detalle</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 dashboard-metric-card">
                <div class="card-body">
                    <div class="dashboard-metric-title">Stock en riesgo</div>
                    <div class="dashboard-metric-value"><?php echo (int)$lowStockCount; ?></div>
                    <div class="dashboard-metric-meta">
                        <span class="text-muted">Bajo mínimo</span>
                        <a href="index.php?route=inventory/movements" class="dashboard-metric-link">Detalle</a>
                    </div>
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
                    <div class="dashboard-chart dashboard-chart-lg mt-3">
                        <canvas id="productCostChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Ventas por producto</h4>
                        <small class="text-muted">Totales vendidos y unidades.</small>
                    </div>
                    <a href="index.php?route=sales" class="btn btn-outline-primary btn-sm">Ver ventas</a>
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
                    <div class="dashboard-chart dashboard-chart-lg mt-3">
                        <canvas id="salesByProductChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2 mt-2">
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Ganancias por producto</h4>
                        <small class="text-muted">Margen estimado según costo y ventas.</small>
                    </div>
                    <a href="index.php?route=accounting/financial-statements" class="btn btn-outline-primary btn-sm">Ver resultados</a>
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
                    <div class="dashboard-chart mt-3">
                        <canvas id="profitByProductChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Stock bajo</h4>
                        <small class="text-muted">Productos bajo su stock mínimo.</small>
                    </div>
                    <a href="index.php?route=inventory/movements" class="btn btn-outline-primary btn-sm">Ver inventario</a>
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
                    <div class="dashboard-chart mt-3">
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
$productCostUnits = [];
$productCostStocks = [];
foreach (($producedProducts ?? []) as $item) {
    $productCostLabels[] = $item['name'] ?? '';
    $productCostTotals[] = (float)($item['cost'] ?? 0);
    $productCostUnits[] = (int)($item['produced_quantity'] ?? 0);
    $productCostStocks[] = (int)($item['stock'] ?? 0);
}
$salesLabels = [];
$salesTotals = [];
$salesUnits = [];
foreach (($salesByProduct ?? []) as $item) {
    $salesLabels[] = $item['name'] ?? '';
    $salesTotals[] = (float)($item['total'] ?? 0);
    $salesUnits[] = (int)($item['quantity'] ?? 0);
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
    const productCostUnits = <?php echo json_encode($productCostUnits, JSON_UNESCAPED_UNICODE); ?>;
    const productCostStocks = <?php echo json_encode($productCostStocks, JSON_UNESCAPED_UNICODE); ?>;
    const salesLabels = <?php echo json_encode($salesLabels, JSON_UNESCAPED_UNICODE); ?>;
    const salesTotals = <?php echo json_encode($salesTotals, JSON_UNESCAPED_UNICODE); ?>;
    const salesUnits = <?php echo json_encode($salesUnits, JSON_UNESCAPED_UNICODE); ?>;
    const profitLabels = <?php echo json_encode($profitLabels, JSON_UNESCAPED_UNICODE); ?>;
    const profitTotals = <?php echo json_encode($profitTotals, JSON_UNESCAPED_UNICODE); ?>;
    const lowStockLabels = <?php echo json_encode($lowStockLabels, JSON_UNESCAPED_UNICODE); ?>;
    const lowStockValues = <?php echo json_encode($lowStockValues, JSON_UNESCAPED_UNICODE); ?>;
    const lowStockMinimums = <?php echo json_encode($lowStockMinimums, JSON_UNESCAPED_UNICODE); ?>;
    const totalProducts = <?php echo json_encode($totalProducts, JSON_UNESCAPED_UNICODE); ?>;

    if (window.Chart) {
        const isMobile = window.innerWidth <= 576;
        const buildGradient = (ctx, start, end) => {
            const gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, start);
            gradient.addColorStop(1, end);
            return gradient;
        };
        const baseGrid = { color: 'rgba(148, 163, 184, 0.25)' };
        const axisFont = { size: isMobile ? 9 : 12 };
        const mobileLabelLimit = isMobile ? 6 : undefined;
        const shortenLabel = (value) => {
            if (!isMobile || typeof value !== 'string') return value;
            return value.length > 12 ? `${value.slice(0, 12)}…` : value;
        };
        const yTickOptions = {
            font: axisFont,
            beginAtZero: true,
            ticks: { font: axisFont, maxTicksLimit: isMobile ? 4 : undefined }
        };
        const xTickOptions = {
            grid: { display: false },
            ticks: {
                font: axisFont,
                maxTicksLimit: isMobile ? 4 : undefined,
                callback: shortenLabel
            }
        };

        const productCostCtx = document.getElementById('productCostChart');
        if (productCostCtx) {
            const productCostGradient = buildGradient(productCostCtx.getContext('2d'), 'rgba(90, 77, 225, 0.6)', 'rgba(90, 77, 225, 0.1)');
            new Chart(productCostCtx, {
                type: 'bar',
                data: {
                    labels: productCostLabels,
                    datasets: [
                        {
                            label: 'Costo unitario',
                            data: productCostTotals,
                            backgroundColor: productCostGradient,
                            borderColor: '#5a4de1',
                            borderWidth: 1,
                            borderRadius: 8,
                            maxBarThickness: isMobile ? 14 : 30,
                            barPercentage: isMobile ? 0.65 : 0.85,
                            categoryPercentage: isMobile ? 0.7 : 0.8
                        },
                        {
                            label: 'Stock actual',
                            data: productCostStocks,
                            backgroundColor: 'rgba(34, 181, 154, 0.35)',
                            borderColor: '#22b59a',
                            borderWidth: 1,
                            borderRadius: 8,
                            maxBarThickness: isMobile ? 14 : 30,
                            barPercentage: isMobile ? 0.65 : 0.85,
                            categoryPercentage: isMobile ? 0.7 : 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, boxHeight: 10, font: axisFont, usePointStyle: true }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: axisFont, maxTicksLimit: mobileLabelLimit, callback: shortenLabel } },
                        y: { grid: baseGrid, beginAtZero: true, ticks: { font: axisFont, maxTicksLimit: isMobile ? 4 : undefined } }
                    }
                }
            });
        }

        const salesCtx = document.getElementById('salesByProductChart');
        if (salesCtx) {
            const salesBar = buildGradient(salesCtx.getContext('2d'), 'rgba(74, 163, 255, 0.6)', 'rgba(74, 163, 255, 0.15)');
            new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: salesLabels,
                    datasets: [
                        {
                            label: 'Ventas',
                            data: salesTotals,
                            backgroundColor: salesBar,
                            borderColor: '#4aa3ff',
                            borderWidth: 1,
                            borderRadius: 8,
                            maxBarThickness: isMobile ? 14 : 30,
                            barPercentage: isMobile ? 0.65 : 0.85,
                            categoryPercentage: isMobile ? 0.7 : 0.8
                        },
                        {
                            label: 'Unidades',
                            data: salesUnits,
                            backgroundColor: 'rgba(34, 181, 154, 0.35)',
                            borderColor: '#22b59a',
                            borderWidth: 1,
                            borderRadius: 8,
                            maxBarThickness: isMobile ? 14 : 30,
                            barPercentage: isMobile ? 0.65 : 0.85,
                            categoryPercentage: isMobile ? 0.7 : 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, boxHeight: 10, font: axisFont, usePointStyle: true }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: axisFont, maxTicksLimit: mobileLabelLimit, callback: shortenLabel } },
                        y: { grid: baseGrid, beginAtZero: true, ticks: { font: axisFont, maxTicksLimit: isMobile ? 4 : undefined } }
                    }
                }
            });
        }

        const profitCtx = document.getElementById('profitByProductChart');
        if (profitCtx) {
            const profitBar = buildGradient(profitCtx.getContext('2d'), 'rgba(243, 162, 87, 0.6)', 'rgba(243, 162, 87, 0.15)');
            const costBar = buildGradient(profitCtx.getContext('2d'), 'rgba(148, 163, 184, 0.5)', 'rgba(148, 163, 184, 0.15)');
            new Chart(profitCtx, {
                type: 'bar',
                data: {
                    labels: profitLabels,
                    datasets: [
                        {
                            label: 'Ganancia',
                            data: profitTotals,
                            backgroundColor: profitBar,
                            borderColor: '#f3a257',
                            borderWidth: 1,
                            borderRadius: 8,
                            maxBarThickness: isMobile ? 14 : 30,
                            barPercentage: isMobile ? 0.65 : 0.85,
                            categoryPercentage: isMobile ? 0.7 : 0.8
                        },
                        {
                            label: 'Costo total',
                            data: productCostTotals,
                            backgroundColor: costBar,
                            borderColor: '#94a3b8',
                            borderWidth: 1,
                            borderRadius: 8,
                            maxBarThickness: isMobile ? 14 : 30,
                            barPercentage: isMobile ? 0.65 : 0.85,
                            categoryPercentage: isMobile ? 0.7 : 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, boxHeight: 10, font: axisFont, usePointStyle: true }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: axisFont, maxTicksLimit: mobileLabelLimit, callback: shortenLabel } },
                        y: { grid: baseGrid, beginAtZero: true, ticks: { font: axisFont, maxTicksLimit: isMobile ? 4 : undefined } }
                    }
                }
            });
        }

        const lowStockCtx = document.getElementById('lowStockChart');
        if (lowStockCtx) {
            const lowStockCount = lowStockLabels.length;
            const safeCount = lowStockCount > 0 ? lowStockCount : 0;
            const totalCount = Math.max(totalProducts, safeCount);
            const remainder = Math.max(0, totalCount - safeCount);
            const donutPlugin = {
                id: 'centerText',
                afterDraw(chart) {
                    const { ctx } = chart;
                    const meta = chart.getDatasetMeta(0);
                    if (!meta?.data?.length) return;
                    ctx.save();
                    ctx.font = `${isMobile ? 20 : 28}px Nunito, sans-serif`;
                    ctx.fillStyle = '#f59e0b';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    const { x, y } = meta.data[0];
                    ctx.fillText(String(safeCount), x, y);
                    ctx.restore();
                }
            };

            new Chart(lowStockCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Stock bajo', 'OK'],
                    datasets: [{
                        data: [safeCount, remainder],
                        backgroundColor: ['#f59e0b', '#e2e8f0'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: isMobile ? '62%' : '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, boxHeight: 10, font: axisFont, usePointStyle: true }
                        }
                    }
                },
                plugins: [donutPlugin]
            });
        }
    }
</script>
