<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Nueva AFP</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/hr-pension-funds/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="index.php?route=maintainers/hr-pension-funds" class="btn btn-light">Cancelar</a>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'maintainers/hr-pension-funds/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
