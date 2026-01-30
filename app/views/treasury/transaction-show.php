<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Detalle movimiento bancario</h4>
        <a href="index.php?route=treasury/transactions" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted small">Cuenta</div>
                <div class="fw-semibold"><?php echo e($transaction['account_name'] ?? ''); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Fecha</div>
                <div class="fw-semibold"><?php echo e(format_date($transaction['transaction_date'] ?? null)); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Tipo</div>
                <div class="fw-semibold text-capitalize"><?php echo e($transaction['type'] ?? ''); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Monto</div>
                <div class="fw-semibold"><?php echo e(format_currency((float)($transaction['amount'] ?? 0))); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Saldo</div>
                <div class="fw-semibold"><?php echo e(format_currency((float)($transaction['balance'] ?? 0))); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Referencia</div>
                <div class="fw-semibold"><?php echo e($transaction['reference'] ?? ''); ?></div>
            </div>
            <div class="col-12">
                <div class="text-muted small">Descripción</div>
                <div class="fw-semibold"><?php echo e($transaction['description'] ?? ''); ?></div>
            </div>
        </div>
    </div>
</div>
