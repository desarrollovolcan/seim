<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Editar servicio</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/services/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($service['name']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo de servicio</label>
                    <select name="service_type_id" class="form-select" required>
                        <?php foreach ($types as $type): ?>
                            <option value="<?php echo $type['id']; ?>" <?php echo (int)$service['service_type_id'] === (int)$type['id'] ? 'selected' : ''; ?>>
                                <?php echo e($type['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Costo</label>
                    <input type="number" step="0.01" name="cost" class="form-control" value="<?php echo e($service['cost'] ?? 0); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Moneda</label>
                    <select name="currency" class="form-select">
                        <option value="CLP" <?php echo ($service['currency'] ?? '') === 'CLP' ? 'selected' : ''; ?>>CLP</option>
                        <option value="USD" <?php echo ($service['currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>USD</option>
                        <option value="EUR" <?php echo ($service['currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo e($service['description'] ?? ''); ?></textarea>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=maintainers/services" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'maintainers/services/edit';
    include __DIR__ . '/../../partials/report-download.php';
    ?>
</form>
    </div>
</div>
