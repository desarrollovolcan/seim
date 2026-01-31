<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="name" class="form-control" value="<?php echo e($company['name'] ?? ''); ?>" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">RUT</label>
        <input type="text" name="rut" class="form-control" value="<?php echo e($company['rut'] ?? ''); ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
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
</div>
