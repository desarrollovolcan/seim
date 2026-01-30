<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Nuevo movimiento</h4>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=inventory/movements/store">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">Selecciona producto</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo (int)$product['id']; ?>">
                                    <?php echo e($product['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="movement_date" class="form-control" value="<?php echo e($today); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de movimiento</label>
                        <select name="movement_type" class="form-select">
                            <option value="entrada">Entrada</option>
                            <option value="salida">Salida</option>
                            <option value="ajuste">Ajuste</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Costo unitario</label>
                        <input type="number" step="0.01" name="unit_cost" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Referencia</label>
                        <input type="text" name="reference_type" class="form-control" placeholder="Compra, venta, ajuste">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ID referencia</label>
                        <input type="number" name="reference_id" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Kardex / movimientos</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Costo</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movements as $movement): ?>
                                <tr>
                                    <td><?php echo e($movement['product_name'] ?? ''); ?></td>
                                    <td><?php echo e(format_date($movement['movement_date'] ?? null)); ?></td>
                                    <td class="text-capitalize"><?php echo e($movement['movement_type']); ?></td>
                                    <td><?php echo (int)($movement['quantity'] ?? 0); ?></td>
                                    <td><?php echo e(format_currency((float)($movement['unit_cost'] ?? 0))); ?></td>
                                    <td class="text-end">
                                        <div class="dropdown actions-dropdown">
                                            <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Acciones
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="index.php?route=inventory/movements/show&id=<?php echo (int)$movement['id']; ?>">Ver detalle</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="index.php?route=inventory/movements/edit&id=<?php echo (int)$movement['id']; ?>">Editar</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($movements)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay movimientos registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
