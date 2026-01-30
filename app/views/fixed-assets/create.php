<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Registrar activo fijo</h4>
        <a href="index.php?route=fixed-assets" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=fixed-assets/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoría</label>
                    <input type="text" name="category" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha adquisición</label>
                    <input type="date" name="acquisition_date" class="form-control" value="<?php echo e($today); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Valor adquisición</label>
                    <input type="number" step="0.01" name="acquisition_value" class="form-control" value="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Método depreciación</label>
                    <select name="depreciation_method" class="form-select">
                        <option value="linea_recta">Línea recta</option>
                        <option value="acelerada">Acelerada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Vida útil (meses)</label>
                    <input type="number" name="useful_life_months" class="form-control" value="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Depreciación acumulada</label>
                    <input type="number" step="0.01" name="accumulated_depreciation" class="form-control" value="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="activo">Activo</option>
                        <option value="dado_de_baja">Dado de baja</option>
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'fixed-assets/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
