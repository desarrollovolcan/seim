<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h4 class="card-title mb-0">Análisis de ganancias</h4>
            <p class="text-muted mb-0">Comparativa entre precio de venta, proveedor y competencia.</p>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-end">Precio proveedor</th>
                        <th class="text-end">Precio competencia</th>
                        <th class="text-end">Precio venta</th>
                        <th class="text-end">Ganancia vs proveedor</th>
                        <th class="text-end">Ganancia vs competencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($analysis)): ?>
                        <tr>
                            <td colspan="6" class="text-muted text-center">Sin productos para analizar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($analysis as $row): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?php echo e($row['product']['name'] ?? ''); ?></div>
                                    <div class="text-muted small"><?php echo e($row['product']['sku'] ?? ''); ?></div>
                                </td>
                                <td class="text-end"><?php echo e(format_currency($row['supplier_price'])); ?></td>
                                <td class="text-end"><?php echo e(format_currency($row['competition_price'])); ?></td>
                                <td class="text-end"><?php echo e(format_currency($row['sale_price'])); ?></td>
                                <td class="text-end">
                                    <?php echo e(format_currency($row['profit_supplier'])); ?>
                                    <?php if ($row['profit_supplier_pct'] !== null): ?>
                                        <div class="text-muted small"><?php echo e(number_format($row['profit_supplier_pct'], 2)); ?>%</div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php echo e(format_currency($row['profit_competition'])); ?>
                                    <?php if ($row['profit_competition_pct'] !== null): ?>
                                        <div class="text-muted small"><?php echo e(number_format($row['profit_competition_pct'], 2)); ?>%</div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
