<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Editar comuna</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/chile-communes/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo $commune['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Comuna</label>
                <input type="text" name="commune" class="form-control" value="<?php echo e($commune['commune']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ciudad</label>
                <select name="city_id" class="form-select" required>
                    <option value="">Selecciona ciudad</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo $city['id']; ?>" <?php echo ((int)$city['id'] === (int)$commune['city_id']) ? 'selected' : ''; ?>>
                            <?php echo e($city['name']); ?> (<?php echo e($city['region']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=maintainers/chile-communes" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
