<div class="card mb-4" id="renewal-form-card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="card-title mb-1" data-renewal-form-title>Nueva renovación</h4>
            <p class="text-muted mb-0">Centraliza la información para anticipar vencimientos y mantener seguimiento.</p>
        </div>
        <a class="btn btn-outline-primary" href="#renewals-list">Ver listado</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=crm/renewals/store" id="renewal-form">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" id="renewal-id">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="renewal-client">Cliente</label>
                    <select class="form-select" id="renewal-client" name="client_id" data-client-select required>
                        <option value="">Selecciona cliente</option>
                        <?php foreach ($clients as $client): ?>
                            <?php
                            $contactName = $client['contact'] ?: $client['name'];
                            ?>
                            <option value="<?php echo (int)$client['id']; ?>"
                                data-contact-name="<?php echo e($contactName); ?>"
                                data-contact-email="<?php echo e($client['email'] ?? ''); ?>"
                                data-contact-phone="<?php echo e($client['phone'] ?? ''); ?>"
                                data-address="<?php echo e($client['address'] ?? ''); ?>"
                                data-rut="<?php echo e($client['rut'] ?? ''); ?>"
                                data-billing-email="<?php echo e($client['billing_email'] ?? ''); ?>">
                                <?php echo e($client['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="renewal-service">Servicio</label>
                    <select class="form-select" id="renewal-service" name="service_id">
                        <option value="">Selecciona servicio</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo (int)$service['id']; ?>"><?php echo e($service['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="renewal-date">Fecha renovación</label>
                    <input type="date" class="form-control" id="renewal-date" name="renewal_date" value="<?php echo date('Y-m-d'); ?>" data-default-date="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="renewal-amount">Monto (CLP)</label>
                    <input type="number" class="form-control" id="renewal-amount" name="amount" min="0" step="0.01" placeholder="Ej: 450000" inputmode="decimal" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="renewal-currency">Moneda</label>
                    <select class="form-select" id="renewal-currency" name="currency">
                        <option value="CLP" selected>CLP</option>
                        <option value="USD">USD</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="renewal-status">Estado</label>
                    <select class="form-select" id="renewal-status" name="status">
                        <option value="pendiente">Pendiente</option>
                        <option value="en_negociacion">En negociación</option>
                        <option value="renovado">Renovado</option>
                        <option value="no_renovado">No renovado</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="renewal-reminder">Recordatorio (días)</label>
                    <input type="number" class="form-control" id="renewal-reminder" name="reminder_days" min="1" value="15">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="renewal-contact-name">Contacto cliente</label>
                    <input type="text" class="form-control" id="renewal-contact-name" name="contact_name" data-client-field="contact_name" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="renewal-contact-email">Correo contacto</label>
                    <input type="email" class="form-control" id="renewal-contact-email" name="contact_email" data-client-field="contact_email" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="renewal-contact-phone">Teléfono contacto</label>
                    <input type="text" class="form-control" id="renewal-contact-phone" name="contact_phone" data-client-field="contact_phone" readonly>
                </div>
                <div class="col-12">
                    <label class="form-label" for="renewal-notes">Notas</label>
                    <textarea class="form-control" id="renewal-notes" name="notes" rows="3" placeholder="Responsables, acuerdos y próximos pasos"></textarea>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                <button type="button" class="btn btn-light w-100 w-sm-auto" id="renewal-reset">Cancelar edición</button>
                <button type="submit" class="btn btn-primary w-100 w-sm-auto" data-renewal-submit>Guardar renovación</button>
            </div>
            <?php
            $reportTemplate = 'informeIcargaEspanol.php';
            $reportSource = 'crm/renewals';
            include __DIR__ . '/../partials/report-download.php';
            ?>
        </form>
        <form method="post" action="index.php?route=crm/renewals/send-email" class="mt-3 d-none" id="renewal-email-form">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" id="renewal-email-id">
            <button type="submit" class="btn btn-soft-info w-100 w-sm-auto">Enviar correo</button>
        </form>
    </div>
</div>

<div class="card" id="renewals-list">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="card-title mb-1">Renovaciones</h4>
            <p class="text-muted mb-0">Anticipa renovaciones y mantén el control de servicios activos.</p>
        </div>
        <a class="btn btn-outline-primary" href="#renewal-form-card" data-mode="create">Nueva renovación</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Fecha renovación</th>
                        <th>Estado</th>
                        <th class="text-end">Monto</th>
                        <th class="text-end">Recordatorio</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($renewals)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay renovaciones registradas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($renewals as $renewal): ?>
                            <tr>
                                <td class="text-muted">#<?php echo (int)$renewal['id']; ?></td>
                                <td><?php echo e($renewal['client_name'] ?? ''); ?></td>
                                <td><?php echo e($renewal['service_name'] ?? '-'); ?></td>
                                <td><?php echo e(format_date($renewal['renewal_date'])); ?></td>
                                <td>
                                    <?php $status = $renewal['status'] ?? 'pendiente'; ?>
                                    <span class="badge bg-<?php echo $status === 'renovado' ? 'success' : ($status === 'no_renovado' ? 'danger' : ($status === 'en_negociacion' ? 'warning' : 'info')); ?>-subtle text-<?php echo $status === 'renovado' ? 'success' : ($status === 'no_renovado' ? 'danger' : ($status === 'en_negociacion' ? 'warning' : 'info')); ?>">
                                        <?php echo e(str_replace('_', ' ', $status)); ?>
                                    </span>
                                </td>
                                <td class="text-end"><?php echo e(format_currency((float)($renewal['amount'] ?? 0))); ?></td>
                                <td class="text-end"><?php echo (int)($renewal['reminder_days'] ?? 0); ?> días</td>
                                <td class="text-end">
                                    <div class="dropdown actions-dropdown">
                                        <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <?php if (($renewal['status'] ?? '') !== 'renovado'): ?>
                                                <li>
                                                    <form method="post" action="index.php?route=crm/renewals/approve" onsubmit="return confirm('¿Aprobar esta renovación?');">
                                                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                        <input type="hidden" name="id" value="<?php echo (int)$renewal['id']; ?>">
                                                        <button type="submit" class="dropdown-item dropdown-item-button">Aprobar</button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                            <li>
                                                <button
                                                    type="button"
                                                    class="dropdown-item dropdown-item-button js-renewal-edit"
                                                    data-id="<?php echo (int)$renewal['id']; ?>"
                                                    data-date="<?php echo e($renewal['renewal_date']); ?>"
                                                    data-status="<?php echo e($renewal['status']); ?>"
                                                    data-amount="<?php echo e($renewal['amount']); ?>"
                                                    data-currency="<?php echo e($renewal['currency']); ?>"
                                                    data-reminder="<?php echo e($renewal['reminder_days']); ?>"
                                                    data-notes="<?php echo e($renewal['notes']); ?>"
                                                    data-client-id="<?php echo (int)$renewal['client_id']; ?>"
                                                    data-service-id="<?php echo (int)($renewal['service_id'] ?? 0); ?>"
                                                >
                                                    Editar
                                                </button>
                                            </li>
                                            <?php if (($renewal['status'] ?? '') === 'renovado'): ?>
                                                <li>
                                                    <form method="post" action="index.php?route=crm/renewals/send-email" onsubmit="return confirm('¿Enviar correo de renovación exitosa?');">
                                                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                        <input type="hidden" name="id" value="<?php echo (int)$renewal['id']; ?>">
                                                        <button type="submit" class="dropdown-item dropdown-item-button">Enviar correo</button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                            <li>
                                                <form method="post" action="index.php?route=crm/renewals/delete" onsubmit="return confirm('¿Eliminar esta renovación?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo (int)$renewal['id']; ?>">
                                                    <button type="submit" class="dropdown-item dropdown-item-button text-danger">Eliminar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="assets/js/pages/crm-modal-forms.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('renewal-form');
    if (!form) return;

    var titleEl = document.querySelector('[data-renewal-form-title]');
    var submitBtn = form.querySelector('[data-renewal-submit]');
    var clientSelect = document.getElementById('renewal-client');
    var serviceSelect = document.getElementById('renewal-service');
    var dateInput = document.getElementById('renewal-date');
    var statusSelect = document.getElementById('renewal-status');
    var amountInput = document.getElementById('renewal-amount');
    var currencySelect = document.getElementById('renewal-currency');
    var reminderInput = document.getElementById('renewal-reminder');
    var notesInput = document.getElementById('renewal-notes');
    var idInput = document.getElementById('renewal-id');
    var emailForm = document.getElementById('renewal-email-form');
    var emailIdInput = document.getElementById('renewal-email-id');
    var resetBtn = document.getElementById('renewal-reset');

    var defaultDate = dateInput ? dateInput.getAttribute('data-default-date') : '';
    var updateEmailButtonVisibility = function (status, hasId) {
        if (!emailForm) return;
        if (status === 'renovado' && hasId) {
            emailForm.classList.remove('d-none');
        } else {
            emailForm.classList.add('d-none');
        }
    };

    var resetForm = function () {
        if (!form) return;
        form.reset();
        if (dateInput && defaultDate) {
            dateInput.value = defaultDate;
        }
        form.action = 'index.php?route=crm/renewals/store';
        if (idInput) {
            idInput.value = '';
        }
        if (emailIdInput) {
            emailIdInput.value = '';
        }
        if (titleEl) {
            titleEl.textContent = 'Nueva renovación';
        }
        if (submitBtn) {
            submitBtn.textContent = 'Guardar renovación';
        }
        updateEmailButtonVisibility(statusSelect ? statusSelect.value : 'pendiente', false);
    };

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            resetForm();
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    document.querySelectorAll('.js-renewal-edit').forEach(function (button) {
        button.addEventListener('click', function () {
            var id = button.getAttribute('data-id') || '';
            var date = button.getAttribute('data-date') || '';
            var status = button.getAttribute('data-status') || 'pendiente';
            var amount = button.getAttribute('data-amount') || '';
            var currency = button.getAttribute('data-currency') || 'CLP';
            var reminder = button.getAttribute('data-reminder') || '15';
            var notes = button.getAttribute('data-notes') || '';
            var clientId = button.getAttribute('data-client-id') || '';
            var serviceId = button.getAttribute('data-service-id') || '';

            form.action = 'index.php?route=crm/renewals/update';
            if (idInput) {
                idInput.value = id;
            }
            if (emailIdInput) {
                emailIdInput.value = id;
            }
            if (titleEl) {
                titleEl.textContent = 'Editar renovación';
            }
            if (submitBtn) {
                submitBtn.textContent = 'Actualizar renovación';
            }

            if (clientSelect) {
                clientSelect.value = clientId;
                clientSelect.dispatchEvent(new Event('change'));
            }
            if (serviceSelect) {
                serviceSelect.value = serviceId;
            }
            if (dateInput) {
                dateInput.value = date;
            }
            if (statusSelect) {
                statusSelect.value = status;
            }
            if (amountInput) {
                amountInput.value = amount;
            }
            if (currencySelect) {
                currencySelect.value = currency;
            }
            if (reminderInput) {
                reminderInput.value = reminder;
            }
            if (notesInput) {
                notesInput.value = notes;
            }

            updateEmailButtonVisibility(status, !!id);
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    if (statusSelect) {
        statusSelect.addEventListener('change', function () {
            var status = statusSelect.value || 'pendiente';
            var hasId = !!(idInput && idInput.value);
            updateEmailButtonVisibility(status, hasId);
        });
    }

    updateEmailButtonVisibility(statusSelect ? statusSelect.value : 'pendiente', false);
});
</script>
