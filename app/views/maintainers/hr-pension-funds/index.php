<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">AFP</h4>
        <a href="index.php?route=maintainers/hr-pension-funds/create" class="btn btn-primary">Nueva AFP</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($funds as $fund): ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($fund['id'] ?? null); ?></td>
                            <td><?php echo e($fund['name'] ?? ''); ?></td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="index.php?route=maintainers/hr-pension-funds/edit&id=<?php echo (int)$fund['id']; ?>">Editar</a></li>
                                        <li>
                                            <form method="post" action="index.php?route=maintainers/hr-pension-funds/delete" onsubmit="return confirm('¿Eliminar esta AFP?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$fund['id']; ?>">
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
