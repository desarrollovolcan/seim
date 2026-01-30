<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h4 class="card-title mb-0">Actividades SII</h4>
            <small class="text-muted">Catálogo oficial de actividades económicas.</small>
        </div>
        <div class="d-flex align-items-center gap-2 align-self-start align-self-md-center">
            <span class="badge bg-soft-primary text-primary">
                <?php echo count($activities); ?> registros
            </span>
            <a href="index.php?route=maintainers/sii-activities/create" class="btn btn-primary btn-sm">
                Agregar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width: 140px;">Código</th>
                        <th>Actividad</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activities)): ?>
                        <tr>
                            <td colspan="3" class="text-muted text-center">No hay actividades disponibles.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($activities as $activity): ?>
                            <tr>
                                <td><?php echo e($activity['code'] ?? ''); ?></td>
                                <td><?php echo e($activity['name'] ?? ''); ?></td>
                                <td class="text-end">
                                    <div class="dropdown actions-dropdown">
                                        <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="index.php?route=maintainers/sii-activities/edit&id=<?php echo $activity['id']; ?>" class="dropdown-item">Editar</a>
                                            </li>
                                            <li>
                                                <form method="post" action="index.php?route=maintainers/sii-activities/delete" onsubmit="return confirm('¿Eliminar esta actividad?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo (int)$activity['id']; ?>">
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
