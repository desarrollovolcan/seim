<div class="row">
    <div class="col-12 col-lg-10 col-xl-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Editar proveedor</h4>
                <a href="index.php?route=suppliers" class="btn btn-outline-secondary">Volver al listado</a>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=suppliers/update">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id" value="<?php echo (int)($supplier['id'] ?? 0); ?>">
                    <div class="row g-3">
                        <div class="col-md-6 col-xl-4">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="name" class="form-control" required value="<?php echo e($supplier['name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <label class="form-label">Persona de contacto</label>
                            <input type="text" name="contact_name" class="form-control" value="<?php echo e($supplier['contact_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <label class="form-label">RUT / ID</label>
                            <input type="text" name="tax_id" class="form-control" value="<?php echo e($supplier['tax_id'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo e($supplier['email'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo e($supplier['phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <label class="form-label">Sitio web</label>
                            <input type="url" name="website" class="form-control" value="<?php echo e($supplier['website'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="address" class="form-control" value="<?php echo e($supplier['address'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <label class="form-label">Giro</label>
                            <input type="text" name="giro" class="form-control" value="<?php echo e($supplier['giro'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <?php
                            $activityCodeValue = $supplier['activity_code'] ?? '';
                            $activityCodeOptions = $activityCodeOptions ?? [];
                            include __DIR__ . '/../partials/activity-code-field.php';
                            ?>
                        </div>
                        <div class="col-12">
                            <?php
                            $communeValue = $supplier['commune'] ?? '';
                            $cityValue = $supplier['city'] ?? '';
                            include __DIR__ . '/../partials/commune-city-fields.php';
                            ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea name="notes" class="form-control" rows="3"><?php echo e($supplier['notes'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Actualizar proveedor</button>
                            <a href="index.php?route=suppliers" class="btn btn-light ms-2">Cancelar</a>
                        </div>
                    </div>
                
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'suppliers/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
            </div>
        </div>
    </div>
</div>
