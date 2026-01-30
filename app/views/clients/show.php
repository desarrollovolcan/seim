<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h5 class="mb-0"><?php echo e($client['name'] ?? ''); ?></h5>
                    <?php echo render_id_badge($client['id'] ?? null); ?>
                </div>
                <p class="mb-1"><strong>ID:</strong> <?php echo render_id_badge($client['id'] ?? null); ?></p>
                <p class="mb-1"><strong>Email:</strong> <?php echo e($client['email'] ?? '-'); ?></p>
                <p class="mb-1"><strong>Teléfono:</strong> <?php echo e($client['phone'] ?? '-'); ?></p>
                <p class="mb-1"><strong>Contacto:</strong> <?php echo e($client['contact'] ?? '-'); ?></p>
                <p class="mb-1"><strong>Mandante:</strong> <?php echo e($client['mandante_name'] ?? '-'); ?></p>
                <p class="mb-1"><strong>Mandante RUT:</strong> <?php echo e($client['mandante_rut'] ?? '-'); ?></p>
                <p class="mb-1"><strong>Mandante Teléfono:</strong> <?php echo e($client['mandante_phone'] ?? '-'); ?></p>
                <p class="mb-1"><strong>Mandante Correo:</strong> <?php echo e($client['mandante_email'] ?? '-'); ?></p>
                <p class="mb-1"><strong>Dirección:</strong> <?php echo e($client['address'] ?? '-'); ?></p>
                <p class="mb-0"><strong>Estado:</strong> <?php echo e($client['status'] ?? '-'); ?></p>
                <hr>
                <div class="mb-2 fw-semibold">Acceso intranet</div>
                <?php if (!empty($portalUrl)): ?>
                    <div class="input-group">
                        <input type="text" class="form-control" value="<?php echo e($portalUrl); ?>" readonly>
                        <button class="btn btn-soft-primary" type="button" data-copy-input>Copiar</button>
                    </div>
                    <small class="text-muted">Comparte este link con el cliente.</small>
                <?php else: ?>
                    <div class="text-muted">Genera el link desde la edición del cliente.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h4 class="card-title mb-0">Acciones rápidas</h4></div>
            <div class="card-body d-grid gap-2">
                <a href="index.php?route=projects/create&client_id=<?php echo (int)$client['id']; ?>" class="btn btn-outline-primary">Nuevo proyecto</a>
                <a href="index.php?route=services/create&client_id=<?php echo (int)$client['id']; ?>" class="btn btn-outline-warning">Nuevo servicio</a>
                <a href="index.php?route=quotes/create&client_id=<?php echo (int)$client['id']; ?>" class="btn btn-outline-info">Nueva cotización</a>
                <a href="index.php?route=invoices/create&client_id=<?php echo (int)$client['id']; ?>" class="btn btn-outline-success">Nueva factura</a>
                <a href="index.php?route=tickets/create&client_id=<?php echo (int)$client['id']; ?>" class="btn btn-outline-danger">Abrir ticket</a>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Servicios asociados</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($services as $service): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="index.php?route=services/show&id=<?php echo (int)$service['id']; ?>" class="link-reset">
                                <div class="d-flex flex-column gap-1">
                                    <?php echo render_id_badge($service['id'] ?? null); ?>
                                    <span><?php echo e($service['name']); ?></span>
                                </div>
                            </a>
                            <span class="badge bg-info-subtle text-info"><?php echo e($service['service_type']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Facturas</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($invoices as $invoice): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="index.php?route=invoices/show&id=<?php echo (int)$invoice['id']; ?>" class="link-reset">
                                <div class="d-flex flex-column gap-1">
                                    <?php echo render_id_badge($invoice['id'] ?? null); ?>
                                    <span><?php echo e($invoice['numero']); ?></span>
                                </div>
                            </a>
                            <span class="badge bg-secondary-subtle text-secondary"><?php echo e($invoice['estado']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('[data-copy-input]').forEach((button) => {
        button.addEventListener('click', async () => {
            const input = button.closest('.input-group')?.querySelector('input');
            if (!input) {
                return;
            }
            try {
                await navigator.clipboard.writeText(input.value);
                button.textContent = 'Copiado';
                setTimeout(() => {
                    button.textContent = 'Copiar';
                }, 2000);
            } catch (error) {
                input.select();
                document.execCommand('copy');
            }
        });
    });
</script>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4 class="card-title mb-0">Proyectos</h4></div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($projects as $project): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="index.php?route=projects/show&id=<?php echo (int)$project['id']; ?>" class="link-reset">
                                <?php echo e($project['name']); ?>
                            </a>
                            <span class="badge bg-light text-dark"><?php echo e($project['status']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4 class="card-title mb-0">Historial de correos</h4></div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($emails as $email): ?>
                        <li class="list-group-item">
                            <strong><?php echo e($email['subject']); ?></strong>
                            <div class="text-muted fs-xs"><?php echo e($email['status']); ?> - <?php echo e($email['created_at']); ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h4 class="card-title mb-0">Pagos registrados</h4></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Factura</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <th>Método</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td>#<?php echo e($payment['invoice_id']); ?></td>
                            <td><?php echo e(format_currency((float)($payment['monto'] ?? 0))); ?></td>
                            <td><?php echo e($payment['fecha_pago']); ?></td>
                            <td><?php echo e($payment['metodo']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
