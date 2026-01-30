<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Productos fabricados</h4>
        <a href="index.php?route=produced-products/create" class="btn btn-primary">Nuevo producto fabricado</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Stock</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <?php
                        $status = $product['status'] ?? 'activo';
                        $statusColor = match ($status) {
                            'activo' => 'success',
                            'inactivo' => 'secondary',
                            default => 'info',
                        };
                        ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($product['id'] ?? null); ?></td>
                            <td><?php echo e($product['name'] ?? ''); ?></td>
                            <td><?php echo e($product['sku'] ?? ''); ?></td>
                            <td class="text-end"><?php echo e(format_currency((float)($product['price'] ?? 0), 0)); ?></td>
                            <td class="text-end">
                                <span class="badge bg-light text-body fw-semibold">
                                    <?php echo (int)($product['stock'] ?? 0); ?>
                                    <?php if (!empty($product['stock_min']) && (int)$product['stock'] <= (int)$product['stock_min']): ?>
                                        <span class="text-danger ms-1">(Bajo)</span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $statusColor; ?>-subtle text-<?php echo $statusColor; ?>">
                                    <?php echo e($status); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="index.php?route=produced-products/edit&id=<?php echo (int)$product['id']; ?>">Editar</a></li>
                                        <li>
                                            <form method="post" action="index.php?route=produced-products/delete" onsubmit="return confirm('¿Eliminar este producto?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$product['id']; ?>">
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
