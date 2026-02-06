<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Despacho <?php echo e($dispatch['truck_code']); ?></h4>
        <span class="badge <?php echo $dispatch['status'] === 'cerrado' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'; ?>">
            <?php echo e(strtoupper($dispatch['status'])); ?>
        </span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><strong>Fecha:</strong> <?php echo e($dispatch['dispatch_date']); ?></div>
            <div class="col-md-3"><strong>Vendedor:</strong> <?php echo e($dispatch['seller_name']); ?></div>
            <div class="col-md-3"><strong>POS vinculado:</strong> <?php echo e($dispatch['session_code'] ?? 'No vinculado'); ?></div>
            <div class="col-md-3"><strong>Ventas POS ref.:</strong> $<?php echo number_format((float)$dispatch['pos_sales_total'], 0, ',', '.'); ?></div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="card-title mb-0">Balance POS del vendedor (día despacho)</h4>
    </div>
    <div class="card-body">
        <?php if (empty($posBalance['sessions'])): ?>
            <p class="text-muted mb-0">No hay sesiones POS encontradas para este vendedor en la fecha del despacho.</p>
        <?php else: ?>
            <div class="table-responsive mb-3">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Caja</th>
                            <th>Estado</th>
                            <th class="text-end">Apertura</th>
                            <th class="text-end">Ventas</th>
                            <th class="text-end">Retiros</th>
                            <th class="text-end">Cierre</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posBalance['sessions'] as $session): ?>
                            <tr>
                                <td>#<?php echo (int)$session['id']; ?> · <?php echo e($session['session_code'] ?? 'Caja'); ?></td>
                                <td><?php echo e($session['status'] ?? ''); ?></td>
                                <td class="text-end">$<?php echo number_format((float)($session['opening_amount'] ?? 0), 0, ',', '.'); ?></td>
                                <td class="text-end">$<?php echo number_format((float)($session['sales_total'] ?? 0), 0, ',', '.'); ?></td>
                                <td class="text-end">$<?php echo number_format((float)($session['withdrawals_total'] ?? 0), 0, ',', '.'); ?></td>
                                <td class="text-end">$<?php echo number_format((float)($session['closing_amount'] ?? 0), 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="row g-3">
                <div class="col-md-3"><strong>Total ventas POS:</strong> $<?php echo number_format((float)($posBalance['totals']['sales'] ?? 0), 0, ',', '.'); ?></div>
                <div class="col-md-3"><strong>Total retiros:</strong> $<?php echo number_format((float)($posBalance['totals']['withdrawals'] ?? 0), 0, ',', '.'); ?></div>
                <div class="col-md-3"><strong>Total cierre POS:</strong> $<?php echo number_format((float)($posBalance['totals']['closing'] ?? 0), 0, ',', '.'); ?></div>
                <div class="col-md-3"><strong>Dinero entregado:</strong> $<?php echo number_format((float)($dispatch['cash_delivered'] ?? 0), 0, ',', '.'); ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Retorno de envases y balance de productos</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=sales/dispatches/close">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)$dispatch['id']; ?>">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-end">Despachado</th>
                            <th class="text-end">Retorna</th>
                            <th class="text-end">Vendido</th>
                            <th class="text-end">Muy bueno</th>
                            <th class="text-end">Bueno</th>
                            <th class="text-end">Aceptable</th>
                            <th class="text-end">Malo</th>
                            <th class="text-end">Merma</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <?php echo e($item['produced_product_name']); ?>
                                    <input type="hidden" name="item_id[]" value="<?php echo (int)$item['id']; ?>">
                                </td>
                                <td class="text-end"><?php echo number_format((float)$item['quantity_dispatched'], 0, ',', '.'); ?></td>
                                <td><input type="number" min="0" name="empty_returned_total[]" class="form-control text-end" value="<?php echo (int)$item['empty_returned_total']; ?>" <?php echo $dispatch['status'] === 'cerrado' ? 'readonly' : ''; ?>></td>
                                <td class="text-end fw-semibold"><?php echo number_format((float)($item['sold_quantity'] ?? 0), 0, ',', '.'); ?></td>
                                <td><input type="number" min="0" name="empty_muy_bueno[]" class="form-control text-end" value="<?php echo (int)$item['empty_muy_bueno']; ?>" <?php echo $dispatch['status'] === 'cerrado' ? 'readonly' : ''; ?>></td>
                                <td><input type="number" min="0" name="empty_bueno[]" class="form-control text-end" value="<?php echo (int)$item['empty_bueno']; ?>" <?php echo $dispatch['status'] === 'cerrado' ? 'readonly' : ''; ?>></td>
                                <td><input type="number" min="0" name="empty_aceptable[]" class="form-control text-end" value="<?php echo (int)$item['empty_aceptable']; ?>" <?php echo $dispatch['status'] === 'cerrado' ? 'readonly' : ''; ?>></td>
                                <td><input type="number" min="0" name="empty_malo[]" class="form-control text-end" value="<?php echo (int)$item['empty_malo']; ?>" <?php echo $dispatch['status'] === 'cerrado' ? 'readonly' : ''; ?>></td>
                                <td><input type="number" min="0" name="empty_merma[]" class="form-control text-end" value="<?php echo (int)$item['empty_merma']; ?>" <?php echo $dispatch['status'] === 'cerrado' ? 'readonly' : ''; ?>></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th class="text-end"><?php echo number_format((float)($itemTotals['dispatched'] ?? 0), 0, ',', '.'); ?></th>
                            <th class="text-end"><?php echo number_format((float)($itemTotals['returned'] ?? 0), 0, ',', '.'); ?></th>
                            <th class="text-end"><?php echo number_format((float)($itemTotals['sold'] ?? 0), 0, ',', '.'); ?></th>
                            <th colspan="4"></th>
                            <th class="text-end text-danger"><?php echo number_format((float)($itemTotals['merma'] ?? 0), 0, ',', '.'); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-4 ms-auto">
                    <label class="form-label">Dinero entregado por el vendedor</label>
                    <input type="number" min="0" step="0.01" name="cash_delivered" class="form-control" value="<?php echo e($dispatch['cash_delivered']); ?>" <?php echo $dispatch['status'] === 'cerrado' ? 'readonly' : ''; ?> >
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="index.php?route=sales/dispatches" class="btn btn-light">Volver</a>
                <?php if ($dispatch['status'] !== 'cerrado'): ?>
                    <button class="btn btn-success" type="submit">Cerrar despacho</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
