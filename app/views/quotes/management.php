<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Gestión de cotizaciones</h4>
        <a href="index.php?route=quotes" class="btn btn-outline-primary btn-sm">Ver listado</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Próxima acción</th>
                        <th>Cerrar cotización</th>
                        <th class="text-end">Guardar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($quotes)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay cotizaciones registradas.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($quotes as $quote): ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($quote['id'] ?? null); ?></td>
                            <td><?php echo e($quote['numero'] ?? ''); ?></td>
                            <td><?php echo e($quote['client_name'] ?? 'Sin cliente'); ?></td>
                            <td>
                                <form method="post" action="index.php?route=quotes/management/update" class="d-flex gap-2 align-items-center">
                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int)($quote['id'] ?? 0); ?>">
                                    <select name="estado" class="form-select form-select-sm" style="min-width: 150px;">
                                        <?php foreach (($statusOptions ?? []) as $status): ?>
                                            <option value="<?php echo e($status); ?>" <?php echo ($quote['estado'] ?? '') === $status ? 'selected' : ''; ?>>
                                                <?php echo e(ucfirst(str_replace('_', ' ', $status))); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                            </td>
                            <td>
                                    <input type="date" name="next_action_date" class="form-control form-control-sm" value="<?php echo e($quote['next_action_date'] ?? ''); ?>">
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_closed" value="1" id="closed_<?php echo (int)$quote['id']; ?>" <?php echo !empty($quote['is_closed']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="closed_<?php echo (int)$quote['id']; ?>">Sí</label>
                                </div>
                            </td>
                            <td class="text-end">
                                    <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
