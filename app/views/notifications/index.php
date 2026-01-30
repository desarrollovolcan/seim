<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Notificaciones</h4>
    </div>
    <div class="card-body">
        <ul class="list-group">
            <?php foreach ($notifications as $notification): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="mb-1"><?php echo render_id_badge($notification['id'] ?? null); ?></div>
                        <strong><?php echo e($notification['title']); ?></strong>
                        <p class="mb-0 text-muted fs-xs"><?php echo e($notification['message']); ?></p>
                    </div>
                    <span class="badge bg-<?php echo $notification['type'] === 'success' ? 'success' : ($notification['type'] === 'danger' ? 'danger' : 'info'); ?>-subtle text-<?php echo $notification['type'] === 'success' ? 'success' : ($notification['type'] === 'danger' ? 'danger' : 'info'); ?>">
                        <?php echo e($notification['type']); ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
