<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Detalle de activo fijo</h4>
        <a href="index.php?route=fixed-assets" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small">Activo</div>
                <div class="fw-semibold"><?php echo e($asset['name'] ?? ''); ?></div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Categoría</div>
                <div class="fw-semibold"><?php echo e($asset['category'] ?? ''); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Fecha adquisición</div>
                <div class="fw-semibold"><?php echo e(format_date($asset['acquisition_date'] ?? null)); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Valor adquisición</div>
                <div class="fw-semibold"><?php echo e(format_currency((float)($asset['acquisition_value'] ?? 0))); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Estado</div>
                <div class="fw-semibold text-capitalize"><?php echo e($asset['status'] ?? 'activo'); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Depreciación acumulada</div>
                <div class="fw-semibold"><?php echo e(format_currency((float)($asset['accumulated_depreciation'] ?? 0))); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Valor libro</div>
                <div class="fw-semibold"><?php echo e(format_currency((float)($asset['book_value'] ?? 0))); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Método depreciación</div>
                <div class="fw-semibold text-capitalize"><?php echo e(str_replace('_', ' ', $asset['depreciation_method'] ?? '')); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Vida útil (meses)</div>
                <div class="fw-semibold"><?php echo (int)($asset['useful_life_months'] ?? 0); ?></div>
            </div>
        </div>
    </div>
</div>
