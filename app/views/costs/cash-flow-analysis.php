<div class="card mb-3">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="card-title mb-0">Análisis contable de entradas y salidas</h5>
            <small class="text-muted">Movimientos ordenados por fecha con cuenta bancaria, moneda y saldos.</small>
        </div>
        <a href="index.php?route=costs/export-cash-flow-excel" class="btn btn-success btn-sm">Exportar a Excel (CSV)</a>
    </div>
    <div class="card-body pt-2">
        <div class="d-flex gap-2 flex-wrap mb-3">
            <span class="badge bg-success-subtle text-success">Entradas: <?php echo e(format_currency((float)($cashSummary['entries'] ?? 0))); ?></span>
            <span class="badge bg-danger-subtle text-danger">Salidas: <?php echo e(format_currency((float)($cashSummary['exits'] ?? 0))); ?></span>
            <span class="badge bg-info-subtle text-info">Neto: <?php echo e(format_currency((float)($cashSummary['net'] ?? 0))); ?></span>
            <span class="badge bg-primary-subtle text-primary">Movimientos: <?php echo (int)($cashSummary['movements_count'] ?? 0); ?></span>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cuenta</th>
                        <th>Banco</th>
                        <th>Moneda</th>
                        <th>Referencia</th>
                        <th>Descripción</th>
                        <th class="text-end text-success">Entrada</th>
                        <th class="text-end text-danger">Salida</th>
                        <th class="text-end">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cashMovements)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-3">No hay movimientos bancarios para analizar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cashMovements as $movement): ?>
                            <tr>
                                <td><?php echo (int)($movement['id'] ?? 0); ?></td>
                                <td><?php echo e(format_date($movement['transaction_date'] ?? null)); ?></td>
                                <td><?php echo e($movement['account_name'] ?? '-'); ?></td>
                                <td><?php echo e($movement['bank_name'] ?? '-'); ?></td>
                                <td><?php echo e($movement['currency'] ?? 'CLP'); ?></td>
                                <td><?php echo e($movement['reference'] ?: '-'); ?></td>
                                <td><?php echo e($movement['description'] ?: '-'); ?></td>
                                <td class="text-end text-success"><?php echo e(format_currency((float)($movement['entry_amount'] ?? 0))); ?></td>
                                <td class="text-end text-danger"><?php echo e(format_currency((float)($movement['exit_amount'] ?? 0))); ?></td>
                                <td class="text-end"><?php echo e(format_currency((float)($movement['balance'] ?? 0))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
