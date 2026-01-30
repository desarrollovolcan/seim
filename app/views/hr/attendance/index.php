<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Control de asistencia</h4>
        <a href="index.php?route=hr/attendance/create" class="btn btn-primary">Registrar asistencia</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trabajador</th>
                        <th>Fecha</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Horas</th>
                        <th>Horas extra</th>
                        <th>Ausencia</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance as $record): ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($record['id'] ?? null); ?></td>
                            <td>
                                <?php echo e(trim(($record['first_name'] ?? '') . ' ' . ($record['last_name'] ?? ''))); ?>
                                <div class="text-muted fs-12"><?php echo e($record['rut'] ?? ''); ?></div>
                            </td>
                            <td><?php echo e(format_date($record['date'] ?? null)); ?></td>
                            <td><?php echo e($record['check_in'] ?? ''); ?></td>
                            <td><?php echo e($record['check_out'] ?? ''); ?></td>
                            <td><?php echo e($record['worked_hours'] ?? ''); ?></td>
                            <td><?php echo e($record['overtime_hours'] ?? ''); ?></td>
                            <td><?php echo e($record['absence_type'] ?? ''); ?></td>
                            <td class="text-end">
                                <form method="post" action="index.php?route=hr/attendance/delete" onsubmit="return confirm('¿Eliminar este registro?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int)$record['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-soft-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
