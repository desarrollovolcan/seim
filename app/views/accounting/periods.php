<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Nuevo período contable</h4>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=accounting/periods/store">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Período (ej: 2025-03)</label>
                        <input type="month" name="period" class="form-control" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Crear período</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Cierres contables</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th>Estado</th>
                                <th>Cerrado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($periods as $period): ?>
                                <tr>
                                    <td><?php echo e($period['period']); ?></td>
                                    <td class="text-capitalize"><?php echo e($period['status']); ?></td>
                                    <td><?php echo e($period['closed_at'] ? format_date($period['closed_at']) : '-'); ?></td>
                                    <td class="text-end">
                                        <?php if (($period['status'] ?? '') !== 'cerrado'): ?>
                                            <form method="post" action="index.php?route=accounting/periods/close">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="period_id" value="<?php echo (int)$period['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Cerrar</button>
                                            </form>
                                        <?php else: ?>
                                            <div class="d-flex flex-wrap align-items-center gap-2 justify-content-end">
                                                <form method="post" action="index.php?route=accounting/periods/request-open">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="period_id" value="<?php echo (int)$period['id']; ?>">
                                                    <button type="submit" class="btn btn-outline-primary btn-sm">Enviar código</button>
                                                </form>
                                                <form method="post" action="index.php?route=accounting/periods/open" class="d-flex align-items-center gap-2 flex-wrap">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="period_id" value="<?php echo (int)$period['id']; ?>">
                                                    <input type="text" name="open_code" class="form-control form-control-sm" placeholder="Código" inputmode="numeric" autocomplete="off" required style="width: 120px;">
                                                    <button type="submit" class="btn btn-success btn-sm">Abrir</button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($periods)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay períodos registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
