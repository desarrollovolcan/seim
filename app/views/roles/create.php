<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Nuevo rol</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=roles/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="mb-3">
                <label class="form-label">Nombre del rol</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="index.php?route=roles" class="btn btn-light">Cancelar</a>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'roles/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
