<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <h4 class="card-title mb-0"><?php echo e($service['name']); ?></h4>
            <?php echo render_id_badge($service['id'] ?? null); ?>
        </div>
        <?php
        $status = $service['status'] ?? 'activo';
        $statusColor = match ($status) {
            'activo' => 'success',
            'vencido' => 'danger',
            'renovado' => 'primary',
            default => 'secondary',
        };
        ?>
        <span class="badge bg-<?php echo $statusColor; ?>-subtle text-<?php echo $statusColor; ?>">
            <?php echo e($status); ?>
        </span>
    </div>
    <div class="card-body">
        <p><strong>ID:</strong> <?php echo render_id_badge($service['id'] ?? null); ?></p>
        <p><strong>Cliente:</strong> <?php echo e($client['name'] ?? ''); ?></p>
        <p><strong>Tipo:</strong> <?php echo e($service['service_type']); ?></p>
        <p><strong>Costo:</strong> <?php echo e(format_currency((float)($service['cost'] ?? 0))); ?></p>
        <p><strong>Vencimiento:</strong> <?php echo e(format_date($service['due_date'])); ?></p>
        <p><strong>Eliminación:</strong> <?php echo e(format_date($service['delete_date'])); ?></p>
        <p><strong>Auto facturar:</strong> <?php echo $service['auto_invoice'] ? 'Sí' : 'No'; ?></p>
        <p><strong>Auto correo:</strong> <?php echo $service['auto_email'] ? 'Sí' : 'No'; ?></p>
        <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="index.php?route=clients/show&id=<?php echo (int)($client['id'] ?? 0); ?>" class="btn btn-outline-primary btn-sm">Ver cliente</a>
            <a href="index.php?route=invoices/create&service_id=<?php echo (int)$service['id']; ?>&client_id=<?php echo (int)($client['id'] ?? 0); ?>" class="btn btn-outline-success btn-sm">Crear factura</a>
            <a href="index.php?route=tickets/create&client_id=<?php echo (int)($client['id'] ?? 0); ?>" class="btn btn-outline-warning btn-sm">Abrir ticket</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h4 class="card-title mb-0">Facturas generadas</h4></div>
    <div class="card-body">
        <ul class="list-group">
            <?php foreach ($invoices as $invoice): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column gap-1">
                        <?php echo render_id_badge($invoice['id'] ?? null); ?>
                        <span><?php echo e($invoice['numero']); ?></span>
                    </div>
                    <span class="badge bg-light text-dark"><?php echo e($invoice['estado']); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php if (!empty($renewals)): ?>
<div class="card mt-3">
    <div class="card-header"><h4 class="card-title mb-0">Historial de renovaciones</h4></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-end">Monto</th>
                        <th>Moneda</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($renewals as $renewal): ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($renewal['id'] ?? null); ?></td>
                            <td><?php echo e(format_date($renewal['renewal_date'])); ?></td>
                            <td><?php echo e(str_replace('_', ' ', $renewal['status'] ?? '')); ?></td>
                            <td class="text-end"><?php echo e(format_currency((float)($renewal['amount'] ?? 0))); ?></td>
                            <td><?php echo e($renewal['currency'] ?? ''); ?></td>
                            <td><?php echo e($renewal['notes'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
