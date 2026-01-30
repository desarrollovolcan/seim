<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Ventas</h4>
        <div class="d-flex gap-2">
            <a href="index.php?route=pos" class="btn btn-soft-secondary">Punto de venta</a>
            <a href="index.php?route=sales/create" class="btn btn-primary">Registrar venta</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Número</th>
                        <th>Canal</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                        <?php
                        $status = $sale['status'] ?? 'pagado';
                        $statusColor = match ($status) {
                            'pagado' => 'success',
                            'pendiente' => 'warning',
                            'borrador' => 'secondary',
                            'en_espera' => 'info',
                            default => 'info',
                        };
                        $channel = $sale['channel'] ?? 'venta';
                        ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($sale['id'] ?? null, 'Venta'); ?></td>
                            <td><?php echo e($sale['numero'] ?? ''); ?></td>
                            <td><span class="badge bg-light text-body"><?php echo e(strtoupper($channel)); ?></span></td>
                            <td><?php echo e($sale['client_name'] ?? 'Consumidor final'); ?></td>
                            <td><?php echo e(format_date($sale['sale_date'] ?? null)); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $statusColor; ?>-subtle text-<?php echo $statusColor; ?>">
                                    <?php echo e($status); ?>
                                </span>
                            </td>
                            <td class="text-end"><?php echo e(format_currency((float)($sale['total'] ?? 0))); ?></td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="index.php?route=sales/show&id=<?php echo (int)$sale['id']; ?>" class="dropdown-item">Ver</a>
                                        </li>
                                        <li>
                                            <form method="post" action="index.php?route=sales/delete" onsubmit="return confirm('¿Eliminar esta venta?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$sale['id']; ?>">
                                                <button type="submit" class="dropdown-item dropdown-item-button text-danger">Eliminar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
