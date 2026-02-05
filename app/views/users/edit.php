<div class="card">
    <div class="card-body">
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form method="post" action="index.php?route=users/update" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <div class="mb-3">
                <?php echo render_id_badge($user['id'] ?? null); ?>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($user['name']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Empresa</label>
                    <select name="company_id" class="form-select" required>
                        <?php foreach (($companies ?? []) as $company): ?>
                            <option value="<?php echo e((string)$company['id']); ?>" <?php echo ((int)$company['id'] === (int)$user['company_id']) ? 'selected' : ''; ?>>
                                <?php echo e($company['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Empresa principal de inicio de sesión.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Empresas adicionales</label>
                    <select name="company_ids[]" class="form-select" multiple>
                        <?php foreach (($companies ?? []) as $company): ?>
                            <option value="<?php echo e((string)$company['id']); ?>" <?php echo in_array((int)$company['id'], $userCompanyIds ?? [], true) ? 'selected' : ''; ?>>
                                <?php echo e($company['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Mantén presionado Ctrl (Cmd) para seleccionar varias.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e($user['email']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contraseña (opcional)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Rol</label>
                    <select name="role_id" class="form-select">
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['id']; ?>" <?php echo $role['id'] == $user['role_id'] ? 'selected' : ''; ?>><?php echo e($role['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Nombre para cotización</label>
                    <input type="text" name="signature" class="form-control" value="<?php echo e($user['signature']); ?>" placeholder="Nombre Apellido">
                    <div class="form-text">Este nombre se mostrará sobre la firma en la cotización.</div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Firma para cotización (PNG)</label>
                    <input type="file" name="signature_image" class="form-control" accept="image/png">
                    <div class="form-text">Formato permitido: PNG (máx 2MB).</div>
                    <?php if (!empty($user['signature_image_path'])): ?>
                        <div class="mt-2">
                            <img src="<?php echo e($user['signature_image_path']); ?>" alt="Firma de usuario" style="max-height: 80px; width: auto;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Foto de perfil</label>
                    <input type="file" name="avatar" class="form-control" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text">Formatos permitidos: JPG, PNG o WEBP (máx 2MB).</div>
                    <?php if (!empty($user['avatar_path'])): ?>
                        <div class="mt-2">
                            <img src="<?php echo e($user['avatar_path']); ?>" alt="Avatar de usuario" class="rounded-circle" style="width: 64px; height: 64px; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=users" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'users/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
