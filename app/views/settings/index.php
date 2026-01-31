<div class="card mb-4">
    <div class="card-header">
        <h4 class="card-title mb-0">Datos empresa</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=settings/update" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="section" value="company">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($company['name'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">RUT</label>
                    <input type="text" name="rut" class="form-control" value="<?php echo e($company['rut'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Banco</label>
                    <input type="text" name="bank" class="form-control" value="<?php echo e($company['bank'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo cuenta</label>
                    <input type="text" name="account_type" class="form-control" value="<?php echo e($company['account_type'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Número cuenta</label>
                    <input type="text" name="account_number" class="form-control" value="<?php echo e($company['account_number'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email contacto</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e($company['email'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo e($company['phone'] ?? ''); ?>">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="address" class="form-control" value="<?php echo e($company['address'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giro</label>
                    <input type="text" name="giro" class="form-control" value="<?php echo e($company['giro'] ?? ''); ?>" placeholder="Ej: Servicios informáticos">
                </div>
                <div class="col-md-6 mb-3">
                    <?php
                    $activityCodeValue = $company['activity_code'] ?? '';
                    $activityCodeOptions = $activityCodeOptions ?? [];
                    include __DIR__ . '/../partials/activity-code-field.php';
                    ?>
                </div>
                <div class="col-12 mb-3">
                    <?php
                    $communeValue = $company['commune'] ?? '';
                    include __DIR__ . '/../partials/commune-city-fields.php';
                    ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Logo color</label>
                    <input type="file" name="logo_color" class="form-control" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text">Formatos permitidos: JPG, PNG o WEBP (máx 2MB).</div>
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <?php if (!empty($company['logo_color'])): ?>
                        <img src="<?php echo e($company['logo_color']); ?>" alt="Logo color" class="rounded border" style="height: 48px; object-fit: contain;">
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Logo negro</label>
                    <input type="file" name="logo_black" class="form-control" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text">Usa la versión en negro para fondos claros.</div>
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <?php if (!empty($company['logo_black'])): ?>
                        <img src="<?php echo e($company['logo_black']); ?>" alt="Logo negro" class="rounded border" style="height: 48px; object-fit: contain;">
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Logo para login</label>
                    <input type="file" name="login_logo" class="form-control" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text">Logo que se mostrará en la pantalla de acceso.</div>
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <?php if (!empty($company['login_logo'])): ?>
                        <img src="<?php echo e($company['login_logo']); ?>" alt="Logo login" class="rounded border" style="height: 48px; object-fit: contain;">
                    <?php endif; ?>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Firma</label>
                    <textarea name="signature" class="form-control" rows="3"><?php echo e($company['signature'] ?? ''); ?></textarea>
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
        <form method="post" action="index.php?route=settings/test-smtp" class="mt-3">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">Probar envío a mi correo</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h4 class="card-title mb-0">Parámetros cobranza</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=settings/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="section" value="billing">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Días aviso 1</label>
                    <input type="number" name="notice_days_1" class="form-control" value="<?php echo e($billing['notice_days_1'] ?? 15); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Días aviso 2</label>
                    <input type="number" name="notice_days_2" class="form-control" value="<?php echo e($billing['notice_days_2'] ?? 5); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Horario envío</label>
                    <input type="time" name="send_time" class="form-control" value="<?php echo e($billing['send_time'] ?? '09:00'); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Zona horaria</label>
                    <input type="text" name="timezone" class="form-control" value="<?php echo e($billing['timezone'] ?? 'America/Santiago'); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Prefijo factura</label>
                    <input type="text" name="invoice_prefix" class="form-control" value="<?php echo e($billing['invoice_prefix'] ?? 'FAC-'); ?>">
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h4 class="card-title mb-0">Facturación</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=settings/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="section" value="invoice">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Moneda por defecto</label>
                    <select name="currency" class="form-select">
                        <option value="CLP" <?php echo ($invoiceDefaults['currency'] ?? 'CLP') === 'CLP' ? 'selected' : ''; ?>>CLP</option>
                        <option value="USD" <?php echo ($invoiceDefaults['currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>USD</option>
                        <option value="EUR" <?php echo ($invoiceDefaults['currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Impuesto (%)</label>
                    <input type="number" step="0.01" name="tax_rate" class="form-control" value="<?php echo e($invoiceDefaults['tax_rate'] ?? 0); ?>">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-center">
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="apply_tax" id="apply_tax" <?php echo !empty($invoiceDefaults['apply_tax']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="apply_tax">Aplicar impuesto por defecto</label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Símbolo moneda</label>
                    <input type="text" name="currency_symbol" class="form-control" value="<?php echo e($currencyFormat['symbol'] ?? '$'); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Decimales</label>
                    <input type="number" min="0" name="currency_decimals" class="form-control" value="<?php echo e($currencyFormat['decimals'] ?? 0); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Separador de miles</label>
                    <input type="text" name="currency_thousands_separator" class="form-control" value="<?php echo e($currencyFormat['thousands_separator'] ?? '.'); ?>">
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
