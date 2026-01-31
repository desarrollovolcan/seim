<div class="card">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h5 class="mb-0"><?php echo e($client['name'] ?? ''); ?></h5>
                    <?php echo render_id_badge($client['id'] ?? null); ?>
                </div>
                <div class="text-muted small">Ficha simplificada del cliente.</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="index.php?route=clients/edit&id=<?php echo (int)$client['id']; ?>" class="btn btn-outline-primary">Editar</a>
                <a href="index.php?route=quotes/create&client_id=<?php echo (int)$client['id']; ?>" class="btn btn-primary">Cotizar</a>
            </div>
        </div>
        <hr class="my-3">
        <div>
            <label class="form-label fw-semibold">Nota interna</label>
            <textarea class="form-control" rows="4" readonly><?php echo e($client['notes'] ?? ''); ?></textarea>
            <?php if (empty($client['notes'])): ?>
                <small class="text-muted">Sin notas internas registradas.</small>
            <?php endif; ?>
        </div>
    </div>
</div>
