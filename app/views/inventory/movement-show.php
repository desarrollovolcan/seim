<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Detalle movimiento de inventario</h4>
        <a href="index.php?route=inventory/movements" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted small">Producto</div>
                <div class="fw-semibold"><?php echo e($movement['product_name'] ?? ''); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Fecha</div>
                <div class="fw-semibold"><?php echo e(format_date($movement['movement_date'] ?? null)); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Tipo</div>
                <div class="fw-semibold text-capitalize"><?php echo e($movement['movement_type'] ?? ''); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Cantidad</div>
                <div class="fw-semibold"><?php echo (int)($movement['quantity'] ?? 0); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Costo unitario</div>
                <div class="fw-semibold"><?php echo e(format_currency((float)($movement['unit_cost'] ?? 0))); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Referencia</div>
                <div class="fw-semibold"><?php echo e($movement['reference_type'] ?? ''); ?> <?php echo e($movement['reference_id'] ?? ''); ?></div>
            </div>
            <div class="col-12">
                <div class="text-muted small">Notas</div>
                <div class="fw-semibold"><?php echo e($movement['notes'] ?? ''); ?></div>
            </div>
        </div>
    </div>
</div>
