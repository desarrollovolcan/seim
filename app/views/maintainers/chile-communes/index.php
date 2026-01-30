<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h4 class="card-title mb-0">Comunas</h4>
            <small class="text-muted">Listado oficial de comunas asociadas a sus ciudades y regiones.</small>
        </div>
        <div class="d-flex align-items-center gap-2 align-self-start align-self-md-center">
            <span class="badge bg-soft-primary text-primary">
                <?php echo count($communes); ?> registros
            </span>
            <a href="index.php?route=maintainers/chile-communes/create" class="btn btn-primary btn-sm">
                Agregar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Comuna</th>
                        <th>Ciudad</th>
                        <th>Región</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($communes)): ?>
                        <tr>
                            <td colspan="4" class="text-muted text-center">No hay comunas disponibles.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($communes as $commune): ?>
                            <tr>
                                <td><?php echo e($commune['commune'] ?? ''); ?></td>
                                <td><?php echo e($commune['city'] ?? ''); ?></td>
                                <td><?php echo e($commune['region'] ?? ''); ?></td>
                                <td class="text-end">
                                    <div class="dropdown actions-dropdown">
                                        <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="index.php?route=maintainers/chile-communes/edit&id=<?php echo $commune['id']; ?>" class="dropdown-item">Editar</a>
                                            </li>
                                            <li>
                                                <form method="post" action="index.php?route=maintainers/chile-communes/delete" onsubmit="return confirm('¿Eliminar esta comuna?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo (int)$commune['id']; ?>">
                                                    <button type="submit" class="dropdown-item dropdown-item-button text-danger">Eliminar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
