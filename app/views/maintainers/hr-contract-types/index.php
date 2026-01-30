<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Tipos de contrato</h4>
        <a href="index.php?route=maintainers/hr-contract-types/create" class="btn btn-primary">Nuevo tipo</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Duración máxima (meses)</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contractTypes as $type): ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($type['id'] ?? null); ?></td>
                            <td><?php echo e($type['name'] ?? ''); ?></td>
                            <td><?php echo e($type['description'] ?? ''); ?></td>
                            <td><?php echo e($type['max_duration_months'] ?? ''); ?></td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="index.php?route=maintainers/hr-contract-types/edit&id=<?php echo (int)$type['id']; ?>">Editar</a></li>
                                        <li>
                                            <form method="post" action="index.php?route=maintainers/hr-contract-types/delete" onsubmit="return confirm('¿Eliminar este tipo de contrato?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$type['id']; ?>">
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
