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
                    <label class="form-label">Nombre para cotización</label>
                    <input type="text" name="signature" class="form-control" placeholder="Nombre Apellido">
                    <div class="form-text">Este nombre se mostrará sobre la firma en la cotización.</div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Firma para cotización (PNG)</label>
                    <input type="file" name="signature_image" class="form-control" accept="image/png" id="signatureImageCreateInput">
                    <div class="form-text">Formato permitido: PNG (máx 2MB).</div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#quoteSignaturePreviewCreateModal" id="quoteSignaturePreviewCreateButton">
                        Probar cómo se verá la cotización
                    </button>
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


<div class="modal fade" id="quoteSignaturePreviewCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista previa de firma en cotización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="border rounded p-3 bg-light">
                    <div class="text-center fw-semibold mb-2" id="quoteSignaturePreviewCreateName">Nombre Apellido</div>
                    <div class="text-center mb-2" style="min-height:70px;">
                        <img id="quoteSignaturePreviewCreateImage" alt="Firma" style="max-height:70px; width:auto; display:none; margin:0 auto;">
                        <div id="quoteSignaturePreviewCreateEmpty" class="text-muted small">Sube una firma PNG para previsualizarla.</div>
                    </div>
                    <div class="text-center text-muted small border-top pt-1">Firma responsable</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const nameInput = document.querySelector('input[name="signature"]');
    const imageInput = document.getElementById('signatureImageCreateInput');
    const modal = document.getElementById('quoteSignaturePreviewCreateModal');
    if (!nameInput || !imageInput || !modal) {
        return;
    }

    const previewName = document.getElementById('quoteSignaturePreviewCreateName');
    const previewImage = document.getElementById('quoteSignaturePreviewCreateImage');
    const previewEmpty = document.getElementById('quoteSignaturePreviewCreateEmpty');
    let objectUrl = null;

    const refreshPreview = function () {
        const name = (nameInput.value || '').trim();
        previewName.textContent = name !== '' ? name : 'Nombre Apellido';

        const file = imageInput.files && imageInput.files[0] ? imageInput.files[0] : null;
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }

        if (file) {
            objectUrl = URL.createObjectURL(file);
            previewImage.src = objectUrl;
            previewImage.style.display = 'block';
            previewEmpty.style.display = 'none';
        } else {
            previewImage.removeAttribute('src');
            previewImage.style.display = 'none';
            previewEmpty.style.display = 'block';
        }
    };

    modal.addEventListener('show.bs.modal', refreshPreview);
    imageInput.addEventListener('change', refreshPreview);
    nameInput.addEventListener('input', refreshPreview);
})();
</script>
