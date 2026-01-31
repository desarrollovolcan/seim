<?php
$editBrief = $editBrief ?? null;
$isEdit = is_array($editBrief) && !empty($editBrief);
$formBrief = $isEdit ? $editBrief : [];
$formAction = $isEdit ? 'index.php?route=crm/briefs/update' : 'index.php?route=crm/briefs/store';
?>

<div class="card mb-4">
    <div class="card-header">
        <h4 class="card-title mb-1"><?php echo $isEdit ? 'Editar brief comercial' : 'Nuevo brief comercial'; ?></h4>
        <p class="text-muted mb-0">
            <?php echo $isEdit ? 'Actualiza la información del brief y genera el reporte PDF.' : 'Registra los requerimientos comerciales y genera el reporte PDF.'; ?>
        </p>
    </div>
    <div class="card-body">
        <form method="post" action="<?php echo e($formAction); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?php echo (int)($formBrief['id'] ?? 0); ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="brief-title">Nombre del brief</label>
                    <input type="text" class="form-control" id="brief-title" name="title" value="<?php echo e($formBrief['title'] ?? ''); ?>" placeholder="Ej: Campaña verano 2025" autocomplete="off" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="brief-client">Cliente</label>
                    <select class="form-select" id="brief-client" name="client_id" data-client-select required>
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
                                data-billing-email="<?php echo e($client['billing_email'] ?? ''); ?>"
                                <?php echo (int)($formBrief['client_id'] ?? 0) === (int)$client['id'] ? 'selected' : ''; ?>>
                                <?php echo e($client['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="brief-contact-name">Contacto</label>
                    <input type="text" class="form-control" id="brief-contact-name" name="contact_name" value="<?php echo e($formBrief['contact_name'] ?? ''); ?>" placeholder="Nombre del contacto" autocomplete="name" data-client-field="contact_name">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="brief-contact-email">Correo contacto</label>
                    <input type="email" class="form-control" id="brief-contact-email" name="contact_email" value="<?php echo e($formBrief['contact_email'] ?? ''); ?>" placeholder="contacto@cliente.cl" autocomplete="email" inputmode="email" data-client-field="contact_email">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="brief-contact-phone">Teléfono contacto</label>
                    <input type="tel" class="form-control" id="brief-contact-phone" name="contact_phone" value="<?php echo e($formBrief['contact_phone'] ?? ''); ?>" placeholder="+56 9 1234 5678" autocomplete="tel" inputmode="tel" data-client-field="contact_phone">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="brief-service">Servicio solicitado</label>
                    <input type="text" class="form-control" id="brief-service" name="service_summary" value="<?php echo e($formBrief['service_summary'] ?? ''); ?>" placeholder="Ej: Branding, Ads, Web">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="brief-budget">Presupuesto estimado (CLP)</label>
                    <input type="number" class="form-control" id="brief-budget" name="expected_budget" min="0" step="0.01" value="<?php echo e($formBrief['expected_budget'] ?? ''); ?>" placeholder="Ej: 1500000" inputmode="decimal">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="brief-start">Fecha deseada</label>
                    <input type="date" class="form-control" id="brief-start" name="desired_start_date" value="<?php echo e($formBrief['desired_start_date'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="brief-status">Estado</label>
                    <select class="form-select" id="brief-status" name="status">
                        <?php $currentStatus = $formBrief['status'] ?? 'nuevo'; ?>
                        <option value="nuevo" <?php echo $currentStatus === 'nuevo' ? 'selected' : ''; ?>>Nuevo</option>
                        <option value="en_revision" <?php echo $currentStatus === 'en_revision' ? 'selected' : ''; ?>>En revisión</option>
                        <option value="en_ejecucion" <?php echo $currentStatus === 'en_ejecucion' ? 'selected' : ''; ?>>En ejecución</option>
                        <option value="aprobado" <?php echo $currentStatus === 'aprobado' ? 'selected' : ''; ?>>Aprobado</option>
                        <option value="descartado" <?php echo $currentStatus === 'descartado' ? 'selected' : ''; ?>>Descartado</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label" for="brief-notes">Notas comerciales</label>
                    <textarea class="form-control" id="brief-notes" name="notes" rows="3" placeholder="Contexto, objetivos y próximos pasos"><?php echo e($formBrief['notes'] ?? ''); ?></textarea>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
                <?php if ($isEdit): ?>
                    <a href="index.php?route=crm/briefs" class="btn btn-light">Cancelar edición</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Actualizar brief' : 'Guardar brief'; ?></button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="card-title mb-1">Briefs comerciales</h4>
            <p class="text-muted mb-0">Revisa el historial de briefs comerciales cargados.</p>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Brief</th>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th class="text-end">Presupuesto</th>
                        <th>Fecha deseada</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($briefs)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay briefs comerciales registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($briefs as $brief): ?>
                            <tr>
                                <td class="text-muted"><?php echo render_id_badge($brief['id'] ?? null); ?></td>
                                <td><?php echo e($brief['title']); ?></td>
                                <td><?php echo e($brief['client_name'] ?? ''); ?></td>
                                <td><?php echo e($brief['service_summary'] ?? ''); ?></td>
                                <td>
                                    <?php $status = $brief['status'] ?? 'nuevo'; ?>
                                    <?php
                                    $statusClasses = [
                                        'aprobado' => ['success', 'success'],
                                        'en_revision' => ['warning', 'warning'],
                                        'en_ejecucion' => ['primary', 'primary'],
                                        'descartado' => ['danger', 'danger'],
                                        'nuevo' => ['info', 'info'],
                                    ];
                                    [$bgClass, $textClass] = $statusClasses[$status] ?? ['info', 'info'];
                                    ?>
                                    <span class="badge bg-<?php echo $bgClass; ?>-subtle text-<?php echo $textClass; ?>">
                                        <?php echo e(str_replace('_', ' ', $status)); ?>
                                    </span>
                                </td>
                                <td class="text-end"><?php echo e(format_currency((float)($brief['expected_budget'] ?? 0))); ?></td>
                                <td><?php echo e($brief['desired_start_date'] ?? '-'); ?></td>
                                <td class="text-end">
                                    <div class="dropdown actions-dropdown">
                                        <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <form method="post" action="index.php?route=crm/briefs/execute" onsubmit="return confirm('¿Marcar este brief como en ejecución?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo (int)($brief['id'] ?? 0); ?>">
                                                    <button type="submit" class="dropdown-item dropdown-item-button">Ejecutar</button>
                                                </form>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="index.php?route=crm/briefs/edit&amp;id=<?php echo (int)($brief['id'] ?? 0); ?>">
                                                    Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form method="post" action="index.php?route=crm/briefs/delete" onsubmit="return confirm('¿Eliminar este brief comercial?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo (int)($brief['id'] ?? 0); ?>">
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
