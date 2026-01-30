<?php
$defaultIssueDate = $quote['fecha_emision'] ?? date('Y-m-d');
$item = $items[0] ?? [
    'descripcion' => '',
    'cantidad' => 1,
    'precio_unitario' => 0,
    'total' => 0,
];
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
                'sii_receiver_activity_code' => $quote['sii_receiver_activity_code'] ?? '',
                'sii_receiver_address' => $quote['sii_receiver_address'] ?? '',
                'sii_receiver_commune' => $quote['sii_receiver_commune'] ?? '',
                'sii_receiver_city' => $quote['sii_receiver_city'] ?? '',
                'sii_tax_rate' => $quote['sii_tax_rate'] ?? 19,
                'sii_exempt_amount' => $quote['sii_exempt_amount'] ?? 0,
            ];
            include __DIR__ . '/../partials/sii-document-fields.php';
            ?>

            <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                <select class="form-select form-select-sm" data-product-select>
                    <option value="">Selecciona producto</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>"
                                data-name="<?php echo e($product['name'] ?? ''); ?>"
                                data-price="<?php echo e($product['price'] ?? 0); ?>">
                            <?php echo e($product['name']); ?>
                            <?php if (!empty($product['produced_qty'])): ?>
                                (Producido: <?php echo (int)$product['produced_qty']; ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-outline-success btn-sm" data-apply-product>Usar producto</button>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Descripción</th>
                            <th style="width: 120px;">Cantidad</th>
                            <th style="width: 160px;">Precio unitario</th>
                            <th style="width: 160px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" name="items[0][descripcion]" class="form-control" value="<?php echo e($item['descripcion']); ?>" data-item-description>
                            </td>
                            <td>
                                <input type="number" name="items[0][cantidad]" class="form-control" value="<?php echo e($item['cantidad']); ?>" min="1" data-item-qty>
                            </td>
                            <td>
                                <input type="number" name="items[0][precio_unitario]" class="form-control" value="<?php echo e($item['precio_unitario']); ?>" step="0.01" data-item-price>
                            </td>
                            <td>
                                <input type="number" name="items[0][total]" class="form-control" value="<?php echo e($item['total']); ?>" step="0.01" readonly data-item-total>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Subtotal</label>
                        <input type="number" name="subtotal" class="form-control" value="<?php echo e($quote['subtotal'] ?? 0); ?>" step="0.01" readonly data-subtotal>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Impuestos</label>
                        <input type="number" name="impuestos" class="form-control" value="<?php echo e($quote['impuestos'] ?? 0); ?>" step="0.01" data-taxes>
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
    const descriptionInput = document.querySelector('[data-item-description]');
    const qtyInput = document.querySelector('[data-item-qty]');
    const priceInput = document.querySelector('[data-item-price]');
    const totalInput = document.querySelector('[data-item-total]');
    const subtotalInput = document.querySelector('[data-subtotal]');
    const taxesInput = document.querySelector('[data-taxes]');
    const totalSummaryInput = document.querySelector('[data-total]');
    const clientSelect = document.querySelector('select[name="client_id"]');
    const productSelect = document.querySelector('[data-product-select]');
    const applyProductButton = document.querySelector('[data-apply-product]');
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

    const updateTotals = () => {
        const qty = Number(qtyInput?.value || 0);
        const price = Number(priceInput?.value || 0);
        const subtotal = qty * price;
        if (totalInput) {
            totalInput.value = subtotal.toFixed(2);
        }
        if (subtotalInput) {
            subtotalInput.value = subtotal.toFixed(2);
        }
        const taxes = Number(taxesInput?.value || 0);
        if (totalSummaryInput) {
            totalSummaryInput.value = (subtotal + taxes).toFixed(2);
        }
    };

    const applySourceData = (option) => {
        if (!option) {
            return;
        }
        const name = option.dataset.name || '';
        const price = option.dataset.price || 0;
        if (descriptionInput) {
            descriptionInput.value = name;
        }
        if (priceInput) {
            priceInput.value = Number(price).toFixed(2);
        }
        updateTotals();
    };

    serviceSelect?.addEventListener('change', (event) => {
        if (projectSelect) {
            projectSelect.value = '';
        }
        applySourceData(event.target.selectedOptions[0]);
    });

    applyProductButton?.addEventListener('click', () => {
        if (serviceSelect) {
            serviceSelect.value = '';
        }
        if (projectSelect) {
            projectSelect.value = '';
        }
        applySourceData(productSelect?.selectedOptions?.[0]);
        if (productSelect) {
            productSelect.value = '';
        }
    });

    projectSelect?.addEventListener('change', (event) => {
        if (serviceSelect) {
            serviceSelect.value = '';
        }
        applySourceData(event.target.selectedOptions[0]);
        const clientId = event.target.selectedOptions[0]?.dataset?.clientId;
        if (clientSelect && clientId) {
            clientSelect.value = clientId;
            applyClientSii(Number(clientId));
        }
    });

    qtyInput?.addEventListener('input', updateTotals);
    priceInput?.addEventListener('input', updateTotals);
    taxesInput?.addEventListener('input', updateTotals);
    clientSelect?.addEventListener('change', () => {
        applyClientSii(Number(clientSelect?.value || 0));
    });

    updateTotals();
</script>
