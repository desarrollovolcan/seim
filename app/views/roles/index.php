<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>
<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo e($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Roles de usuarios</h4>
        <a href="index.php?route=roles/create" class="btn btn-primary">Nuevo rol</a>
    </div>
    <div class="card-body">
        <?php if (!empty($roles)): ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Rol</th>
                            <th>Usuarios asignados</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                            <?php $roleId = (int)($role['id'] ?? 0); ?>
                            <tr>
                                <td class="text-muted"><?php echo render_id_badge($roleId); ?></td>
                                <td><?php echo e($role['name'] ?? ''); ?></td>
                                <td><?php echo (int)($countByRole[$roleId] ?? 0); ?></td>
                                <td class="text-end">
                                    <div class="dropdown actions-dropdown">
                                        <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="index.php?route=roles/edit&id=<?php echo $roleId; ?>" class="dropdown-item">Editar</a></li>
                                            <li>
                                                <form method="post" action="index.php?route=roles/delete" onsubmit="return confirm('¿Eliminar este rol?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo $roleId; ?>">
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
            <div class="text-muted">No hay roles registrados.</div>
        <?php endif; ?>
    </div>
</div>
