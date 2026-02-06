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
                    <label class="form-label">Vendedor (usuario del sistema)</label>
                    <select name="seller_user_id" id="seller_user_id" class="form-select" required>
                        <option value="">Seleccionar vendedor</option>
                        <?php foreach ($sellerUsers as $sellerUser): ?>
                            <option value="<?php echo (int)$sellerUser['id']; ?>"><?php echo e($sellerUser['name'] . " (" . ($sellerUser['email'] ?? "sin email") . ")"); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sesión POS (opcional)</label>
                    <select name="pos_session_id" class="form-select">
                        <option value="">Sin vincular</option>
                        <?php foreach ($sessions as $session): ?>
                            <option value="<?php echo (int)$session['id']; ?>" data-user-id="<?php echo (int)($session['user_id'] ?? 0); ?>"><?php echo e(($session['session_code'] ?? 'Caja') . ' · ' . ($session['opened_at'] ?? '')); ?></option>
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

<script>
(function () {
    const sellerSelect = document.getElementById('seller_user_id');
    const sessionSelect = document.querySelector('select[name="pos_session_id"]');
    if (!sellerSelect || !sessionSelect) return;

    const allOptions = Array.from(sessionSelect.querySelectorAll('option')).map((opt) => ({
        value: opt.value,
        text: opt.textContent,
        userId: opt.dataset.userId || '',
    }));

    const renderSessions = () => {
        const sellerId = sellerSelect.value;
        const previous = sessionSelect.value;
        sessionSelect.innerHTML = '';

        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = sellerId ? 'Sin vincular' : 'Selecciona vendedor primero';
        sessionSelect.appendChild(defaultOpt);

        allOptions.forEach((opt) => {
            if (!opt.value) return;
            if (!sellerId || String(opt.userId) !== String(sellerId)) return;
            const o = document.createElement('option');
            o.value = opt.value;
            o.textContent = opt.text;
            o.dataset.userId = opt.userId;
            if (opt.value === previous) o.selected = true;
            sessionSelect.appendChild(o);
        });
    };

    sellerSelect.addEventListener('change', renderSessions);
    renderSessions();
})();
</script>

<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title mb-0">Últimos despachos registrados</h4>
    </div>
    <div class="card-body">
        <?php if (empty($recentDispatches)): ?>
            <p class="text-muted mb-0">Aún no hay despachos registrados.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Camión</th>
                            <th>Vendedor</th>
                            <th>POS</th>
                            <th class="text-end">Despachado</th>
                            <th class="text-end">Retorno</th>
                            <th class="text-end">Dinero</th>
                            <th>Estado</th>
                            <th class="text-end">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentDispatches as $dispatch): ?>
                            <tr>
                                <td><?php echo e($dispatch['dispatch_date']); ?></td>
                                <td><?php echo e($dispatch['truck_code']); ?></td>
                                <td><?php echo e($dispatch['seller_name']); ?></td>
                                <td><?php echo e($dispatch['session_code'] ?? '-'); ?></td>
                                <td class="text-end"><?php echo number_format((float)$dispatch['total_dispatched'], 0, ',', '.'); ?></td>
                                <td class="text-end"><?php echo number_format((float)$dispatch['total_empty_returned'], 0, ',', '.'); ?></td>
                                <td class="text-end">$<?php echo number_format((float)$dispatch['cash_delivered'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="badge <?php echo ($dispatch['status'] ?? '') === 'cerrado' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'; ?>">
                                        <?php echo e(ucfirst((string)($dispatch['status'] ?? ''))); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="index.php?route=sales/dispatches/show&id=<?php echo (int)$dispatch['id']; ?>" class="btn btn-sm btn-soft-primary">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
