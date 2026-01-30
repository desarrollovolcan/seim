<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Empresa actual</th>
                        <th>Asignar empresa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($users ?? []) as $user): ?>
                        <tr>
                            <td><?php echo e($user['name'] ?? ''); ?></td>
                            <td><?php echo e($user['email'] ?? ''); ?></td>
                            <td><?php echo e($user['role'] ?? ''); ?></td>
                            <td><?php echo e($user['company_name'] ?? 'Sin empresa'); ?></td>
                            <td>
                                <form method="post" action="index.php?route=users/assign-company/update" class="d-flex gap-2">
                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="user_id" value="<?php echo e((string)$user['id']); ?>">
                                    <select name="company_ids[]" class="form-select form-select-sm" multiple required>
                                        <?php foreach (($companies ?? []) as $company): ?>
                                            <option value="<?php echo e((string)$company['id']); ?>" <?php echo in_array((int)$company['id'], $user['company_ids'] ?? [], true) ? 'selected' : ''; ?>>
                                                <?php echo e($company['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
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
