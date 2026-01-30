<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Editar cuenta contable</h4>
        <a href="index.php?route=accounting/chart" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=accounting/chart/update" id="chart-edit-form">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)$account['id']; ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Código</label>
                    <input type="text" name="code" class="form-control" inputmode="numeric" autocomplete="off" value="<?php echo e($account['code']); ?>" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" autocomplete="off" value="<?php echo e($account['name']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo</label>
                    <select name="type" class="form-select" data-account-type required>
                        <option value="">Selecciona tipo</option>
                        <option value="activo" <?php echo $account['type'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="pasivo" <?php echo $account['type'] === 'pasivo' ? 'selected' : ''; ?>>Pasivo</option>
                        <option value="patrimonio" <?php echo $account['type'] === 'patrimonio' ? 'selected' : ''; ?>>Patrimonio</option>
                        <option value="resultado" <?php echo $account['type'] === 'resultado' ? 'selected' : ''; ?>>Resultado</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cuenta madre</label>
                    <select name="parent_id" class="form-select" data-parent-select>
                        <option value="">Sin cuenta madre</option>
                        <?php foreach ($accounts as $parentAccount): ?>
                            <?php if ((int)$parentAccount['id'] === (int)$account['id']): ?>
                                <?php continue; ?>
                            <?php endif; ?>
                            <option value="<?php echo (int)$parentAccount['id']; ?>" data-parent-type="<?php echo e($parentAccount['type'] ?? ''); ?>" <?php echo (int)($account['parent_id'] ?? 0) === (int)$parentAccount['id'] ? 'selected' : ''; ?>>
                                <?php echo e($parentAccount['code'] . ' - ' . $parentAccount['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($children)): ?>
                        <div class="form-text text-muted">
                            Esta cuenta tiene <?php echo count($children); ?> subcuenta(s). Cambiar la cuenta madre podría afectar la estructura.
                        </div>
                    <?php endif; ?>
                    <div class="form-text text-muted">Las cuentas madre se filtran por el tipo seleccionado.</div>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="account-active" <?php echo !empty($account['is_active']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="account-active">Cuenta activa</label>
                    </div>
                </div>
                <div class="col-12 form-actions">
                    <a href="index.php?route=accounting/chart" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'accounting/chart-edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>

<script>
    (() => {
        const form = document.getElementById('chart-edit-form');
        const typeSelect = form?.querySelector('[data-account-type]');
        const parentSelect = form?.querySelector('[data-parent-select]');
        if (!typeSelect || !parentSelect) {
            return;
        }
        const syncParents = () => {
            const type = typeSelect.value;
            const options = parentSelect.querySelectorAll('option');
            options.forEach((option) => {
                if (!option.value) {
                    option.hidden = false;
                    option.disabled = false;
                    return;
                }
                const parentType = option.dataset.parentType || '';
                const matches = !type || parentType === type;
                option.hidden = !matches;
                option.disabled = !matches;
            });
            if (parentSelect.value && parentSelect.selectedOptions[0]?.disabled) {
                parentSelect.value = '';
            }
        };
        typeSelect.addEventListener('change', syncParents);
        syncParents();
    })();
</script>
