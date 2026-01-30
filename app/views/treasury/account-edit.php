<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Editar cuenta bancaria</h4>
        <a href="index.php?route=treasury/accounts" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=treasury/accounts/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)($account['id'] ?? 0); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($account['name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Banco</label>
                    <input type="text" name="bank_name" class="form-control" value="<?php echo e($account['bank_name'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Número cuenta</label>
                    <input type="text" name="account_number" class="form-control" value="<?php echo e($account['account_number'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Moneda</label>
                    <select name="currency" class="form-select">
                        <?php foreach (['CLP', 'USD', 'EUR', 'UF'] as $currency): ?>
                            <option value="<?php echo $currency; ?>" <?php echo ($account['currency'] ?? 'CLP') === $currency ? 'selected' : ''; ?>>
                                <?php echo $currency; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Saldo actual</label>
                    <input type="number" step="0.01" name="current_balance" class="form-control" value="<?php echo e($account['current_balance'] ?? 0); ?>">
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'treasury/account-edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
