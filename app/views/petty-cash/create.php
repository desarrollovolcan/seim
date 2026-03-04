<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Registro de boleta</h4>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickProductModal">Agregar producto rápido</button>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=petty-cash/store" id="pettyCashForm">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">N° Boleta</label>
                    <input type="text" name="receipt_number" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="receipt_date" class="form-control" value="<?php echo e($today); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Proveedor</label>
                    <input type="text" name="supplier_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Moneda</label>
                    <select name="currency" class="form-select" required>
                        <option value="CLP">CLP</option>
                        <option value="USD">USD</option>
                        <option value="PEN">PEN</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead>
                        <tr>
                            <th style="width:20%">Producto</th>
                            <th style="width:20%">Descripción</th>
                            <th style="width:10%">Cantidad</th>
                            <th style="width:12%">Precio unitario</th>
                            <th style="width:12%">Subtotal</th>
                            <th style="width:20%">Observación</th>
                            <th style="width:6%"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td>
                                <select name="item_product_id[]" class="form-select product-select">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo (int)$product['id']; ?>" data-name="<?php echo e($product['name']); ?>" data-price="<?php echo e((string)$product['suggested_price']); ?>" data-unit-measure="<?php echo e($product['unit_measure'] ?? 'Unidad'); ?>">
                                            <?php $cls = ($product['classification'] ?? $product['category'] ?? 'servicio'); ?><?php echo e($product['name']); ?> (<?php echo e(ucfirst($cls)); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="item_description[]" class="form-control description-input" required></td>
                            <td><input type="number" min="1" step="1" name="item_quantity[]" class="form-control qty-input" value="1" required></td>
                            <td><input type="number" min="0" step="0.01" name="item_unit_price[]" class="form-control price-input" value="0" required></td>
                            <td><input type="text" class="form-control subtotal-input" value="0.00" readonly></td>
                            <td><input type="text" name="item_observation[]" class="form-control" placeholder="Observación por ítem"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">✕</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Observación general</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total</label>
                    <input type="text" id="receiptTotal" class="form-control fw-bold" value="0.00" readonly>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-outline-primary" id="addRowBtn">Agregar fila</button>
                <button type="submit" class="btn btn-primary">Guardar boleta</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="quickProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar producto rápido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="index.php?route=petty-cash/products/store" id="quickProductForm">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Clasificación</label>
                        <select name="classification" class="form-select" required>
                            <option value="servicio">Servicio</option>
                            <option value="producto">Producto</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <input type="text" name="category" class="form-control" placeholder="General">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unidad de medida</label>
                        <input type="text" name="unit_measure" class="form-control" placeholder="Unidad, kg, hora, litro..." value="Unidad">
                    </div>
                    <div>
                        <label class="form-label">Precio sugerido</label>
                        <input type="number" min="0" step="0.01" name="suggested_price" class="form-control" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
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
                if (!description.value.trim()) {
                    description.value = selected.dataset.name || '';
                }
                if (!parseFloat(price.value || '0')) {
                    price.value = (parseFloat(selected.dataset.price || '0')).toFixed(2);
                }
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
            if (el.classList.contains('qty-input')) el.value = '1';
            else if (el.classList.contains('subtotal-input')) el.value = '0.00';
            else el.value = '';
        });
        clone.querySelector('.price-input').value = '0';
        clone.querySelector('.product-select').selectedIndex = 0;
        body.appendChild(clone);
        bindRow(clone);
        recalc();
    });

    const quickProductForm = document.getElementById('quickProductForm');
    const quickProductModal = document.getElementById('quickProductModal');

    quickProductForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(quickProductForm);
        const submitButton = quickProductForm.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Guardando...';

        try {
            const response = await fetch(quickProductForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();
            if (!response.ok || !data.ok) {
                throw new Error(data.message || 'No se pudo crear el producto.');
            }

            const product = data.product;
            const optionHtml = `${product.name} (${product.category || 'General'} · ${product.unit_measure || 'Unidad'})`;
            document.querySelectorAll('.product-select').forEach((select) => {
                const option = document.createElement('option');
                option.value = product.id;
                option.dataset.name = product.name;
                option.dataset.price = product.suggested_price;
                option.dataset.unit = product.unit_measure || 'Unidad';
                option.textContent = optionHtml;
                select.appendChild(option);
            });

            quickProductForm.reset();
            const instance = bootstrap.Modal.getInstance(quickProductModal);
            if (instance) instance.hide();
        } catch (error) {
            alert(error.message || 'No se pudo crear el producto.');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    });

    body.querySelectorAll('.item-row').forEach(bindRow);
    recalc();
})();
</script>
