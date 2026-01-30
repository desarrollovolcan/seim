<div class="card">
    <div class="card-body">
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo e($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <form method="post" action="index.php?route=auth/switch-company/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Selecciona empresa</label>
                    <select name="company_id" class="form-select" required>
                        <?php foreach (($companies ?? []) as $company): ?>
                            <option value="<?php echo e((string)$company['id']); ?>" <?php echo ((int)$company['id'] === (int)($currentCompanyId ?? 0)) ? 'selected' : ''; ?>>
                                <?php echo e($company['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=dashboard" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Cambiar empresa</button>
            </div>
        </form>
    </div>
</div>
