<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Configuración de pagos en línea</h4>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            Esta configuración habilita la integración con Flow para crear pagos y consultar su estado extendido
            (endpoint <code>/payment/getStatusExtended</code>). Revisa la documentación oficial para obtener tus credenciales.
        </div>
        <form method="post" action="index.php?route=maintainers/online-payments/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row">
                <div class="col-12 mb-2">
                    <h6 class="text-uppercase text-muted mb-0">Credenciales Flow</h6>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ambiente</label>
                    <select name="environment" class="form-select">
                        <option value="sandbox" <?php echo ($flowConfig['environment'] ?? '') === 'sandbox' ? 'selected' : ''; ?>>Sandbox</option>
                        <option value="production" <?php echo ($flowConfig['environment'] ?? '') === 'production' ? 'selected' : ''; ?>>Producción</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">API Key</label>
                    <input type="text" name="api_key" class="form-control" value="<?php echo e($flowConfig['api_key'] ?? ''); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Secret Key</label>
                    <input type="password" name="secret_key" class="form-control" value="<?php echo e($flowConfig['secret_key'] ?? ''); ?>">
                </div>

                <div class="col-12 mt-2 mb-2">
                    <h6 class="text-uppercase text-muted mb-0">Endpoints</h6>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">URL base API</label>
                    <input type="text" name="base_url" class="form-control" value="<?php echo e($flowConfig['base_url'] ?? ''); ?>" placeholder="<?php echo e($defaultBaseUrls[$flowConfig['environment'] ?? 'sandbox'] ?? ''); ?>">
                    <div class="form-text">Ejemplo: <?php echo e($defaultBaseUrls['sandbox']); ?> o <?php echo e($defaultBaseUrls['production']); ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">URL retorno (return)</label>
                    <input type="url" name="return_url" class="form-control" value="<?php echo e($flowConfig['return_url'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">URL confirmación (confirmation)</label>
                    <input type="url" name="confirmation_url" class="form-control" value="<?php echo e($flowConfig['confirmation_url'] ?? ''); ?>">
                </div>
            </div>
            <div class="d-flex flex-wrap justify-content-end gap-2">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
