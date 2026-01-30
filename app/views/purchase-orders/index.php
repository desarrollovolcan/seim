<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Órdenes de compra</h4>
        <a href="index.php?route=purchase-orders/create" class="btn btn-primary">Nueva orden</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Referencia</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <?php
                        $status = $order['status'] ?? 'pendiente';
                        $statusColor = match ($status) {
                            'aprobada', 'cerrada' => 'success',
                            'pendiente' => 'warning',
                            default => 'secondary',
                        };
                        ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($order['id'] ?? null, 'OC'); ?></td>
                            <td><?php echo e($order['reference'] ?? '-'); ?></td>
                            <td><?php echo e($order['supplier_name'] ?? ''); ?></td>
                            <td><?php echo e(format_date($order['order_date'] ?? null)); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $statusColor; ?>-subtle text-<?php echo $statusColor; ?>">
                                    <?php echo e($status); ?>
                                </span>
                            </td>
                            <td class="text-end"><?php echo e(format_currency((float)($order['total'] ?? 0))); ?></td>
                            <td class="text-end">
                                <a href="index.php?route=purchase-orders/show&id=<?php echo (int)$order['id']; ?>" class="btn btn-soft-primary btn-sm">Ver</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
