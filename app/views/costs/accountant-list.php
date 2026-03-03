<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="card-title mb-0">Listado completo para contador</h5>
            <small class="text-muted">Registros ordenados por fecha y con información tributaria para exportación.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary">Total registros: <?php echo (int)count($purchases ?? []); ?></span>
            <a href="index.php?route=costs/export-purchases-excel" class="btn btn-success btn-sm">Exportar a Excel (CSV)</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Documento</th>
                        <th>Tipo DTE</th>
                        <th>Nro DTE</th>
                        <th>Proveedor</th>
                        <th>Fecha compra</th>
                        <th>Estado</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Impuesto</th>
                        <th class="text-end">Exento</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($purchases)): ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted py-3">No hay compras/gastos registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($purchases as $purchase): ?>
                            <tr>
                                <td><?php echo (int)($purchase['id'] ?? 0); ?></td>
                                <td><?php echo e($purchase['reference'] ?: ('Compra #' . (int)($purchase['id'] ?? 0))); ?></td>
                                <td><?php echo e($purchase['sii_document_type'] ?? '-'); ?></td>
                                <td><?php echo e($purchase['sii_document_number'] ?? '-'); ?></td>
                                <td><?php echo e($purchase['supplier_name'] ?? '-'); ?></td>
                                <td><?php echo e(format_date($purchase['purchase_date'] ?? null)); ?></td>
                                <td><?php echo e($purchase['status'] ?? 'pendiente'); ?></td>
                                <td class="text-end"><?php echo e(format_currency((float)($purchase['subtotal'] ?? 0))); ?></td>
                                <td class="text-end"><?php echo e(format_currency((float)($purchase['tax'] ?? 0))); ?></td>
                                <td class="text-end"><?php echo e(format_currency((float)($purchase['sii_exempt_amount'] ?? 0))); ?></td>
                                <td class="text-end"><?php echo e(format_currency((float)($purchase['total'] ?? 0))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
