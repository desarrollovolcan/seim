<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Editar período tributario</h4>
        <a href="index.php?route=taxes" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=taxes/periods/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)($period['id'] ?? 0); ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Período</label>
                    <input type="text" name="period" class="form-control" value="<?php echo e($period['period'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">IVA débito</label>
                    <input type="number" step="0.01" name="iva_debito" class="form-control" value="<?php echo e($period['iva_debito'] ?? 0); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">IVA crédito</label>
                    <input type="number" step="0.01" name="iva_credito" class="form-control" value="<?php echo e($period['iva_credito'] ?? 0); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Remanente</label>
                    <input type="number" step="0.01" name="remanente" class="form-control" value="<?php echo e($period['remanente'] ?? 0); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Impuesto único</label>
                    <input type="number" step="0.01" name="impuesto_unico" class="form-control" value="<?php echo e($period['impuesto_unico'] ?? 0); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="pendiente" <?php echo ($period['status'] ?? '') === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="declarado" <?php echo ($period['status'] ?? '') === 'declarado' ? 'selected' : ''; ?>>Declarado</option>
                        <option value="pagado" <?php echo ($period['status'] ?? '') === 'pagado' ? 'selected' : ''; ?>>Pagado</option>
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'taxes/period-edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
