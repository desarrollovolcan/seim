<div class="card">
    <div class="card-body">
        <form method="post" action="index.php?route=companies/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo e((string)($company['id'] ?? 0)); ?>">
            <div class="mb-3">
                <?php echo render_id_badge($company['id'] ?? null); ?>
            </div>
            <?php include __DIR__ . '/_form.php'; ?>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=companies" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'companies/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
