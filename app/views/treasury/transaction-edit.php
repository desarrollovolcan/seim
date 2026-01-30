<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Editar movimiento bancario</h4>
        <a href="index.php?route=treasury/transactions" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=treasury/transactions/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)($transaction['id'] ?? 0); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Cuenta</label>
                    <input type="text" class="form-control" value="<?php echo e($transaction['account_name'] ?? ''); ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha</label>
                    <input type="text" class="form-control" value="<?php echo e(format_date($transaction['transaction_date'] ?? null)); ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <input type="text" class="form-control text-capitalize" value="<?php echo e($transaction['type'] ?? ''); ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Monto</label>
                    <input type="text" class="form-control" value="<?php echo e(format_currency((float)($transaction['amount'] ?? 0))); ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Referencia</label>
                    <input type="text" name="reference" class="form-control" value="<?php echo e($transaction['reference'] ?? ''); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo e($transaction['description'] ?? ''); ?></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'treasury/transaction-edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
