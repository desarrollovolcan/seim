<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Plan de cuentas</h4>
    <a href="index.php?route=accounting/chart/create" class="btn btn-primary">Nueva cuenta</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Cuenta madre</th>
                        <th>Nivel</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $accountLookup = []; ?>
                    <?php foreach ($accounts as $account): ?>
                        <?php $accountLookup[$account['id']] = $account; ?>
                    <?php endforeach; ?>
                    <?php foreach ($accounts as $account): ?>
                        <?php
                            $parentLabel = 'Principal';
                            if (!empty($account['parent_id']) && isset($accountLookup[$account['parent_id']])) {
                                $parent = $accountLookup[$account['parent_id']];
                                $parentLabel = $parent['code'] . ' - ' . $parent['name'];
                            }
                        ?>
                        <tr>
                            <td><?php echo e($account['code']); ?></td>
                            <td><?php echo e($account['name']); ?></td>
                            <td class="text-capitalize"><?php echo e($account['type']); ?></td>
                            <td><?php echo e($parentLabel); ?></td>
                            <td><?php echo (int)($account['level'] ?? 1); ?></td>
                            <td><?php echo !empty($account['is_active']) ? 'Activa' : 'Inactiva'; ?></td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="index.php?route=accounting/chart/edit&id=<?php echo (int)$account['id']; ?>">Editar</a>
                                        </li>
                                        <li>
                                            <form method="post" action="index.php?route=accounting/chart/delete" onsubmit="return confirm('¿Eliminar esta cuenta contable?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$account['id']; ?>">
                                                <button type="submit" class="dropdown-item dropdown-item-button text-danger">Eliminar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($accounts)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay cuentas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
