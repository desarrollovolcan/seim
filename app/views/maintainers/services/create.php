<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Nuevo servicio</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/services/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo de servicio</label>
                    <select name="service_type_id" class="form-select" required>
                        <option value="">Selecciona un tipo</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?php echo $type['id']; ?>"><?php echo e($type['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Costo</label>
                    <input type="number" step="0.01" name="cost" class="form-control" value="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Moneda</label>
                    <select name="currency" class="form-select">
                        <option value="CLP">CLP</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=maintainers/services" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'maintainers/services/create';
    include __DIR__ . '/../../partials/report-download.php';
    ?>
</form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Servicios existentes</h4>
    </div>
    <div class="card-body">
        <?php if (empty($services)): ?>
            <p class="text-muted mb-0">No hay servicios registrados.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th class="text-end">Costo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td><?php echo e($service['name']); ?></td>
                                <td><?php echo e($service['type_name']); ?></td>
                                <td class="text-end"><?php echo e(format_currency((float)($service['cost'] ?? 0))); ?></td>
                                <td class="text-end">
                                    <div class="dropdown actions-dropdown">
                                        <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="index.php?route=maintainers/services/edit&id=<?php echo $service['id']; ?>" class="dropdown-item">Editar</a></li>
                                            <li>
                                                <form method="post" action="index.php?route=maintainers/services/delete" onsubmit="return confirm('¿Eliminar este servicio?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                                    <button type="submit" class="dropdown-item dropdown-item-button text-danger">Eliminar</button>
                                                
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'maintainers/services/create';
    include __DIR__ . '/../../partials/report-download.php';
    ?>
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
        <?php endif; ?>
    </div>
</div>
