<div class="card">
    <div class="card-body">
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form method="post" action="index.php?route=users/store" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Empresa</label>
                    <select name="company_id" class="form-select" required>
                        <option value="">Selecciona empresa</option>
                        <?php foreach (($companies ?? []) as $company): ?>
                            <option value="<?php echo e((string)$company['id']); ?>"><?php echo e($company['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Empresa principal de inicio de sesión.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Empresas adicionales</label>
                    <select name="company_ids[]" class="form-select" multiple>
                        <?php foreach (($companies ?? []) as $company): ?>
                            <option value="<?php echo e((string)$company['id']); ?>"><?php echo e($company['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Mantén presionado Ctrl (Cmd) para seleccionar varias.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Rol</label>
                    <select name="role_id" class="form-select">
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['id']; ?>"><?php echo e($role['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Firma</label>
                    <textarea name="signature" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Foto de perfil</label>
                    <input type="file" name="avatar" class="form-control" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text">Formatos permitidos: JPG, PNG o WEBP (máx 2MB).</div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=users" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'users/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
