<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

if (!isset($_SESSION['user'])) {
    redirect('auth-2-sign-in.php');
}

$stats = [
    'products_total' => 0,
    'stock_value' => 0,
    'sales_day' => 0,
    'sales_month' => 0,
    'profit_day' => 0,
    'profit_month' => 0,
    'low_stock_total' => 0,
];
$lowStockProducts = [];
$recentMovements = [];
$currentUser = $_SESSION['user']['nombre'] ?? 'Usuario';
$currentUserLastName = $_SESSION['user']['apellido'] ?? '';
$currentUserFullName = trim($currentUser . ' ' . $currentUserLastName);

try {
    $stats['products_total'] = (int) db()->query('SELECT COUNT(*) FROM productos')->fetchColumn();
    $stats['stock_value'] = (float) db()->query(
        'SELECT COALESCE(SUM(sa.cantidad * p.costo_promedio), 0)
         FROM stock_actual sa
         INNER JOIN productos p ON p.id = sa.producto_id'
    )->fetchColumn();
    $stats['sales_day'] = (float) db()->query(
        'SELECT COALESCE(SUM(total), 0)
         FROM ventas
         WHERE fecha = CURDATE() AND estado = "registrada"'
    )->fetchColumn();
    $stats['sales_month'] = (float) db()->query(
        'SELECT COALESCE(SUM(total), 0)
         FROM ventas
         WHERE fecha >= DATE_FORMAT(CURDATE(), "%Y-%m-01") AND estado = "registrada"'
    )->fetchColumn();
    $stats['profit_day'] = (float) db()->query(
        'SELECT COALESCE(SUM((vd.precio_unitario - vd.costo_unitario) * vd.cantidad), 0)
         FROM ventas v
         INNER JOIN venta_detalles vd ON vd.venta_id = v.id
         WHERE v.fecha = CURDATE() AND v.estado = "registrada"'
    )->fetchColumn();
    $stats['profit_month'] = (float) db()->query(
        'SELECT COALESCE(SUM((vd.precio_unitario - vd.costo_unitario) * vd.cantidad), 0)
         FROM ventas v
         INNER JOIN venta_detalles vd ON vd.venta_id = v.id
         WHERE v.fecha >= DATE_FORMAT(CURDATE(), "%Y-%m-01") AND v.estado = "registrada"'
    )->fetchColumn();

    $stmt = db()->prepare(
        'SELECT p.nombre, p.stock_minimo, COALESCE(SUM(sa.cantidad), 0) AS stock_actual
         FROM productos p
         LEFT JOIN stock_actual sa ON sa.producto_id = p.id
         GROUP BY p.id
         HAVING stock_actual <= p.stock_minimo
         ORDER BY stock_actual ASC
         LIMIT 8'
    );
    $stmt->execute();
    $lowStockProducts = $stmt->fetchAll();
    $stats['low_stock_total'] = count($lowStockProducts);

    $stmt = db()->prepare(
        'SELECT m.tipo, m.referencia, m.cantidad, m.costo_unitario, m.created_at,
                p.nombre AS producto, b.nombre AS bodega
         FROM inventario_movimientos m
         LEFT JOIN productos p ON p.id = m.producto_id
         LEFT JOIN bodegas b ON b.id = m.bodega_id
         ORDER BY m.created_at DESC
         LIMIT 8'
    );
    $stmt->execute();
    $recentMovements = $stmt->fetchAll();
} catch (Exception $e) {
} catch (Error $e) {
}

include('partials/html.php');
?>

<head>
    <?php $title = "Panel"; include('partials/title-meta.php'); ?>

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

            <div class="container-fluid dashboard-compact">

                <?php $subtitle = "Resumen general"; $title = "Panel de control"; include('partials/page-title.php'); ?>

                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm dashboard-hero dashboard-card">
                            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <h4 class="mb-1">Panel de inventario y ventas</h4>
                                    <p class="text-muted mb-0">Resumen en tiempo real del estado del negocio.</p>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge bg-primary-subtle text-primary">Usuario conectado: <?php echo htmlspecialchars($currentUserFullName !== '' ? $currentUserFullName : 'Usuario', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="badge bg-info-subtle text-info">Productos con stock mínimo: <?php echo (int) $stats['low_stock_total']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Total de productos</p>
                                        <h4 class="mb-0"><?php echo (int) $stats['products_total']; ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                        <i class="ti ti-package fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Catálogo activo en inventario.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Stock valorizado</p>
                                        <h4 class="mb-0"><?php echo number_format($stats['stock_value'], 2, ',', '.'); ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                                        <i class="ti ti-coins fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Valor total de inventario.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Ventas del día</p>
                                        <h4 class="mb-0"><?php echo number_format($stats['sales_day'], 2, ',', '.'); ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center">
                                        <i class="ti ti-cash fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Ventas del mes: <?php echo number_format($stats['sales_month'], 2, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Utilidad del día</p>
                                        <h4 class="mb-0"><?php echo number_format($stats['profit_day'], 2, ',', '.'); ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center">
                                        <i class="ti ti-trending-up fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Utilidad del mes: <?php echo number_format($stats['profit_month'], 2, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-xl-5">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Productos con stock mínimo</h5>
                                <span class="text-muted small">Alertas</span>
                            </div>
                            <div class="card-body">
                                <?php if (empty($lowStockProducts)) : ?>
                                    <div class="text-muted">No hay productos con stock bajo.</div>
                                <?php else : ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th class="text-end">Stock actual</th>
                                                    <th class="text-end">Stock mínimo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($lowStockProducts as $product) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($product['nombre'] ?? 'Sin nombre', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end"><?php echo (int) ($product['stock_actual'] ?? 0); ?></td>
                                                        <td class="text-end"><?php echo (int) ($product['stock_minimo'] ?? 0); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Últimos movimientos</h5>
                                <span class="text-muted small">Entradas, ventas y traslados</span>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentMovements)) : ?>
                                    <div class="text-muted">No hay movimientos recientes.</div>
                                <?php else : ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Tipo</th>
                                                    <th>Producto</th>
                                                    <th>Bodega</th>
                                                    <th class="text-end">Cantidad</th>
                                                    <th class="text-end">Costo unitario</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentMovements as $movement) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars((string) ($movement['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($movement['tipo'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($movement['producto'] ?? 'Sin producto'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) ($movement['bodega'] ?? 'Sin bodega'), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end"><?php echo (int) ($movement['cantidad'] ?? 0); ?></td>
                                                        <td class="text-end"><?php echo number_format((float) ($movement['costo_unitario'] ?? 0), 2, ',', '.'); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
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

    <style>
        .dashboard-hero {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.08), rgba(13, 110, 253, 0.02));
        }

        .dashboard-compact .card-body {
            padding: 16px;
        }

        .dashboard-compact .card-header {
            padding: 14px 16px 0;
        }

        .dashboard-stat h4 {
            letter-spacing: -0.02em;
        }

        .dashboard-stat .avatar-sm {
            height: 44px;
            width: 44px;
        }

        .dashboard-stat .avatar-sm i {
            font-size: 20px;
        }

        .dashboard-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
        }

        @media (max-width: 767.98px) {
            .dashboard-stat {
                margin-bottom: 0;
            }

            .dashboard-stat-col {
                flex: 0 0 auto;
                width: 50%;
            }
        }
    </style>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
