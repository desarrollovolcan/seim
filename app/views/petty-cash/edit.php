<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Editar boleta de caja chica</h4>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickProductModal">Agregar producto rápido</button>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=petty-cash/update" id="pettyCashForm">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)$receipt['id']; ?>">

            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">N° Boleta</label><input type="text" name="receipt_number" class="form-control" value="<?php echo e($receipt['receipt_number'] ?? ''); ?>" required></div>
                <div class="col-md-3"><label class="form-label">Fecha</label><input type="date" name="receipt_date" class="form-control" value="<?php echo e($receipt['receipt_date'] ?? date('Y-m-d')); ?>" required></div>
                <div class="col-md-3"><label class="form-label">Proveedor</label><input type="text" name="supplier_name" class="form-control" value="<?php echo e($receipt['supplier_name'] ?? ''); ?>" required></div>
                <div class="col-md-3">
                    <label class="form-label">Moneda</label>
                    <select name="currency" class="form-select" required>
                        <?php foreach (['CLP', 'USD', 'PEN'] as $currency): ?>
                            <option value="<?php echo $currency; ?>" <?php echo ($receipt['currency'] ?? 'CLP') === $currency ? 'selected' : ''; ?>><?php echo $currency; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead><tr><th style="width:20%">Producto</th><th style="width:20%">Descripción</th><th style="width:10%">Cantidad</th><th style="width:12%">Precio unitario</th><th style="width:12%">Subtotal</th><th style="width:20%">Observación</th><th style="width:6%"></th></tr></thead>
                    <tbody id="itemsBody">
                        <?php $rows = !empty($items) ? $items : [['product_id' => null, 'description' => '', 'quantity' => 1, 'unit_price' => 0, 'observation' => '']]; ?>
                        <?php foreach ($rows as $row): ?>
                            <tr class="item-row">
                                <td>
                                    <select name="item_product_id[]" class="form-select product-select">
                                        <option value="">Seleccionar...</option>
                                        <?php foreach ($products as $product): ?>
                                            <?php $selectedProduct = (int)($row['product_id'] ?? 0) === (int)$product['id']; ?>
                                            <option value="<?php echo (int)$product['id']; ?>" data-name="<?php echo e($product['name']); ?>" data-price="<?php echo e((string)$product['suggested_price']); ?>" <?php echo $selectedProduct ? 'selected' : ''; ?>><?php echo e($product['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="text" name="item_description[]" class="form-control description-input" value="<?php echo e($row['description'] ?? ''); ?>" required></td>
                                <td><input type="number" min="0.01" step="0.01" name="item_quantity[]" class="form-control qty-input" value="<?php echo e((string)($row['quantity'] ?? '1.00')); ?>" required></td>
                                <td><input type="number" min="0" step="0.01" name="item_unit_price[]" class="form-control price-input" value="<?php echo e((string)($row['unit_price'] ?? '0')); ?>" required></td>
                                <td><input type="text" class="form-control subtotal-input" value="0.00" readonly></td>
                                <td><input type="text" name="item_observation[]" class="form-control" value="<?php echo e($row['observation'] ?? ''); ?>"></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">✕</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row g-3">
                <div class="col-md-8"><label class="form-label">Observación general</label><textarea name="notes" class="form-control" rows="2"><?php echo e($receipt['notes'] ?? ''); ?></textarea></div>
                <div class="col-md-4"><label class="form-label">Total</label><input type="text" id="receiptTotal" class="form-control fw-bold" value="0.00" readonly></div>
            </div>

            <div class="mt-3 d-flex gap-2 justify-content-end">
                <a href="index.php?route=petty-cash" class="btn btn-light">Cancelar</a>
                <button type="button" class="btn btn-outline-primary" id="addRowBtn">Agregar fila</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="quickProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Agregar producto rápido</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="post" action="index.php?route=petty-cash/products/store" id="quickProductForm"><input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Clasificación</label><select name="classification" class="form-select" required><option value="servicio">Servicio</option><option value="producto">Producto</option></select></div>
                <div class="mb-3"><label class="form-label">Categoría</label><input type="text" name="category" class="form-control" value="General"></div>
                <div class="mb-3"><label class="form-label">Unidad de medida</label><input type="text" name="unit_measure" class="form-control" value="Unidad"></div>
                <div><label class="form-label">Precio sugerido</label><input type="number" min="0" step="0.01" name="suggested_price" class="form-control" value="0"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button><button type="submit" class="btn btn-primary">Guardar</button></div>
        </form>
    </div></div>
</div>

<script>
(function () {
    const body = document.getElementById('itemsBody');
    const addBtn = document.getElementById('addRowBtn');
    const totalInput = document.getElementById('receiptTotal');

    function recalc() {
        let total = 0;
        body.querySelectorAll('.item-row').forEach((row) => {
            const qty = parseFloat(row.querySelector('.qty-input').value || '0');
            const price = parseFloat(row.querySelector('.price-input').value || '0');
            const subtotal = qty * price;
            row.querySelector('.subtotal-input').value = subtotal.toFixed(2);
            total += subtotal;
        });
        totalInput.value = total.toFixed(2);
    }

    function bindRow(row) {
        const productSelect = row.querySelector('.product-select');
        const description = row.querySelector('.description-input');
        const price = row.querySelector('.price-input');

        productSelect.addEventListener('change', () => {
            const selected = productSelect.options[productSelect.selectedIndex];
            if (selected && selected.value) {
                if (!description.value.trim()) description.value = selected.dataset.name || '';
                if (!parseFloat(price.value || '0')) price.value = (parseFloat(selected.dataset.price || '0')).toFixed(2);
            }
            recalc();
        });

        row.querySelector('.qty-input').addEventListener('input', recalc);
        row.querySelector('.price-input').addEventListener('input', recalc);
        row.querySelector('.remove-row').addEventListener('click', () => {
            if (body.querySelectorAll('.item-row').length === 1) return;
            row.remove();
            recalc();
        });
    }

    addBtn.addEventListener('click', () => {
        const first = body.querySelector('.item-row');
        const clone = first.cloneNode(true);
        clone.querySelectorAll('input').forEach((el) => {
            if (el.classList.contains('qty-input')) el.value = '1.00';
            else if (el.classList.contains('subtotal-input')) el.value = '0.00';
            else el.value = '';
        });
        clone.querySelector('.price-input').value = '0';
        clone.querySelector('.product-select').selectedIndex = 0;
        body.appendChild(clone);
        bindRow(clone);
        recalc();
    });

    body.querySelectorAll('.item-row').forEach(bindRow);
    recalc();
})();
</script>
