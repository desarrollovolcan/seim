<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Editar actividad SII</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/sii-activities/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo $activity['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Código</label>
                <input type="text" name="code" class="form-control" value="<?php echo e($activity['code']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Actividad</label>
                <input type="text" name="name" class="form-control" value="<?php echo e($activity['name']); ?>" required>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=maintainers/sii-activities" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
