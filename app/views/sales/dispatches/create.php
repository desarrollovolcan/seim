<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Registrar despacho de camión</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=sales/dispatches/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Fecha despacho</label>
                    <input type="date" name="dispatch_date" class="form-control" value="<?php echo e($today); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Camión</label>
                    <input type="text" name="truck_code" class="form-control" required placeholder="Ej: CAM-12 / Patente">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Vendedor</label>
                    <input type="text" name="seller_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sesión POS (opcional)</label>
                    <select name="pos_session_id" class="form-select">
                        <option value="">Sin vincular</option>
                        <?php foreach ($sessions as $session): ?>
                            <option value="<?php echo (int)$session['id']; ?>"><?php echo e(($session['session_code'] ?? 'Caja') . ' · ' . ($session['opened_at'] ?? '')); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Producto fabricado</th>
                            <th style="width: 170px;">Cantidad despachada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($producedProducts as $product): ?>
                            <tr>
                                <td>
                                    <?php echo e($product['name']); ?>
                                    <small class="text-muted d-block"><?php echo e($product['sku'] ?: 'Sin SKU'); ?></small>
                                    <input type="hidden" name="produced_product_id[]" value="<?php echo (int)$product['id']; ?>">
                                </td>
                                <td>
                                    <input type="number" min="0" class="form-control" name="quantity_dispatched[]" value="0">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mb-3">
                <label class="form-label">Observaciones</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=sales/dispatches" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar despacho</button>
            </div>
        </form>
    </div>
</div>
