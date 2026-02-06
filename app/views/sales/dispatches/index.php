<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Despachos de camiones</h4>
        <a href="index.php?route=sales/dispatches/create" class="btn btn-primary">Nuevo despacho</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Camión</th>
                        <th>Vendedor</th>
                        <th>POS</th>
                        <th class="text-end">Cant. despachada</th>
                        <th class="text-end">Envases retorno</th>
                        <th class="text-end">Merma</th>
                        <th class="text-end">Dinero entregado</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dispatches as $dispatch): ?>
                        <tr>
                            <td><?php echo e($dispatch['dispatch_date']); ?></td>
                            <td><?php echo e($dispatch['truck_code']); ?></td>
                            <td><?php echo e($dispatch['seller_name']); ?></td>
                            <td><?php echo e($dispatch['session_code'] ?? '-'); ?></td>
                            <td class="text-end"><?php echo number_format((float)$dispatch['total_dispatched'], 0, ',', '.'); ?></td>
                            <td class="text-end"><?php echo number_format((float)$dispatch['total_empty_returned'], 0, ',', '.'); ?></td>
                            <td class="text-end text-danger"><?php echo number_format((float)$dispatch['total_merma'], 0, ',', '.'); ?></td>
                            <td class="text-end">$<?php echo number_format((float)$dispatch['cash_delivered'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="badge <?php echo $dispatch['status'] === 'cerrado' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'; ?>">
                                    <?php echo e(ucfirst($dispatch['status'])); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="index.php?route=sales/dispatches/show&id=<?php echo (int)$dispatch['id']; ?>" class="btn btn-sm btn-soft-primary">Ver</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
