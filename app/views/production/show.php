<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1">Producción #<?php echo (int)$order['id']; ?></h4>
                    <div class="text-muted">Fecha: <?php echo e(format_date($order['production_date'] ?? null)); ?></div>
                </div>
                <a href="index.php?route=production" class="btn btn-light">Volver</a>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <div class="text-muted">Costo total</div>
                            <div class="fs-5 fw-semibold"><?php echo e(format_currency((float)($order['total_cost'] ?? 0))); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <div class="text-muted">Estado</div>
                            <div class="fs-5 fw-semibold"><?php echo e($order['status'] ?? 'completada'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <div class="text-muted">Notas</div>
                            <div><?php echo e($order['notes'] ?? ''); ?></div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-semibold">Productos finales</h6>
                <div class="table-responsive mb-4">
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
                            <?php foreach ($outputs as $item): ?>
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

                <h6 class="fw-semibold">Materias primas / insumos</h6>
                <div class="table-responsive mb-4">
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
                            <?php foreach ($inputs as $item): ?>
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

                <h6 class="fw-semibold">Gastos adicionales</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($expenses)): ?>
                                <tr>
                                    <td colspan="2" class="text-muted">Sin gastos asociados.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($expenses as $expense): ?>
                                    <tr>
                                        <td><?php echo e($expense['description'] ?? ''); ?></td>
                                        <td class="text-end"><?php echo e(format_currency((float)($expense['amount'] ?? 0))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
