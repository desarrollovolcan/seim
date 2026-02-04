<div class="card shadow-sm border-0">
    <div class="card-body p-3 p-md-4">
        <form method="post" action="index.php?route=maintainers/competitor-companies/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="d-grid gap-3">
                        <section class="border rounded-3 p-3 p-md-4 bg-white">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                                <div>
                                    <h5 class="mb-1">Información de la empresa competencia</h5>
                                    <p class="text-muted mb-0">Datos principales para identificación y contacto.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Código</label>
                                    <input type="text" name="code" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nombre</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">RUT</label>
                                    <input type="text" name="rut" class="form-control" placeholder="12.345.678-9" autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Correo</label>
                                    <input type="email" name="email" class="form-control" autocomplete="email">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Dirección</label>
                                    <input type="text" name="address" class="form-control" autocomplete="street-address">
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <div class="form-actions mt-3">
                <a href="index.php?route=maintainers/competitor-companies" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
