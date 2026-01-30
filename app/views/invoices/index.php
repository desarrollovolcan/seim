<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Facturas</h4>
        <div class="d-flex gap-2">
            <a href="index.php?route=invoices/export" class="btn btn-outline-secondary">Exportar CSV</a>
            <a href="index.php?route=invoices/create" class="btn btn-primary">Nueva factura</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Monto</th>
                        <th>Cliente</th>
                        <th>Emisión</th>
                        <th>Vencimiento</th>
                        <th>Estado pago</th>
                        <th>Estado vencimiento</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <?php
                        $dueDate = $invoice['fecha_vencimiento'] ?? '';
                        $dueBadgeClass = 'bg-secondary-subtle text-secondary';
                        $dueLabel = 'Sin fecha';
                        if ($dueDate !== '') {
                            $today = new DateTime('today');
                            $due = DateTime::createFromFormat('Y-m-d', $dueDate) ?: new DateTime($dueDate);
                            $diffDays = (int)$today->diff($due)->format('%r%a');
                            if ($diffDays < 0) {
                                $dueBadgeClass = 'bg-danger-subtle text-danger';
                                $dueLabel = 'Vencida hace ' . abs($diffDays) . ' días';
                            } elseif ($diffDays <= 10) {
                                $dueBadgeClass = 'bg-warning-subtle text-warning';
                                $dueLabel = 'Vence en ' . $diffDays . ' días';
                            } else {
                                $dueBadgeClass = 'bg-success-subtle text-success';
                                $dueLabel = 'Vence en ' . $diffDays . ' días';
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo e($invoice['numero']); ?></td>
                            <td><?php echo e(format_currency((float)($invoice['total'] ?? 0))); ?></td>
                            <td><?php echo e($invoice['client_name']); ?></td>
                            <td><?php echo e($invoice['fecha_emision']); ?></td>
                            <td><?php echo e($invoice['fecha_vencimiento']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $invoice['estado'] === 'pagada' ? 'success' : ($invoice['estado'] === 'vencida' ? 'danger' : 'warning'); ?>-subtle text-<?php echo $invoice['estado'] === 'pagada' ? 'success' : ($invoice['estado'] === 'vencida' ? 'danger' : 'warning'); ?>">
                                    <?php echo e($invoice['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $dueBadgeClass; ?>">
                                    <?php echo e($dueLabel); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <?php if (($invoice['estado'] ?? '') !== 'pagada'): ?>
                                            <li>
                                                <form method="post" action="index.php?route=invoices/flow-payment">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="invoice_id" value="<?php echo (int)$invoice['id']; ?>">
                                                    <button type="submit" class="dropdown-item dropdown-item-button">Crear pago Flow</button>
                                                </form>
                                            </li>
                                        <?php endif; ?>
                                        <li><a href="index.php?route=invoices/show&id=<?php echo $invoice['id']; ?>" class="dropdown-item">Ver</a></li>
                                        <li><a href="index.php?route=invoices/edit&id=<?php echo $invoice['id']; ?>" class="dropdown-item">Editar</a></li>
                                        <li><a href="index.php?route=invoices/details&id=<?php echo $invoice['id']; ?>" class="dropdown-item">Ver factura</a></li>
                                        <li>
                                            <form method="post" action="index.php?route=invoices/delete" onsubmit="return confirm('¿Eliminar esta factura?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo $invoice['id']; ?>">
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
