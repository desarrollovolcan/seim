<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Editar institución de salud</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/hr-health-providers/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)$provider['id']; ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($provider['name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipo</label>
                    <?php $providerType = $provider['provider_type'] ?? 'fonasa'; ?>
                    <select name="provider_type" class="form-select">
                        <option value="fonasa" <?php echo $providerType === 'fonasa' ? 'selected' : ''; ?>>FONASA</option>
                        <option value="isapre" <?php echo $providerType === 'isapre' ? 'selected' : ''; ?>>ISAPRE</option>
                        <option value="otro" <?php echo $providerType === 'otro' ? 'selected' : ''; ?>>Otro</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="index.php?route=maintainers/hr-health-providers" class="btn btn-light">Cancelar</a>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'maintainers/hr-health-providers/edit';
    include __DIR__ . '/../../partials/report-download.php';
    ?>
</form>
    </div>
</div>
