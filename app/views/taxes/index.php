<div class="row">
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title mb-0">Nuevo período tributario</h4>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=taxes/periods/store">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Período</label>
                        <input type="text" name="period" class="form-control" placeholder="YYYY-MM" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">IVA débito</label>
                            <input type="number" step="0.01" name="iva_debito" class="form-control" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">IVA crédito</label>
                            <input type="number" step="0.01" name="iva_credito" class="form-control" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Remanente</label>
                            <input type="number" step="0.01" name="remanente" class="form-control" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Impuesto único</label>
                            <input type="number" step="0.01" name="impuesto_unico" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="pendiente">Pendiente</option>
                            <option value="declarado">Declarado</option>
                            <option value="pagado">Pagado</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Guardar</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Registrar retención</h4>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=taxes/withholdings/store">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Período</label>
                        <select name="period_id" class="form-select" required>
                            <option value="">Selecciona período</option>
                            <?php foreach ($periods as $period): ?>
                                <option value="<?php echo (int)$period['id']; ?>" <?php echo (int)$selectedPeriodId === (int)$period['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($period['period']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <input type="text" name="type" class="form-control" placeholder="Honorarios, boleta terceros, etc." required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Base</label>
                            <input type="number" step="0.01" name="base_amount" class="form-control" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tasa (%)</label>
                            <input type="number" step="0.01" name="rate" class="form-control" value="0">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-primary mt-3">Agregar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title mb-0">Períodos registrados</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th>IVA débito</th>
                                <th>IVA crédito</th>
                                <th>Retenciones</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($periods as $period): ?>
                                <tr>
                                    <td><?php echo e($period['period']); ?></td>
                                    <td><?php echo e(format_currency((float)($period['iva_debito'] ?? 0))); ?></td>
                                    <td><?php echo e(format_currency((float)($period['iva_credito'] ?? 0))); ?></td>
                                    <td><?php echo e(format_currency((float)($period['total_retenciones'] ?? 0))); ?></td>
                                    <td class="text-capitalize"><?php echo e($period['status'] ?? 'pendiente'); ?></td>
                                    <td class="text-end">
                                        <div class="dropdown actions-dropdown">
                                            <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Acciones
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="index.php?route=taxes/periods/edit&id=<?php echo (int)$period['id']; ?>">Editar</a>
                                                </li>
                                                <li>
                                                    <form method="post" action="index.php?route=taxes/periods/delete" onsubmit="return confirm('¿Eliminar este período y sus retenciones?');">
                                                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                        <input type="hidden" name="id" value="<?php echo (int)$period['id']; ?>">
                                                        <button type="submit" class="dropdown-item dropdown-item-button text-danger">Eliminar</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($periods)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay períodos tributarios.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Retenciones del período</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Base</th>
                                <th>Tasa</th>
                                <th>Monto</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($withholdings as $row): ?>
                                <tr>
                                    <td><?php echo e($row['type']); ?></td>
                                    <td><?php echo e(format_currency((float)($row['base_amount'] ?? 0))); ?></td>
                                    <td><?php echo e(number_format((float)($row['rate'] ?? 0), 2)); ?>%</td>
                                    <td><?php echo e(format_currency((float)($row['amount'] ?? 0))); ?></td>
                                    <td class="text-end">
                                        <div class="dropdown actions-dropdown">
                                            <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Acciones
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="index.php?route=taxes/withholdings/edit&id=<?php echo (int)$row['id']; ?>">Editar</a>
                                                </li>
                                                <li>
                                                    <form method="post" action="index.php?route=taxes/withholdings/delete" onsubmit="return confirm('¿Eliminar esta retención?');">
                                                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                        <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                                                        <button type="submit" class="dropdown-item dropdown-item-button text-danger">Eliminar</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($withholdings)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay retenciones registradas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
