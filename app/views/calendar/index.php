<?php
$documents = $documents ?? [];
$eventTypes = $eventTypes ?? [];
$users = $users ?? [];
$documentCount = count($documents);
$eventTypeCount = count($eventTypes);
$userCount = count($users);
$documentHelp = $documentCount > 0
    ? 'Adjunta documentos del módulo Documentos para preparar cada reunión o recordatorio.'
    : 'Aún no hay documentos disponibles. Súbelos desde el módulo de Documentos.';
$attendeeHelp = $userCount > 0
    ? 'Selecciona participantes internos para compartir el evento.'
    : 'No hay usuarios disponibles en tu empresa.';
$eventTypeHelp = $eventTypeCount > 0
    ? 'Arrastra un tipo de evento al calendario o haz clic en la fecha.'
    : 'Crea un tipo para comenzar a agendar eventos.';
$typeClasses = [
    'bg-primary-subtle text-primary' => 'Primario',
    'bg-secondary-subtle text-secondary' => 'Secundario',
    'bg-success-subtle text-success' => 'Éxito',
    'bg-info-subtle text-info' => 'Info',
    'bg-warning-subtle text-warning' => 'Advertencia',
    'bg-danger-subtle text-danger' => 'Urgente',
    'bg-dark-subtle text-dark' => 'Oscuro',
];
?>

<div class="d-flex mb-3 gap-1">
    <div class="card h-100 mb-0 d-none d-lg-flex rounded-end-0">
        <div class="card-body">
            <button class="btn btn-primary w-100 btn-new-event">
                <i class="ti ti-plus me-2 align-middle"></i>
                Crear evento
            </button>

            <div id="external-events">
                <div class="d-flex align-items-center justify-content-between mb-2 mt-2">
                    <span class="text-uppercase text-muted fs-xxs fw-semibold">Tipos de evento</span>
                    <button class="btn btn-sm btn-outline-primary" type="button" id="btn-add-type">
                        <i class="ti ti-plus me-1"></i>Nuevo
                    </button>
                </div>
                <p class="text-muted fst-italic fs-xs mb-3" id="event-types-help"><?php echo e($eventTypeHelp); ?></p>
                <div id="event-types-list"></div>
                <div class="border rounded-2 p-2 bg-light text-muted fs-xs" id="event-types-empty">
                    No hay tipos de evento. Crea uno para habilitar el calendario.
                </div>
            </div>

        </div>
    </div> <!-- end card-->

    <div class="card h-100 mb-0 rounded-start-0 flex-grow-1 border-start-0">
        <div class="d-lg-none d-inline-flex card-header">
            <button class="btn btn-primary btn-new-event">
                <i class="ti ti-plus me-2 align-middle"></i>
                Crear evento
            </button>
        </div>

        <div class="card-body" style="height: calc(100% - 350px);" data-simplebar data-simplebar-md>
            <div id="calendar"></div>
        </div> <!-- end card-body -->
    </div> <!-- end card-->
</div> <!-- end row-->

<!-- Modal Add/Edit -->
<div class="modal fade" id="event-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form class="needs-validation" name="event-form" id="forms-event" novalidate>
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">
                        Crear evento
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="event-id">

                    <div class="row g-2">
                        <div class="col-12">
                            <div class="mb-2">
                                <label class="control-label form-label" for="event-title">Título</label>
                                <input class="form-control" placeholder="Ingresa un título" type="text" name="title" id="event-title" required>
                                <div class="invalid-feedback">Ingresa un título válido.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="control-label form-label" for="event-type">Tipo de evento</label>
                                <select class="form-select" name="type" id="event-type" required>
                                    <?php if ($eventTypeCount === 0): ?>
                                        <option value="">No hay tipos disponibles</option>
                                    <?php else: ?>
                                        <?php foreach ($eventTypes as $type): ?>
                                            <option value="<?php echo e((string)$type['id']); ?>">
                                                <?php echo e((string)$type['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">Selecciona un tipo de evento.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="control-label form-label" for="event-category">Color</label>
                                <select class="form-select" name="category" id="event-category" required disabled>
                                    <?php foreach ($typeClasses as $className => $label): ?>
                                        <option value="<?php echo e($className); ?>">
                                            <?php echo e($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Selecciona un color válido.</div>
                                <small class="text-muted fs-xxs">El color se define desde el tipo de evento.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="control-label form-label" for="event-start">Inicio</label>
                                <input class="form-control" type="datetime-local" name="start" id="event-start" required>
                                <div class="invalid-feedback">Ingresa una fecha de inicio.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="control-label form-label" for="event-end">Término</label>
                                <input class="form-control" type="datetime-local" name="end" id="event-end">
                                <div class="invalid-feedback">La fecha de término no puede ser anterior al inicio.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="control-label form-label" for="event-location">Lugar o enlace</label>
                                <input class="form-control" type="text" name="location" id="event-location" placeholder="Sala, Zoom, enlace Meet">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="control-label form-label" for="event-reminder">Recordatorio</label>
                                <select class="form-select" name="reminder" id="event-reminder">
                                    <option value="">Sin recordatorio</option>
                                    <option value="5">5 minutos antes</option>
                                    <option value="10">10 minutos antes</option>
                                    <option value="30">30 minutos antes</option>
                                    <option value="60">1 hora antes</option>
                                    <option value="1440">1 día antes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="event-all-day" name="all_day">
                                <label class="form-check-label" for="event-all-day">Todo el día</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <label class="control-label form-label" for="event-description">Descripción</label>
                                <textarea class="form-control" name="description" id="event-description" rows="3" placeholder="Agrega notas, agenda o detalles"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <label class="control-label form-label">Documentos adjuntos</label>
                                <p class="text-muted fs-xs mb-2"><?php echo e($documentHelp); ?></p>
                                <?php if ($documentCount === 0): ?>
                                    <a class="btn btn-sm btn-outline-primary" href="index.php?route=documents">Ir a Documentos</a>
                                <?php else: ?>
                                    <div class="border rounded-2 p-2 mb-2" style="max-height: 180px; overflow-y: auto;">
                                        <?php foreach ($documents as $document): ?>
                                            <?php $docId = (int)$document['id']; ?>
                                            <div class="form-check mb-1">
                                                <input class="form-check-input calendar-document-checkbox" type="checkbox" value="<?php echo e((string)$docId); ?>" id="calendar-doc-<?php echo e((string)$docId); ?>" data-document-name="<?php echo e((string)$document['name']); ?>" data-document-url="<?php echo e((string)$document['download_url']); ?>">
                                                <label class="form-check-label" for="calendar-doc-<?php echo e((string)$docId); ?>">
                                                    <?php echo e((string)$document['name']); ?>
                                                    <span class="text-muted fs-xxs">· <?php echo e((string)$document['extension']); ?></span>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="border rounded-2 p-2 bg-light" id="event-documents-preview">
                                    <span class="text-muted fs-xs">Sin documentos adjuntos.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <label class="control-label form-label" for="event-attendees">Invitados internos</label>
                                <p class="text-muted fs-xs mb-2"><?php echo e($attendeeHelp); ?></p>
                                <?php if ($userCount === 0): ?>
                                    <span class="text-muted fs-xs">No hay usuarios activos para asignar.</span>
                                <?php else: ?>
                                    <select class="form-select" name="attendees[]" id="event-attendees" multiple size="5">
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?php echo e((string)$user['id']); ?>" data-user-name="<?php echo e((string)($user['name'] ?? '')); ?>">
                                                <?php echo e((string)($user['name'] ?? '')); ?>
                                                <?php if (!empty($user['email'])): ?>
                                                    (<?php echo e((string)$user['email']); ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                                <div class="border rounded-2 p-2 bg-light mt-2" id="event-attendees-preview">
                                    <span class="text-muted fs-xs">Sin invitados asignados.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                        <button type="button" class="btn btn-danger" id="btn-delete-event">
                            Eliminar
                        </button>

                        <button type="button" class="btn btn-light ms-auto" data-bs-dismiss="modal">
                            Cerrar
                        </button>

                        <button type="submit" class="btn btn-primary" id="btn-save-event">
                            Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <!-- end modal-content-->
    </div>
    <!-- end modal dialog-->
</div>
<!-- end modal-->

<!-- Modal Event Types -->
<div class="modal fade" id="event-type-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form class="needs-validation" name="event-type-form" id="event-type-form" novalidate>
                <div class="modal-header">
                    <h4 class="modal-title" id="event-type-modal-title">
                        Nuevo tipo de evento
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="event-type-id">
                    <div class="mb-2">
                        <label class="control-label form-label" for="event-type-name">Nombre</label>
                        <input class="form-control" placeholder="Ej: Reunión con cliente" type="text" name="name" id="event-type-name" required>
                        <div class="invalid-feedback">Ingresa un nombre válido.</div>
                    </div>
                    <div class="mb-2">
                        <label class="control-label form-label" for="event-type-class">Color</label>
                        <select class="form-select" name="class_name" id="event-type-class" required>
                            <?php foreach ($typeClasses as $className => $label): ?>
                                <option value="<?php echo e($className); ?>">
                                    <?php echo e($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Selecciona un color válido.</div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3">
                        <button type="button" class="btn btn-danger" id="btn-delete-type">
                            Eliminar
                        </button>
                        <button type="button" class="btn btn-light ms-auto" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                        <button type="submit" class="btn btn-primary" id="btn-save-type">
                            Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end modal-->

<script>
    window.calendarConfig = {
        eventsUrl: 'index.php?route=calendar/events',
        storeUrl: 'index.php?route=calendar/store',
        deleteUrl: 'index.php?route=calendar/delete',
        typesUrl: 'index.php?route=calendar/types',
        storeTypeUrl: 'index.php?route=calendar/types/store',
        updateTypeUrl: 'index.php?route=calendar/types/update',
        deleteTypeUrl: 'index.php?route=calendar/types/delete',
        eventTypes: <?php echo json_encode($eventTypes, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>,
        typeClasses: <?php echo json_encode(array_keys($typeClasses), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>,
        csrfToken: '<?php echo csrf_token(); ?>'
    };
</script>

<!-- Fullcalendar js -->
<script src="assets/plugins/fullcalendar/index.global.min.js"></script>

<!-- Calendar App js -->
<script src="assets/js/pages/apps-calendar.js"></script>
