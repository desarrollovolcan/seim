<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Editar ítem de remuneración</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/hr-payroll-items/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($item['name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <?php $itemType = $item['item_type'] ?? 'haber'; ?>
                    <select name="item_type" class="form-select">
                        <option value="haber" <?php echo $itemType === 'haber' ? 'selected' : ''; ?>>Haber</option>
                        <option value="descuento" <?php echo $itemType === 'descuento' ? 'selected' : ''; ?>>Descuento</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Imponible</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="taxable" id="taxable-item" <?php echo !empty($item['taxable']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="taxable-item">Sí</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="index.php?route=maintainers/hr-payroll-items" class="btn btn-light">Cancelar</a>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'maintainers/hr-payroll-items/edit';
    include __DIR__ . '/../../partials/report-download.php';
    ?>
</form>
    </div>
</div>
