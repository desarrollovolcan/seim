<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Jornadas laborales</h4>
        <a href="index.php?route=maintainers/hr-work-schedules/create" class="btn btn-primary">Nueva jornada</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jornada</th>
                        <th>Horas semanales</th>
                        <th>Horario</th>
                        <th>Colación (min)</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($schedule['id'] ?? null); ?></td>
                            <td><?php echo e($schedule['name'] ?? ''); ?></td>
                            <td><?php echo e($schedule['weekly_hours'] ?? ''); ?></td>
                            <td><?php echo e($schedule['start_time'] ?? ''); ?> - <?php echo e($schedule['end_time'] ?? ''); ?></td>
                            <td><?php echo e($schedule['lunch_break_minutes'] ?? ''); ?></td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="index.php?route=maintainers/hr-work-schedules/edit&id=<?php echo (int)$schedule['id']; ?>">Editar</a></li>
                                        <li>
                                            <form method="post" action="index.php?route=maintainers/hr-work-schedules/delete" onsubmit="return confirm('¿Eliminar esta jornada?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$schedule['id']; ?>">
                                                <button type="submit" class="dropdown-item dropdown-item-button text-danger">Eliminar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
