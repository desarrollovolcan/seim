<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Editar región</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/chile-regions/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo $region['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Región</label>
                <input type="text" name="name" class="form-control" value="<?php echo e($region['name']); ?>" required>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=maintainers/chile-regions" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
