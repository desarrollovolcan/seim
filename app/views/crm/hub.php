<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <h4 class="card-title mb-1">Panel Comercial Unificado</h4>
                        <p class="text-muted mb-0">Conecta oportunidades, cotizaciones, tickets y facturación desde un solo lugar.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="index.php?route=quotes/create" class="btn btn-primary">Nueva Cotización</a>
                        <a href="index.php?route=tickets/create" class="btn btn-info">Nuevo Ticket</a>
                        <a href="index.php?route=sales/create" class="btn btn-success">Nueva Venta</a>
                        <a href="index.php?route=invoices" class="btn btn-outline-secondary">Facturación</a>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border">
                            <div class="card-body">
                                <h5 class="mb-2">Pipeline &amp; Oportunidades</h5>
                                <p class="text-muted">Convierte leads en acuerdos y mantiene la visibilidad del equipo.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="index.php?route=quotes" class="btn btn-sm btn-primary">Cotizaciones</a>
                                    <a href="index.php?route=sales" class="btn btn-sm btn-outline-primary">Ventas</a>
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
                                <h5 class="mb-2">Soporte &amp; Service Desk</h5>
                                <p class="text-muted">Gestiona tickets de soporte, SLA y requerimientos técnicos.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="index.php?route=tickets" class="btn btn-sm btn-warning">Service Desk</a>
                                    <a href="index.php?route=tickets/create" class="btn btn-sm btn-outline-warning">Nuevo Ticket</a>
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
    <div class="col-lg-6" id="crm-quick-quote">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">Acceso a Nueva Cotización</h5>
                <p class="text-muted mb-0">Crea propuestas comerciales detalladas para tus clientes.</p>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <p class="text-secondary fs-13">El módulo de cotizaciones te permite armar propuestas personalizadas con productos del catálogo, cálculo automático de IVA y control de estados (Borrador, Enviada, Aprobada, Rechazada).</p>
                <div class="pt-3 border-top d-flex gap-2">
                    <a href="index.php?route=quotes/create" class="btn btn-primary"><i class="ti ti-plus"></i> Crear Cotización</a>
                    <a href="index.php?route=quotes" class="btn btn-outline-secondary">Ver Cotizaciones</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6" id="crm-quick-ticket">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">Service Desk / Tickets</h5>
                <p class="text-muted mb-0">Captura y resuelve incidencias o solicitudes de clientes activos.</p>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <p class="text-secondary fs-13">Centraliza la atención al cliente con seguimiento de prioridades, responsables asignados, tiempos de resolución y estados en tiempo real.</p>
                <div class="pt-3 border-top d-flex gap-2">
                    <a href="index.php?route=tickets/create" class="btn btn-warning"><i class="ti ti-plus"></i> Crear Nuevo Ticket</a>
                    <a href="index.php?route=tickets" class="btn btn-outline-secondary">Ir al Service Desk</a>
                </div>
            </div>
        </div>
    </div>
</div>
