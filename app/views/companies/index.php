<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Empresas</h5>
            <a href="index.php?route=companies/create" class="btn btn-primary">Nueva empresa</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>RUT</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($companies ?? []) as $company): ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($company['id'] ?? null); ?></td>
                            <td><?php echo e($company['name'] ?? ''); ?></td>
                            <td><?php echo e($company['rut'] ?? ''); ?></td>
                            <td><?php echo e($company['email'] ?? ''); ?></td>
                            <td><?php echo e($company['phone'] ?? ''); ?></td>
                            <td><?php echo e($company['address'] ?? ''); ?></td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="index.php?route=companies/edit&id=<?php echo e((string)$company['id']); ?>" class="dropdown-item">Editar</a>
                                        </li>
                                        <li>
                                            <form method="post" action="index.php?route=companies/delete" onsubmit="return confirm('¿Eliminar esta empresa?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo e((string)$company['id']); ?>">
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
