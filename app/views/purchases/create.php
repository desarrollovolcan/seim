<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Nueva compra</h4>
                <a href="index.php?route=products/create" class="btn btn-soft-secondary btn-sm">Crear producto</a>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=purchases/store" id="purchase-form">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Proveedor</label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Selecciona proveedor</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?php echo (int)$supplier['id']; ?>"><?php echo e($supplier['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="purchase_date" class="form-control" value="<?php echo e($today); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select name="status" class="form-select">
                                <option value="pendiente">Pendiente</option>
                                <option value="recibida">Recibida</option>
                                <option value="completada">Completada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Referencia / N° documento</label>
                            <input type="text" name="reference" class="form-control" placeholder="Factura, OC u otro identificador">
                        </div>
                        <div class="col-12">
                            <?php
                            $siiData = [
                                'sii_document_type' => 'factura_electronica',
                                'sii_tax_rate' => 19,
                                'sii_exempt_amount' => 0,
                            ];
                            $siiLabel = 'Proveedor';
                            include __DIR__ . '/../partials/sii-document-fields.php';
                            ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Productos</label>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle" id="purchase-items-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%;">Producto</th>
                                            <th style="width: 15%;">Cantidad</th>
                                            <th style="width: 20%;">Costo unitario</th>
                                            <th class="text-end" style="width: 20%;">Subtotal</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="item-row">
                                            <td>
                                                <select name="product_id[]" class="form-select form-select-sm product-select">
                                                    <option value="">Selecciona</option>
                                                    <?php foreach ($products as $product): ?>
                                                        <option value="<?php echo (int)$product['id']; ?>" data-cost="<?php echo e((float)($product['cost'] ?? $product['price'] ?? 0)); ?>">
                                                            <?php echo e($product['name']); ?> (Stock: <?php echo (int)($product['stock'] ?? 0); ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td><input type="number" name="quantity[]" class="form-control form-control-sm quantity-input" min="1" value="1"></td>
                                            <td><input type="number" name="unit_cost[]" class="form-control form-control-sm cost-input" step="0.01" min="0" value="0"></td>
                                            <td class="text-end item-subtotal fw-semibold">0</td>
                                            <td><button type="button" class="btn btn-link text-danger p-0 remove-row">✕</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-item">Agregar producto</button>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Notas internas</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Observaciones de la compra"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column align-items-end gap-2">
                                <div class="d-flex justify-content-between w-100">
                                    <span>Subtotal</span>
                                    <strong id="subtotal-display"><?php echo format_currency(0); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between w-100 align-items-center">
                                    <span>Impuestos</span>
                                    <input type="number" name="tax" id="tax-input" class="form-control form-control-sm w-auto" style="width: 140px;" step="0.01" min="0" value="<?php echo e($taxDefault ?? 0); ?>">
                                </div>
                                <div class="d-flex justify-content-between w-100">
                                    <span>Total</span>
                                    <strong id="total-display"><?php echo format_currency(0); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-actions">
                                <a href="index.php?route=purchases" class="btn btn-light">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar compra</button>
                            </div>
                        </div>
                    </div>
                
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'purchases/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
            </div>
        </div>
    </div>
</div>

<script>
    const supplierSiiMap = <?php echo json_encode(array_reduce($suppliers ?? [], static function (array $carry, array $supplier): array {
        $carry[(int)($supplier['id'] ?? 0)] = [
            'rut' => $supplier['tax_id'] ?? '',
            'name' => $supplier['name'] ?? '',
            'giro' => $supplier['giro'] ?? '',
            'address' => $supplier['address'] ?? '',
            'commune' => $supplier['commune'] ?? '',
        ];
        return $carry;
    }, [])); ?>;

    const supplierSelect = document.querySelector('[name="supplier_id"]');
    const siiInputs = {
        sii_receiver_rut: document.querySelector('[name="sii_receiver_rut"]'),
        sii_receiver_name: document.querySelector('[name="sii_receiver_name"]'),
        sii_receiver_giro: document.querySelector('[name="sii_receiver_giro"]'),
        sii_receiver_address: document.querySelector('[name="sii_receiver_address"]'),
        sii_receiver_commune: document.querySelector('[name="sii_receiver_commune"]'),
    };
    const siiWarning = document.querySelector('[data-sii-warning]');
    const siiWarningText = document.querySelector('[data-sii-warning-text]');
    const siiWarningLink = document.querySelector('[data-sii-warning-link]');
    const siiRequiredFields = [
        { key: 'rut', label: 'RUT' },
        { key: 'name', label: 'Razón social' },
        { key: 'giro', label: 'Giro' },
        { key: 'address', label: 'Dirección' },
        { key: 'commune', label: 'Comuna' },
    ];

    const updateSiiWarning = (data, supplierId) => {
        if (!siiWarning || !siiWarningText || !siiWarningLink) {
            return;
        }
        const missing = siiRequiredFields.filter((field) => !(data?.[field.key] || '').trim());
        if (missing.length === 0 || !supplierId) {
            siiWarning.classList.add('d-none');
            return;
        }
        siiWarningText.textContent = `Completa en la ficha del proveedor: ${missing.map((field) => field.label).join(', ')}.`;
        siiWarningLink.href = `index.php?route=suppliers/edit&id=${supplierId}`;
        siiWarning.classList.remove('d-none');
    };
    const applySupplierSii = (supplierId, force = false) => {
        const data = supplierSiiMap?.[supplierId];
        if (!data) {
            updateSiiWarning({}, supplierId);
            return;
        }
        if (siiInputs.sii_receiver_rut) siiInputs.sii_receiver_rut.value = data.rut || '';
        if (siiInputs.sii_receiver_name) siiInputs.sii_receiver_name.value = data.name || '';
        if (siiInputs.sii_receiver_giro) siiInputs.sii_receiver_giro.value = data.giro || '';
        if (siiInputs.sii_receiver_address) siiInputs.sii_receiver_address.value = data.address || '';
        if (siiInputs.sii_receiver_commune) siiInputs.sii_receiver_commune.value = data.commune || '';
        updateSiiWarning(data, supplierId);
    };

    supplierSelect?.addEventListener('change', (event) => {
        const supplierId = Number(event.target.value || 0);
        applySupplierSii(supplierId);
    });
    if (supplierSelect?.value) {
        applySupplierSii(Number(supplierSelect.value || 0), true);
    }

    (function() {
        const tableBody = document.querySelector('#purchase-items-table tbody');
        const addButton = document.querySelector('#add-item');
        const subtotalDisplay = document.getElementById('subtotal-display');
        const totalDisplay = document.getElementById('total-display');
        const taxInput = document.getElementById('tax-input');

        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 }).format(amount || 0);
        }

        function recalc() {
            let subtotal = 0;
            tableBody.querySelectorAll('.item-row').forEach((row) => {
                const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
                const total = qty * cost;
                subtotal += total;
                row.querySelector('.item-subtotal').innerText = formatCurrency(total);
            });
            subtotalDisplay.innerText = formatCurrency(subtotal);
            const tax = parseFloat(taxInput.value) || 0;
            totalDisplay.innerText = formatCurrency(subtotal + tax);
        }

        function addRow() {
            const template = tableBody.querySelector('.item-row');
            const clone = template.cloneNode(true);
            clone.querySelectorAll('input').forEach((input) => {
                input.value = input.classList.contains('quantity-input') ? '1' : '0';
            });
            clone.querySelector('.product-select').selectedIndex = 0;
            clone.querySelector('.item-subtotal').innerText = formatCurrency(0);
            tableBody.appendChild(clone);
        }

        tableBody.addEventListener('change', (event) => {
            if (event.target.classList.contains('product-select')) {
                const cost = event.target.selectedOptions[0]?.dataset.cost || 0;
                const row = event.target.closest('.item-row');
                row.querySelector('.cost-input').value = cost;
            }
            recalc();
        });

        tableBody.addEventListener('input', (event) => {
            if (event.target.classList.contains('quantity-input') || event.target.classList.contains('cost-input')) {
                recalc();
            }
        });

        tableBody.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-row')) {
                const rows = tableBody.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    event.target.closest('.item-row').remove();
                    recalc();
                }
            }
        });

        addButton?.addEventListener('click', () => {
            addRow();
        });

        taxInput?.addEventListener('input', recalc);
        recalc();
    })();
</script>
