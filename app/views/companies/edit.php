<div class="card">
    <div class="card-body">
        <form method="post" action="index.php?route=companies/update" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo e((string)($company['id'] ?? 0)); ?>">
            <div class="mb-3">
                <?php echo render_id_badge($company['id'] ?? null); ?>
            </div>
            <?php include __DIR__ . '/_form.php'; ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Logo color (por empresa)</label>
                    <input type="file" name="logo_color" class="form-control" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text">Se usará en cotizaciones y documentos de esta empresa (JPG/PNG/WEBP, máx 2MB).</div>
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <?php if (!empty($companySettings['logo_color'])): ?>
                        <img src="<?php echo e($companySettings['logo_color']); ?>" alt="Logo color empresa" class="rounded border" style="height: 48px; object-fit: contain;">
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Logo negro (por empresa)</label>
                    <input type="file" name="logo_black" class="form-control" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text">Opcional para variantes en fondos claros.</div>
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <?php if (!empty($companySettings['logo_black'])): ?>
                        <img src="<?php echo e($companySettings['logo_black']); ?>" alt="Logo negro empresa" class="rounded border" style="height: 48px; object-fit: contain;">
                    <?php endif; ?>
                </div>
            </div>
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
