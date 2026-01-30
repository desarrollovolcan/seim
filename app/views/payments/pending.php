<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Pagos pendientes</h4>
    </div>
    <div class="card-body">
        <?php if (empty($pendingInvoices)): ?>
            <div class="text-muted">No hay facturas con saldo pendiente.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-centered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Emisión</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Pagado</th>
                            <th class="text-end">Pendiente</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingInvoices as $invoice): ?>
                            <?php
                            $status = $invoice['estado'] ?? 'pendiente';
                            $badgeClass = $status === 'pagada' ? 'success' : ($status === 'vencida' ? 'danger' : 'warning');
                            ?>
                            <tr>
                                <td>#<?php echo e($invoice['numero'] ?? ''); ?></td>
                                <td><?php echo e($invoice['client_name'] ?? ''); ?></td>
                                <td><?php echo e($invoice['fecha_emision'] ?? ''); ?></td>
                                <td><?php echo e($invoice['fecha_vencimiento'] ?? ''); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $badgeClass; ?>-subtle text-<?php echo $badgeClass; ?>">
                                        <?php echo e(ucfirst($status)); ?>
                                    </span>
                                </td>
                                <td class="text-end"><?php echo e(format_currency((float)($invoice['total'] ?? 0))); ?></td>
                                <td class="text-end"><?php echo e(format_currency((float)($invoice['paid_total'] ?? 0))); ?></td>
                                <td class="text-end"><?php echo e(format_currency((float)($invoice['pending_total'] ?? 0))); ?></td>
                                <td>
                                    <a class="btn btn-soft-primary btn-sm" href="index.php?route=invoices/show&id=<?php echo (int)$invoice['id']; ?>">Ver factura</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
