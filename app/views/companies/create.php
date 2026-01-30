<div class="card">
    <div class="card-body">
        <form method="post" action="index.php?route=companies/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <?php $company = $company ?? []; ?>
            <?php include __DIR__ . '/_form.php'; ?>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=companies" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'companies/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
