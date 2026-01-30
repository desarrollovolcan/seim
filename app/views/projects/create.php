<div class="card">
    <div class="card-body">
        <form method="post" action="index.php?route=projects/store" id="project-create-form">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="accordion" id="projectCreateAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="projectCreateHeadingInfo">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#projectCreateInfo" aria-expanded="true" aria-controls="projectCreateInfo">
                            Datos del proyecto
                        </button>
                    </h2>
                    <div id="projectCreateInfo" class="accordion-collapse collapse show" aria-labelledby="projectCreateHeadingInfo" data-bs-parent="#projectCreateAccordion">
                        <div class="accordion-body">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Cliente</label>
                                    <select name="client_id" class="form-select" required data-mandante-source>
                                        <option value="">Selecciona cliente</option>
                                        <?php foreach ($clients as $client): ?>
                                            <option value="<?php echo $client['id']; ?>"
                                                data-mandante-name="<?php echo e($client['mandante_name'] ?? ''); ?>"
                                                data-mandante-rut="<?php echo e($client['mandante_rut'] ?? ''); ?>"
                                                data-mandante-phone="<?php echo e($client['mandante_phone'] ?? ''); ?>"
                                                data-mandante-email="<?php echo e($client['mandante_email'] ?? ''); ?>"
                                                <?php echo (int)($selectedClientId ?? 0) === (int)$client['id'] ? 'selected' : ''; ?>>
                                                <?php echo e($client['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nombre del proyecto</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="projectCreateHeadingPlan">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#projectCreatePlan" aria-expanded="false" aria-controls="projectCreatePlan">
                            Estado y planificación
                        </button>
                    </h2>
                    <div id="projectCreatePlan" class="accordion-collapse collapse" aria-labelledby="projectCreateHeadingPlan" data-bs-parent="#projectCreateAccordion">
                        <div class="accordion-body">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Estado</label>
                                    <select name="status" class="form-select">
                                        <option value="cotizado">Cotizado</option>
                                        <option value="en_curso">En curso</option>
                                        <option value="en_pausa">En pausa</option>
                                        <option value="finalizado">Finalizado</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha inicio</label>
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha entrega</label>
                                    <input type="date" name="delivery_date" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Valor</label>
                                    <input type="number" step="0.01" name="value" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="projectCreateHeadingMandante">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#projectCreateMandante" aria-expanded="false" aria-controls="projectCreateMandante">
                            Datos del mandante
                        </button>
                    </h2>
                    <div id="projectCreateMandante" class="accordion-collapse collapse" aria-labelledby="projectCreateHeadingMandante" data-bs-parent="#projectCreateAccordion">
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
                                    <input type="text" name="mandante_name" class="form-control" data-mandante-field="name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mandante - RUT</label>
                                    <input type="text" name="mandante_rut" class="form-control" data-mandante-field="rut">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mandante - Teléfono</label>
                                    <input type="text" name="mandante_phone" class="form-control" data-mandante-field="phone">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mandante - Correo</label>
                                    <input type="email" name="mandante_email" class="form-control" data-mandante-field="email">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="projectCreateHeadingNotes">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#projectCreateNotes" aria-expanded="false" aria-controls="projectCreateNotes">
                            Notas
                        </button>
                    </h2>
                    <div id="projectCreateNotes" class="accordion-collapse collapse" aria-labelledby="projectCreateHeadingNotes" data-bs-parent="#projectCreateAccordion">
                        <div class="accordion-body">
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label">Notas</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="index.php?route=projects" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'projects/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>

<script>
    const projectClientSelect = document.querySelector('[data-mandante-source]');
    const projectMandanteFields = {
        name: document.querySelector('[data-mandante-field="name"]'),
        rut: document.querySelector('[data-mandante-field="rut"]'),
        phone: document.querySelector('[data-mandante-field="phone"]'),
        email: document.querySelector('[data-mandante-field="email"]'),
    };
    const projectMandanteFillButton = document.querySelector('[data-mandante-fill]');
    const projectMandanteClearButton = document.querySelector('[data-mandante-clear]');

    const getProjectMandanteValues = () => {
        const option = projectClientSelect?.selectedOptions?.[0];
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

    const fillProjectMandanteFromClient = (override = false) => {
        const values = getProjectMandanteValues();
        if (!values) {
            return;
        }
        Object.entries(projectMandanteFields).forEach(([key, field]) => {
            if (field && (override || field.value.trim() === '')) {
                field.value = values[key];
            }
        });
    };

    projectClientSelect?.addEventListener('change', fillProjectMandanteFromClient);
    projectMandanteFillButton?.addEventListener('click', () => fillProjectMandanteFromClient(true));
    projectMandanteClearButton?.addEventListener('click', () => {
        Object.values(projectMandanteFields).forEach((field) => {
            if (field) {
                field.value = '';
            }
        });
    });

    if (projectClientSelect?.value) {
        fillProjectMandanteFromClient();
    }

</script>
