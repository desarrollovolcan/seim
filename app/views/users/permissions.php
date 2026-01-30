<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Asignación de privilegios</h4>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            Nota: cada vez que se cree un menú o submenú nuevo, debe agregarse al catálogo de permisos en <strong>app/helpers.php</strong> para poder asignarlo a los perfiles.
            Ahora puedes asignar permisos de <strong>ver</strong> o <strong>editar</strong> por menú y submenú.
        </div>
        <form method="get" action="index.php" class="row g-3 align-items-end mb-3">
            <input type="hidden" name="route" value="users/permissions">
            <div class="col-md-6">
                <label class="form-label">Perfil</label>
                <select name="role_id" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>" <?php echo (int)$selectedRoleId === (int)$role['id'] ? 'selected' : ''; ?>>
                            <?php echo e($role['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <form method="post" action="index.php?route=users/permissions/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="role_id" value="<?php echo (int)$selectedRoleId; ?>">
            <div class="row">
                <?php foreach ($permissionCatalog as $permission): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="border rounded p-3 h-100">
                            <div class="fw-semibold mb-2"><?php echo e($permission['label']); ?></div>
                            <div class="d-flex flex-column gap-2">
                                <?php
                                $legacyKey = $permission['legacy_key'] ?? null;
                                $viewKey = $permission['view_key'] ?? null;
                                $editKey = $permission['edit_key'] ?? null;
                                $hasLegacy = $legacyKey && in_array($legacyKey, $selectedPermissions, true);
                                $viewChecked = $viewKey && (in_array($viewKey, $selectedPermissions, true) || $hasLegacy);
                                $editChecked = $editKey && (in_array($editKey, $selectedPermissions, true) || $hasLegacy);
                                ?>
                                <?php if ($viewKey): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="perm-<?php echo e($viewKey); ?>" name="permissions[]" value="<?php echo e($viewKey); ?>" <?php echo $viewChecked ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="perm-<?php echo e($viewKey); ?>">Ver</label>
                                    </div>
                                <?php endif; ?>
                                <?php if ($editKey): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="perm-<?php echo e($editKey); ?>" name="permissions[]" value="<?php echo e($editKey); ?>" <?php echo $editChecked ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="perm-<?php echo e($editKey); ?>">Editar</label>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($permission['routes'])): ?>
                                <div class="text-muted small mt-2">
                                    Rutas: <?php echo e(implode(', ', $permission['routes'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Guardar permisos</button>
            </div>
        </form>
    </div>
</div>
