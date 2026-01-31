<?php
$siiData = $siiData ?? [];
$siiLabel = $siiLabel ?? 'Receptor';
$siiRequired = $siiRequired ?? true;
$requiredAttr = $siiRequired ? 'required' : '';
$documentTypes = sii_document_types();
$receiverHelp = $siiLabel === 'Proveedor'
    ? 'Estos datos se toman desde la ficha del proveedor.'
    : 'Estos datos se toman desde la ficha del cliente.';
?>
<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0">Datos tributarios (SII)</h5>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3"><?php echo e($receiverHelp); ?></p>
        <div class="alert alert-warning d-none" data-sii-warning>
            <strong>Faltan datos SII.</strong>
            <span data-sii-warning-text></span>
            <a class="alert-link ms-1" href="#" data-sii-warning-link>Completar ficha</a>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Tipo de documento</label>
                <select name="sii_document_type" class="form-select" <?php echo $requiredAttr; ?>>
                    <option value="">Selecciona tipo</option>
                    <?php foreach ($documentTypes as $value => $label): ?>
                        <option value="<?php echo e($value); ?>" <?php echo ($siiData['sii_document_type'] ?? '') === $value ? 'selected' : ''; ?>>
                            <?php echo e($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Folio / Nº documento</label>
                <input type="text" name="sii_document_number" class="form-control" value="<?php echo e($siiData['sii_document_number'] ?? ''); ?>" <?php echo $requiredAttr; ?>>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Tasa impuesto (%)</label>
                <input type="number" name="sii_tax_rate" class="form-control" step="0.01" min="0" max="100" value="<?php echo e($siiData['sii_tax_rate'] ?? 19); ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Monto exento</label>
                <input type="number" name="sii_exempt_amount" class="form-control" step="0.01" min="0" value="<?php echo e($siiData['sii_exempt_amount'] ?? 0); ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">RUT <?php echo e($siiLabel); ?></label>
                <input type="text" name="sii_receiver_rut" class="form-control" placeholder="12.345.678-9" value="<?php echo e($siiData['sii_receiver_rut'] ?? ''); ?>" readonly data-sii-field="rut">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Razón social</label>
                <input type="text" name="sii_receiver_name" class="form-control" value="<?php echo e($siiData['sii_receiver_name'] ?? ''); ?>" readonly data-sii-field="name">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Giro</label>
                <input type="text" name="sii_receiver_giro" class="form-control" value="<?php echo e($siiData['sii_receiver_giro'] ?? ''); ?>" readonly data-sii-field="giro">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Dirección</label>
                <input type="text" name="sii_receiver_address" class="form-control" value="<?php echo e($siiData['sii_receiver_address'] ?? ''); ?>" readonly data-sii-field="address">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Comuna</label>
                <input type="text" name="sii_receiver_commune" class="form-control" value="<?php echo e($siiData['sii_receiver_commune'] ?? ''); ?>" readonly data-sii-field="commune">
            </div>
        </div>
    </div>
</div>
