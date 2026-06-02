<?php
$totalSales = 0.0;
$totalProfit = 0.0;
$lowStockCount = 0;
$totalProducts = max(1, count($inventoryProducts ?? []));

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

$marginPercent = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;
$avgSale = $totalSales / $totalProducts;
$riskPercent = ($lowStockCount / $totalProducts) * 100;

$productLabels = [];
$productCosts = [];
$productUnits = [];
$productStocks = [];
foreach (($inventoryProducts ?? []) as $item) {
    $productLabels[] = $item['name'] ?? '';
    $productCosts[] = (float)($item['cost'] ?? 0);
    $productUnits[] = (int)($item['stock'] ?? 0);
    $productStocks[] = (int)($item['stock'] ?? 0);
}

$salesLabels = [];
$salesTotals = [];
$salesQty = [];
foreach (($salesByProduct ?? []) as $item) {
    $salesLabels[] = $item['name'] ?? '';
    $salesTotals[] = (float)($item['total'] ?? 0);
    $salesQty[] = (int)($item['quantity'] ?? 0);
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

<div class="seim-dashboard mt-2">
    <div class="card seim-panel-card mb-3">
        <div class="card-body py-3">
            <div class="row g-3">
                <div class="col-6 col-xl-3">
                    <div class="seim-kpi-title">Productos</div>
                    <div class="seim-kpi-value"><?php echo (int)$totalProducts; ?></div>
                    <div class="seim-kpi-split">Catálogo activo</div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="seim-kpi-title">Ingreso promedio</div>
                    <div class="seim-kpi-value"><?php echo e(format_currency($avgSale)); ?></div>
                    <div class="seim-kpi-split">Ventas: <strong><?php echo e(format_currency($totalSales)); ?></strong></div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="seim-kpi-title">Ingreso de capital</div>
                    <div class="seim-kpi-value"><?php echo e(format_currency($totalProfit)); ?></div>
                    <div class="seim-kpi-split">Margen: <strong><?php echo e(number_format($marginPercent, 1, ',', '.')); ?>%</strong></div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="seim-kpi-title">Riesgo inventario</div>
                    <div class="seim-kpi-value"><?php echo (int)$lowStockCount; ?></div>
                    <div class="seim-kpi-split">Impacto: <strong><?php echo e(number_format($riskPercent, 1, ',', '.')); ?>%</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card seim-panel-card h-100">
                <div class="card-body">
                    <h5 class="seim-panel-title">Demografía comercial anual</h5>
                    <p class="seim-panel-subtitle">Costos vs stock por producto</p>
                    <div class="seim-chart-lg"><canvas id="seimMainBars"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card seim-panel-card h-100">
                <div class="card-body">
                    <h5 class="seim-panel-title">Stock por producto</h5>
                    <p class="seim-panel-subtitle">Unidades disponibles</p>
                    <div class="seim-chart-sm"><canvas id="seimDonutOne"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-xl-4">
            <div class="card seim-panel-card h-100">
                <div class="card-body">
                    <h5 class="seim-panel-title">Ventas por línea</h5>
                    <div class="seim-chart-sm"><canvas id="seimSalesBars"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card seim-panel-card h-100">
                <div class="card-body">
                    <h5 class="seim-panel-title">Mix de stock bajo</h5>
                    <div class="seim-chart-sm"><canvas id="seimDonutTwo"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card seim-panel-card h-100">
                <div class="card-body">
                    <h5 class="seim-panel-title">Ganancia por línea</h5>
                    <div class="seim-chart-sm"><canvas id="seimProfitBars"></canvas></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const productLabels = <?php echo json_encode($productLabels, JSON_UNESCAPED_UNICODE); ?>;
const productCosts = <?php echo json_encode($productCosts, JSON_UNESCAPED_UNICODE); ?>;
const productStocks = <?php echo json_encode($productStocks, JSON_UNESCAPED_UNICODE); ?>;
const productUnits = <?php echo json_encode($productUnits, JSON_UNESCAPED_UNICODE); ?>;
const salesLabels = <?php echo json_encode($salesLabels, JSON_UNESCAPED_UNICODE); ?>;
const salesTotals = <?php echo json_encode($salesTotals, JSON_UNESCAPED_UNICODE); ?>;
const salesQty = <?php echo json_encode($salesQty, JSON_UNESCAPED_UNICODE); ?>;
const profitLabels = <?php echo json_encode($profitLabels, JSON_UNESCAPED_UNICODE); ?>;
const profitTotals = <?php echo json_encode($profitTotals, JSON_UNESCAPED_UNICODE); ?>;
const lowStockLabels = <?php echo json_encode($lowStockLabels, JSON_UNESCAPED_UNICODE); ?>;
const lowStockValues = <?php echo json_encode($lowStockValues, JSON_UNESCAPED_UNICODE); ?>;
const lowStockMinimums = <?php echo json_encode($lowStockMinimums, JSON_UNESCAPED_UNICODE); ?>;

if (window.Chart) {
    const compact = window.innerWidth < 768;
    const commonLegend = { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: compact ? 9 : 11 } } };

    new Chart(document.getElementById('seimMainBars'), {
        type: 'bar',
        data: {
            labels: productLabels,
            datasets: [
                { label: 'Costo unitario', data: productCosts, backgroundColor: '#4558d4', borderRadius: 6 },
                { label: 'Stock', data: productStocks, backgroundColor: '#e252b2', borderRadius: 6 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: commonLegend }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('seimDonutOne'), {
        type: 'doughnut',
        data: { labels: productLabels, datasets: [{ data: productUnits, backgroundColor: ['#4558d4', '#6d7df0', '#8c9bff', '#adb8ff', '#cfd5ff'] }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: commonLegend } }
    });

    new Chart(document.getElementById('seimSalesBars'), {
        type: 'bar',
        data: { labels: salesLabels, datasets: [{ label: 'Ventas', data: salesTotals, backgroundColor: '#4558d4', borderRadius: 6 }, { label: 'Unidades', data: salesQty, backgroundColor: '#e252b2', borderRadius: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: commonLegend }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('seimDonutTwo'), {
        type: 'doughnut',
        data: { labels: lowStockLabels, datasets: [{ data: lowStockMinimums.map((m, i) => Math.max(m - (lowStockValues[i] || 0), 0)), backgroundColor: ['#4558d4', '#5f71e5', '#7889f6', '#91a0ff', '#abb7ff'] }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: commonLegend } }
    });

    new Chart(document.getElementById('seimProfitBars'), {
        type: 'bar',
        data: { labels: profitLabels, datasets: [{ label: 'Ganancia', data: profitTotals, backgroundColor: '#e252b2', borderRadius: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: commonLegend }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } }
    });
}
</script>
