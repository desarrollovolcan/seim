<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Pagos realizados</h4>
    </div>
    <div class="card-body">
        <?php if (empty($payments)): ?>
            <div class="text-muted">No hay pagos registrados.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-centered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Fecha pago</th>
                            <th>Método</th>
                            <th>Referencia</th>
                            <th class="text-end">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td>#<?php echo e($payment['invoice_number'] ?? $payment['invoice_id']); ?></td>
                                <td><?php echo e($payment['client_name'] ?? ''); ?></td>
                                <td><?php echo e($payment['fecha_pago'] ?? ''); ?></td>
                                <td><?php echo e($payment['metodo'] ?? ''); ?></td>
                                <td><?php echo e($payment['referencia'] ?? '-'); ?></td>
                                <td class="text-end"><?php echo e(format_currency((float)($payment['monto'] ?? 0))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
