<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Registro simplificado de compra con factura</h4>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#supplierModal">Agregar proveedor</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#catalogItemModal">Agregar producto/servicio al catálogo</button>
                </div>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=purchases/store" id="purchase-form">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Proveedor</label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Selecciona proveedor</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?php echo (int)$supplier['id']; ?>"><?php echo e($supplier['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha factura</label>
                            <input type="date" name="purchase_date" class="form-control" value="<?php echo e($today); ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select name="status" class="form-select">
                                <option value="pendiente">Pendiente</option>
                                <option value="recibida">Recibida</option>
                                <option value="completada">Completada</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">N° factura / ref.</label>
                            <input type="text" name="reference" class="form-control" placeholder="F-12345">
                        </div>

                        <div class="col-12">
                            <?php
                            $siiData = [
                                'sii_document_type' => 'factura_electronica',
                                'sii_tax_rate' => 19,
                                'sii_exempt_amount' => 0,
                            ];
                            $siiLabel = 'Proveedor';
                            $siiShowDocumentNumber = false;
                            include __DIR__ . '/../partials/sii-document-fields.php';
                            ?>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Detalle de compra (catálogo compartido con caja chica)</label>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-item">Agregar ítem</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle" id="purchase-items-table">
                                    <thead>
                                        <tr>
                                            <th>Clasificación</th>
                                            <th>Ítem catálogo</th>
                                            <th>Descripción</th>
                                            <th>Cantidad</th>
                                            <th>Costo unitario</th>
                                            <th class="text-end">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="item-row">
                                            <td><span class="badge bg-secondary-subtle text-secondary classification-badge">-</span></td>
                                            <td>
                                                <select name="catalog_product_id[]" class="form-select form-select-sm catalog-select">
                                                    <option value="">Selecciona</option>
                                                    <?php foreach ($catalogProducts as $product): ?>
                                                        <?php $classification = ($product['classification'] ?? $product['category'] ?? '') === 'producto' ? 'producto' : 'servicio'; ?>
                                                        <option value="<?php echo (int)$product['id']; ?>"
                                                                data-name="<?php echo e($product['name']); ?>"
                                                                data-type="<?php echo e($classification); ?>"
                                                                data-price="<?php echo e((float)($product['suggested_price'] ?? 0)); ?>">
                                                            <?php echo e($product['name']); ?> (<?php echo e(ucfirst($classification)); ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td><input type="text" name="description[]" class="form-control form-control-sm description-input" placeholder="Detalle del servicio o producto" required></td>
                                            <td><input type="number" name="quantity[]" class="form-control form-control-sm quantity-input" min="1" step="1" value="1" required></td>
                                            <td><input type="number" name="unit_cost[]" class="form-control form-control-sm cost-input" min="0" step="0.01" value="0" required></td>
                                            <td class="text-end item-subtotal"><?php echo e(format_currency(0)); ?></td>
                                            <td class="text-end"><button type="button" class="btn btn-link text-danger p-0 remove-row">Quitar</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Notas</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Observaciones de la compra"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="card border mt-4 mt-md-0">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong id="subtotal-display"><?php echo e(format_currency(0)); ?></strong></div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Impuesto</span>
                                        <input type="number" name="tax" id="tax-input" class="form-control form-control-sm w-auto" style="width: 150px;" step="0.01" min="0" value="<?php echo e($taxDefault ?? 0); ?>">
                                    </div>
                                    <div class="d-flex justify-content-between border-top pt-2"><span class="fw-semibold">Total</span><strong id="total-display"><?php echo e(format_currency(0)); ?></strong></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="index.php?route=purchases" class="btn btn-light">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar compra</button>
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

<div class="modal fade" id="catalogItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar ítem (catálogo compartido con caja chica)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="post" action="index.php?route=purchases/catalog-products/store">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <input type="text" name="category" class="form-control" placeholder="Aseo, Transporte, Insumos...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Clasificación</label>
                        <select name="classification" class="form-select" required>
                            <option value="servicio">Servicio</option>
                            <option value="producto">Producto</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Precio sugerido</label>
                        <input type="number" name="suggested_price" class="form-control" min="0" step="0.01" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar en catálogo</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="supplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar proveedor (mismo formulario de mantenedor)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="post" action="index.php?route=purchases/suppliers/store">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="supplier_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Código</label>
                            <input type="text" name="supplier_code" class="form-control" placeholder="PROV-001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">RUT / ID</label>
                            <input type="text" name="supplier_tax_id" class="form-control" placeholder="12.345.678-9">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Persona de contacto</label>
                            <input type="text" name="supplier_contact_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="supplier_email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="supplier_phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sitio web</label>
                            <input type="url" name="supplier_website" class="form-control" placeholder="https://">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="supplier_address" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giro</label>
                            <input type="text" name="supplier_giro" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Comuna</label>
                            <input type="text" name="supplier_commune" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea name="supplier_notes" class="form-control" rows="2"></textarea>
                        </div>
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

    const applySupplierSii = (supplierId) => {
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
        applySupplierSii(Number(event.target.value || 0));
    });
    if (supplierSelect?.value) {
        applySupplierSii(Number(supplierSelect.value || 0));
    }

    (function () {
        const tableBody = document.querySelector('#purchase-items-table tbody');
        const addButton = document.getElementById('add-item');
        const subtotalDisplay = document.getElementById('subtotal-display');
        const totalDisplay = document.getElementById('total-display');
        const taxInput = document.getElementById('tax-input');

        const formatCurrency = (amount) => new Intl.NumberFormat('es-CL', {
            style: 'currency', currency: 'CLP', minimumFractionDigits: 0,
        }).format(amount || 0);

        const updateClassification = (row) => {
            const select = row.querySelector('.catalog-select');
            const badge = row.querySelector('.classification-badge');
            const option = select?.selectedOptions?.[0];
            const type = option?.dataset?.type || '';
            if (!badge) return;
            if (!type) {
                badge.className = 'badge bg-secondary-subtle text-secondary classification-badge';
                badge.textContent = '-';
                return;
            }
            badge.className = type === 'producto'
                ? 'badge bg-primary-subtle text-primary classification-badge'
                : 'badge bg-info-subtle text-info classification-badge';
            badge.textContent = type.charAt(0).toUpperCase() + type.slice(1);
        };

        const recalc = () => {
            let subtotal = 0;
            tableBody.querySelectorAll('.item-row').forEach((row) => {
                const qty = parseFloat(row.querySelector('.quantity-input')?.value || '0');
                const cost = parseFloat(row.querySelector('.cost-input')?.value || '0');
                const itemTotal = qty * cost;
                subtotal += itemTotal;
                row.querySelector('.item-subtotal').innerText = formatCurrency(itemTotal);
            });
            subtotalDisplay.innerText = formatCurrency(subtotal);
            const tax = parseFloat(taxInput?.value || '0');
            totalDisplay.innerText = formatCurrency(subtotal + tax);
        };

        const syncCatalogSelection = (row) => {
            const select = row.querySelector('.catalog-select');
            const option = select?.selectedOptions?.[0];
            const descriptionInput = row.querySelector('.description-input');
            const costInput = row.querySelector('.cost-input');

            if (option && option.value) {
                if (descriptionInput && !descriptionInput.value.trim()) {
                    descriptionInput.value = option.dataset.name || '';
                }
                if (costInput && !parseFloat(costInput.value || '0')) {
                    costInput.value = option.dataset.price || '0';
                }
            }

            updateClassification(row);
            recalc();
        };

        const addRow = () => {
            const firstRow = tableBody.querySelector('.item-row');
            if (!firstRow) return;
            const clone = firstRow.cloneNode(true);
            clone.querySelectorAll('input').forEach((input) => {
                if (input.classList.contains('quantity-input')) {
                    input.value = '1';
                } else if (input.classList.contains('cost-input')) {
                    input.value = '0';
                } else {
                    input.value = '';
                }
            });
            clone.querySelector('.catalog-select').selectedIndex = 0;
            clone.querySelector('.item-subtotal').innerText = formatCurrency(0);
            tableBody.appendChild(clone);
            updateClassification(clone);
            recalc();
        };

        tableBody.addEventListener('change', (event) => {
            const row = event.target.closest('.item-row');
            if (!row) return;
            if (event.target.classList.contains('catalog-select')) {
                syncCatalogSelection(row);
                return;
            }
            recalc();
        });

        tableBody.addEventListener('input', (event) => {
            if (event.target.classList.contains('quantity-input') || event.target.classList.contains('cost-input')) {
                recalc();
            }
        });

        tableBody.addEventListener('click', (event) => {
            if (!event.target.classList.contains('remove-row')) return;
            const rows = tableBody.querySelectorAll('.item-row');
            if (rows.length <= 1) return;
            event.target.closest('.item-row')?.remove();
            recalc();
        });

        addButton?.addEventListener('click', addRow);
        taxInput?.addEventListener('input', recalc);
        tableBody.querySelectorAll('.item-row').forEach((row) => updateClassification(row));
        recalc();
    })();
</script>
