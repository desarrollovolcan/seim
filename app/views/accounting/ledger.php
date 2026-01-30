<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Libro mayor</h4>
        <a href="index.php?route=accounting/chart" class="btn btn-light btn-sm">Plan de cuentas</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Cuenta</th>
                        <th>Tipo</th>
                        <th>Débito</th>
                        <th>Crédito</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ledger as $row): ?>
                        <?php $balance = (float)$row['total_debit'] - (float)$row['total_credit']; ?>
                        <tr>
                            <td><?php echo e($row['code'] . ' - ' . $row['name']); ?></td>
                            <td class="text-capitalize"><?php echo e($row['type']); ?></td>
                            <td><?php echo e(format_currency((float)$row['total_debit'])); ?></td>
                            <td><?php echo e(format_currency((float)$row['total_credit'])); ?></td>
                            <td><?php echo e(format_currency($balance)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ledger)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay movimientos contables.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
