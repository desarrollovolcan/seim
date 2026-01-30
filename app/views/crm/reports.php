<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <h4 class="card-title mb-1">Resumen Comercial</h4>
                        <p class="text-muted mb-0">KPIs clave de facturación, pipeline y servicio.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="index.php?route=invoices" class="btn btn-primary">Ver facturación</a>
                        <a href="index.php?route=quotes" class="btn btn-outline-primary">Ver pipeline</a>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100">
                            <p class="text-muted mb-1">Facturación mensual</p>
                            <h3 class="mb-0"><?php echo e(format_currency((float)($billingTotal ?? 0))); ?></h3>
                            <span class="badge text-bg-success">Pagadas</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100">
                            <p class="text-muted mb-1">Pipeline activo</p>
                            <h3 class="mb-0"><?php echo e(format_currency((float)($pipelineTotal ?? 0))); ?></h3>
                            <span class="badge text-bg-info"><?php echo (int)($pipelineCount ?? 0); ?> oportunidades</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100">
                            <p class="text-muted mb-1">SLA de servicio</p>
                            <h3 class="mb-0"><?php echo (int)($slaPercent ?? 0); ?>%</h3>
                            <span class="badge text-bg-warning"><?php echo (int)($alertCount ?? 0); ?> alertas</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <h5 class="mb-3">Actividad prioritaria</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Oportunidad</th>
                                    <th>Responsable</th>
                                    <th>Estado</th>
                                    <th>Siguiente paso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($activities)): ?>
                                    <?php foreach ($activities as $activity): ?>
                                        <?php
                                        $status = $activity['estado'] ?? 'pendiente';
                                        $statusLabel = ucfirst($status);
                                        $badgeClass = $status === 'aceptada' ? 'success' : ($status === 'rechazada' ? 'danger' : 'warning');
                                        $nextStep = match ($status) {
                                            'aceptada' => 'Planificar entrega',
                                            'rechazada' => 'Revisar feedback',
                                            default => 'Seguimiento comercial',
                                        };
                                        ?>
                                        <tr>
                                            <td class="text-muted"><?php echo render_id_badge($activity['id'] ?? null); ?></td>
                                            <td><?php echo e($activity['client_name'] ?? '-'); ?></td>
                                            <td><?php echo e($activity['numero'] ?? 'Cotización'); ?></td>
                                            <td>Equipo comercial</td>
                                            <td><span class="badge text-bg-<?php echo $badgeClass; ?>"><?php echo e($statusLabel); ?></span></td>
                                            <td><?php echo e($nextStep); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-muted text-center">No hay oportunidades registradas en el rango seleccionado.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Filtros inteligentes</h5>
                <form method="get" action="index.php">
                    <input type="hidden" name="route" value="crm/reports">
                    <div class="mb-3">
                        <label class="form-label" for="crm-report-range">Rango de fechas</label>
                        <select class="form-select" id="crm-report-range" name="range">
                            <option value="30d" <?php echo ($filters['range'] ?? '') === '30d' ? 'selected' : ''; ?>>Últimos 30 días</option>
                            <option value="quarter" <?php echo ($filters['range'] ?? '') === 'quarter' ? 'selected' : ''; ?>>Trimestre actual</option>
                            <option value="year" <?php echo ($filters['range'] ?? '') === 'year' ? 'selected' : ''; ?>>Año en curso</option>
                            <option value="custom" <?php echo ($filters['range'] ?? '') === 'custom' ? 'selected' : ''; ?>>Personalizado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="crm-report-status">Estado pipeline</label>
                        <select class="form-select" id="crm-report-status" name="status">
                            <option value="all" <?php echo ($filters['status'] ?? '') === 'all' ? 'selected' : ''; ?>>Todos</option>
                            <option value="pendiente" <?php echo ($filters['status'] ?? '') === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="aceptada" <?php echo ($filters['status'] ?? '') === 'aceptada' ? 'selected' : ''; ?>>Aceptada</option>
                            <option value="rechazada" <?php echo ($filters['status'] ?? '') === 'rechazada' ? 'selected' : ''; ?>>Rechazada</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha inicio</label>
                        <input type="date" name="start" class="form-control" value="<?php echo e($filters['start'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha término</label>
                        <input type="date" name="end" class="form-control" value="<?php echo e($filters['end'] ?? ''); ?>">
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-secondary">Aplicar filtros</button>
                        <a href="index.php?route=crm/reports" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Accesos rápidos</h5>
                <div class="d-grid gap-2">
                    <a href="index.php?route=projects" class="btn btn-outline-primary">Proyectos</a>
                    <a href="index.php?route=notifications" class="btn btn-outline-info">Actividad</a>
                    <a href="index.php?route=tickets" class="btn btn-outline-warning">Service Desk</a>
                    <a href="index.php?route=invoices" class="btn btn-outline-success">Facturación</a>
                </div>
            </div>
        </div>
    </div>
</div>
