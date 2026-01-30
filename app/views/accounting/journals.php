<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Libro diario</h4>
    <a href="index.php?route=accounting/journals/create" class="btn btn-primary">Nuevo asiento</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Nº asiento</th>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Origen</th>
                        <th>Débito</th>
                        <th>Crédito</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($journals as $journal): ?>
                        <tr>
                            <td><?php echo e($journal['entry_number']); ?></td>
                            <td><?php echo e(format_date($journal['entry_date'] ?? null)); ?></td>
                            <td><?php echo e($journal['description'] ?? ''); ?></td>
                            <td class="text-capitalize"><?php echo e($journal['source'] ?? 'manual'); ?></td>
                            <td><?php echo e(format_currency((float)($journal['total_debit'] ?? 0))); ?></td>
                            <td><?php echo e(format_currency((float)($journal['total_credit'] ?? 0))); ?></td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="index.php?route=accounting/journals/show&id=<?php echo (int)$journal['id']; ?>">Ver detalle</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($journals)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay asientos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
