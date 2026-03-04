<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Listado de boletas</h4>
        <div class="d-flex gap-2">
            <a href="index.php?route=petty-cash/create" class="btn btn-primary">Nueva boleta</a>
            <a href="index.php?route=petty-cash/export&date_from=<?php echo urlencode($filters['date_from'] ?? ''); ?>&date_to=<?php echo urlencode($filters['date_to'] ?? ''); ?>&supplier=<?php echo urlencode($filters['supplier'] ?? ''); ?>" class="btn btn-success">Exportar Excel tabulado</a>
        </div>
    </div>
    <div class="card-body">
        <form class="row g-2 mb-3" method="get" action="index.php">
            <input type="hidden" name="route" value="petty-cash">
            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo e($filters['date_from'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo e($filters['date_to'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Proveedor</label>
                <input type="text" name="supplier" class="form-control" value="<?php echo e($filters['supplier'] ?? ''); ?>" placeholder="Buscar proveedor">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>N° Boleta</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Moneda</th>
                        <th class="text-end">Items</th>
                        <th class="text-end">Total</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($receipts)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-3">No hay registros de caja chica.</td></tr>
                    <?php else: ?>
                        <?php foreach ($receipts as $receipt): ?>
                            <tr>
                                <td><?php echo e($receipt['receipt_number']); ?></td>
                                <td><?php echo e(format_date($receipt['receipt_date'])); ?></td>
                                <td><?php echo e($receipt['supplier_name']); ?></td>
                                <td><?php echo e($receipt['currency']); ?></td>
                                <td class="text-end"><?php echo (int)($receipt['items_count'] ?? 0); ?></td>
                                <td class="text-end"><?php echo e(number_format((float)($receipt['total_amount'] ?? 0), 2, ',', '.')); ?></td>
                                <td>
                                    <?php foreach (($receipt['items'] ?? []) as $item): ?>
                                        <div class="small text-muted">
                                            • <?php echo e($item['description']); ?> x<?php echo e((string)$item['quantity']); ?> <?php echo e($item['unit_measure'] ?? 'Unidad'); ?>
                                            <?php if (!empty($item['observation'])): ?>
                                                <em>(<?php echo e($item['observation']); ?>)</em>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
