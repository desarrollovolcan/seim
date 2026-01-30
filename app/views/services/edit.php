<div class="card">
    <div class="card-body">
        <form method="post" action="index.php?route=services/update" id="service-edit-form">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo $service['id'] ?? ''; ?>">
            <div class="d-flex justify-content-end gap-2 mb-3">
                <a href="index.php?route=invoices/create&service_id=<?php echo (int)($service['id'] ?? 0); ?>&client_id=<?php echo (int)($service['client_id'] ?? 0); ?>" class="btn btn-outline-success btn-sm">
                    Crear factura
                </a>
            </div>
            <div class="mb-3">
                <?php echo render_id_badge($service['id'] ?? null); ?>
            </div>
            <div class="accordion" id="serviceEditAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="serviceEditHeadingInfo">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#serviceEditInfo" aria-expanded="true" aria-controls="serviceEditInfo">
                            Cliente y servicio <span class="text-muted ms-2">#<?php echo (int)($service['id'] ?? 0); ?></span>
                        </button>
                    </h2>
                    <div id="serviceEditInfo" class="accordion-collapse collapse show" aria-labelledby="serviceEditHeadingInfo" data-bs-parent="#serviceEditAccordion">
                        <div class="accordion-body">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Cliente</label>
                                    <select name="client_id" class="form-select" required>
                                        <?php foreach ($clients as $client): ?>
                                            <option value="<?php echo $client['id']; ?>" <?php echo $client['id'] == ($service['client_id'] ?? null) ? 'selected' : ''; ?>><?php echo e($client['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tipo de servicio</label>
                                    <select name="service_type_id" class="form-select" data-service-type-id required>
                                        <option value="">Selecciona tipo</option>
                                        <?php foreach (($serviceTypes ?? []) as $type): ?>
                                            <option value="<?php echo $type['id']; ?>" <?php echo (int)($selectedServiceTypeId ?? 0) === (int)$type['id'] ? 'selected' : ''; ?>>
                                                <?php echo e($type['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Servicio catálogo</label>
                                    <select name="system_service_id" class="form-select" data-system-service>
                                        <option value="">Selecciona servicio</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nombre servicio</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo e($service['name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Costo</label>
                                    <input type="number" step="0.01" name="cost" class="form-control" value="<?php echo e($service['cost'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Moneda</label>
                                    <select name="currency" class="form-select">
                                        <option value="CLP" <?php echo ($service['currency'] ?? 'CLP') === 'CLP' ? 'selected' : ''; ?>>CLP</option>
                                        <option value="USD" <?php echo ($service['currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>USD</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="serviceEditHeadingCycle">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#serviceEditCycle" aria-expanded="false" aria-controls="serviceEditCycle">
                            Ciclo y fechas
                        </button>
                    </h2>
                    <div id="serviceEditCycle" class="accordion-collapse collapse" aria-labelledby="serviceEditHeadingCycle" data-bs-parent="#serviceEditAccordion">
                        <div class="accordion-body">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Ciclo de cobro</label>
                                    <select name="billing_cycle" class="form-select" data-billing-cycle>
                                        <option value="mensual" <?php echo ($service['billing_cycle'] ?? '') === 'mensual' ? 'selected' : ''; ?>>Mensual</option>
                                        <option value="anual" <?php echo ($service['billing_cycle'] ?? '') === 'anual' ? 'selected' : ''; ?>>Anual</option>
                                        <option value="unico" <?php echo ($service['billing_cycle'] ?? '') === 'unico' ? 'selected' : ''; ?>>Único</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha inicio</label>
                                    <input type="date" name="start_date" class="form-control" value="<?php echo e($service['start_date'] ?? ''); ?>" data-start-date>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha vencimiento</label>
                                    <div class="input-group">
                                        <input type="date" name="due_date" class="form-control" value="<?php echo e($service['due_date'] ?? ''); ?>" data-due-date>
                                        <button class="btn btn-outline-secondary" type="button" data-calc-due>Calcular</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha eliminación</label>
                                    <input type="date" name="delete_date" class="form-control" value="<?php echo e($service['delete_date'] ?? ''); ?>" data-delete-date>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Días aviso 1</label>
                                    <input type="number" name="notice_days_1" class="form-control" value="<?php echo e($service['notice_days_1'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Días aviso 2</label>
                                    <input type="number" name="notice_days_2" class="form-control" value="<?php echo e($service['notice_days_2'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="serviceEditHeadingAutomation">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#serviceEditAutomation" aria-expanded="false" aria-controls="serviceEditAutomation">
                            Automatización
                        </button>
                    </h2>
                    <div id="serviceEditAutomation" class="accordion-collapse collapse" aria-labelledby="serviceEditHeadingAutomation" data-bs-parent="#serviceEditAccordion">
                        <div class="accordion-body">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Estado</label>
                                    <select name="status" class="form-select">
                                        <option value="activo" <?php echo ($service['status'] ?? '') === 'activo' ? 'selected' : ''; ?>>Activo</option>
                                        <option value="suspendido" <?php echo ($service['status'] ?? '') === 'suspendido' ? 'selected' : ''; ?>>Suspendido</option>
                                        <option value="cancelado" <?php echo ($service['status'] ?? '') === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="auto_invoice" id="auto_invoice" <?php echo !empty($service['auto_invoice']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="auto_invoice">Auto facturar</label>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="auto_email" id="auto_email" <?php echo !empty($service['auto_email']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="auto_email">Auto enviar correos</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="index.php?route=services" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'services/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>

<?php if (!empty($renewals)): ?>
<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title mb-0">Historial de renovaciones</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-end">Monto</th>
                        <th>Moneda</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($renewals as $renewal): ?>
                        <tr>
                            <td><?php echo e(format_date($renewal['renewal_date'])); ?></td>
                            <td><?php echo e(str_replace('_', ' ', $renewal['status'] ?? '')); ?></td>
                            <td class="text-end"><?php echo e(format_currency((float)($renewal['amount'] ?? 0))); ?></td>
                            <td><?php echo e($renewal['currency'] ?? ''); ?></td>
                            <td><?php echo e($renewal['notes'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    const serviceBillingCycleSelect = document.querySelector('[data-billing-cycle]');
    const serviceStartDateInput = document.querySelector('[data-start-date]');
    const serviceDueDateInput = document.querySelector('[data-due-date]');
    const serviceCalcDueButton = document.querySelector('[data-calc-due]');
    const serviceDeleteDateInput = document.querySelector('[data-delete-date]');
    const serviceTypeSelect = document.querySelector('[data-service-type-id]');
    const systemServiceSelect = document.querySelector('[data-system-service]');
    const serviceNameInput = document.querySelector('input[name="name"]');
    const serviceCostInput = document.querySelector('input[name="cost"]');
    const currencySelect = document.querySelector('select[name="currency"]');
    const systemServices = <?php echo json_encode($systemServices ?? []); ?>;
    const selectedSystemServiceId = <?php echo json_encode($selectedSystemServiceId ?? null); ?>;
    let serviceDueDateTouched = Boolean(serviceDueDateInput?.value);
    let selectedSystemServiceApplied = false;

    const renderSystemServices = () => {
        if (!systemServiceSelect || !serviceTypeSelect) {
            return;
        }
        const selectedTypeId = parseInt(serviceTypeSelect.value, 10);
        systemServiceSelect.innerHTML = '<option value="">Selecciona servicio</option>';
        if (!Number.isNaN(selectedTypeId)) {
            systemServices
                .filter((service) => Number(service.service_type_id) === selectedTypeId)
                .forEach((service) => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = service.name;
                    option.dataset.cost = service.cost;
                    option.dataset.currency = service.currency;
                    systemServiceSelect.appendChild(option);
                });
        }
        if (selectedSystemServiceId && !selectedSystemServiceApplied) {
            systemServiceSelect.value = String(selectedSystemServiceId);
            selectedSystemServiceApplied = true;
        }
    };

    const applySystemService = () => {
        if (!systemServiceSelect) {
            return;
        }
        const selected = systemServiceSelect.options[systemServiceSelect.selectedIndex];
        if (!selected || !selected.value) {
            return;
        }
        if (serviceNameInput && !serviceNameInput.value) {
            serviceNameInput.value = selected.textContent ?? '';
        }
        if (serviceCostInput && !serviceCostInput.value) {
            serviceCostInput.value = selected.dataset.cost ?? '';
        }
        if (currencySelect && selected.dataset.currency) {
            currencySelect.value = selected.dataset.currency;
        }
    };

    serviceTypeSelect?.addEventListener('change', () => {
        renderSystemServices();
        if (systemServiceSelect) {
            systemServiceSelect.value = '';
        }
    });
    systemServiceSelect?.addEventListener('change', applySystemService);
    renderSystemServices();

    const computeServiceDueDate = () => {
        if (!serviceStartDateInput?.value || !serviceBillingCycleSelect) {
            return;
        }
        const startDate = new Date(serviceStartDateInput.value);
        if (Number.isNaN(startDate.getTime())) {
            return;
        }
        const cycle = serviceBillingCycleSelect.value;
        if (cycle === 'mensual') {
            startDate.setMonth(startDate.getMonth() + 1);
        } else if (cycle === 'anual') {
            startDate.setFullYear(startDate.getFullYear() + 1);
        } else {
            return;
        }
        const isoDate = startDate.toISOString().slice(0, 10);
        if (!serviceDueDateTouched || !serviceDueDateInput?.value) {
            serviceDueDateInput.value = isoDate;
        }
        if (serviceDeleteDateInput && !serviceDeleteDateInput.value) {
            serviceDeleteDateInput.value = isoDate;
        }
    };

    serviceDueDateInput?.addEventListener('input', () => {
        serviceDueDateTouched = true;
    });

    serviceBillingCycleSelect?.addEventListener('change', computeServiceDueDate);
    serviceStartDateInput?.addEventListener('change', computeServiceDueDate);
    serviceCalcDueButton?.addEventListener('click', () => {
        serviceDueDateTouched = false;
        computeServiceDueDate();
    });
</script>
