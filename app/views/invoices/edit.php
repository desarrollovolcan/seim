<?php
$invoiceItems = $items ?? [];
if (empty($invoiceItems)) {
    $invoiceItems = [[
        'descripcion' => '',
        'cantidad' => 1,
        'precio_unitario' => 0,
        'impuesto_pct' => $invoiceDefaults['tax_rate'] ?? 0,
        'impuesto_monto' => 0,
        'total' => 0,
    ]];
}
?>

<div class="card">
    <div class="card-body">
        <form method="post" action="index.php?route=invoices/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo $invoice['id'] ?? ''; ?>">
            <div class="mb-3">
                <?php echo render_id_badge($invoice['id'] ?? null); ?>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Cliente</label>
                    <select name="client_id" class="form-select" required>
                        <option value="">Selecciona cliente</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['id']; ?>" <?php echo (int)($invoice['client_id'] ?? 0) === (int)$client['id'] ? 'selected' : ''; ?>>
                                <?php echo e($client['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Proyecto origen</label>
                    <select name="project_id" class="form-select">
                        <option value="">Sin proyecto</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?php echo $project['id']; ?>"
                                data-client-id="<?php echo $project['client_id'] ?? ''; ?>"
                                data-project-name="<?php echo e($project['name'] ?? ''); ?>"
                                data-project-value="<?php echo e($project['value'] ?? 0); ?>"
                                <?php echo (int)($invoice['project_id'] ?? 0) === (int)$project['id'] ? 'selected' : ''; ?>>
                                <?php echo e($project['name']); ?> (<?php echo e($project['client_name']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Moneda</label>
                    <select name="currency_display" class="form-select" data-currency-display>
                        <option value="CLP" <?php echo ($invoiceDefaults['currency'] ?? 'CLP') === 'CLP' ? 'selected' : ''; ?>>CLP</option>
                        <option value="USD" <?php echo ($invoiceDefaults['currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>USD</option>
                        <option value="EUR" <?php echo ($invoiceDefaults['currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                    </select>
                    <small class="text-muted">Referencia visual, no afecta el cálculo.</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Número</label>
                    <input type="text" name="numero" class="form-control" value="<?php echo e($invoice['numero'] ?? ''); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Fecha emisión</label>
                    <input type="date" name="fecha_emision" class="form-control" value="<?php echo e($invoice['fecha_emision'] ?? date('Y-m-d')); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Fecha vencimiento</label>
                    <input type="date" name="fecha_vencimiento" class="form-control" value="<?php echo e($invoice['fecha_vencimiento'] ?? date('Y-m-d')); ?>">
                    <div class="mt-2">
                        <span class="badge" data-due-indicator>Sin fecha</span>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="pendiente" <?php echo ($invoice['estado'] ?? '') === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="pagada" <?php echo ($invoice['estado'] ?? '') === 'pagada' ? 'selected' : ''; ?>>Pagada</option>
                        <option value="vencida" <?php echo ($invoice['estado'] ?? '') === 'vencida' ? 'selected' : ''; ?>>Vencida</option>
                        <option value="anulada" <?php echo ($invoice['estado'] ?? '') === 'anulada' ? 'selected' : ''; ?>>Anulada</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Notas</label>
                    <textarea name="notas" class="form-control" rows="3"><?php echo e($invoice['notas'] ?? ''); ?></textarea>
                </div>
            </div>
            <?php
            $siiData = [
                'sii_document_type' => $invoice['sii_document_type'] ?? ($invoiceDefaults['sii_document_type'] ?? 'factura_electronica'),
                'sii_document_number' => $invoice['sii_document_number'] ?? '',
                'sii_receiver_rut' => $invoice['sii_receiver_rut'] ?? '',
                'sii_receiver_name' => $invoice['sii_receiver_name'] ?? '',
                'sii_receiver_giro' => $invoice['sii_receiver_giro'] ?? '',
                'sii_receiver_address' => $invoice['sii_receiver_address'] ?? '',
                'sii_receiver_commune' => $invoice['sii_receiver_commune'] ?? '',
                'sii_tax_rate' => $invoice['sii_tax_rate'] ?? ($invoiceDefaults['tax_rate'] ?? 19),
                'sii_exempt_amount' => $invoice['sii_exempt_amount'] ?? 0,
            ];
            include __DIR__ . '/../partials/sii-document-fields.php';
            ?>
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <h5 class="card-title mb-0">Items de factura</h5>
                        <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-2 text-nowrap">
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" data-add-manual-item>Agregar item manual</button>
                            <div class="d-flex align-items-center gap-2">
                                <select class="form-select form-select-sm py-1" data-service-item-select>
                                    <option value="">Selecciona servicio</option>
                                    <?php foreach ($catalogServices as $service): ?>
                                        <option value="<?php echo $service['id']; ?>" data-service-price="<?php echo e($service['cost'] ?? 0); ?>">
                                            <?php echo e($service['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" data-add-service-item>Agregar servicio</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-2 fw-semibold text-muted small">
                        <div class="col-md-3">Descripción</div>
                        <div class="col-md-2">Cantidad</div>
                        <div class="col-md-2">Precio unitario</div>
                        <div class="col-md-2">Impuesto %</div>
                        <div class="col-md-2">Impuesto $</div>
                        <div class="col-md-1">Total</div>
                    </div>
                    <?php foreach ($invoiceItems as $index => $item): ?>
                        <div class="row g-2 mb-2" data-item-row>
                            <div class="col-md-3">
                                <input type="text" name="items[<?php echo $index; ?>][descripcion]" class="form-control" placeholder="Descripción" data-item-description value="<?php echo e($item['descripcion'] ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[<?php echo $index; ?>][cantidad]" class="form-control" value="<?php echo e($item['cantidad'] ?? 1); ?>" data-item-qty>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[<?php echo $index; ?>][precio_unitario]" class="form-control" value="<?php echo e($item['precio_unitario'] ?? 0); ?>" data-item-price>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[<?php echo $index; ?>][impuesto_pct]" class="form-control" value="<?php echo e($item['impuesto_pct'] ?? ($invoiceDefaults['tax_rate'] ?? 0)); ?>" data-item-tax-rate>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[<?php echo $index; ?>][impuesto_monto]" class="form-control" value="<?php echo e($item['impuesto_monto'] ?? 0); ?>" data-item-tax readonly>
                            </div>
                            <div class="col-md-1">
                                <input type="number" name="items[<?php echo $index; ?>][total]" class="form-control" value="<?php echo e($item['total'] ?? 0); ?>" data-item-total readonly>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Impuesto (%)</label>
                    <input type="number" step="0.01" name="tax_rate" class="form-control" value="<?php echo e($invoiceDefaults['tax_rate'] ?? 0); ?>" data-tax-rate>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-center">
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="apply_tax_display" id="apply_tax_display" <?php echo !empty($invoiceDefaults['apply_tax']) ? 'checked' : ''; ?> data-apply-tax>
                        <label class="form-check-label" for="apply_tax_display">Aplicar impuesto</label>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Subtotal</label>
                    <input type="number" step="0.01" name="subtotal" class="form-control" value="<?php echo e($invoice['subtotal'] ?? 0); ?>" data-subtotal readonly>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Impuestos</label>
                    <input type="number" step="0.01" name="impuestos" class="form-control" value="<?php echo e($invoice['impuestos'] ?? 0); ?>" data-impuestos readonly>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Total</label>
                    <input type="number" step="0.01" name="total" class="form-control" value="<?php echo e($invoice['total'] ?? 0); ?>" data-total readonly>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=invoices" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaInvoice.php';
    $reportSource = 'invoices/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>

<script>
    const subtotalInput = document.querySelector('[data-subtotal]');
    const impuestosInput = document.querySelector('[data-impuestos]');
    const totalInput = document.querySelector('[data-total]');
    const taxRateInput = document.querySelector('[data-tax-rate]');
    const applyTaxCheckbox = document.querySelector('[data-apply-tax]');
    const addManualItemButton = document.querySelector('[data-add-manual-item]');
    const addServiceItemButton = document.querySelector('[data-add-service-item]');
    const serviceItemSelect = document.querySelector('[data-service-item-select]');
    const projectSelect = document.querySelector('select[name="project_id"]');
    const clientSelect = document.querySelector('select[name="client_id"]');
    const dueDateInput = document.querySelector('input[name="fecha_vencimiento"]');
    const dueIndicator = document.querySelector('[data-due-indicator]');
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
        const totalField = row.querySelector('[data-item-total]');
        const taxRateField = row.querySelector('[data-item-tax-rate]');
        const taxField = row.querySelector('[data-item-tax]');
        const taxRate = Number(taxRateField?.value || 0);
        const applyTax = !!applyTaxCheckbox?.checked;
        const rowSubtotal = formatNumber(qty * price);
        if (totalField) {
            totalField.value = rowSubtotal.toFixed(2);
        }
        if (taxField) {
            const taxAmount = applyTax ? formatNumber(rowSubtotal * (taxRate / 100)) : 0;
            taxField.value = taxAmount.toFixed(2);
        }
    };

    const updateTotals = () => {
        const subtotal = Number(subtotalInput?.value || 0);
        const impuestos = Number(impuestosInput?.value || 0);
        if (totalInput) {
            totalInput.value = formatNumber(subtotal + impuestos).toFixed(2);
        }
    };

    const updateFromItems = () => {
        const rows = document.querySelectorAll('[data-item-row]');
        let subtotal = 0;
        let taxes = 0;
        rows.forEach((row) => {
            updateItemTotal(row);
            subtotal += Number(row.querySelector('[data-item-total]')?.value || 0);
            taxes += Number(row.querySelector('[data-item-tax]')?.value || 0);
        });
        if (subtotalInput) {
            subtotalInput.value = formatNumber(subtotal).toFixed(2);
        }
        if (impuestosInput) {
            impuestosInput.value = formatNumber(taxes).toFixed(2);
        }
        updateTotals();
    };

    document.addEventListener('input', (event) => {
        if (event.target?.matches('[data-item-qty], [data-item-price], [data-item-tax-rate]')) {
            updateFromItems();
        }
    });

    const addItemRow = ({ description = '', price = 0 } = {}) => {
        const rows = document.querySelectorAll('[data-item-row]');
        const index = rows.length;
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2';
        row.setAttribute('data-item-row', 'true');
        const defaultTaxRate = Number(taxRateInput?.value || 0);
        row.innerHTML = `
            <div class="col-md-3">
                <input type="text" name="items[${index}][descripcion]" class="form-control" placeholder="Descripción" data-item-description value="${description}">
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][cantidad]" class="form-control" value="1" data-item-qty>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][precio_unitario]" class="form-control" value="${formatNumber(price).toFixed(2)}" data-item-price>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][impuesto_pct]" class="form-control" value="${defaultTaxRate}" data-item-tax-rate>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][impuesto_monto]" class="form-control" value="0" data-item-tax readonly>
            </div>
            <div class="col-md-1">
                <input type="number" name="items[${index}][total]" class="form-control" value="0" data-item-total readonly>
            </div>
        `;
        rows[rows.length - 1]?.after(row);
        updateFromItems();
    };

    addManualItemButton?.addEventListener('click', () => {
        addItemRow();
    });

    addServiceItemButton?.addEventListener('click', () => {
        const selected = serviceItemSelect?.selectedOptions?.[0];
        if (!selected || !selected.value) {
            return;
        }
        addItemRow({
            description: selected.textContent?.trim() || '',
            price: Number(selected.dataset.servicePrice || 0),
        });
        serviceItemSelect.value = '';
    });

    const fillFromProject = () => {
        const selected = projectSelect?.selectedOptions?.[0];
        if (!selected) {
            return;
        }
        const projectName = selected.dataset.projectName || '';
        const projectValue = Number(selected.dataset.projectValue || 0);
        const projectClientId = selected.dataset.clientId || '';
        const firstRow = document.querySelector('[data-item-row]');
        if (firstRow) {
            const descriptionInput = firstRow.querySelector('[data-item-description]');
            const priceInput = firstRow.querySelector('[data-item-price]');
            const qtyInput = firstRow.querySelector('[data-item-qty]');
            const taxRateInputRow = firstRow.querySelector('[data-item-tax-rate]');
            if (descriptionInput) {
                descriptionInput.value = projectName;
            }
            if (priceInput) {
                priceInput.value = formatNumber(projectValue).toFixed(2);
            }
            if (qtyInput) {
                qtyInput.value = '1';
                qtyInput.readOnly = true;
            }
            if (taxRateInputRow) {
                taxRateInputRow.value = taxRateInput?.value || '0';
            }
            updateFromItems();
        }
        if (clientSelect && projectClientId) {
            clientSelect.value = projectClientId;
            applyClientSii(Number(projectClientId));
        }
    };

    projectSelect?.addEventListener('change', fillFromProject);
    clientSelect?.addEventListener('change', () => {
        applyClientSii(Number(clientSelect?.value || 0));
    });

    taxRateInput?.addEventListener('input', () => {
        document.querySelectorAll('[data-item-tax-rate]').forEach((input) => {
            input.value = taxRateInput.value;
        });
        updateFromItems();
    });

    applyTaxCheckbox?.addEventListener('change', () => {
        updateFromItems();
    });

    const updateDueIndicator = () => {
        if (!dueDateInput || !dueIndicator) {
            return;
        }
        const dueDate = new Date(dueDateInput.value);
        if (Number.isNaN(dueDate.getTime())) {
            dueIndicator.textContent = 'Sin fecha';
            dueIndicator.className = 'badge';
            return;
        }
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        dueDate.setHours(0, 0, 0, 0);
        const diffDays = Math.round((dueDate - today) / (1000 * 60 * 60 * 24));
        if (diffDays < 0) {
            dueIndicator.textContent = `Vencida hace ${Math.abs(diffDays)} días`;
            dueIndicator.className = 'badge bg-danger';
            return;
        }
        if (diffDays <= 10) {
            dueIndicator.textContent = `Vence en ${diffDays} días`;
            dueIndicator.className = 'badge bg-warning text-dark';
            return;
        }
        dueIndicator.textContent = `Vence en ${diffDays} días`;
        dueIndicator.className = 'badge bg-success';
    };

    dueDateInput?.addEventListener('change', updateDueIndicator);

    updateFromItems();
    updateDueIndicator();
</script>
