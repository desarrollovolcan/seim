<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Editar retención</h4>
        <a href="index.php?route=taxes&period_id=<?php echo (int)($withholding['period_id'] ?? 0); ?>" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=taxes/withholdings/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)($withholding['id'] ?? 0); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tipo</label>
                    <input type="text" name="type" class="form-control" value="<?php echo e($withholding['type'] ?? ''); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Base</label>
                    <input type="number" step="0.01" name="base_amount" class="form-control" value="<?php echo e($withholding['base_amount'] ?? 0); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tasa (%)</label>
                    <input type="number" step="0.01" name="rate" class="form-control" value="<?php echo e($withholding['rate'] ?? 0); ?>">
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'taxes/withholding-edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
