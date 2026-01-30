<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Editar ciudad</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/chile-cities/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo $city['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Ciudad</label>
                <input type="text" name="name" class="form-control" value="<?php echo e($city['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Región</label>
                <select name="region_id" class="form-select" required>
                    <option value="">Selecciona región</option>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?php echo $region['id']; ?>" <?php echo ((int)$region['id'] === (int)$city['region_id']) ? 'selected' : ''; ?>>
                            <?php echo e($region['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=maintainers/chile-cities" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
