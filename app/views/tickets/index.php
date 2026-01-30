<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>
<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo e($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Tickets de soporte</h4>
        <a href="index.php?route=tickets/create" class="btn btn-primary btn-sm">Nuevo ticket</a>
    </div>
    <div class="card-body">
        <?php if (!empty($tickets)): ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th>Prioridad</th>
                            <th>Actualizado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?php echo (int)$ticket['id']; ?></td>
                                <td><?php echo e($ticket['client_name'] ?? ''); ?></td>
                                <td><?php echo e($ticket['subject'] ?? ''); ?></td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <?php echo e(str_replace('_', ' ', $ticket['status'] ?? 'abierto')); ?>
                                    </span>
                                </td>
                                <td><?php echo e(ucfirst($ticket['priority'] ?? 'media')); ?></td>
                                <td><?php echo e($ticket['updated_at'] ?? ''); ?></td>
                                <td class="text-end">
                                    <div class="dropdown actions-dropdown">
                                        <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="index.php?route=tickets/show&id=<?php echo (int)$ticket['id']; ?>" class="dropdown-item">Ver</a>
                                            </li>
                                            <li>
                                                <form method="post" action="index.php?route=tickets/delete" onsubmit="return confirm('¿Eliminar este ticket?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo (int)$ticket['id']; ?>">
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
        <?php else: ?>
            <div class="text-muted">No hay tickets registrados.</div>
        <?php endif; ?>
    </div>
</div>
