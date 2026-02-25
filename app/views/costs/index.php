<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Documentos del mes</h6>
                <h3 class="mb-0"><?php echo (int)($summary['documents_count'] ?? 0); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total gastos del mes</h6>
                <h3 class="mb-0"><?php echo e(format_currency((float)($summary['total_amount'] ?? 0))); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Pendiente por pagar</h6>
                <h3 class="mb-0"><?php echo e(format_currency((float)($summary['pending_amount'] ?? 0))); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Accesos rápidos de costos</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="index.php?route=purchases/create" class="btn btn-primary">Registrar compra / gasto</a>
                <a href="index.php?route=purchase-orders/create" class="btn btn-outline-primary">Nueva orden de compra</a>
                <a href="index.php?route=suppliers/create" class="btn btn-outline-secondary">Crear proveedor</a>
                <a href="index.php?route=treasury/transactions" class="btn btn-outline-secondary">Movimientos de tesorería</a>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Últimos documentos de gastos</h5>
                <a href="index.php?route=purchases" class="btn btn-soft-primary btn-sm">Ver todos</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Documento</th>
                                <th>Proveedor</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentPurchases)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Sin compras registradas todavía.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentPurchases as $purchase): ?>
                                    <tr>
                                        <td><?php echo e($purchase['reference'] ?: ('Compra #' . (int)$purchase['id'])); ?></td>
                                        <td><?php echo e($purchase['supplier_name'] ?? '-'); ?></td>
                                        <td><?php echo e(format_date($purchase['purchase_date'] ?? null)); ?></td>
                                        <td><?php echo e($purchase['status'] ?? 'pendiente'); ?></td>
                                        <td class="text-end"><?php echo e(format_currency((float)($purchase['total'] ?? 0))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
