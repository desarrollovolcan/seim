<div class="row">
    <div class="col-12 col-lg-6 col-xl-5">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Editar familia</h4>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=maintainers/product-families/update">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id" value="<?php echo (int)($family['id'] ?? 0); ?>">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" value="<?php echo e($family['name'] ?? ''); ?>" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <a href="index.php?route=maintainers/product-families" class="btn btn-light">Cancelar</a>
                    </div>
                
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'maintainers/product-families/edit';
    include __DIR__ . '/../../partials/report-download.php';
    ?>
</form>
            </div>
        </div>
    </div>
</div>
