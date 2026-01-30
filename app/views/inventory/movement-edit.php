<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Editar movimiento de inventario</h4>
        <a href="index.php?route=inventory/movements" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=inventory/movements/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)($movement['id'] ?? 0); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Producto</label>
                    <input type="text" class="form-control" value="<?php echo e($movement['product_name'] ?? ''); ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha</label>
                    <input type="text" class="form-control" value="<?php echo e(format_date($movement['movement_date'] ?? null)); ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <input type="text" class="form-control text-capitalize" value="<?php echo e($movement['movement_type'] ?? ''); ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cantidad</label>
                    <input type="text" class="form-control" value="<?php echo (int)($movement['quantity'] ?? 0); ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Costo unitario</label>
                    <input type="text" class="form-control" value="<?php echo e(format_currency((float)($movement['unit_cost'] ?? 0))); ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Referencia</label>
                    <input type="text" name="reference_type" class="form-control" value="<?php echo e($movement['reference_type'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ID referencia</label>
                    <input type="number" name="reference_id" class="form-control" value="<?php echo e($movement['reference_id'] ?? ''); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Notas</label>
                    <textarea name="notes" class="form-control" rows="3"><?php echo e($movement['notes'] ?? ''); ?></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'inventory/movement-edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
