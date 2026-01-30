<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Consumos de producción</h4>
        <a href="index.php?route=production" class="btn btn-light">Volver a producción</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Costo unitario</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo e(format_date($row['production_date'] ?? null)); ?></td>
                            <td><?php echo e($row['product_name'] ?? ''); ?></td>
                            <td class="text-end"><?php echo (int)($row['quantity'] ?? 0); ?></td>
                            <td class="text-end"><?php echo e(format_currency((float)($row['unit_cost'] ?? 0))); ?></td>
                            <td class="text-end"><?php echo e(format_currency((float)($row['subtotal'] ?? 0))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
