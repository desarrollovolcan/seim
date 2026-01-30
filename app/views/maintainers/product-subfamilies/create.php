<div class="row">
    <div class="col-12 col-lg-6 col-xl-5">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Nueva subfamilia</h4>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=maintainers/product-subfamilies/store">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Familia</label>
                        <select name="family_id" class="form-select" required>
                            <option value="">Selecciona familia</option>
                            <?php foreach ($families as $family): ?>
                                <option value="<?php echo (int)$family['id']; ?>"><?php echo e($family['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="index.php?route=maintainers/product-subfamilies" class="btn btn-light">Cancelar</a>
                    </div>
                
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'maintainers/product-subfamilies/create';
    include __DIR__ . '/../../partials/report-download.php';
    ?>
</form>
            </div>
        </div>
    </div>
</div>
