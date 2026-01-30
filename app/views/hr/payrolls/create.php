<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Nueva remuneración</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=hr/payrolls/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Trabajador *</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">Selecciona</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?php echo (int)$employee['id']; ?>">
                                <?php echo e(trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? ''))); ?> - <?php echo e($employee['rut'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Inicio período *</label>
                    <input type="date" name="period_start" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fin período *</label>
                    <input type="date" name="period_end" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sueldo base *</label>
                    <input type="number" name="base_salary" class="form-control" min="0" step="0.01" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Haberes</label>
                    <input type="number" name="bonuses" class="form-control" min="0" step="0.01" value="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Otros haberes</label>
                    <input type="number" name="other_earnings" class="form-control" min="0" step="0.01" value="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Otros descuentos</label>
                    <input type="number" name="other_deductions" class="form-control" min="0" step="0.01" value="0">
                    <div class="form-text">Las cotizaciones se calculan según AFP/Salud del trabajador.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="borrador">Borrador</option>
                        <option value="procesado">Procesado</option>
                        <option value="pagado">Pagado</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="index.php?route=hr/payrolls" class="btn btn-light">Cancelar</a>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'hr/payrolls/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
