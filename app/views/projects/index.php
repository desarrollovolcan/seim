<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Proyectos</h4>
        <a href="index.php?route=projects/create" class="btn btn-primary">Nuevo proyecto</a>
    </div>
    <div class="card-body">
        <form class="row g-2 align-items-end flex-wrap mb-3" method="get" action="index.php">
            <input type="hidden" name="route" value="projects">
            <div class="col-xl-3 col-lg-4 col-md-6">
                <label class="form-label mb-1">Cliente</label>
                <select name="client_id" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client['id']; ?>" <?php echo (int)($filters['client_id'] ?? 0) === (int)$client['id'] ? 'selected' : ''; ?>>
                            <?php echo e($client['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6">
                <label class="form-label mb-1">Estado</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="cotizado" <?php echo ($filters['status'] ?? '') === 'cotizado' ? 'selected' : ''; ?>>Cotizado</option>
                    <option value="en_curso" <?php echo ($filters['status'] ?? '') === 'en_curso' ? 'selected' : ''; ?>>En curso</option>
                    <option value="en_pausa" <?php echo ($filters['status'] ?? '') === 'en_pausa' ? 'selected' : ''; ?>>En pausa</option>
                    <option value="finalizado" <?php echo ($filters['status'] ?? '') === 'finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                </select>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6">
                <label class="form-label mb-1">Mandante</label>
                <input type="text" name="mandante" class="form-control" value="<?php echo e($filters['mandante'] ?? ''); ?>" placeholder="Nombre del mandante">
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6">
                <label class="form-label mb-1">Proyecto</label>
                <input type="text" name="name" class="form-control" value="<?php echo e($filters['name'] ?? ''); ?>" placeholder="Nombre del proyecto">
            </div>
            <div class="col-auto d-flex gap-2 align-items-end flex-wrap">
                <a href="index.php?route=projects" class="btn btn-light text-nowrap">Limpiar</a>
                <button type="submit" class="btn btn-primary text-nowrap">Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Tareas</th>
                        <th>Entrega</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <?php
                        $projectId = $project['id'] ?? null;
                        $projectClientId = $project['client_id'] ?? null;
                        $projectName = $project['name'] ?? '';
                        $projectStatus = $project['status'] ?? '';
                        $projectStatusColor = match ($projectStatus) {
                            'cotizado' => 'info',
                            'en_curso' => 'primary',
                            'en_pausa' => 'warning',
                            'finalizado' => 'success',
                            default => 'secondary',
                        };
                        $projectStatusLabel = $projectStatus !== '' ? ucwords(str_replace('_', ' ', $projectStatus)) : '-';
                        $projectDeliveryDate = $project['delivery_date'] ?? '';
                        $projectClientName = $project['client_name'] ?? '-';
                        $projectTasks = $tasksByProject[$projectId] ?? [];
                        ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($projectId); ?></td>
                            <td><?php echo e($projectName); ?></td>
                            <td><?php echo e($projectClientName); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $projectStatusColor; ?>-subtle text-<?php echo $projectStatusColor; ?>">
                                    <?php echo e($projectStatusLabel); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1 tasks-toggle-btn" type="button" data-task-target="project-tasks-<?php echo (int)$projectId; ?>" aria-expanded="false">
                                    <i class="ti ti-square-rounded-plus-filled align-middle tasks-toggle-icon"></i>
                                    <span class="badge text-bg-light border"><?php echo count($projectTasks); ?></span>
                                </button>
                            </td>
                            <td><?php echo e($projectDeliveryDate); ?></td>
                            <td class="text-end">
                                <?php if ($projectId !== null): ?>
                                    <div class="dropdown actions-dropdown">
                                        <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="index.php?route=projects/show&id=<?php echo (int)$projectId; ?>" class="dropdown-item">Ver</a></li>
                                            <li><a href="index.php?route=projects/show&id=<?php echo (int)$projectId; ?>#tareas" class="dropdown-item">Ver tareas</a></li>
                                            <?php if ($projectClientId !== null): ?>
                                                <li><a href="index.php?route=invoices/create&project_id=<?php echo (int)$projectId; ?>&client_id=<?php echo (int)$projectClientId; ?>" class="dropdown-item">Crear factura</a></li>
                                            <?php endif; ?>
                                            <li><a href="index.php?route=projects/edit&id=<?php echo (int)$projectId; ?>" class="dropdown-item">Editar</a></li>
                                            <li>
                                                <form method="post" action="index.php?route=projects/delete" onsubmit="return confirm('¿Eliminar este proyecto?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo (int)$projectId; ?>">
                                                    <button type="submit" class="dropdown-item dropdown-item-button text-danger">Eliminar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if ($projectId !== null): ?>
                            <tr id="project-tasks-<?php echo (int)$projectId; ?>" class="project-task-row d-none">
                                <td colspan="7" class="bg-light">
                                    <?php if (!empty($projectTasks)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0 align-middle">
                                                <thead>
                                                    <tr>
                                                        <th class="text-muted small">Tarea</th>
                                                        <th class="text-muted small">Fecha inicio</th>
                                                        <th class="text-muted small">Fecha fin</th>
                                                        <th class="text-muted small text-end">% avance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($projectTasks as $task): ?>
                                                        <tr>
                                                            <td><?php echo e($task['title']); ?></td>
                                                            <td><?php echo e($task['start_date'] ?? '-'); ?></td>
                                                            <td><?php echo e($task['end_date'] ?? '-'); ?></td>
                                                            <td class="text-end">
                                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                                    <div class="progress flex-grow-1" style="max-width: 200px; height: 8px;">
                                                                        <div class="progress-bar" role="progressbar" style="width: <?php echo (int)$task['progress_percent']; ?>%;" aria-valuenow="<?php echo (int)$task['progress_percent']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                    <span class="text-muted small"><?php echo (int)$task['progress_percent']; ?>%</span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted small">No hay tareas registradas para este proyecto.</div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tasks-toggle-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            var targetId = button.getAttribute('data-task-target');
            var targetRow = document.getElementById(targetId);
            if (!targetRow) {
                return;
            }
            var isHidden = targetRow.classList.contains('d-none');
            targetRow.classList.toggle('d-none');
            button.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            var icon = button.querySelector('.tasks-toggle-icon');
            if (icon) {
                icon.classList.toggle('ti-square-rounded-plus-filled', !isHidden);
                icon.classList.toggle('ti-square-rounded-minus-filled', isHidden);
            }
        });
    });
});
</script>
