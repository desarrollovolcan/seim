<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Producciones registradas</h4>
        <a href="index.php?route=production/create" class="btn btn-primary">Registrar producción</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-end">Cantidad total</th>
                        <th class="text-end">Costo total</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <?php
                        $status = $order['status'] ?? 'completada';
                        $statusColor = match ($status) {
                            'completada' => 'success',
                            'pendiente' => 'warning',
                            default => 'secondary',
                        };
                        ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($order['id'] ?? null, 'Producción'); ?></td>
                            <td><?php echo e(format_date($order['production_date'] ?? null)); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $statusColor; ?>-subtle text-<?php echo $statusColor; ?>">
                                    <?php echo e($status); ?>
                                </span>
                            </td>
                            <td class="text-end"><?php echo (int)($order['total_quantity'] ?? 0); ?></td>
                            <td class="text-end"><?php echo e(format_currency((float)($order['total_cost'] ?? 0))); ?></td>
                            <td class="text-end">
                                <a href="index.php?route=production/show&id=<?php echo (int)$order['id']; ?>" class="btn btn-soft-primary btn-sm">Ver detalle</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
