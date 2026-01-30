<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Editar tipo de contrato</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/hr-contract-types/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)$contractType['id']; ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($contractType['name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Duración máxima (meses)</label>
                    <input type="number" name="max_duration_months" class="form-control" min="0" value="<?php echo e($contractType['max_duration_months'] ?? ''); ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Descripción</label>
                    <input type="text" name="description" class="form-control" value="<?php echo e($contractType['description'] ?? ''); ?>">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="index.php?route=maintainers/hr-contract-types" class="btn btn-light">Cancelar</a>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'maintainers/hr-contract-types/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
