<div class="card shadow-sm border-0">
    <div class="card-body p-3 p-md-4">
        <form method="post" action="index.php?route=maintainers/competitor-companies/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)($competitor['id'] ?? 0); ?>">
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="d-grid gap-3">
                        <section class="border rounded-3 p-3 p-md-4 bg-white">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                                <div>
                                    <h5 class="mb-1">Información de la empresa competencia</h5>
                                    <p class="text-muted mb-0">Actualiza datos de identificación y contacto.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Código</label>
                                    <input type="text" name="code" class="form-control" value="<?php echo e($competitor['code'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nombre</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo e($competitor['name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">RUT</label>
                                    <input type="text" name="rut" class="form-control" value="<?php echo e($competitor['rut'] ?? ''); ?>" placeholder="12.345.678-9" autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Correo</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo e($competitor['email'] ?? ''); ?>" autocomplete="email">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Dirección</label>
                                    <input type="text" name="address" class="form-control" value="<?php echo e($competitor['address'] ?? ''); ?>" autocomplete="street-address">
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <div class="form-actions mt-3">
                <a href="index.php?route=maintainers/competitor-companies" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>
