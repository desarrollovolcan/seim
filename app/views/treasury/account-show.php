<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Detalle cuenta bancaria</h4>
        <a href="index.php?route=treasury/accounts" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small">Cuenta</div>
                <div class="fw-semibold"><?php echo e($account['name'] ?? ''); ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Banco</div>
                <div class="fw-semibold"><?php echo e($account['bank_name'] ?? ''); ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Moneda</div>
                <div class="fw-semibold"><?php echo e($account['currency'] ?? ''); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Número cuenta</div>
                <div class="fw-semibold"><?php echo e($account['account_number'] ?? ''); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Saldo actual</div>
                <div class="fw-semibold"><?php echo e(format_currency((float)($account['current_balance'] ?? 0))); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Movimientos recientes</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Monto</th>
                        <th>Saldo</th>
                        <th>Referencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo e(format_date($transaction['transaction_date'] ?? null)); ?></td>
                            <td class="text-capitalize"><?php echo e($transaction['type'] ?? ''); ?></td>
                            <td><?php echo e(format_currency((float)($transaction['amount'] ?? 0))); ?></td>
                            <td><?php echo e(format_currency((float)($transaction['balance'] ?? 0))); ?></td>
                            <td><?php echo e($transaction['reference'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay movimientos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
