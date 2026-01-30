<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Balance General</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Activos</span>
                    <strong><?php echo e(format_currency((float)($statement['activo'] ?? 0))); ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Pasivos</span>
                    <strong><?php echo e(format_currency((float)($statement['pasivo'] ?? 0))); ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Patrimonio</span>
                    <strong><?php echo e(format_currency((float)($statement['patrimonio'] ?? 0))); ?></strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Estado de resultados</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Resultado del período</span>
                    <strong><?php echo e(format_currency((float)($statement['resultado'] ?? 0))); ?></strong>
                </div>
                <p class="text-muted mb-0">Se calcula con el neto de cuentas de resultado.</p>
            </div>
        </div>
    </div>
</div>
