<div class="card">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h5 class="mb-0"><?php echo e($client['name'] ?? ''); ?></h5>
                    <?php echo render_id_badge($client['id'] ?? null); ?>
                </div>
                <div class="text-muted small">Ficha del cliente.</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="index.php?route=clients/edit&id=<?php echo (int)$client['id']; ?>" class="btn btn-outline-primary">Editar</a>
                <a href="index.php?route=quotes/create&client_id=<?php echo (int)$client['id']; ?>" class="btn btn-primary">Cotizar</a>
            </div>
        </div>
        <hr class="my-3">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="text-muted small">Email</div>
                <div class="fw-semibold"><?php echo e($client['email'] ?? '-'); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Teléfono</div>
                <div class="fw-semibold"><?php echo e($client['phone'] ?? '-'); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Contacto</div>
                <div class="fw-semibold"><?php echo e($client['contact'] ?? '-'); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">RUT</div>
                <div class="fw-semibold"><?php echo e($client['rut'] ?? '-'); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Giro</div>
                <div class="fw-semibold"><?php echo e($client['giro'] ?? '-'); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Dirección</div>
                <div class="fw-semibold"><?php echo e($client['address'] ?? '-'); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Comuna</div>
                <div class="fw-semibold"><?php echo e($client['commune'] ?? '-'); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Mandante</div>
                <div class="fw-semibold"><?php echo e($client['mandante_name'] ?? '-'); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Mandante RUT</div>
                <div class="fw-semibold"><?php echo e($client['mandante_rut'] ?? '-'); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Mandante Teléfono</div>
                <div class="fw-semibold"><?php echo e($client['mandante_phone'] ?? '-'); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Mandante Correo</div>
                <div class="fw-semibold"><?php echo e($client['mandante_email'] ?? '-'); ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Estado</div>
                <div class="fw-semibold"><?php echo e($client['status'] ?? '-'); ?></div>
            </div>
        </div>
        <div>
            <label class="form-label fw-semibold">Nota interna</label>
            <textarea class="form-control" rows="4" readonly><?php echo e($client['notes'] ?? ''); ?></textarea>
            <?php if (empty($client['notes'])): ?>
                <small class="text-muted">Sin notas internas registradas.</small>
            <?php endif; ?>
        </div>
    </div>
</div>
