<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1">Orden de compra #<?php echo (int)$order['id']; ?></h4>
                    <div class="text-muted">Fecha: <?php echo e(format_date($order['order_date'] ?? null)); ?></div>
                </div>
                <a href="index.php?route=purchase-orders" class="btn btn-light">Volver</a>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <div class="text-muted">Proveedor</div>
                            <div class="fs-6 fw-semibold"><?php echo e($order['supplier_name'] ?? ''); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <div class="text-muted">Estado</div>
                            <div class="fs-6 fw-semibold"><?php echo e($order['status'] ?? 'pendiente'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <div class="text-muted">Total</div>
                            <div class="fs-6 fw-semibold"><?php echo e(format_currency((float)($order['total'] ?? 0))); ?></div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-semibold">Detalle productos</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Costo unitario</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo e($item['product_name'] ?? ''); ?></td>
                                    <td class="text-end"><?php echo (int)($item['quantity'] ?? 0); ?></td>
                                    <td class="text-end"><?php echo e(format_currency((float)($item['unit_cost'] ?? 0))); ?></td>
                                    <td class="text-end"><?php echo e(format_currency((float)($item['subtotal'] ?? 0))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
