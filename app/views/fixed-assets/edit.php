<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Editar activo fijo</h4>
        <a href="index.php?route=fixed-assets" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=fixed-assets/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)($asset['id'] ?? 0); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Activo</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($asset['name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Categoría</label>
                    <input type="text" name="category" class="form-control" value="<?php echo e($asset['category'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha adquisición</label>
                    <input type="date" name="acquisition_date" class="form-control" value="<?php echo e($asset['acquisition_date'] ?? date('Y-m-d')); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valor adquisición</label>
                    <input type="number" step="0.01" name="acquisition_value" class="form-control" value="<?php echo e($asset['acquisition_value'] ?? 0); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Depreciación acumulada</label>
                    <input type="number" step="0.01" name="accumulated_depreciation" class="form-control" value="<?php echo e($asset['accumulated_depreciation'] ?? 0); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Método depreciación</label>
                    <select name="depreciation_method" class="form-select">
                        <option value="linea_recta" <?php echo ($asset['depreciation_method'] ?? '') === 'linea_recta' ? 'selected' : ''; ?>>Línea recta</option>
                        <option value="acelerada" <?php echo ($asset['depreciation_method'] ?? '') === 'acelerada' ? 'selected' : ''; ?>>Acelerada</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Vida útil (meses)</label>
                    <input type="number" name="useful_life_months" class="form-control" value="<?php echo e($asset['useful_life_months'] ?? 0); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="activo" <?php echo ($asset['status'] ?? '') === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo ($asset['status'] ?? '') === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                        <option value="baja" <?php echo ($asset['status'] ?? '') === 'baja' ? 'selected' : ''; ?>>Baja</option>
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'fixed-assets/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
