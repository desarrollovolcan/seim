<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Cotizaciones</h4>
        <a href="index.php?route=quotes/create" class="btn btn-primary">Nueva cotización</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Emisión</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotes as $quote): ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($quote['id'] ?? null); ?></td>
                            <td><?php echo e($quote['numero']); ?></td>
                            <td><?php echo e($quote['client_name']); ?></td>
                            <td><?php echo e($quote['fecha_emision']); ?></td>
                            <td>
                                <?php
                                $estado = (string)($quote['estado'] ?? 'creada');
                                $estadoClass = match ($estado) {
                                    'aprobada' => 'success',
                                    'rechazada' => 'danger',
                                    'enviada' => 'info',
                                    'en_curso' => 'primary',
                                    default => 'warning',
                                };
                                ?>
                                <span class="badge bg-<?php echo $estadoClass; ?>-subtle text-<?php echo $estadoClass; ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $estado))); ?>
                                </span>
                                <?php if (!empty($quote['is_closed'])): ?>
                                    <span class="badge bg-dark-subtle text-dark ms-1">Cerrada</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end"><?php echo e(format_currency((float)($quote['total'] ?? 0))); ?></td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a href="index.php?route=quotes/show&id=<?php echo $quote['id']; ?>" class="dropdown-item">Ver</a></li>
                                        <?php if (empty($quote['is_closed'])): ?>
                                            <li><a href="index.php?route=quotes/edit&id=<?php echo $quote['id']; ?>" class="dropdown-item">Editar</a></li>
                                        <?php else: ?>
                                            <li><span class="dropdown-item text-muted">Edición bloqueada (cerrada)</span></li>
                                        <?php endif; ?>
                                        <li><a href="index.php?route=quotes/print&id=<?php echo $quote['id']; ?>" class="dropdown-item" target="_blank">Imprimir</a></li>
                                        <li>
                                            <form method="post" action="index.php?route=quotes/send">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo $quote['id']; ?>">
                                                <button type="submit" class="dropdown-item dropdown-item-button">Enviar</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="post" action="index.php?route=quotes/delete" onsubmit="return confirm('¿Eliminar esta cotización?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo $quote['id']; ?>">
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
