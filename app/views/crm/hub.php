<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <h4 class="card-title mb-1">Panel Comercial Unificado</h4>
                        <p class="text-muted mb-0">Conecta oportunidades, proyectos, servicios y facturación desde un solo lugar.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="index.php?route=projects/create" class="btn btn-primary">Nuevo Proyecto</a>
                        <a href="index.php?route=tickets/create" class="btn btn-info">Nuevo Ticket</a>
                        <a href="index.php?route=invoices/create" class="btn btn-success">Nueva Factura</a>
                        <a href="#crm-quick-project" class="btn btn-outline-primary">Registro rápido</a>
                        <a href="#crm-quick-service" class="btn btn-outline-warning">Solicitud servicio</a>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border">
                            <div class="card-body">
                                <h5 class="mb-2">Pipeline &amp; Oportunidades</h5>
                                <p class="text-muted">Convierte leads en acuerdos y mantiene la visibilidad del equipo.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="index.php?route=projects" class="btn btn-sm btn-primary">Ver proyectos</a>
                                    <a href="index.php?route=quotes" class="btn btn-sm btn-outline-primary">Cotizaciones</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border">
                            <div class="card-body">
                                <h5 class="mb-2">Clientes &amp; Cuentas</h5>
                                <p class="text-muted">Centraliza la información de clientes y su historial.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="index.php?route=clients" class="btn btn-sm btn-secondary">Listado</a>
                                    <a href="index.php?route=clients/create" class="btn btn-sm btn-outline-secondary">Nuevo cliente</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border">
                            <div class="card-body">
                                <h5 class="mb-2">Servicios &amp; Soporte</h5>
                                <p class="text-muted">Gestiona tickets, SLA y renovación de servicios.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="index.php?route=tickets" class="btn btn-sm btn-warning">Service Desk</a>
                                    <a href="index.php?route=services" class="btn btn-sm btn-outline-warning">Servicios</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border">
                            <div class="card-body">
                                <h5 class="mb-2">Facturación &amp; Cobranza</h5>
                                <p class="text-muted">Controla facturas emitidas, pendientes y vencidas.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="index.php?route=invoices" class="btn btn-sm btn-success">Facturas</a>
                                    <a href="index.php?route=invoices/create" class="btn btn-sm btn-outline-success">Emitir factura</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border">
                            <div class="card-body">
                                <h5 class="mb-2">Reportes &amp; Insights</h5>
                                <p class="text-muted">Mide desempeño comercial y productividad del equipo.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="index.php?route=crm/reports" class="btn btn-sm btn-info">Ver reportes</a>
                                    <a href="index.php?route=notifications" class="btn btn-sm btn-outline-info">Actividad</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border">
                            <div class="card-body">
                                <h5 class="mb-2">Usuarios &amp; Roles</h5>
                                <p class="text-muted">Administra equipos, permisos y accesos.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="index.php?route=users" class="btn btn-sm btn-danger">Usuarios</a>
                                    <a href="index.php?route=users/permissions" class="btn btn-sm btn-outline-danger">Permisos</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6" id="crm-quick-project">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">Registro rápido de proyecto</h5>
                <p class="text-muted mb-0">Estandariza el ingreso de proyectos ligados a clientes y servicios.</p>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="crm-project-name">Nombre del proyecto</label>
                            <input type="text" class="form-control" id="crm-project-name" placeholder="Ej: Renovación sitio web">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="crm-project-client">Cliente</label>
                            <select class="form-select" id="crm-project-client" data-client-select>
                                <option value="">Selecciona cliente</option>
                                <?php foreach ($clients as $client): ?>
                                    <?php $contactName = $client['contact'] ?: $client['name']; ?>
                                    <option value="<?php echo (int)$client['id']; ?>"
                                        data-contact-name="<?php echo e($contactName); ?>"
                                        data-contact-email="<?php echo e($client['email'] ?? ''); ?>"
                                        data-contact-phone="<?php echo e($client['phone'] ?? ''); ?>"
                                        data-address="<?php echo e($client['address'] ?? ''); ?>">
                                        <?php echo e($client['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="crm-project-contact">Contacto</label>
                            <input type="text" class="form-control" id="crm-project-contact" data-client-field="contact_name" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="crm-project-contact-email">Correo contacto</label>
                            <input type="email" class="form-control" id="crm-project-contact-email" data-client-field="contact_email" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="crm-project-stage">Estado comercial</label>
                            <select class="form-select" id="crm-project-stage">
                                <option selected>Selecciona estado</option>
                                <option>Descubrimiento</option>
                                <option>Propuesta enviada</option>
                                <option>Negociación</option>
                                <option>Ganada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="crm-project-start">Inicio</label>
                            <input type="date" class="form-control" id="crm-project-start">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="crm-project-end">Entrega estimada</label>
                            <input type="date" class="form-control" id="crm-project-end">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="crm-project-scope">Resumen de alcance</label>
                            <textarea class="form-control" id="crm-project-scope" placeholder="Describe entregables, tiempos y responsables"></textarea>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <button type="reset" class="btn btn-light w-100 w-sm-auto">Limpiar</button>
                        <button type="submit" class="btn btn-primary w-100 w-sm-auto">Guardar proyecto</button>
                    </div>
                    <?php
                    $reportTemplate = 'informeIcargaEspanol.php';
                    $reportSource = 'crm/hub/project';
                    include __DIR__ . '/../partials/report-download.php';
                    ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6" id="crm-quick-service">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">Solicitud de servicio</h5>
                <p class="text-muted mb-0">Captura solicitudes de soporte para clientes activos.</p>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="crm-service-client">Cliente</label>
                            <select class="form-select" id="crm-service-client" data-client-select>
                                <option value="">Selecciona cliente</option>
                                <?php foreach ($clients as $client): ?>
                                    <?php $contactName = $client['contact'] ?: $client['name']; ?>
                                    <option value="<?php echo (int)$client['id']; ?>"
                                        data-contact-name="<?php echo e($contactName); ?>"
                                        data-contact-email="<?php echo e($client['email'] ?? ''); ?>"
                                        data-contact-phone="<?php echo e($client['phone'] ?? ''); ?>">
                                        <?php echo e($client['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="crm-service-type">Tipo de servicio</label>
                            <select class="form-select" id="crm-service-type">
                                <option selected>Selecciona servicio</option>
                                <option>Implementación</option>
                                <option>Mantenimiento</option>
                                <option>Consultoría</option>
                                <option>Soporte urgente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="crm-service-contact">Contacto</label>
                            <input type="text" class="form-control" id="crm-service-contact" data-client-field="contact_name" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="crm-service-contact-email">Correo contacto</label>
                            <input type="email" class="form-control" id="crm-service-contact-email" data-client-field="contact_email" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="crm-service-priority">Prioridad</label>
                            <select class="form-select" id="crm-service-priority">
                                <option selected>Normal</option>
                                <option>Alta</option>
                                <option>Crítica</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="crm-service-owner">Responsable</label>
                            <input type="text" class="form-control" id="crm-service-owner" placeholder="Equipo o consultor">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="crm-service-notes">Detalle de solicitud</label>
                            <textarea class="form-control" id="crm-service-notes" placeholder="Describe el servicio solicitado"></textarea>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <button type="reset" class="btn btn-light w-100 w-sm-auto">Limpiar</button>
                        <button type="submit" class="btn btn-warning w-100 w-sm-auto">Crear ticket</button>
                    </div>
                    <?php
                    $reportTemplate = 'informeIcargaEspanol.php';
                    $reportSource = 'crm/hub/service';
                    include __DIR__ . '/../partials/report-download.php';
                    ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/pages/crm-modal-forms.js"></script>
