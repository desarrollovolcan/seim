<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Nuevo movimiento</h4>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=treasury/transactions/store">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Cuenta bancaria</label>
                        <select name="bank_account_id" class="form-select" required>
                            <option value="">Selecciona cuenta</option>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?php echo (int)$account['id']; ?>">
                                    <?php echo e($account['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="transaction_date" class="form-control" value="<?php echo e($today); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select name="type" class="form-select">
                            <option value="deposito">Depósito</option>
                            <option value="retiro">Retiro</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monto</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Referencia</label>
                        <input type="text" name="reference" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Movimientos bancarios</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Cuenta</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Saldo</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><?php echo e($transaction['account_name']); ?></td>
                                    <td><?php echo e(format_date($transaction['transaction_date'] ?? null)); ?></td>
                                    <td class="text-capitalize"><?php echo e($transaction['type']); ?></td>
                                    <td><?php echo e(format_currency((float)($transaction['amount'] ?? 0))); ?></td>
                                    <td><?php echo e(format_currency((float)($transaction['balance'] ?? 0))); ?></td>
                                    <td class="text-end">
                                        <div class="dropdown actions-dropdown">
                                            <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Acciones
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="index.php?route=treasury/transactions/show&id=<?php echo (int)$transaction['id']; ?>">Ver detalle</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="index.php?route=treasury/transactions/edit&id=<?php echo (int)$transaction['id']; ?>">Editar</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay movimientos registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
