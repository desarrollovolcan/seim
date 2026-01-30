<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Vista previa: <?php echo e($template['name']); ?></h4>
        <a href="index.php?route=email-templates/edit&id=<?php echo $template['id']; ?>" class="btn btn-light">Volver</a>
    </div>
    <div class="card-body">
        <p class="text-muted">Cliente: <?php echo e($client['name'] ?? 'Sin cliente'); ?></p>
        <div class="border rounded p-3">
            <?php echo $body; ?>
        </div>
    </div>
</div>
