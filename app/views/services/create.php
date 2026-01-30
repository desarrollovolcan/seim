<div class="card">
    <div class="card-body">
        <form method="post" action="index.php?route=services/store" id="service-create-form">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="d-flex justify-content-end gap-2 mb-3">
                <button type="button" class="btn btn-outline-success btn-sm" disabled title="Disponible al guardar el servicio">
                    Crear factura
                </button>
            </div>
            <div class="accordion" id="serviceCreateAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="serviceCreateHeadingInfo">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#serviceCreateInfo" aria-expanded="true" aria-controls="serviceCreateInfo">
                            Cliente y servicio
                        </button>
                    </h2>
                    <div id="serviceCreateInfo" class="accordion-collapse collapse show" aria-labelledby="serviceCreateHeadingInfo" data-bs-parent="#serviceCreateAccordion">
                        <div class="accordion-body">
                            <div class="row g-2">
                                <div class="col-md-6">
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
                                <div class="col-md-6">
                                    <label class="form-label">Tipo de servicio</label>
                                    <select name="service_type_id" class="form-select" data-service-type-id required>
                                        <option value="">Selecciona tipo</option>
                                        <?php foreach (($serviceTypes ?? []) as $type): ?>
                                            <option value="<?php echo $type['id']; ?>">
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
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Costo</label>
                                    <input type="number" step="0.01" name="cost" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Moneda</label>
                                    <select name="currency" class="form-select">
                                        <option value="CLP">CLP</option>
                                        <option value="USD">USD</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="serviceCreateHeadingCycle">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#serviceCreateCycle" aria-expanded="false" aria-controls="serviceCreateCycle">
                            Ciclo y fechas
                        </button>
                    </h2>
                    <div id="serviceCreateCycle" class="accordion-collapse collapse" aria-labelledby="serviceCreateHeadingCycle" data-bs-parent="#serviceCreateAccordion">
                        <div class="accordion-body">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Ciclo de cobro</label>
                                    <select name="billing_cycle" class="form-select" data-billing-cycle>
                                        <option value="mensual">Mensual</option>
                                        <option value="anual">Anual</option>
                                        <option value="unico">Único</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha inicio</label>
                                    <input type="date" name="start_date" class="form-control" data-start-date>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha vencimiento</label>
                                    <div class="input-group">
                                        <input type="date" name="due_date" class="form-control" data-due-date>
                                        <button class="btn btn-outline-secondary" type="button" data-calc-due>Calcular</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha eliminación</label>
                                    <input type="date" name="delete_date" class="form-control" data-delete-date>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Días aviso 1</label>
                                    <input type="number" name="notice_days_1" class="form-control" value="15">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Días aviso 2</label>
                                    <input type="number" name="notice_days_2" class="form-control" value="5">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="serviceCreateHeadingAutomation">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#serviceCreateAutomation" aria-expanded="false" aria-controls="serviceCreateAutomation">
                            Automatización
                        </button>
                    </h2>
                    <div id="serviceCreateAutomation" class="accordion-collapse collapse" aria-labelledby="serviceCreateHeadingAutomation" data-bs-parent="#serviceCreateAccordion">
                        <div class="accordion-body">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Estado</label>
                                    <select name="status" class="form-select">
                                        <option value="activo">Activo</option>
                                        <option value="suspendido">Suspendido</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="auto_invoice" id="auto_invoice" checked>
                                        <label class="form-check-label" for="auto_invoice">Auto facturar</label>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="auto_email" id="auto_email" checked>
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
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'services/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Servicios de catálogo</h4>
    </div>
    <div class="card-body">
        <?php if (empty($systemServices)): ?>
            <p class="text-muted mb-0">No hay servicios de catálogo registrados.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th class="text-end">Costo</th>
                            <th>Moneda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($systemServices as $systemService): ?>
                            <tr>
                                <td><?php echo e($systemService['name']); ?></td>
                                <td><?php echo e($systemService['type_name']); ?></td>
                                <td class="text-end"><?php echo e(format_currency((float)($systemService['cost'] ?? 0))); ?></td>
                                <td><?php echo e($systemService['currency'] ?? 'CLP'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const billingCycleSelect = document.querySelector('[data-billing-cycle]');
    const startDateInput = document.querySelector('[data-start-date]');
    const dueDateInput = document.querySelector('[data-due-date]');
    const calcDueButton = document.querySelector('[data-calc-due]');
    const deleteDateInput = document.querySelector('[data-delete-date]');
    const serviceTypeSelect = document.querySelector('[data-service-type-id]');
    const systemServiceSelect = document.querySelector('[data-system-service]');
    const serviceNameInput = document.querySelector('input[name="name"]');
    const serviceCostInput = document.querySelector('input[name="cost"]');
    const currencySelect = document.querySelector('select[name="currency"]');
    const systemServices = <?php echo json_encode($systemServices ?? []); ?>;
    let dueDateTouched = false;

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

    const computeDueDate = () => {
        if (!startDateInput?.value || !billingCycleSelect) {
            return;
        }
        const startDate = new Date(startDateInput.value);
        if (Number.isNaN(startDate.getTime())) {
            return;
        }
        const cycle = billingCycleSelect.value;
        if (cycle === 'mensual') {
            startDate.setMonth(startDate.getMonth() + 1);
        } else if (cycle === 'anual') {
            startDate.setFullYear(startDate.getFullYear() + 1);
        } else {
            return;
        }
        const isoDate = startDate.toISOString().slice(0, 10);
        if (!dueDateTouched || !dueDateInput?.value) {
            dueDateInput.value = isoDate;
        }
        if (deleteDateInput && !deleteDateInput.value) {
            deleteDateInput.value = isoDate;
        }
    };

    dueDateInput?.addEventListener('input', () => {
        dueDateTouched = true;
    });

    billingCycleSelect?.addEventListener('change', computeDueDate);
    startDateInput?.addEventListener('change', computeDueDate);
    calcDueButton?.addEventListener('click', () => {
        dueDateTouched = false;
        computeDueDate();
    });
</script>
