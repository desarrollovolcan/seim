<!-- Datatables css -->
<link href="assets/plugins/datatables/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css">

<div class="card">
    <div class="card-body">
        <?php if (!empty($selectedProjectId) && ($projectInvoiceCount ?? 0) > 0): ?>
            <div class="alert alert-warning">
                Este proyecto ya tiene <?php echo (int)$projectInvoiceCount; ?> factura(s) asociada(s). Revisa antes de crear una nueva.
            </div>
        <?php endif; ?>
        <form method="post" action="index.php?route=invoices/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Cliente</label>
                    <select name="client_id" class="form-select" required>
                        <option value="">Selecciona cliente</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['id']; ?>" <?php echo (int)($selectedClientId ?? 0) === (int)$client['id'] ? 'selected' : ''; ?>>
                                <?php echo e($client['name']); ?>
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
                    <input type="text" name="numero" class="form-control" value="<?php echo e($number); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Fecha emisión</label>
                    <input type="date" name="fecha_emision" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Fecha vencimiento</label>
                    <input type="date" name="fecha_vencimiento" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    <div class="mt-2">
                        <span class="badge" data-due-indicator>Sin fecha</span>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="pendiente">Pendiente</option>
                        <option value="pagada">Pagada</option>
                        <option value="vencida">Vencida</option>
                        <option value="anulada">Anulada</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Notas</label>
                    <textarea name="notas" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <?php
            $siiData = [
                'sii_document_type' => $invoiceDefaults['sii_document_type'] ?? 'factura_electronica',
                'sii_tax_rate' => $invoiceDefaults['tax_rate'] ?? 19,
                'sii_exempt_amount' => 0,
            ];
            include __DIR__ . '/../partials/sii-document-fields.php';
            ?>
            <div class="card mb-3" id="billable-card" hidden>
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Proyectos, servicios y renovaciones facturables</h5>
                    <span class="text-muted small">Selecciona un cliente para cargar la lista</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="billable-items-table" class="table table-striped dt-responsive align-middle mb-0">
                            <thead class="thead-sm text-uppercase fs-xxs">
                                <tr>
                                    <th></th>
                                    <th>Tipo</th>
                                    <th>Nombre</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <p class="text-muted small mt-2 mb-0">La tabla muestra servicios sin facturar, renovaciones pendientes y proyectos finalizados sin factura del cliente seleccionado.</p>
                </div>
            </div>
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
                    <div class="row g-2 mb-2" data-item-row>
                        <div class="col-md-3">
                            <input type="text" name="items[0][descripcion]" class="form-control" placeholder="Descripción" data-item-description>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" name="items[0][cantidad]" class="form-control" value="1" data-item-qty>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-remove-item title="Eliminar item"><i class="ti ti-x"></i></button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][precio_unitario]" class="form-control" value="0" data-item-price>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][impuesto_pct]" class="form-control" value="<?php echo e($invoiceDefaults['tax_rate'] ?? 0); ?>" data-item-tax-rate>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][impuesto_monto]" class="form-control" value="0" data-item-tax readonly>
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="items[0][total]" class="form-control" value="0" data-item-total readonly>
                        </div>
                    </div>
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
                    <input type="number" step="0.01" name="subtotal" class="form-control" value="0" data-subtotal readonly>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Impuestos</label>
                    <input type="number" step="0.01" name="impuestos" class="form-control" value="0" data-impuestos readonly>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Total</label>
                    <input type="number" step="0.01" name="total" class="form-control" value="0" data-total readonly>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=invoices" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
            <input type="hidden" name="service_id" data-service-id value="<?php echo (int)($selectedServiceId ?? 0); ?>">
            <input type="hidden" name="project_id" data-project-id value="<?php echo (int)($selectedProjectId ?? 0); ?>">
        
    <?php
    $reportTemplate = 'informeIcargaInvoice.php';
    $reportSource = 'invoices/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
        <!-- Jquery for Datatables-->
        <script src="assets/plugins/jquery/jquery.min.js"></script>

        <!-- Datatables js -->
        <script src="assets/plugins/datatables/dataTables.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.bootstrap5.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="assets/plugins/datatables/responsive.bootstrap5.min.js"></script>

<!-- Jquery for Datatables-->
<script src="assets/plugins/jquery/jquery.min.js"></script>

<!-- Datatables js -->
<script src="assets/plugins/datatables/dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap5.min.js"></script>
<script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
<script src="assets/plugins/datatables/responsive.bootstrap5.min.js"></script>

<script>
    const subtotalInput = document.querySelector('[data-subtotal]');
    const impuestosInput = document.querySelector('[data-impuestos]');
    const totalInput = document.querySelector('[data-total]');
    const taxRateInput = document.querySelector('[data-tax-rate]');
    const applyTaxCheckbox = document.querySelector('[data-apply-tax]');
    const addManualItemButton = document.querySelector('[data-add-manual-item]');
    const addServiceItemButton = document.querySelector('[data-add-service-item]');
    const serviceItemSelect = document.querySelector('[data-service-item-select]');
    const clientSelect = document.querySelector('select[name="client_id"]');
    const serviceInput = document.querySelector('[data-service-id]');
    const projectInput = document.querySelector('[data-project-id]');
    const dueDateInput = document.querySelector('input[name="fecha_vencimiento"]');
    const dueIndicator = document.querySelector('[data-due-indicator]');
    const billableServices = <?php echo json_encode($billableServices ?? []); ?>;
    const billableRenewals = <?php echo json_encode($billableRenewals ?? []); ?>;
    const billableProjects = <?php echo json_encode($billableProjects ?? []); ?>;
    const billableQuotes = <?php echo json_encode($billableQuotes ?? []); ?>;
    const billableOrders = <?php echo json_encode($billableOrders ?? []); ?>;
    const prefillService = <?php echo json_encode($prefillService ?? null); ?>;
    const clientSiiMap = <?php echo json_encode(array_reduce($clients ?? [], static function (array $carry, array $client): array {
        $carry[$client['id']] = [
            'rut' => $client['rut'] ?? '',
            'name' => $client['name'] ?? '',
            'giro' => $client['giro'] ?? '',
            'activity_code' => $client['activity_code'] ?? '',
            'address' => $client['address'] ?? '',
            'commune' => $client['commune'] ?? '',
            'city' => $client['city'] ?? '',
        ];
        return $carry;
    }, []), JSON_UNESCAPED_UNICODE); ?>;
    const billableTableElement = document.getElementById('billable-items-table');
    const billableCard = document.getElementById('billable-card');
    let billableTable = null;

    const siiInputs = {
        sii_receiver_rut: document.querySelector('[name="sii_receiver_rut"]'),
        sii_receiver_name: document.querySelector('[name="sii_receiver_name"]'),
        sii_receiver_giro: document.querySelector('[name="sii_receiver_giro"]'),
        sii_receiver_activity_code: document.querySelector('[name="sii_receiver_activity_code"]'),
        sii_receiver_address: document.querySelector('[name="sii_receiver_address"]'),
        sii_receiver_commune: document.querySelector('[name="sii_receiver_commune"]'),
        sii_receiver_city: document.querySelector('[name="sii_receiver_city"]'),
    };
    const siiWarning = document.querySelector('[data-sii-warning]');
    const siiWarningText = document.querySelector('[data-sii-warning-text]');
    const siiWarningLink = document.querySelector('[data-sii-warning-link]');
    const siiRequiredFields = [
        { key: 'rut', label: 'RUT' },
        { key: 'name', label: 'Razón social' },
        { key: 'giro', label: 'Giro' },
        { key: 'activity_code', label: 'Código actividad' },
        { key: 'address', label: 'Dirección' },
        { key: 'commune', label: 'Comuna' },
        { key: 'city', label: 'Ciudad' },
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
        if (siiInputs.sii_receiver_activity_code) siiInputs.sii_receiver_activity_code.value = data.activity_code || '';
        if (siiInputs.sii_receiver_address) siiInputs.sii_receiver_address.value = data.address || '';
        if (siiInputs.sii_receiver_commune) siiInputs.sii_receiver_commune.value = data.commune || '';
        if (siiInputs.sii_receiver_city) siiInputs.sii_receiver_city.value = data.city || '';
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

    const addItemRow = ({ description = '', price = 0, qty = 1, qtyReadOnly = false, taxRate = null } = {}) => {
        const rows = document.querySelectorAll('[data-item-row]');
        const index = rows.length;
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2';
        row.setAttribute('data-item-row', 'true');
        const defaultTaxRate = taxRate !== null ? Number(taxRate) : Number(taxRateInput?.value || 0);
        row.innerHTML = `
            <div class="col-md-3">
                <input type="text" name="items[${index}][descripcion]" class="form-control" placeholder="Descripción" data-item-description value="${description}">
            </div>
            <div class="col-md-2">
                <div class="d-flex align-items-center gap-2">
                    <input type="number" name="items[${index}][cantidad]" class="form-control" value="${qty}" data-item-qty ${qtyReadOnly ? 'readonly' : ''}>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-remove-item title="Eliminar item"><i class="ti ti-x"></i></button>
                </div>
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

    const applyOrAddItem = ({ description = '', price = 0, qty = 1, qtyReadOnly = false, taxRate = null }) => {
        const firstRow = document.querySelector('[data-item-row]');
        const firstDesc = firstRow?.querySelector('[data-item-description]')?.value?.trim() || '';
        const firstPrice = Number(firstRow?.querySelector('[data-item-price]')?.value || 0);
        const firstTotal = Number(firstRow?.querySelector('[data-item-total]')?.value || 0);
        const isFirstEmpty = firstRow && firstDesc === '' && firstPrice === 0 && firstTotal === 0;
        if (firstRow && isFirstEmpty) {
            const descriptionInput = firstRow.querySelector('[data-item-description]');
            const priceInput = firstRow.querySelector('[data-item-price]');
            const qtyInput = firstRow.querySelector('[data-item-qty]');
            const taxRateInputRow = firstRow.querySelector('[data-item-tax-rate]');
            if (descriptionInput) descriptionInput.value = description;
            if (priceInput) priceInput.value = formatNumber(price).toFixed(2);
            if (qtyInput) {
                qtyInput.value = String(qty);
                qtyInput.readOnly = qtyReadOnly;
            }
            if (taxRateInputRow && taxRate !== null) {
                taxRateInputRow.value = taxRate;
            }
            updateFromItems();
            return;
        }
        addItemRow({ description, price, qty, qtyReadOnly, taxRate });
    };

    const fillFromProjectData = (project) => {
        if (!project) return;
        applyOrAddItem({ description: project.name || '', price: Number(project.value || 0), qtyReadOnly: true });
        projectInput.value = project.id || 0;
        serviceInput.value = '';
        if (clientSelect && project.client_id) {
            clientSelect.value = project.client_id;
            applyClientSii(Number(project.client_id));
        }
        if (dueDateInput && project.delivery_date) {
            dueDateInput.value = project.delivery_date;
            updateDueIndicator();
        }
    };

    const fillFromServiceData = (service) => {
        if (!service) return;
        applyOrAddItem({ description: service.name || '', price: Number(service.cost || 0), qtyReadOnly: true });
        serviceInput.value = service.id || 0;
        projectInput.value = '';
        if (clientSelect && service.client_id) {
            clientSelect.value = service.client_id;
            applyClientSii(Number(service.client_id));
        }
        if (dueDateInput && service.due_date) {
            dueDateInput.value = service.due_date;
            updateDueIndicator();
        }
    };

    const fillFromRenewalData = (renewal) => {
        if (!renewal) return;
        const description = renewal.service_name ? `Renovación ${renewal.service_name}` : 'Renovación de servicio';
        applyOrAddItem({ description, price: Number(renewal.amount || 0), qtyReadOnly: true });
        serviceInput.value = renewal.service_id || 0;
        projectInput.value = '';
        if (clientSelect && renewal.client_id) {
            clientSelect.value = renewal.client_id;
            applyClientSii(Number(renewal.client_id));
        }
        if (dueDateInput && renewal.renewal_date) {
            dueDateInput.value = renewal.renewal_date;
            updateDueIndicator();
        }
    };

    const fillFromQuoteData = (quote) => {
        if (!quote) return;
        const description = quote.numero ? `Cotización ${quote.numero}` : 'Cotización aprobada';
        serviceInput.value = quote.service_id || 0;
        projectInput.value = quote.project_id || '';
        if (clientSelect && quote.client_id) {
            clientSelect.value = quote.client_id;
            applyClientSii(Number(quote.client_id));
        }
        const quoteItems = Array.isArray(quote.items) ? quote.items : [];
        if (quoteItems.length > 0) {
            quoteItems.forEach((item, index) => {
                applyOrAddItem({
                    description: item.descripcion || description,
                    price: Number(item.precio_unitario || 0),
                    qty: Number(item.cantidad || 1),
                    qtyReadOnly: true,
                });
            });
        } else {
            applyOrAddItem({ description, price: Number(quote.total || 0), qtyReadOnly: true });
        }
        if (dueDateInput && quote.fecha_emision) {
            dueDateInput.value = quote.fecha_emision;
            updateDueIndicator();
        }
        if (qtyInput) {
            qtyInput.value = '1';
            qtyInput.readOnly = qtyReadOnly;
        }
        if (taxRateInputRow) {
            taxRateInputRow.value = taxRateInput?.value || '0';
        }
        updateFromItems();
    };

    const fillFromOrderData = (order) => {
        if (!order) return;
        const description = order.order_number ? `Orden de venta ${order.order_number}` : 'Orden de venta';
        applyOrAddItem({ description, price: Number(order.total || 0), qtyReadOnly: true });
        serviceInput.value = '';
        projectInput.value = '';
        if (clientSelect && order.client_id) {
            clientSelect.value = order.client_id;
            applyClientSii(Number(order.client_id));
        }
        if (dueDateInput && order.order_date) {
            dueDateInput.value = order.order_date;
            updateDueIndicator();
        }
    };

    taxRateInput?.addEventListener('input', () => {
        document.querySelectorAll('[data-item-tax-rate]').forEach((input) => {
            input.value = taxRateInput.value;
        });
        updateFromItems();
    });

    applyTaxCheckbox?.addEventListener('change', () => {
        updateFromItems();
    });

    const filterOptionsByClient = (select, items, labelKey, valueKey) => {
        if (!select) {
            return;
        }
        const clientId = Number(clientSelect?.value || 0);
        select.innerHTML = '<option value="">Sin ' + labelKey + '</option>';
        items.forEach((item) => {
            if (clientId > 0 && Number(item.client_id) !== clientId) {
                return;
            }
            const option = document.createElement('option');
            option.value = item[valueKey];
            if (item.client_id) {
                option.dataset.clientId = item.client_id;
            }
            if (item.name) {
                option.dataset.projectName = item.name;
                option.dataset.serviceName = item.name;
            }
            if (item.value) {
                option.dataset.projectValue = item.value;
            }
            if (item.delivery_date) {
                option.dataset.projectDelivery = item.delivery_date;
            }
            if (item.cost) {
                option.dataset.serviceCost = item.cost;
            }
            if (item.due_date) {
                option.dataset.serviceDue = item.due_date;
            }
            if (item.client_name) {
                option.textContent = `${item.name} (${item.client_name})`;
            } else {
                option.textContent = item.name;
            }
            if (Number(valueKey === 'id' ? item.id : item[valueKey]) === Number(select.dataset.prefillId || 0)) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    };

    clientSelect?.addEventListener('change', () => {
        applyClientSii(Number(clientSelect?.value || 0));
        filterOptionsByClient(serviceSelect, billableServices, 'servicio', 'id');
        filterOptionsByClient(projectSelect, billableProjects, 'proyecto', 'id');
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

    const buildBillableRows = () => {
        const clientId = Number(clientSelect?.value || 0);
        const rows = [];
        const filteredServices = clientId > 0 ? billableServices.filter((service) => Number(service.client_id) === clientId) : [];
        const filteredRenewals = clientId > 0 ? billableRenewals.filter((renewal) => Number(renewal.client_id) === clientId) : [];
        const filteredProjects = clientId > 0 ? billableProjects.filter((project) => Number(project.client_id) === clientId) : [];
        const filteredQuotes = clientId > 0 ? billableQuotes.filter((quote) => Number(quote.client_id) === clientId) : [];
        const filteredOrders = clientId > 0 ? billableOrders.filter((order) => Number(order.client_id) === clientId) : [];
        filteredServices.forEach((service) => {
            rows.push({
                id: service.id,
                type: 'Servicio',
                name: service.name,
                client_name: service.client_name,
                amount: Number(service.cost || 0),
                date: service.due_date || '',
                currency: service.currency || 'CLP',
                raw: service,
                source: 'service',
            });
        });
        filteredRenewals.forEach((renewal) => {
            rows.push({
                id: renewal.id,
                type: 'Renovación',
                name: renewal.service_name ? `Renovación ${renewal.service_name}` : 'Renovación de servicio',
                client_name: renewal.client_name,
                amount: Number(renewal.amount || 0),
                date: renewal.renewal_date || '',
                currency: renewal.currency || 'CLP',
                raw: renewal,
                source: 'renewal',
            });
        });
        filteredQuotes.forEach((quote) => {
            rows.push({
                id: quote.id,
                type: 'Cotización',
                name: quote.numero ? `Cotización ${quote.numero}` : 'Cotización',
                client_name: quote.client_name,
                amount: Number(quote.total || 0),
                date: quote.fecha_emision || '',
                currency: 'CLP',
                raw: quote,
                source: 'quote',
            });
        });
        filteredOrders.forEach((order) => {
            rows.push({
                id: order.id,
                type: 'Orden de venta',
                name: order.order_number ? `Orden ${order.order_number}` : 'Orden de venta',
                client_name: order.client_name,
                amount: Number(order.total || 0),
                date: order.order_date || '',
                currency: order.currency || 'CLP',
                raw: order,
                source: 'order',
            });
        });
        filteredProjects.forEach((project) => {
            rows.push({
                id: project.id,
                type: 'Proyecto',
                name: project.name,
                client_name: project.client_name,
                amount: Number(project.value || 0),
                date: project.delivery_date || '',
                currency: project.currency || 'CLP',
                raw: project,
                source: 'project',
            });
        });
        return rows;
    };

    const formatDetails = (rowData) => {
        if (!rowData?.raw) return '';
        if (rowData.source === 'service') {
            return `
                <div class="row">
                    <div class="col-md-4"><strong>Vence:</strong> ${rowData.raw.due_date || '-'}</div>
                    <div class="col-md-4"><strong>Moneda:</strong> ${rowData.currency}</div>
                    <div class="col-md-4"><strong>Monto:</strong> ${formatNumber(rowData.amount)} ${rowData.currency}</div>
                </div>
            `;
        }
        if (rowData.source === 'renewal') {
            return `
                <div class="row">
                    <div class="col-md-4"><strong>Renovación:</strong> ${rowData.raw.renewal_date || '-'}</div>
                    <div class="col-md-4"><strong>Moneda:</strong> ${rowData.currency}</div>
                    <div class="col-md-4"><strong>Monto:</strong> ${formatNumber(rowData.amount)} ${rowData.currency}</div>
                </div>
            `;
        }
        if (rowData.source === 'quote') {
            return `
                <div class="row">
                    <div class="col-md-4"><strong>Emisión:</strong> ${rowData.raw.fecha_emision || '-'}</div>
                    <div class="col-md-4"><strong>Estado:</strong> ${rowData.raw.estado || ''}</div>
                    <div class="col-md-4"><strong>Monto:</strong> ${formatNumber(rowData.amount)} CLP</div>
                </div>
            `;
        }
        if (rowData.source === 'order') {
            return `
                <div class="row">
                    <div class="col-md-4"><strong>Fecha orden:</strong> ${rowData.raw.order_date || '-'}</div>
                    <div class="col-md-4"><strong>Estado:</strong> ${rowData.raw.status || ''}</div>
                    <div class="col-md-4"><strong>Monto:</strong> ${formatNumber(rowData.amount)} ${rowData.currency}</div>
                </div>
            `;
        }
        return `
            <div class="row">
                <div class="col-md-4"><strong>Entrega:</strong> ${rowData.raw.delivery_date || '-'}</div>
                <div class="col-md-4"><strong>Estado:</strong> ${rowData.raw.status || ''}</div>
                <div class="col-md-4"><strong>Monto:</strong> ${formatNumber(rowData.amount)} ${rowData.currency}</div>
            </div>
        `;
    };

    const initBillableTable = () => {
        if (!billableTableElement) return;
        billableTable = $(billableTableElement).DataTable({
            data: [],
            columns: [
                {
                    className: 'details-control text-center',
                    orderable: false,
                    data: null,
                    defaultContent: '<i class="ti ti-chevron-right"></i>',
                },
                { data: 'type' },
                { data: 'name' },
                { data: 'client_name' },
                { data: 'amount', render: (data, type) => type === 'display' ? formatNumber(data) : data },
                { data: 'date' },
                {
                    data: null,
                    orderable: false,
                    defaultContent: '<button type="button" class="btn btn-sm btn-outline-primary">Seleccionar</button>',
                },
            ],
            order: [[1, 'asc']],
            responsive: true,
            language: {
                emptyTable: 'Selecciona un cliente para ver servicios y proyectos facturables.',
            },
        });

        $('#billable-items-table tbody').on('click', 'td.details-control', function () {
            const tr = $(this).closest('tr');
            const row = billableTable.row(tr);
            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(formatDetails(row.data())).show();
                tr.addClass('shown');
            }
        });

        $('#billable-items-table tbody').on('click', 'button', function () {
            const row = billableTable.row($(this).parents('tr'));
            const data = row.data();
            if (!data) return;
            if (data.source === 'service') {
                fillFromServiceData(data.raw);
            } else if (data.source === 'renewal') {
                fillFromRenewalData(data.raw);
            } else if (data.source === 'quote') {
                fillFromQuoteData(data.raw);
            } else if (data.source === 'order') {
                fillFromOrderData(data.raw);
            } else {
                fillFromProjectData(data.raw);
            }
        });
    };

    const reloadBillableTable = () => {
        if (!billableTable) return;
        const rows = buildBillableRows();
        billableTable.clear();
        billableTable.rows.add(rows).draw();
        if (billableCard) {
            billableCard.hidden = rows.length === 0;
        }
    };

    initBillableTable();

    clientSelect?.addEventListener('change', () => {
        serviceInput.value = '';
        projectInput.value = '';
        reloadBillableTable();
    });

    if (prefillService) {
        fillFromServiceData(prefillService);
    }
    applyClientSii(Number(clientSelect?.value || 0));
    reloadBillableTable();

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-remove-item]')) {
            const row = event.target.closest('[data-item-row]');
            row?.remove();
            updateFromItems();
        }
    });
</script>
