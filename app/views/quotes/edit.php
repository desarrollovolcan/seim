<?php
$defaultIssueDate = $quote['fecha_emision'] ?? date('Y-m-d');
$itemsData = $items ?: [[
    'descripcion' => '',
    'cantidad' => 1,
    'precio_unitario' => 0,
    'descuento' => 0,
    'total' => 0,
]];
$applyTaxDefault = (float)($quote['impuestos'] ?? 0) > 0 ? '1' : '0';
?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Editar cotización</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=quotes/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo $quote['id']; ?>">
            <div class="mb-3">
                <?php echo render_id_badge($quote['id'] ?? null); ?>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Número</label>
                    <input type="text" name="numero" class="form-control" value="<?php echo e($quote['numero'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha emisión</label>
                    <input type="date" name="fecha_emision" class="form-control" value="<?php echo e($defaultIssueDate); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="pendiente" <?php echo ($quote['estado'] ?? '') === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="aceptada" <?php echo ($quote['estado'] ?? '') === 'aceptada' ? 'selected' : ''; ?>>Aceptada</option>
                        <option value="rechazada" <?php echo ($quote['estado'] ?? '') === 'rechazada' ? 'selected' : ''; ?>>Rechazada</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Cliente</label>
                    <select name="client_id" class="form-select" required>
                        <option value="">Selecciona un cliente</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['id']; ?>" <?php echo (int)$quote['client_id'] === (int)$client['id'] ? 'selected' : ''; ?>>
                                <?php echo e($client['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Servicio</label>
                    <select name="system_service_id" class="form-select" data-service-select>
                        <option value="">Selecciona servicio</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo $service['id']; ?>"
                                    data-name="<?php echo e($service['name']); ?>"
                                    data-price="<?php echo e($service['cost']); ?>"
                                    <?php echo (int)($quote['system_service_id'] ?? 0) === (int)$service['id'] ? 'selected' : ''; ?>>
                                <?php echo e($service['name']); ?> (<?php echo e($service['type_name']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Proyecto</label>
                    <select name="project_id" class="form-select" data-project-select>
                        <option value="">Selecciona proyecto</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?php echo $project['id']; ?>"
                                    data-client-id="<?php echo $project['client_id'] ?? ''; ?>"
                                    data-name="<?php echo e($project['name']); ?>"
                                    data-price="<?php echo e($project['value'] ?? 0); ?>"
                                    <?php echo (int)($quote['project_id'] ?? 0) === (int)$project['id'] ? 'selected' : ''; ?>>
                                <?php echo e($project['name']); ?> (<?php echo e($project['client_name']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php
            $siiData = [
                'sii_document_type' => $quote['sii_document_type'] ?? 'factura_electronica',
                'sii_document_number' => $quote['sii_document_number'] ?? '',
                'sii_receiver_rut' => $quote['sii_receiver_rut'] ?? '',
                'sii_receiver_name' => $quote['sii_receiver_name'] ?? '',
                'sii_receiver_giro' => $quote['sii_receiver_giro'] ?? '',
                'sii_receiver_address' => $quote['sii_receiver_address'] ?? '',
                'sii_receiver_commune' => $quote['sii_receiver_commune'] ?? '',
                'sii_tax_rate' => $quote['sii_tax_rate'] ?? 19,
                'sii_exempt_amount' => $quote['sii_exempt_amount'] ?? 0,
            ];
            $siiTitle = 'Datos del cliente';
            $siiHelp = 'Información tomada desde la ficha del cliente.';
            $siiShowDocumentType = false;
            $siiShowDocumentNumber = false;
            $siiShowTaxRate = false;
            $siiShowExemptAmount = false;
            $siiShowWarning = false;
            include __DIR__ . '/../partials/sii-document-fields.php';
            ?>

            <div class="card mb-3 mt-3">
                <div class="card-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <h5 class="card-title mb-0">Items de cotización</h5>
                        <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-2 text-nowrap">
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" data-add-manual-item>Agregar item manual</button>
                            <div class="d-flex align-items-center gap-2">
                                <select class="form-select form-select-sm py-1" data-product-item-select>
                                    <option value="">Selecciona producto</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>"
                                                data-product-price="<?php echo e($product['price'] ?? 0); ?>"
                                                data-product-name="<?php echo e($product['name'] ?? ''); ?>">
                                            <?php echo e($product['name']); ?>
                                            <?php if (!empty($product['produced_qty'])): ?>
                                                (Producido: <?php echo (int)$product['produced_qty']; ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-outline-success btn-sm py-1 px-2" data-add-product-item>Agregar producto</button>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <select class="form-select form-select-sm py-1" data-produced-item-select>
                                    <option value="">Selecciona producto fabricado</option>
                                    <?php foreach ($producedProducts as $producedProduct): ?>
                                        <option value="<?php echo $producedProduct['id']; ?>"
                                                data-produced-price="<?php echo e($producedProduct['price'] ?? 0); ?>"
                                                data-produced-name="<?php echo e($producedProduct['name'] ?? ''); ?>">
                                            <?php echo e($producedProduct['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-outline-dark btn-sm py-1 px-2" data-add-produced-item>Agregar fabricado</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-2 fw-semibold text-muted small">
                        <div class="col-md-3">Descripción</div>
                        <div class="col-md-2">Cantidad</div>
                        <div class="col-md-2">Precio unitario</div>
                        <div class="col-md-2">Descuento</div>
                        <div class="col-md-2">Total</div>
                        <div class="col-md-1 text-center">Quitar</div>
                    </div>
                    <?php foreach ($itemsData as $index => $item): ?>
                        <div class="row g-2 mb-2" data-item-row>
                            <div class="col-md-3">
                                <input type="text" name="items[<?php echo $index; ?>][descripcion]" class="form-control" value="<?php echo e($item['descripcion']); ?>" data-item-description>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[<?php echo $index; ?>][cantidad]" class="form-control" value="<?php echo e($item['cantidad']); ?>" min="1" data-item-qty>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[<?php echo $index; ?>][precio_unitario]" class="form-control" value="<?php echo e($item['precio_unitario']); ?>" step="0.01" data-item-price>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[<?php echo $index; ?>][descuento]" class="form-control" value="<?php echo e($item['descuento'] ?? 0); ?>" step="0.01" min="0" data-item-discount>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[<?php echo $index; ?>][total]" class="form-control" value="<?php echo e($item['total']); ?>" step="0.01" readonly data-item-total>
                            </div>
                            <div class="col-md-1 d-flex align-items-center justify-content-center">
                                <button type="button" class="btn btn-link text-danger p-0" data-remove-row aria-label="Eliminar">✕</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <input type="hidden" name="tax_rate" value="<?php echo e($quote['sii_tax_rate'] ?? 19); ?>" data-tax-rate>
                    <div class="mb-3">
                        <label class="form-label">Impuesto</label>
                        <select class="form-select" name="apply_tax_display" data-apply-tax>
                            <option value="1" <?php echo $applyTaxDefault === '1' ? 'selected' : ''; ?>>Aplicar impuesto</option>
                            <option value="0" <?php echo $applyTaxDefault === '0' ? 'selected' : ''; ?>>Sin impuesto</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subtotal</label>
                        <input type="number" name="subtotal" class="form-control" value="<?php echo e($quote['subtotal'] ?? 0); ?>" step="0.01" readonly data-subtotal>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descuento</label>
                        <input type="number" name="discount_total" class="form-control" value="<?php echo e($quote['discount_total'] ?? 0); ?>" step="0.01" min="0" data-discount-total>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Impuestos</label>
                        <input type="number" name="impuestos" class="form-control" value="<?php echo e($quote['impuestos'] ?? 0); ?>" step="0.01" readonly data-impuestos>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total</label>
                        <input type="number" name="total" class="form-control" value="<?php echo e($quote['total'] ?? 0); ?>" step="0.01" readonly data-total>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Notas</label>
                <textarea name="notas" class="form-control" rows="3"><?php echo e($quote['notas'] ?? ''); ?></textarea>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=quotes" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaInvoice.php';
    $reportSource = 'quotes/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>

<script>
    const serviceSelect = document.querySelector('[data-service-select]');
    const projectSelect = document.querySelector('[data-project-select]');
    const subtotalInput = document.querySelector('[data-subtotal]');
    const discountTotalInput = document.querySelector('[data-discount-total]');
    const impuestosInput = document.querySelector('[data-impuestos]');
    const totalSummaryInput = document.querySelector('[data-total]');
    const taxRateInput = document.querySelector('[data-tax-rate]');
    const applyTaxSelect = document.querySelector('[data-apply-tax]');
    const clientSelect = document.querySelector('select[name="client_id"]');
    const addManualItemButton = document.querySelector('[data-add-manual-item]');
    const addProductItemButton = document.querySelector('[data-add-product-item]');
    const addProducedItemButton = document.querySelector('[data-add-produced-item]');
    const productItemSelect = document.querySelector('[data-product-item-select]');
    const producedItemSelect = document.querySelector('[data-produced-item-select]');
    const clientSiiMap = <?php echo json_encode(array_reduce($clients ?? [], static function (array $carry, array $client): array {
            $carry[$client['id']] = [
                'rut' => $client['rut'] ?? '',
                'name' => $client['name'] ?? '',
                'giro' => $client['giro'] ?? '',
                'address' => $client['address'] ?? '',
                'commune' => $client['commune'] ?? '',
            ];
            return $carry;
        }, []), JSON_UNESCAPED_UNICODE); ?>;

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

    const updateSiiWarning = (data, clientId) => {
        if (!siiWarning || !siiWarningText || !siiWarningLink) {
            return;
        }
        const missing = siiRequiredFields.filter((field) => !(data?.[field.key] || '').trim());
        if (missing.length === 0 || !clientId) {
            siiWarning.classList.add('d-none');
            return;
        }
        siiWarningText.textContent = `Completa en la ficha del cliente: ${missing.map((field) => field.label).join(', ')}.`;
        siiWarningLink.href = `index.php?route=clients/edit&id=${clientId}`;
        siiWarning.classList.remove('d-none');
    };

    const applyClientSii = (clientId, force = false) => {
        const data = clientSiiMap?.[clientId];
        if (!data) {
            updateSiiWarning({}, clientId);
            return;
        }
        if (siiInputs.sii_receiver_rut) siiInputs.sii_receiver_rut.value = data.rut || '';
        if (siiInputs.sii_receiver_name) siiInputs.sii_receiver_name.value = data.name || '';
        if (siiInputs.sii_receiver_giro) siiInputs.sii_receiver_giro.value = data.giro || '';
        if (siiInputs.sii_receiver_address) siiInputs.sii_receiver_address.value = data.address || '';
        if (siiInputs.sii_receiver_commune) siiInputs.sii_receiver_commune.value = data.commune || '';
        updateSiiWarning(data, clientId);
    };

    const formatNumber = (value) => Math.round((Number(value) + Number.EPSILON) * 100) / 100;

    const updateItemTotal = (row) => {
        const qty = Number(row.querySelector('[data-item-qty]')?.value || 0);
        const price = Number(row.querySelector('[data-item-price]')?.value || 0);
        const discount = Number(row.querySelector('[data-item-discount]')?.value || 0);
        const totalField = row.querySelector('[data-item-total]');
        const rowSubtotal = formatNumber(Math.max(0, (qty * price) - discount));
        if (totalField) {
            totalField.value = rowSubtotal.toFixed(2);
        }
    };

    const updateTotals = () => {
        const rows = document.querySelectorAll('[data-item-row]');
        let subtotal = 0;
        rows.forEach((row) => {
            updateItemTotal(row);
            subtotal += Number(row.querySelector('[data-item-total]')?.value || 0);
        });
        const discountTotal = Math.max(0, Number(discountTotalInput?.value || 0));
        const taxableBase = Math.max(0, subtotal - discountTotal);
        const taxRate = Number(taxRateInput?.value || 0);
        const applyTax = (applyTaxSelect?.value || '1') === '1';
        const taxes = applyTax ? formatNumber(taxableBase * (taxRate / 100)) : 0;
        if (subtotalInput) {
            subtotalInput.value = formatNumber(subtotal).toFixed(2);
        }
        if (impuestosInput) {
            impuestosInput.value = formatNumber(taxes).toFixed(2);
        }
        if (totalSummaryInput) {
            totalSummaryInput.value = formatNumber(taxableBase + taxes).toFixed(2);
        }
    };

    document.addEventListener('input', (event) => {
        if (event.target?.matches('[data-item-qty], [data-item-price], [data-item-discount], [data-discount-total]')) {
            updateTotals();
        }
    });

    const addItemRow = ({ description = '', price = 0 } = {}) => {
        const rows = document.querySelectorAll('[data-item-row]');
        const index = rows.length;
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2';
        row.setAttribute('data-item-row', 'true');
        row.innerHTML = `
            <div class="col-md-3">
                <input type="text" name="items[${index}][descripcion]" class="form-control" data-item-description value="${description}">
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][cantidad]" class="form-control" value="1" min="1" data-item-qty>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][precio_unitario]" class="form-control" value="${formatNumber(price).toFixed(2)}" step="0.01" data-item-price>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][descuento]" class="form-control" value="0" step="0.01" min="0" data-item-discount>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][total]" class="form-control" value="0" step="0.01" readonly data-item-total>
            </div>
            <div class="col-md-1 d-flex align-items-center justify-content-center">
                <button type="button" class="btn btn-link text-danger p-0" data-remove-row aria-label="Eliminar">✕</button>
            </div>
        `;
        rows[rows.length - 1]?.after(row);
        updateTotals();
    };

    addManualItemButton?.addEventListener('click', () => {
        addItemRow();
    });

    const findFirstEmptyRow = () => {
        return Array.from(document.querySelectorAll('[data-item-row]')).find((row) => {
            const description = row.querySelector('[data-item-description]')?.value || '';
            return description.trim() === '';
        }) || null;
    };

    const fillRow = (row, { description = '', price = 0 } = {}) => {
        const descriptionInput = row.querySelector('[data-item-description]');
        const priceInput = row.querySelector('[data-item-price]');
        const qtyInput = row.querySelector('[data-item-qty]');
        if (descriptionInput) {
            descriptionInput.value = description;
        }
        if (priceInput) {
            priceInput.value = formatNumber(price).toFixed(2);
        }
        if (qtyInput && Number(qtyInput.value || 0) === 0) {
            qtyInput.value = '1';
        }
        updateTotals();
    };

    addProductItemButton?.addEventListener('click', () => {
        const selected = productItemSelect?.selectedOptions?.[0];
        if (!selected || !selected.value) {
            return;
        }
        const payload = {
            description: selected.dataset.productName || selected.textContent?.trim() || '',
            price: Number(selected.dataset.productPrice || 0),
        };
        const emptyRow = findFirstEmptyRow();
        if (emptyRow) {
            fillRow(emptyRow, payload);
        } else {
            addItemRow(payload);
        }
        productItemSelect.value = '';
    });

    addProducedItemButton?.addEventListener('click', () => {
        const selected = producedItemSelect?.selectedOptions?.[0];
        if (!selected || !selected.value) {
            return;
        }
        const payload = {
            description: selected.dataset.producedName || selected.textContent?.trim() || '',
            price: Number(selected.dataset.producedPrice || 0),
        };
        const emptyRow = findFirstEmptyRow();
        if (emptyRow) {
            fillRow(emptyRow, payload);
        } else {
            addItemRow(payload);
        }
        producedItemSelect.value = '';
    });

    applyTaxSelect?.addEventListener('change', () => {
        updateTotals();
    });

    projectSelect?.addEventListener('change', (event) => {
        if (serviceSelect) {
            serviceSelect.value = '';
        }
        const clientId = event.target.selectedOptions[0]?.dataset?.clientId;
        if (clientSelect && clientId) {
            clientSelect.value = clientId;
            applyClientSii(Number(clientId));
        }
    });

    clientSelect?.addEventListener('change', () => {
        applyClientSii(Number(clientSelect?.value || 0));
    });

    document.addEventListener('click', (event) => {
        if (!event.target?.matches('[data-remove-row]')) {
            return;
        }
        const rows = document.querySelectorAll('[data-item-row]');
        const row = event.target.closest('[data-item-row]');
        if (!row) {
            return;
        }
        if (rows.length <= 1) {
            row.querySelectorAll('input').forEach((input) => {
                if (input.matches('[data-item-qty]')) {
                    input.value = '1';
                } else if (input.matches('[data-item-price], [data-item-total], [data-item-discount]')) {
                    input.value = '0';
                } else {
                    input.value = '';
                }
            });
            updateTotals();
            return;
        }
        row.remove();
        updateTotals();
    });

    updateTotals();
</script>
