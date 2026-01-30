<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Registro de trabajadores</h4>
        <a href="index.php?route=hr/employees/create" class="btn btn-primary">Nuevo trabajador</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trabajador</th>
                        <th>RUT</th>
                        <th>Departamento</th>
                        <th>Cargo</th>
                        <th>Ingreso</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <?php
                        $status = $employee['status'] ?? 'activo';
                        $statusColor = match ($status) {
                            'activo' => 'success',
                            'inactivo' => 'secondary',
                            default => 'info',
                        };
                        ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($employee['id'] ?? null); ?></td>
                            <td>
                                <?php echo e(trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? ''))); ?>
                                <div class="text-muted fs-12"><?php echo e($employee['email'] ?? ''); ?></div>
                            </td>
                            <td><?php echo e($employee['rut'] ?? ''); ?></td>
                            <td><?php echo e($employee['department_name'] ?? ''); ?></td>
                            <td><?php echo e($employee['position_name'] ?? ''); ?></td>
                            <td><?php echo e(format_date($employee['hire_date'] ?? null)); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $statusColor; ?>-subtle text-<?php echo $statusColor; ?>">
                                    <?php echo e($status); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="index.php?route=hr/employees/edit&id=<?php echo (int)$employee['id']; ?>">Editar</a></li>
                                        <li><a class="dropdown-item" href="index.php?route=hr/employees/card&id=<?php echo (int)$employee['id']; ?>" target="_blank">Imprimir credencial</a></li>
                                        <li>
                                            <form method="post" action="index.php?route=hr/employees/delete" onsubmit="return confirm('¿Eliminar este trabajador?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$employee['id']; ?>">
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
