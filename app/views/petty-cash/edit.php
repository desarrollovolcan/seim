<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Editar boleta de caja chica</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=petty-cash/update" id="pettyCashForm" enctype="multipart/form-data">
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

            <div class="row g-3 mt-1">
                <div class="col-12">
                    <label class="form-label">Documento tributario</label>
                    <div class="border rounded-3 p-3 bg-light">
                        <?php if (!empty($receipt['document_path'])): ?>
                            <div class="mb-2">
                                <a href="<?php echo e($receipt['document_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success">Ver documento actual</a>
                                <span class="text-muted small ms-2"><?php echo e($receipt['document_original_name'] ?? basename($receipt['document_path'])); ?></span>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="document_file" class="form-control" accept="application/pdf,image/jpeg,image/png,image/webp">
                        <div class="form-text">Sube un nuevo PDF o foto para reemplazar el respaldo tributario actual. Máximo 10 MB.</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-sm table-bordered align-middle small" id="itemsTable">
                    <thead><tr><th style="width:20%">Producto</th><th style="width:20%">Descripción</th><th style="width:10%">Cantidad</th><th style="width:12%">Precio unitario</th><th style="width:12%">Subtotal</th><th style="width:20%">Observación</th><th style="width:6%"></th></tr></thead>
                    <tbody id="itemsBody">
                        <?php $rows = !empty($items) ? $items : [['product_id' => null, 'description' => '', 'quantity' => 1, 'unit_price' => 0, 'observation' => '']]; ?>
                        <?php foreach ($rows as $row): ?>
                            <tr class="item-row">
                                <td>
                                    <select name="item_product_id[]" class="form-select form-select-sm product-select">
                                        <option value="">Seleccionar...</option>
                                        <?php foreach ($products as $product): ?>
                                            <?php $selectedProduct = (int)($row['product_id'] ?? 0) === (int)$product['id']; ?>
                                            <option value="<?php echo (int)$product['id']; ?>" data-name="<?php echo e($product['name']); ?>" data-price="<?php echo e((string)$product['suggested_price']); ?>" <?php echo $selectedProduct ? 'selected' : ''; ?>><?php echo e($product['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="text" name="item_description[]" class="form-control form-control-sm description-input" value="<?php echo e($row['description'] ?? ''); ?>" required></td>
                                <td><input type="number" min="0.01" step="0.01" name="item_quantity[]" class="form-control form-control-sm qty-input" value="<?php echo e((string)($row['quantity'] ?? '1.00')); ?>" required></td>
                                <td><input type="number" min="0" step="0.01" name="item_unit_price[]" class="form-control form-control-sm price-input" value="<?php echo e((string)($row['unit_price'] ?? '0')); ?>" required></td>
                                <td><input type="text" class="form-control form-control-sm subtotal-input" value="0.00" readonly></td>
                                <td><input type="text" name="item_observation[]" class="form-control form-control-sm" value="<?php echo e($row['observation'] ?? ''); ?>"></td>
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
