<div class="row">
    <div class="col-12 col-lg-6 col-xl-5">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Nueva familia</h4>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=maintainers/product-families/store">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="index.php?route=maintainers/product-families" class="btn btn-light">Cancelar</a>
                    </div>
                
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'maintainers/product-families/create';
    include __DIR__ . '/../../partials/report-download.php';
    ?>
</form>
            </div>
        </div>
    </div>
</div>
