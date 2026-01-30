<div class="card">
    <div class="card-body">
        <form method="post" action="index.php?route=projects/update" id="project-edit-form">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
            <div class="mb-3">
                <?php echo render_id_badge($project['id'] ?? null); ?>
            </div>
            <div class="accordion" id="projectEditAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="projectEditHeadingInfo">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#projectEditInfo" aria-expanded="true" aria-controls="projectEditInfo">
                            Datos del proyecto
                        </button>
                    </h2>
                    <div id="projectEditInfo" class="accordion-collapse collapse show" aria-labelledby="projectEditHeadingInfo" data-bs-parent="#projectEditAccordion">
                        <div class="accordion-body">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Cliente</label>
                                    <select name="client_id" class="form-select" required data-mandante-source>
                                        <?php foreach ($clients as $client): ?>
                                            <option value="<?php echo $client['id']; ?>"
                                                <?php echo $client['id'] == $project['client_id'] ? 'selected' : ''; ?>
                                                data-mandante-name="<?php echo e($client['mandante_name'] ?? ''); ?>"
                                                data-mandante-rut="<?php echo e($client['mandante_rut'] ?? ''); ?>"
                                                data-mandante-phone="<?php echo e($client['mandante_phone'] ?? ''); ?>"
                                                data-mandante-email="<?php echo e($client['mandante_email'] ?? ''); ?>">
                                                <?php echo e($client['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nombre del proyecto</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo e($project['name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="description" class="form-control" rows="3"><?php echo e($project['description'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="projectEditHeadingPlan">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#projectEditPlan" aria-expanded="false" aria-controls="projectEditPlan">
                            Estado y planificación
                        </button>
                    </h2>
                    <div id="projectEditPlan" class="accordion-collapse collapse" aria-labelledby="projectEditHeadingPlan" data-bs-parent="#projectEditAccordion">
                        <div class="accordion-body">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Estado</label>
                                    <select name="status" class="form-select">
                                        <option value="cotizado" <?php echo ($project['status'] ?? '') === 'cotizado' ? 'selected' : ''; ?>>Cotizado</option>
                                        <option value="en_curso" <?php echo ($project['status'] ?? '') === 'en_curso' ? 'selected' : ''; ?>>En curso</option>
                                        <option value="en_pausa" <?php echo ($project['status'] ?? '') === 'en_pausa' ? 'selected' : ''; ?>>En pausa</option>
                                        <option value="finalizado" <?php echo ($project['status'] ?? '') === 'finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha inicio</label>
                                    <input type="date" name="start_date" class="form-control" value="<?php echo e($project['start_date'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha entrega</label>
                                    <input type="date" name="delivery_date" class="form-control" value="<?php echo e($project['delivery_date'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Valor</label>
                                    <input type="number" step="0.01" name="value" class="form-control" value="<?php echo e($project['value'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="projectEditHeadingMandante">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#projectEditMandante" aria-expanded="false" aria-controls="projectEditMandante">
                            Datos del mandante
                        </button>
                    </h2>
                    <div id="projectEditMandante" class="accordion-collapse collapse" aria-labelledby="projectEditHeadingMandante" data-bs-parent="#projectEditAccordion">
                        <div class="accordion-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                <div class="text-muted">Puedes cargar los datos del cliente seleccionado.</div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-soft-primary btn-sm" data-mandante-fill>Actualizar desde cliente</button>
                                    <button type="button" class="btn btn-light btn-sm" data-mandante-clear>Limpiar</button>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Mandante - Nombre</label>
                                    <input type="text" name="mandante_name" class="form-control" value="<?php echo e($project['mandante_name'] ?? ''); ?>" data-mandante-field="name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mandante - RUT</label>
                                    <input type="text" name="mandante_rut" class="form-control" value="<?php echo e($project['mandante_rut'] ?? ''); ?>" data-mandante-field="rut">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mandante - Teléfono</label>
                                    <input type="text" name="mandante_phone" class="form-control" value="<?php echo e($project['mandante_phone'] ?? ''); ?>" data-mandante-field="phone">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mandante - Correo</label>
                                    <input type="email" name="mandante_email" class="form-control" value="<?php echo e($project['mandante_email'] ?? ''); ?>" data-mandante-field="email">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="projectEditHeadingNotes">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#projectEditNotes" aria-expanded="false" aria-controls="projectEditNotes">
                            Notas
                        </button>
                    </h2>
                    <div id="projectEditNotes" class="accordion-collapse collapse" aria-labelledby="projectEditHeadingNotes" data-bs-parent="#projectEditAccordion">
                        <div class="accordion-body">
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label">Notas</label>
                                    <textarea name="notes" class="form-control" rows="3"><?php echo e($project['notes'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="index.php?route=invoices/create&project_id=<?php echo $project['id']; ?>&client_id=<?php echo $project['client_id']; ?>" class="btn btn-outline-primary">Crear factura</a>
                <a href="index.php?route=projects" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Agregar tarea al proyecto</h5>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=projects/tasks/store" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
            <div class="col-md-6">
                <label class="form-label">Título de la tarea</label>
                <input type="text" name="title" class="form-control" placeholder="Ej: Brief creativo" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Inicio</label>
                <input type="date" name="start_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Entrega</label>
                <input type="date" name="end_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Avance (%)</label>
                <input type="number" name="progress_percent" class="form-control" min="0" max="100" step="1" value="0" required>
            </div>
            <div class="col-md-9 d-flex align-items-end">
                <div class="text-muted fs-sm">
                    El avance total del proyecto se calcula sumando el porcentaje de cada tarea (máximo 100%).
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Agregar tarea</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'projects/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>

<script>
    const projectEditClientSelect = document.querySelector('[data-mandante-source]');
    const projectEditMandanteFields = {
        name: document.querySelector('[data-mandante-field="name"]'),
        rut: document.querySelector('[data-mandante-field="rut"]'),
        phone: document.querySelector('[data-mandante-field="phone"]'),
        email: document.querySelector('[data-mandante-field="email"]'),
    };
    const projectEditMandanteFillButton = document.querySelector('[data-mandante-fill]');
    const projectEditMandanteClearButton = document.querySelector('[data-mandante-clear]');

    const getProjectEditMandanteValues = () => {
        const option = projectEditClientSelect?.selectedOptions?.[0];
        if (!option) {
            return null;
        }
        return {
            name: option.dataset.mandanteName || '',
            rut: option.dataset.mandanteRut || '',
            phone: option.dataset.mandantePhone || '',
            email: option.dataset.mandanteEmail || '',
        };
    };

    const fillProjectEditMandanteFromClient = (override = false) => {
        const values = getProjectEditMandanteValues();
        if (!values) {
            return;
        }
        Object.entries(projectEditMandanteFields).forEach(([key, field]) => {
            if (field && (override || field.value.trim() === '')) {
                field.value = values[key];
            }
        });
    };

    projectEditClientSelect?.addEventListener('change', fillProjectEditMandanteFromClient);
    projectEditMandanteFillButton?.addEventListener('click', () => fillProjectEditMandanteFromClient(true));
    projectEditMandanteClearButton?.addEventListener('click', () => {
        Object.values(projectEditMandanteFields).forEach((field) => {
            if (field) {
                field.value = '';
            }
        });
    });

</script>
