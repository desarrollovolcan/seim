<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Stock de productos producidos</h4>
        <a href="index.php?route=production" class="btn btn-light">Volver a producción</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th class="text-end">Cantidad producida</th>
                        <th class="text-end">Stock actual</th>
                        <th class="text-end">Costo unitario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo e($row['name'] ?? ''); ?></td>
                            <td><?php echo e($row['sku'] ?? '-'); ?></td>
                            <td class="text-end"><?php echo (int)($row['produced_quantity'] ?? 0); ?></td>
                            <td class="text-end"><?php echo (int)($row['stock'] ?? 0); ?></td>
                            <td class="text-end"><?php echo e(format_currency((float)($row['cost'] ?? 0))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
