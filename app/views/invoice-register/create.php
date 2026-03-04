<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Registro de factura</h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#supplierModal">Agregar proveedor</button>
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#catalogItemModal">Agregar producto/servicio</button>
        </div>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=invoice-register/store" id="invoiceRegisterForm">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tipo documento</label>
                    <select name="document_type" class="form-select" required>
                        <option value="factura">Factura</option>
                        <option value="boleta">Boleta</option>
                        <option value="servicio">Servicio</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">N° Factura</label>
                    <input type="text" name="invoice_number" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha emisión</label>
                    <input type="date" name="invoice_date" class="form-control" value="<?php echo e($today); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Vencimiento</label>
                    <input type="date" name="due_date" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Proveedor</label>
                    <select class="form-select" id="supplierSelect">
                        <option value="">Selecciona proveedor</option>
                        <?php foreach (($suppliers ?? []) as $supplier): ?>
                            <option value="<?php echo (int)$supplier['id']; ?>" data-name="<?php echo e($supplier['name'] ?? ''); ?>" data-rut="<?php echo e($supplier['tax_id'] ?? ''); ?>">
                                <?php echo e($supplier['name'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nombre proveedor</label>
                    <input type="text" name="supplier_name" id="supplierName" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">RUT proveedor</label>
                    <input type="text" name="supplier_tax_id" id="supplierTaxId" class="form-control" placeholder="76.123.456-7">
                </div>
                <div class="col-md-2">
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
                            <th style="width:14%">Catálogo</th>
                            <th style="width:10%">Tipo</th>
                            <th style="width:24%">Descripción</th>
                            <th style="width:10%">Cantidad</th>
                            <th style="width:14%">Precio unitario</th>
                            <th style="width:14%">Subtotal</th>
                            <th style="width:14%">Observación</th>
                            <th style="width:6%"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td>
                                <select class="form-select catalog-select">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach (($catalogProducts ?? []) as $product): ?>
                                        <?php $classification = ($product['classification'] ?? '') === 'producto' ? 'producto' : 'servicio'; ?>
                                        <option value="<?php echo (int)$product['id']; ?>" data-name="<?php echo e($product['name'] ?? ''); ?>" data-type="<?php echo e($classification); ?>" data-price="<?php echo e((string)((float)($product['suggested_price'] ?? 0))); ?>">
                                            <?php echo e($product['name'] ?? ''); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select name="item_type[]" class="form-select item-type-select">
                                    <option value="producto">Producto</option>
                                    <option value="servicio">Servicio</option>
                                </select>
                            </td>
                            <td><input type="text" name="item_description[]" class="form-control description-input" required></td>
                            <td><input type="number" min="1" step="1" name="item_quantity[]" class="form-control qty-input" value="1" required></td>
                            <td><input type="number" min="0" step="0.01" name="item_unit_price[]" class="form-control price-input" value="0" required></td>
                            <td><input type="text" class="form-control subtotal-input" value="0.00" readonly></td>
                            <td><input type="text" name="item_observation[]" class="form-control" placeholder="Opcional"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">✕</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Observación general</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Neto</label>
                    <input type="text" id="netAmount" class="form-control" value="0.00" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">IVA (19%)</label>
                    <input type="text" id="taxAmount" class="form-control" value="0.00" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total</label>
                    <input type="text" id="totalAmount" class="form-control fw-bold" value="0.00" readonly>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-outline-primary" id="addRowBtn">Agregar fila</button>
                <button type="submit" class="btn btn-primary">Guardar factura</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="catalogItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar producto/servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="index.php?route=invoice-register/catalog-products/store" id="catalogItemForm">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Clasificación</label><select name="classification" class="form-select" required><option value="servicio">Servicio</option><option value="producto">Producto</option></select></div>
                    <div class="mb-3"><label class="form-label">Categoría</label><input type="text" name="category" class="form-control" value="General"></div>
                    <div class="mb-3"><label class="form-label">Unidad de medida</label><input type="text" name="unit_measure" class="form-control" value="Unidad"></div>
                    <div><label class="form-label">Precio sugerido</label><input type="number" min="0" step="0.01" name="suggested_price" class="form-control" value="0"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar producto/servicio</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="supplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="index.php?route=invoice-register/suppliers/store" id="quickSupplierForm">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nombre</label><input type="text" name="supplier_name" class="form-control" required></div>
                        <div class="col-md-3"><label class="form-label">Código</label><input type="text" name="supplier_code" class="form-control" required></div>
                        <div class="col-md-3"><label class="form-label">RUT</label><input type="text" name="supplier_tax_id" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Contacto</label><input type="text" name="supplier_contact_name" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Teléfono</label><input type="text" name="supplier_phone" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="supplier_email" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Web</label><input type="url" name="supplier_website" class="form-control" placeholder="https://"></div>
                        <div class="col-12"><label class="form-label">Dirección</label><input type="text" name="supplier_address" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Giro</label><input type="text" name="supplier_giro" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Comuna</label><input type="text" name="supplier_commune" class="form-control"></div>
                        <div class="col-12"><label class="form-label">Notas</label><textarea name="supplier_notes" class="form-control" rows="2"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar proveedor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
    const body = document.getElementById('itemsBody');
    const addRowBtn = document.getElementById('addRowBtn');
    const supplierSelect = document.getElementById('supplierSelect');
    const supplierName = document.getElementById('supplierName');
    const supplierTaxId = document.getElementById('supplierTaxId');

    const recalc = () => {
        let net = 0;
        body.querySelectorAll('.item-row').forEach((row) => {
            const qty = parseFloat(row.querySelector('.qty-input')?.value || '0');
            const price = parseFloat(row.querySelector('.price-input')?.value || '0');
            const subtotal = qty * price;
            row.querySelector('.subtotal-input').value = subtotal.toFixed(2);
            net += subtotal;
        });

        const tax = net * 0.19;
        const total = net + tax;
        document.getElementById('netAmount').value = net.toFixed(2);
        document.getElementById('taxAmount').value = tax.toFixed(2);
        document.getElementById('totalAmount').value = total.toFixed(2);
    };

    const bindRow = (row) => {
        const catalogSelect = row.querySelector('.catalog-select');
        const typeSelect = row.querySelector('.item-type-select');
        const descriptionInput = row.querySelector('.description-input');
        const priceInput = row.querySelector('.price-input');

        catalogSelect?.addEventListener('change', () => {
            const selected = catalogSelect.options[catalogSelect.selectedIndex];
            if (!selected || !selected.value) return;

            if (!descriptionInput.value.trim()) descriptionInput.value = selected.dataset.name || '';
            if (!parseFloat(priceInput.value || '0')) priceInput.value = selected.dataset.price || '0';
            if (selected.dataset.type === 'producto' || selected.dataset.type === 'servicio') {
                typeSelect.value = selected.dataset.type;
            }
            recalc();
        });

        row.querySelectorAll('.qty-input, .price-input').forEach((input) => input.addEventListener('input', recalc));

        row.querySelector('.remove-row')?.addEventListener('click', () => {
            if (body.querySelectorAll('.item-row').length === 1) {
                row.querySelectorAll('input').forEach((input) => {
                    if (input.type === 'number') {
                        input.value = input.classList.contains('qty-input') ? '1' : '0';
                    } else {
                        input.value = '';
                    }
                });
                row.querySelector('.catalog-select').selectedIndex = 0;
                row.querySelector('.item-type-select').value = 'producto';
                recalc();
                return;
            }
            row.remove();
            recalc();
        });
    };

    addRowBtn.addEventListener('click', () => {
        const row = body.querySelector('.item-row').cloneNode(true);
        row.querySelectorAll('input').forEach((input) => {
            if (input.classList.contains('qty-input')) input.value = '1';
            else if (input.classList.contains('subtotal-input')) input.value = '0.00';
            else if (input.classList.contains('price-input')) input.value = '0';
            else input.value = '';
        });
        row.querySelector('.catalog-select').selectedIndex = 0;
        row.querySelector('.item-type-select').value = 'producto';
        body.appendChild(row);
        bindRow(row);
        recalc();
    });

    supplierSelect?.addEventListener('change', () => {
        const selected = supplierSelect.options[supplierSelect.selectedIndex];
        if (!selected || !selected.value) return;
        supplierName.value = selected.dataset.name || '';
        supplierTaxId.value = selected.dataset.rut || '';
    });

    const catalogItemForm = document.getElementById('catalogItemForm');
    catalogItemForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submitButton = catalogItemForm.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Guardando...';

        try {
            const response = await fetch(catalogItemForm.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(catalogItemForm),
            });
            const data = await response.json();
            if (!response.ok || !data.ok) throw new Error(data.message || 'No se pudo guardar el producto/servicio.');

            document.querySelectorAll('.catalog-select').forEach((select) => {
                const option = document.createElement('option');
                option.value = data.product.id;
                option.dataset.name = data.product.name;
                option.dataset.type = data.product.classification;
                option.dataset.price = data.product.suggested_price;
                option.textContent = data.product.name;
                select.appendChild(option);
            });

            catalogItemForm.reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById('catalogItemModal'));
            if (modal) modal.hide();
        } catch (error) {
            alert(error.message || 'No se pudo guardar el producto/servicio.');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    });

    const quickSupplierForm = document.getElementById('quickSupplierForm');
    quickSupplierForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submitButton = quickSupplierForm.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Guardando...';

        try {
            const response = await fetch(quickSupplierForm.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(quickSupplierForm),
            });
            const data = await response.json();
            if (!response.ok || !data.ok) throw new Error(data.message || 'No se pudo guardar el proveedor.');

            const option = document.createElement('option');
            option.value = data.supplier.id;
            option.dataset.name = data.supplier.name;
            option.dataset.rut = data.supplier.tax_id || '';
            option.textContent = data.supplier.name;
            supplierSelect.appendChild(option);
            supplierSelect.value = String(data.supplier.id);
            supplierName.value = data.supplier.name || '';
            supplierTaxId.value = data.supplier.tax_id || '';

            quickSupplierForm.reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById('supplierModal'));
            if (modal) modal.hide();
        } catch (error) {
            alert(error.message || 'No se pudo guardar el proveedor.');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    });

    body.querySelectorAll('.item-row').forEach(bindRow);
    recalc();
})();
</script>
