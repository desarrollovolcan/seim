<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Registro de factura</h4>
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
                <div class="col-md-4">
                    <label class="form-label">Proveedor</label>
                    <input type="text" name="supplier_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">RUT proveedor</label>
                    <input type="text" name="supplier_tax_id" class="form-control" placeholder="76.123.456-7">
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
                            <th style="width:10%">Tipo</th>
                            <th style="width:30%">Descripción</th>
                            <th style="width:10%">Cantidad</th>
                            <th style="width:14%">Precio unitario</th>
                            <th style="width:14%">Subtotal</th>
                            <th style="width:16%">Observación</th>
                            <th style="width:6%"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td>
                                <select name="item_type[]" class="form-select">
                                    <option value="producto">Producto</option>
                                    <option value="servicio">Servicio</option>
                                </select>
                            </td>
                            <td><input type="text" name="item_description[]" class="form-control" required></td>
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

<script>
(() => {
    const body = document.getElementById('itemsBody');
    const addRowBtn = document.getElementById('addRowBtn');

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
        row.querySelectorAll('.qty-input, .price-input').forEach((input) => {
            input.addEventListener('input', recalc);
        });

        row.querySelector('.remove-row')?.addEventListener('click', () => {
            if (body.querySelectorAll('.item-row').length === 1) {
                row.querySelectorAll('input').forEach((input) => {
                    if (input.type === 'number') {
                        input.value = input.classList.contains('qty-input') ? '1' : '0';
                    } else {
                        input.value = '';
                    }
                });
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
            if (input.classList.contains('qty-input')) {
                input.value = '1';
            } else if (input.classList.contains('subtotal-input')) {
                input.value = '0.00';
            } else if (input.classList.contains('price-input')) {
                input.value = '0';
            } else {
                input.value = '';
            }
        });
        row.querySelector('select').value = 'producto';
        body.appendChild(row);
        bindRow(row);
        recalc();
    });

    body.querySelectorAll('.item-row').forEach(bindRow);
    recalc();
})();
</script>
