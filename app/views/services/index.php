<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Servicios recurrentes</h4>
        <a href="index.php?route=services/create" class="btn btn-primary">Nuevo servicio</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Servicio</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td class="text-muted">#<?php echo (int)$service['id']; ?></td>
                            <td><?php echo e($service['name']); ?></td>
                            <td><?php echo e($service['client_name']); ?></td>
                            <td><?php echo e($service['service_type']); ?></td>
                            <td><?php echo e(format_date($service['due_date'])); ?></td>
                            <td>
                                <?php
                                $status = $service['status'] ?? 'activo';
                                $statusColor = match ($status) {
                                    'activo' => 'success',
                                    'vencido' => 'danger',
                                    'renovado' => 'primary',
                                    default => 'secondary',
                                };
                                ?>
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
                                        <li><a href="index.php?route=services/show&id=<?php echo $service['id']; ?>" class="dropdown-item">Ver</a></li>
                                        <li><a href="index.php?route=services/edit&id=<?php echo $service['id']; ?>" class="dropdown-item">Editar</a></li>
                                        <li>
                                            <a href="index.php?route=invoices/create&service_id=<?php echo $service['id']; ?>&client_id=<?php echo (int)$service['client_id']; ?>" class="dropdown-item">Crear factura</a>
                                        </li>
                                        <li>
                                            <form method="post" action="index.php?route=services/generate-invoice">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                                <button type="submit" class="dropdown-item dropdown-item-button">Facturar</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="post" action="index.php?route=services/delete" onsubmit="return confirm('¿Eliminar este servicio?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
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
