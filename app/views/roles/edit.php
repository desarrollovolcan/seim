<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Editar rol</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=roles/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)($role['id'] ?? 0); ?>">
            <div class="mb-3">
                <?php echo render_id_badge($role['id'] ?? null); ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre del rol</label>
                <input type="text" name="name" class="form-control" value="<?php echo e($role['name'] ?? ''); ?>" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="index.php?route=roles" class="btn btn-light">Cancelar</a>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'roles/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
