<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Recepción de camiones vendedores</h4>
        <a href="index.php?route=sales/dispatches/create" class="btn btn-primary">Despachar camión</a>
    </div>
    <div class="card-body">
        <p class="text-muted mb-0">Selecciona un despacho abierto para registrar retorno de envases y entrega de dinero del vendedor.</p>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="card-title mb-0">Pendientes de recepción</h4>
    </div>
    <div class="card-body">
        <?php if (empty($openDispatches)): ?>
            <p class="text-muted mb-0">No hay camiones pendientes por recepcionar.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Camión</th>
                            <th>Vendedor</th>
                            <th>POS</th>
                            <th class="text-end">Cant. despachada</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($openDispatches as $dispatch): ?>
                            <tr>
                                <td><?php echo e($dispatch['dispatch_date']); ?></td>
                                <td><?php echo e($dispatch['truck_code']); ?></td>
                                <td><?php echo e($dispatch['seller_name']); ?></td>
                                <td><?php echo e($dispatch['session_code'] ?? '-'); ?></td>
                                <td class="text-end"><?php echo number_format((float)$dispatch['total_dispatched'], 0, ',', '.'); ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-success" href="index.php?route=sales/dispatches/show&id=<?php echo (int)$dispatch['id']; ?>">Recepcionar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Últimos despachos recepcionados</h4>
    </div>
    <div class="card-body">
        <?php if (empty($closedDispatches)): ?>
            <p class="text-muted mb-0">Aún no hay despachos recepcionados.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Camión</th>
                            <th>Vendedor</th>
                            <th class="text-end">Dinero entregado</th>
                            <th class="text-end">Merma</th>
                            <th class="text-end">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($closedDispatches as $dispatch): ?>
                            <tr>
                                <td><?php echo e($dispatch['dispatch_date']); ?></td>
                                <td><?php echo e($dispatch['truck_code']); ?></td>
                                <td><?php echo e($dispatch['seller_name']); ?></td>
                                <td class="text-end">$<?php echo number_format((float)$dispatch['cash_delivered'], 0, ',', '.'); ?></td>
                                <td class="text-end text-danger"><?php echo number_format((float)$dispatch['total_merma'], 0, ',', '.'); ?></td>
                                <td class="text-end"><a href="index.php?route=sales/dispatches/show&id=<?php echo (int)$dispatch['id']; ?>" class="btn btn-sm btn-soft-primary">Ver</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
