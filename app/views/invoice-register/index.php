<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Listado de facturas</h4>
        <div class="d-flex gap-2">
            <a href="index.php?route=invoice-register/create" class="btn btn-primary">Nueva factura</a>
            <a href="index.php?route=invoice-register/export&date_from=<?php echo urlencode($filters['date_from'] ?? ''); ?>&date_to=<?php echo urlencode($filters['date_to'] ?? ''); ?>&supplier=<?php echo urlencode($filters['supplier'] ?? ''); ?>&invoice_number=<?php echo urlencode($filters['invoice_number'] ?? ''); ?>" class="btn btn-success">Exportar Excel tabulado</a>
        </div>
    </div>
    <div class="card-body">
        <form class="row g-2 mb-3" method="get" action="index.php">
            <input type="hidden" name="route" value="invoice-register">
            <div class="col-md-2">
                <label class="form-label">Desde</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo e($filters['date_from'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Hasta</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo e($filters['date_to'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Proveedor</label>
                <input type="text" name="supplier" class="form-control" value="<?php echo e($filters['supplier'] ?? ''); ?>" placeholder="Nombre proveedor">
            </div>
            <div class="col-md-3">
                <label class="form-label">N° Factura</label>
                <input type="text" name="invoice_number" class="form-control" value="<?php echo e($filters['invoice_number'] ?? ''); ?>" placeholder="Buscar por número">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>N° Factura</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th class="text-end">Items</th>
                        <th class="text-end">Neto</th>
                        <th class="text-end">IVA</th>
                        <th class="text-end">Total</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-3">No hay facturas registradas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($records as $record): ?>
                            <tr>
                                <td><?php echo e(ucfirst($record['document_type'])); ?></td>
                                <td><?php echo e($record['invoice_number']); ?></td>
                                <td><?php echo e(format_date($record['invoice_date'])); ?></td>
                                <td>
                                    <?php echo e($record['supplier_name']); ?>
                                    <?php if (!empty($record['supplier_tax_id'])): ?>
                                        <div class="small text-muted">RUT: <?php echo e($record['supplier_tax_id']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end"><?php echo (int)($record['items_count'] ?? 0); ?></td>
                                <td class="text-end"><?php echo e(number_format((float)($record['net_amount'] ?? 0), 2, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format((float)($record['tax_amount'] ?? 0), 2, ',', '.')); ?></td>
                                <td class="text-end fw-semibold"><?php echo e(number_format((float)($record['total_amount'] ?? 0), 2, ',', '.')); ?></td>
                                <td>
                                    <?php foreach (($record['items'] ?? []) as $item): ?>
                                        <div class="small text-muted">
                                            • <?php echo e(ucfirst($item['item_type'])); ?>: <?php echo e($item['description']); ?> x<?php echo e((string)$item['quantity']); ?>
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
