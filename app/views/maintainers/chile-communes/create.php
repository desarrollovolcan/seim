<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Nueva comuna</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/chile-communes/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="mb-3">
                <label class="form-label">Comuna</label>
                <input type="text" name="commune" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ciudad</label>
                <select name="city_id" class="form-select" required>
                    <option value="">Selecciona ciudad</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo $city['id']; ?>">
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
