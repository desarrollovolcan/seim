<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Nueva institución de salud</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/hr-health-providers/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipo</label>
                    <select name="provider_type" class="form-select">
                        <option value="fonasa">FONASA</option>
                        <option value="isapre">ISAPRE</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="index.php?route=maintainers/hr-health-providers" class="btn btn-light">Cancelar</a>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'maintainers/hr-health-providers/create';
    include __DIR__ . '/../../partials/report-download.php';
    ?>
</form>
    </div>
</div>
