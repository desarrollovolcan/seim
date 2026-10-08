<?php
// Datos de resumen financiero y comercial
$currentMonthSales = (float)($salesThisMonth['total'] ?? 0);
$lastMonthSales = (float)($salesLastMonth['total'] ?? 0);
$salesCountMonth = (int)($salesThisMonth['count'] ?? 0);
$salesGrowth = $lastMonthSales > 0 ? (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100 : ($currentMonthSales > 0 ? 100 : 0);

$grossProfit = (float)($profitRow['gross_profit'] ?? 0);
$totalSubtotal = (float)($profitRow['total_subtotal'] ?? 0);
$totalCost = (float)($profitRow['total_cost'] ?? 0);
$marginPercent = $totalSubtotal > 0 ? ($grossProfit / $totalSubtotal) * 100 : 0;

$pipelineAmount = (float)($quotesSummary['pipeline_amount'] ?? 0);
$pipelineCount = (int)($quotesSummary['pipeline_count'] ?? 0);
$totalQuotes = (int)($quotesSummary['total_quotes'] ?? 0);
$approvedCount = (int)($quotesSummary['approved_count'] ?? 0);
$winRate = $totalQuotes > 0 ? ($approvedCount / $totalQuotes) * 100 : 0;

$inventoryValuation = (float)($inventorySummary['valuation'] ?? 0);
$totalProducts = (int)($inventorySummary['total_products'] ?? 0);
$lowStockCount = (int)($inventorySummary['low_stock_count'] ?? 0);
$outOfStockCount = (int)($inventorySummary['out_of_stock_count'] ?? 0);

// Preparación de datasets para Chart.js
$monthlyLabels = [];
$monthlySalesData = [];
$monthlyCostData = [];
$monthlyProfitData = [];
foreach (($monthlySales ?? []) as $ms) {
    $monthlyLabels[] = $ms['month_name'] ?? $ms['ym'] ?? '';
    $monthlySalesData[] = (float)($ms['sales_total'] ?? 0);
    $monthlyCostData[] = (float)($ms['cost_total'] ?? 0);
    $monthlyProfitData[] = (float)($ms['profit_total'] ?? 0);
}

$familyLabels = [];
$familyData = [];
foreach (($salesByFamily ?? []) as $f) {
    $familyLabels[] = $f['family_name'] ?? 'General';
    $familyData[] = (float)($f['total_amount'] ?? 0);
}

$topProdLabels = [];
$topProdSales = [];
$topProdQty = [];
foreach (($topProducts ?? []) as $tp) {
    $topProdLabels[] = mb_strimwidth($tp['name'] ?? '', 0, 22, '...');
    $topProdSales[] = (float)($tp['total'] ?? 0);
    $topProdQty[] = (int)($tp['quantity'] ?? 0);
}

$quoteStatusLabels = [];
$quoteStatusCounts = [];
$quoteStatusTotals = [];
$statusColorMap = [
    'aprobada' => '#10b981',
    'en_curso' => '#3b82f6',
    'enviada' => '#f59e0b',
    'creada' => '#6366f1',
    'rechazada' => '#ef4444',
    'vencida' => '#64748b'
];
$statusColors = [];
foreach (($quotesByStatus ?? []) as $qs) {
    $st = strtolower($qs['estado'] ?? 'creada');
    $quoteStatusLabels[] = ucfirst(str_replace('_', ' ', $st));
    $quoteStatusCounts[] = (int)($qs['count'] ?? 0);
    $quoteStatusTotals[] = (float)($qs['total'] ?? 0);
    $statusColors[] = $statusColorMap[$st] ?? '#94a3b8';
}
?>

<div class="seim-dashboard">
    <!-- Header con Accesos Rápidos -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Panel de Control</h4>
            <p class="text-muted mb-0 fs-13">Visión ejecutiva de ventas, finanzas, pipeline e inventario en tiempo real.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
            <a href="index.php?route=sales/create" class="btn btn-primary btn-sm flex-fill flex-md-grow-0 d-inline-flex align-items-center justify-content-center gap-1 shadow-sm">
                <i class="ti ti-plus fs-15"></i> Nueva Venta
            </a>
            <a href="index.php?route=quotes/create" class="btn btn-outline-primary btn-sm flex-fill flex-md-grow-0 d-inline-flex align-items-center justify-content-center gap-1 shadow-sm">
                <i class="ti ti-file-invoice fs-15"></i> Cotización
            </a>
            <a href="index.php?route=products/create" class="btn btn-outline-secondary btn-sm flex-fill flex-md-grow-0 d-inline-flex align-items-center justify-content-center gap-1 shadow-sm">
                <i class="ti ti-box fs-15"></i> Producto
            </a>
        </div>
    </div>

    <!-- Tarjetas KPI Principales -->
    <div class="row g-3 mb-4">
        <!-- KPI 1: Facturación del Mes -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-12 text-uppercase fw-semibold text-muted tracking-wide">Ventas del Mes</span>
                        <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="ti ti-currency-dollar fs-18"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h3 class="fw-bold mb-0 text-dark"><?php echo e(format_currency($currentMonthSales)); ?></h3>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-muted fs-12 pt-1 border-top border-light">
                        <span>
                            <?php if ($salesGrowth >= 0): ?>
                                <span class="text-success fw-semibold"><i class="ti ti-arrow-up-right"></i> +<?php echo number_format($salesGrowth, 1); ?>%</span>
                            <?php else: ?>
                                <span class="text-danger fw-semibold"><i class="ti ti-arrow-down-right"></i> <?php echo number_format($salesGrowth, 1); ?>%</span>
                            <?php endif; ?>
                            vs mes ant.
                        </span>
                        <span class="fw-medium text-secondary"><?php echo $salesCountMonth; ?> pedidos</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 2: Ganancia Bruta y Margen -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-12 text-uppercase fw-semibold text-muted tracking-wide">Utilidad Bruta</span>
                        <div class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="ti ti-trending-up fs-18"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h3 class="fw-bold mb-0 text-dark"><?php echo e(format_currency($grossProfit)); ?></h3>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-muted fs-12 pt-1 border-top border-light">
                        <span>Margen promedio:</span>
                        <span class="badge bg-success-subtle text-success fw-bold fs-11 px-2 py-1"><?php echo number_format($marginPercent, 1); ?>%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 3: Pipeline de Cotizaciones -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-12 text-uppercase fw-semibold text-muted tracking-wide">Pipeline Cotizaciones</span>
                        <div class="avatar-sm rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="ti ti-file-text fs-18"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h3 class="fw-bold mb-0 text-dark"><?php echo e(format_currency($pipelineAmount)); ?></h3>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-muted fs-12 pt-1 border-top border-light">
                        <span class="text-secondary"><?php echo $pipelineCount; ?> en negociación</span>
                        <span>Conversión: <strong class="text-dark"><?php echo number_format($winRate, 1); ?>%</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 4: Inventario y Stock -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-12 text-uppercase fw-semibold text-muted tracking-wide">Valoración Stock</span>
                        <div class="avatar-sm rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="ti ti-package fs-18"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h3 class="fw-bold mb-0 text-dark"><?php echo e(format_currency($inventoryValuation)); ?></h3>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-muted fs-12 pt-1 border-top border-light">
                        <span class="text-secondary"><?php echo $totalProducts; ?> productos activos</span>
                        <?php if ($lowStockCount > 0): ?>
                            <span class="badge bg-danger-subtle text-danger fw-bold fs-11 px-2 py-1"><i class="ti ti-alert-triangle"></i> <?php echo $lowStockCount; ?> alertas</span>
                        <?php else: ?>
                            <span class="badge bg-success-subtle text-success fs-11 px-2 py-1">Stock OK</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila Gráficos 1: Evolución Financiera y Ventas por Familia -->
    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title fw-bold text-dark mb-0">Evolución Comercial & Financiera</h6>
                        <span class="text-muted fs-12">Histórico de Ventas, Costos y Utilidad Bruta (Últimos meses)</span>
                    </div>
                    <a href="index.php?route=sales" class="btn btn-sm btn-link text-primary p-0 fs-12 text-decoration-none">Ver ventas &rarr;</a>
                </div>
                <div class="card-body px-3 pb-3 pt-0">
                    <div style="height: 280px; position: relative;">
                        <canvas id="chartFinancialEvolution"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title fw-bold text-dark mb-0">Ventas por Familia</h6>
                        <span class="text-muted fs-12">Participación por categoría de producto</span>
                    </div>
                    <a href="index.php?route=product-families" class="btn btn-sm btn-link text-primary p-0 fs-12 text-decoration-none">Familias &rarr;</a>
                </div>
                <div class="card-body px-3 pb-3 pt-0 d-flex flex-column justify-content-center">
                    <div style="height: 240px; position: relative;">
                        <canvas id="chartSalesByFamily"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila Gráficos 2: Top Productos y Pipeline de Cotizaciones -->
    <div class="row g-3 mb-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title fw-bold text-dark mb-0">Top 5 Productos Más Vendidos</h6>
                        <span class="text-muted fs-12">Mayores ingresos generados</span>
                    </div>
                    <a href="index.php?route=products" class="btn btn-sm btn-link text-primary p-0 fs-12 text-decoration-none">Catálogo &rarr;</a>
                </div>
                <div class="card-body px-3 pb-3 pt-0">
                    <div style="height: 240px; position: relative;">
                        <canvas id="chartTopProducts"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title fw-bold text-dark mb-0">Estado del Pipeline Comercial</h6>
                        <span class="text-muted fs-12">Cotizaciones agrupadas por etapa de cierre</span>
                    </div>
                    <a href="index.php?route=quotes" class="btn btn-sm btn-link text-primary p-0 fs-12 text-decoration-none">Cotizaciones &rarr;</a>
                </div>
                <div class="card-body px-3 pb-3 pt-0 d-flex flex-column justify-content-center">
                    <div style="height: 240px; position: relative;">
                        <canvas id="chartQuotePipeline"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila Tablas Operativas: Ventas Recientes, Cotizaciones en Seguimiento, Stock Crítico -->
    <div class="row g-3">
        <!-- Columna 1: Ventas Recientes -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-2 px-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="ti ti-receipt text-primary"></i> Últimas Ventas
                    </h6>
                    <a href="index.php?route=sales" class="btn btn-sm btn-outline-primary py-0 px-2 fs-11">Ver todas</a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentSales)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 fs-12">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">N° / Cliente</th>
                                        <th class="text-end pe-3">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentSales as $sale): ?>
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-semibold text-dark">
                                                    <a href="index.php?route=sales/show&id=<?php echo (int)$sale['id']; ?>" class="text-dark text-decoration-none">
                                                        #<?php echo e($sale['numero'] ?? ('V-' . $sale['id'])); ?>
                                                    </a>
                                                </div>
                                                <div class="text-muted fs-11 text-truncate" style="max-width: 160px;">
                                                    <?php echo e($sale['client_name'] ?? 'Consumidor final'); ?>
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="fw-bold text-dark"><?php echo e(format_currency($sale['total'] ?? 0)); ?></div>
                                                <span class="badge bg-success-subtle text-success fs-10 px-1 py-0">Emitida</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted fs-13">
                            <i class="ti ti-shopping-cart-off fs-24 d-block mb-1 opacity-50"></i>
                            Sin ventas registradas aún.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Columna 2: Cotizaciones en Seguimiento -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-2 px-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="ti ti-clock-check text-info"></i> Cotizaciones Activas
                    </h6>
                    <a href="index.php?route=quotes" class="btn btn-sm btn-outline-info py-0 px-2 fs-11">Ver todas</a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($actionableQuotes)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 fs-12">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">N° / Cliente</th>
                                        <th class="text-end pe-3">Monto / Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($actionableQuotes as $quote): ?>
                                        <?php
                                        $qStatus = strtolower($quote['estado'] ?? 'creada');
                                        $badgeClass = match($qStatus) {
                                            'aprobada' => 'bg-success-subtle text-success',
                                            'en_curso', 'enviada' => 'bg-info-subtle text-info',
                                            'rechazada' => 'bg-danger-subtle text-danger',
                                            default => 'bg-light text-secondary'
                                        };
                                        ?>
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-semibold">
                                                    <a href="index.php?route=quotes/show&id=<?php echo (int)$quote['id']; ?>" class="text-dark text-decoration-none">
                                                        #<?php echo e($quote['numero'] ?? ('COT-' . $quote['id'])); ?>
                                                    </a>
                                                </div>
                                                <div class="text-muted fs-11 text-truncate" style="max-width: 160px;">
                                                    <?php echo e($quote['client_name'] ?? 'Cliente general'); ?>
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="fw-bold text-dark"><?php echo e(format_currency($quote['total'] ?? 0)); ?></div>
                                                <span class="badge <?php echo $badgeClass; ?> fs-10 px-1 py-0">
                                                    <?php echo ucfirst(str_replace('_', ' ', $qStatus)); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted fs-13">
                            <i class="ti ti-file-off fs-24 d-block mb-1 opacity-50"></i>
                            Sin cotizaciones activas.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Columna 3: Alertas de Stock Crítico -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-2 px-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="ti ti-alert-circle text-danger"></i> Stock Crítico
                    </h6>
                    <a href="index.php?route=products" class="btn btn-sm btn-outline-danger py-0 px-2 fs-11">Inventario</a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($criticalStock)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 fs-12">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Producto</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-end pe-3">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($criticalStock as $item): ?>
                                        <?php
                                        $stock = (int)($item['stock'] ?? 0);
                                        $stockMin = (int)($item['stock_min'] ?? 0);
                                        $isZero = $stock <= 0;
                                        ?>
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-semibold text-dark text-truncate" style="max-width: 140px;" title="<?php echo e($item['name']); ?>">
                                                    <?php echo e($item['name']); ?>
                                                </div>
                                                <div class="text-muted fs-11">SKU: <?php echo e($item['sku'] ?: '-'); ?></div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?php echo $isZero ? 'bg-danger text-white' : 'bg-warning-subtle text-warning'; ?> fw-bold fs-11 px-2 py-0">
                                                    <?php echo $stock; ?> / <?php echo $stockMin; ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="index.php?route=products/edit&id=<?php echo (int)$item['id']; ?>" class="btn btn-xs btn-outline-secondary py-0 px-2 fs-11" title="Editar / Reponer">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-success fs-13">
                            <i class="ti ti-circle-check fs-24 d-block mb-1"></i>
                            Todo el stock se encuentra en niveles normales.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js 4.x -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Chart) return;

    // Configuración compartida
    const isMobile = window.innerWidth < 768;
    const fontConfig = { family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif', size: isMobile ? 10 : 12 };
    const currencyFormatter = (val) => '$' + Number(val || 0).toLocaleString('es-CL');

    // 1. Gráfico Evolución Financiera (Barras + Líneas)
    const financialLabels = <?php echo json_encode($monthlyLabels, JSON_UNESCAPED_UNICODE); ?>;
    const salesData = <?php echo json_encode($monthlySalesData); ?>;
    const costData = <?php echo json_encode($monthlyCostData); ?>;
    const profitData = <?php echo json_encode($monthlyProfitData); ?>;

    const ctxFinancial = document.getElementById('chartFinancialEvolution');
    if (ctxFinancial) {
        new Chart(ctxFinancial, {
            type: 'bar',
            data: {
                labels: financialLabels.length > 0 ? financialLabels : ['Sin datos'],
                datasets: [
                    {
                        label: 'Ventas Totales',
                        data: salesData.length > 0 ? salesData : [0],
                        backgroundColor: 'rgba(37, 99, 235, 0.85)',
                        borderColor: '#2563eb',
                        borderRadius: 5,
                        order: 2
                    },
                    {
                        label: 'Costo de Ventas',
                        data: costData.length > 0 ? costData : [0],
                        backgroundColor: 'rgba(239, 68, 68, 0.75)',
                        borderColor: '#ef4444',
                        borderRadius: 5,
                        order: 3
                    },
                    {
                        label: 'Utilidad Bruta',
                        data: profitData.length > 0 ? profitData : [0],
                        type: 'line',
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
                        pointRadius: 4,
                        fill: false,
                        tension: 0.3,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: fontConfig, usePointStyle: true, boxWidth: 8 }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: ${currencyFormatter(ctx.raw)}`
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: fontConfig } },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: fontConfig,
                            callback: (val) => '$' + Number(val).toLocaleString('es-CL', { notation: 'compact' })
                        },
                        grid: { color: 'rgba(226, 232, 240, 0.6)' }
                    }
                }
            }
        });
    }

    // 2. Gráfico Ventas por Familia (Doughnut)
    const famLabels = <?php echo json_encode($familyLabels, JSON_UNESCAPED_UNICODE); ?>;
    const famData = <?php echo json_encode($familyData); ?>;
    const famPalette = ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe', '#e2e8f0'];

    const ctxFamily = document.getElementById('chartSalesByFamily');
    if (ctxFamily) {
        new Chart(ctxFamily, {
            type: 'doughnut',
            data: {
                labels: famLabels.length > 0 ? famLabels : ['Sin datos'],
                datasets: [{
                    data: famData.length > 0 ? famData : [1],
                    backgroundColor: famPalette,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: fontConfig, usePointStyle: true, boxWidth: 8 }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.label}: ${currencyFormatter(ctx.raw)}`
                        }
                    }
                }
            }
        });
    }

    // 3. Top 5 Productos (Horizontal Bar)
    const topLabels = <?php echo json_encode($topProdLabels, JSON_UNESCAPED_UNICODE); ?>;
    const topSales = <?php echo json_encode($topProdSales); ?>;

    const ctxTop = document.getElementById('chartTopProducts');
    if (ctxTop) {
        new Chart(ctxTop, {
            type: 'bar',
            data: {
                labels: topLabels.length > 0 ? topLabels : ['Sin datos'],
                datasets: [{
                    label: 'Facturación ($)',
                    data: topSales.length > 0 ? topSales : [0],
                    backgroundColor: 'rgba(59, 130, 246, 0.85)',
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` Ventas: ${currencyFormatter(ctx.raw)}`
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            font: fontConfig,
                            callback: (val) => '$' + Number(val).toLocaleString('es-CL', { notation: 'compact' })
                        },
                        grid: { color: 'rgba(226, 232, 240, 0.6)' }
                    },
                    y: { grid: { display: false }, ticks: { font: fontConfig } }
                }
            }
        });
    }

    // 4. Pipeline de Cotizaciones (Doughnut)
    const pipeLabels = <?php echo json_encode($quoteStatusLabels, JSON_UNESCAPED_UNICODE); ?>;
    const pipeCounts = <?php echo json_encode($quoteStatusCounts); ?>;
    const pipeColors = <?php echo json_encode($statusColors); ?>;

    const ctxPipe = document.getElementById('chartQuotePipeline');
    if (ctxPipe) {
        new Chart(ctxPipe, {
            type: 'doughnut',
            data: {
                labels: pipeLabels.length > 0 ? pipeLabels : ['Sin cotizaciones'],
                datasets: [{
                    data: pipeCounts.length > 0 ? pipeCounts : [1],
                    backgroundColor: pipeColors.length > 0 ? pipeColors : ['#cbd5e1'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: fontConfig, usePointStyle: true, boxWidth: 8 }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.label}: ${ctx.raw} cotización(es)`
                        }
                    }
                }
            }
        });
    }
});
</script>
