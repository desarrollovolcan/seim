<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Detalle de asiento</h4>
        <a href="index.php?route=accounting/journals" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-3">
                <div class="text-muted small">Nº asiento</div>
                <div class="fw-semibold"><?php echo e($journal['entry_number'] ?? ''); ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Fecha</div>
                <div class="fw-semibold"><?php echo e(format_date($journal['entry_date'] ?? null)); ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Origen</div>
                <div class="fw-semibold text-capitalize"><?php echo e($journal['source'] ?? 'manual'); ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Estado</div>
                <div class="fw-semibold text-capitalize"><?php echo e($journal['status'] ?? ''); ?></div>
            </div>
            <div class="col-12">
                <div class="text-muted small">Descripción</div>
                <div class="fw-semibold"><?php echo e($journal['description'] ?? ''); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Líneas del asiento</h5>
        <div class="text-muted small">
            Débito: <?php echo e(format_currency((float)($journal['total_debit'] ?? 0))); ?> |
            Crédito: <?php echo e(format_currency((float)($journal['total_credit'] ?? 0))); ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Cuenta</th>
                        <th>Descripción</th>
                        <th>Débito</th>
                        <th>Crédito</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lines as $line): ?>
                        <tr>
                            <td><?php echo e(($line['code'] ?? '') . ' - ' . ($line['account_name'] ?? '')); ?></td>
                            <td><?php echo e($line['line_description'] ?? ''); ?></td>
                            <td><?php echo e(format_currency((float)($line['debit'] ?? 0))); ?></td>
                            <td><?php echo e(format_currency((float)($line['credit'] ?? 0))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($lines)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay líneas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
