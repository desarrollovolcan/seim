<div class="card mb-4">
    <div class="card-header">
        <h4 class="card-title mb-0">Gestión de cotizaciones</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=quotes/management/update" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div class="col-md-6">
                <label class="form-label">Seleccionar cotización</label>
                <select name="id" class="form-select" required onchange="window.location='index.php?route=quotes/management&quote_id=' + this.value;">
                    <option value="">Selecciona una cotización...</option>
                    <?php foreach (($createdQuotes ?? []) as $quoteOption): ?>
                        <option value="<?php echo (int)$quoteOption['id']; ?>" <?php echo (int)($selectedQuote['id'] ?? 0) === (int)$quoteOption['id'] ? 'selected' : ''; ?>>
                            <?php echo e(($quoteOption['numero'] ?? ('#' . (int)$quoteOption['id'])) . ' · ' . ucfirst(str_replace('_', ' ', (string)($quoteOption['estado'] ?? '')))); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($createdQuotes)): ?>
                    <div class="form-text text-muted">No hay cotizaciones para gestionar.</div>
                <?php endif; ?>
            </div>

            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select" <?php echo empty($selectedQuote) ? 'disabled' : ''; ?>>
                    <?php foreach (($statusOptions ?? []) as $status): ?>
                        <option value="<?php echo e($status); ?>" <?php echo ($selectedQuote['estado'] ?? '') === $status ? 'selected' : ''; ?>>
                            <?php echo e(ucfirst(str_replace('_', ' ', $status))); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Próxima acción</label>
                <input type="date" name="next_action_date" class="form-control" value="<?php echo e($selectedQuote['next_action_date'] ?? ''); ?>" <?php echo empty($selectedQuote) ? 'disabled' : ''; ?>>
            </div>

            <div class="col-12">
                <label class="form-label">Acción a seguir</label>
                <textarea name="next_action_note" class="form-control" rows="3" placeholder="Ej: Llamar al cliente para validar aprobación final" <?php echo empty($selectedQuote) ? 'disabled' : ''; ?>><?php echo e($selectedQuote['next_action_note'] ?? ''); ?></textarea>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="is_closed" value="1" id="quote_closed" <?php echo !empty($selectedQuote['is_closed']) ? 'checked' : ''; ?> <?php echo empty($selectedQuote) ? 'disabled' : ''; ?>>
                    <label class="form-check-label" for="quote_closed">Cerrar cotización (bloquea edición)</label>
                </div>
            </div>

            <div class="col-md-6 text-md-end">
                <button type="submit" class="btn btn-primary" <?php echo empty($selectedQuote) ? 'disabled' : ''; ?>>Guardar gestión</button>
                <?php if (!empty($selectedQuote) && empty($selectedQuote['is_closed'])): ?>
                    <a href="index.php?route=quotes/edit&id=<?php echo (int)$selectedQuote['id']; ?>" class="btn btn-outline-secondary ms-2">Editar formulario completo</a>
                <?php elseif (!empty($selectedQuote) && !empty($selectedQuote['is_closed'])): ?>
                    <button type="button" class="btn btn-outline-secondary ms-2" disabled>Edición bloqueada (cerrada)</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Listado de cotizaciones</h4>
        <a href="index.php?route=quotes" class="btn btn-outline-primary btn-sm">Ver módulo cotizaciones</a>
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
                        <th>Acción a seguir</th>
                        <th class="text-end">Gestión de cotizaciones</th>
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
                            <td><?php echo e(ucfirst(str_replace('_', ' ', (string)($quote['estado'] ?? '')))); ?></td>
                            <td><?php echo e($quote['next_action_date'] ?? '-'); ?></td>
                            <td><?php echo e($quote['next_action_note'] ?? '-'); ?></td>
                            <td class="text-end">
                                <a href="index.php?route=quotes/management&quote_id=<?php echo (int)$quote['id']; ?>" class="btn btn-sm btn-soft-primary">Gestionar</a>
                                <?php if (empty($quote['is_closed'])): ?>
                                    <a href="index.php?route=quotes/edit&id=<?php echo (int)$quote['id']; ?>" class="btn btn-sm btn-outline-secondary">Editar formulario</a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled>Cerrada</button>
                                <?php endif; ?>
                                <a href="index.php?route=quotes/show&id=<?php echo (int)$quote['id']; ?>" class="btn btn-sm btn-light">Revisar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
