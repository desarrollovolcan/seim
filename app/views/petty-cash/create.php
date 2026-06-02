<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-body d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="card-title mb-1">Registro rápido de caja chica</h4>
            <p class="text-muted mb-0">Carga varias boletas en formato planilla: fecha, número, detalle, valor y respaldo tributario.</p>
        </div>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickProductModal">Agregar producto rápido</button>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=petty-cash/store" id="quickPettyCashForm" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="entry_mode" value="quick_table">

            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-3">
                    <label class="form-label">Moneda para las filas</label>
                    <select name="quick_currency" class="form-select">
                        <option value="CLP">CLP</option>
                        <option value="USD">USD</option>
                        <option value="PEN">PEN</option>
                    </select>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Observación general del lote</label>
                    <input type="text" name="quick_notes" class="form-control" placeholder="Ej: Rendición mayo, compras operacionales, colaciones, insumos...">
                </div>
            </div>

            <div class="alert alert-info d-flex align-items-start gap-2" role="alert">
                <span class="fw-bold">Tip:</span>
                <div>Puedes pegar desde Excel/Sheets columnas en este orden: <strong>Fecha, N° Boleta, Detalle, Valor</strong>. Cada fila permite adjuntar foto o PDF de la boleta.</div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-2" id="quickEntriesTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:4%">N°</th>
                            <th style="width:14%">Fecha</th>
                            <th style="width:14%">N° Boleta</th>
                            <th>Detalle</th>
                            <th style="width:16%">Valor</th>
                            <th style="width:16%">Proveedor</th>
                            <th style="width:18%">Documento</th>
                            <th class="text-center" style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="quickEntriesBody">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <tr class="quick-entry-row">
                                <td class="text-center fw-semibold row-number"><?php echo $i; ?></td>
                                <td><input type="date" name="quick_receipt_date[]" class="form-control form-control-sm quick-date" value="<?php echo e($today); ?>"></td>
                                <td><input type="text" name="quick_receipt_number[]" class="form-control form-control-sm" placeholder="136009"></td>
                                <td><input type="text" name="quick_description[]" class="form-control form-control-sm quick-description" placeholder="Compra de insumos, colación, combustible..."></td>
                                <td><input type="text" name="quick_amount[]" class="form-control form-control-sm quick-amount text-end" inputmode="decimal" placeholder="8.300"></td>
                                <td><input type="text" name="quick_supplier_name[]" class="form-control form-control-sm" placeholder="Caja chica"></td>
                                <td>
                                    <input type="file" name="quick_document[]" class="form-control form-control-sm quick-document" accept="application/pdf,image/jpeg,image/png,image/webp">
                                    <div class="form-text small">PDF/JPG/PNG/WEBP · máx. 10 MB</div>
                                </td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-quick-row" title="Eliminar fila">✕</button></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="4" class="text-end">Total lote</th>
                            <th><input type="text" id="quickBatchTotal" class="form-control form-control-sm fw-bold text-end" value="$ 0" readonly></th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex flex-wrap justify-content-between gap-2 mt-3">
                <button type="button" class="btn btn-outline-secondary" id="addQuickRowsBtn">Agregar 5 filas</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light" id="clearQuickRowsBtn">Limpiar filas vacías</button>
                    <button type="submit" class="btn btn-success">Guardar registros rápidos</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="card-title mb-0">Registro detallado de una boleta</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=petty-cash/store" id="pettyCashForm" enctype="multipart/form-data">
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
                <div class="col-12">
                    <label class="form-label">Documento tributario</label>
                    <div class="border rounded-3 p-3 bg-light">
                        <input type="file" name="document_file" class="form-control" accept="application/pdf,image/jpeg,image/png,image/webp">
                        <div class="form-text">Adjunta foto o PDF de la boleta/factura. Formatos permitidos: PDF, JPG, PNG y WEBP hasta 10 MB.</div>
                    </div>
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
                            <td><input type="number" min="0.01" step="0.01" name="item_quantity[]" class="form-control qty-input" value="1.00" required></td>
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
    const numberFormatter = new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 0 });

    function parseAmount(value) {
        value = String(value || '').replace(/[^0-9,.-]/g, '');
        if (value.includes(',') && value.includes('.')) value = value.replace(/\./g, '').replace(',', '.');
        else if (value.includes(',')) value = value.replace(',', '.');
        else if (/^\d{1,3}(\.\d{3})+$/.test(value)) value = value.replace(/\./g, '');
        return Math.max(0, parseFloat(value || '0') || 0);
    }

    const quickBody = document.getElementById('quickEntriesBody');
    const quickTotal = document.getElementById('quickBatchTotal');
    const quickTemplate = quickBody.querySelector('.quick-entry-row').cloneNode(true);

    function refreshQuickRows() {
        let total = 0;
        quickBody.querySelectorAll('.quick-entry-row').forEach((row, index) => {
            row.querySelector('.row-number').textContent = index + 1;
            total += parseAmount(row.querySelector('.quick-amount').value);
        });
        quickTotal.value = numberFormatter.format(total);
    }

    function bindQuickRow(row) {
        row.querySelector('.quick-amount').addEventListener('input', refreshQuickRows);
        row.querySelector('.remove-quick-row').addEventListener('click', () => {
            if (quickBody.querySelectorAll('.quick-entry-row').length === 1) return;
            row.remove();
            refreshQuickRows();
        });
        row.querySelector('.quick-description').addEventListener('paste', (event) => {
            const text = event.clipboardData ? event.clipboardData.getData('text') : '';
            if (!text.includes('\t') && !text.includes('\n')) return;
            event.preventDefault();
            const rows = text.trim().split(/\r?\n/).map((line) => line.split('\t'));
            let current = row;
            rows.forEach((cols, offset) => {
                if (offset > 0) {
                    current = addQuickRow();
                }
                const inputs = current.querySelectorAll('input');
                if (cols[0]) inputs[0].value = cols[0].match(/^\d{2}\.\d{2}\.\d{4}$/) ? cols[0].split('.').reverse().join('-') : cols[0];
                if (cols[1]) inputs[1].value = cols[1];
                if (cols[2]) inputs[2].value = cols[2];
                if (cols[3]) inputs[3].value = cols[3];
            });
            refreshQuickRows();
        });
    }

    function addQuickRow() {
        const clone = quickTemplate.cloneNode(true);
        clone.querySelectorAll('input').forEach((input) => {
            if (input.type === 'date') input.value = '<?php echo e($today); ?>';
            else input.value = '';
        });
        quickBody.appendChild(clone);
        bindQuickRow(clone);
        refreshQuickRows();
        return clone;
    }

    document.getElementById('addQuickRowsBtn').addEventListener('click', () => {
        for (let i = 0; i < 5; i++) addQuickRow();
    });
    document.getElementById('clearQuickRowsBtn').addEventListener('click', () => {
        quickBody.querySelectorAll('.quick-entry-row').forEach((row) => {
            const hasData = Array.from(row.querySelectorAll('input')).some((input) => input.type !== 'date' && input.type !== 'file' && input.value.trim() !== '');
            if (!hasData && quickBody.querySelectorAll('.quick-entry-row').length > 1) row.remove();
        });
        refreshQuickRows();
    });
    quickBody.querySelectorAll('.quick-entry-row').forEach(bindQuickRow);
    refreshQuickRows();

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

    body.querySelectorAll('.item-row').forEach(bindRow);
    recalc();
})();
</script>
