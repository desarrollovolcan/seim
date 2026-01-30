<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Contratos laborales</h4>
        <div class="d-flex gap-2">
            <a href="index.php?route=hr/contracts/bulk" class="btn btn-outline-primary">Contratos masivos</a>
            <a href="index.php?route=hr/contracts/create" class="btn btn-primary">Nuevo contrato</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trabajador</th>
                        <th>Tipo</th>
                        <th>Inicio</th>
                        <th>Término</th>
                        <th class="text-end">Sueldo base</th>
                        <th>Jornada</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contracts as $contract): ?>
                        <?php
                        $status = $contract['status'] ?? 'vigente';
                        $statusColor = match ($status) {
                            'vigente' => 'success',
                            'terminado' => 'secondary',
                            'suspendido' => 'warning',
                            default => 'info',
                        };
                        ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($contract['id'] ?? null); ?></td>
                            <td>
                                <?php echo e(trim(($contract['first_name'] ?? '') . ' ' . ($contract['last_name'] ?? ''))); ?>
                                <div class="text-muted fs-12"><?php echo e($contract['rut'] ?? ''); ?></div>
                            </td>
                            <td>
                                <?php echo e($contract['contract_type_name'] ?? ''); ?>
                                <div class="text-muted fs-12"><?php echo e($contract['department_name'] ?? ''); ?></div>
                            </td>
                            <td><?php echo e(format_date($contract['start_date'] ?? null)); ?></td>
                            <td><?php echo e(format_date($contract['end_date'] ?? null)); ?></td>
                            <td class="text-end"><?php echo e(format_currency((float)($contract['salary'] ?? 0), 0)); ?></td>
                            <td>
                                <?php echo e($contract['schedule_name'] ?? ''); ?>
                                <div class="text-muted fs-12"><?php echo (int)($contract['weekly_hours'] ?? 0); ?> hrs</div>
                            </td>
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
                                        <li><a class="dropdown-item" href="index.php?route=hr/contracts/edit&id=<?php echo (int)$contract['id']; ?>">Editar</a></li>
                                        <li>
                                            <form method="post" action="index.php?route=hr/contracts/delete" onsubmit="return confirm('¿Eliminar este contrato?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$contract['id']; ?>">
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
