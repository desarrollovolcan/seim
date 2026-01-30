<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Activos fijos</h4>
    <a href="index.php?route=fixed-assets/create" class="btn btn-primary">Nuevo activo</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Activo</th>
                        <th>Categoría</th>
                        <th>Fecha</th>
                        <th>Valor</th>
                        <th>Depreciación acumulada</th>
                        <th>Valor libro</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assets as $asset): ?>
                        <tr>
                            <td><?php echo e($asset['name']); ?></td>
                            <td><?php echo e($asset['category']); ?></td>
                            <td><?php echo e(format_date($asset['acquisition_date'] ?? null)); ?></td>
                            <td><?php echo e(format_currency((float)($asset['acquisition_value'] ?? 0))); ?></td>
                            <td><?php echo e(format_currency((float)($asset['accumulated_depreciation'] ?? 0))); ?></td>
                            <td><?php echo e(format_currency((float)($asset['book_value'] ?? 0))); ?></td>
                            <td class="text-capitalize"><?php echo e($asset['status'] ?? 'activo'); ?></td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="index.php?route=fixed-assets/show&id=<?php echo (int)$asset['id']; ?>">Ver detalle</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="index.php?route=fixed-assets/edit&id=<?php echo (int)$asset['id']; ?>">Editar</a>
                                        </li>
                                        <li>
                                            <form method="post" action="index.php?route=fixed-assets/delete" onsubmit="return confirm('¿Eliminar este activo fijo?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$asset['id']; ?>">
                                                <button type="submit" class="dropdown-item dropdown-item-button text-danger">Eliminar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($assets)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay activos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
