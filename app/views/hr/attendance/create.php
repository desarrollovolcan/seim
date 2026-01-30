<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Registrar asistencia</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=hr/attendance/store">
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
                <div class="col-md-6">
                    <label class="form-label">Fecha *</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hora de entrada</label>
                    <input type="time" name="check_in" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hora de salida</label>
                    <input type="time" name="check_out" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Horas extra</label>
                    <input type="number" name="overtime_hours" class="form-control" min="0" step="0.5" value="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipo de ausencia</label>
                    <input type="text" name="absence_type" class="form-control" placeholder="Permiso, licencia, vacaciones">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Observaciones</label>
                    <input type="text" name="notes" class="form-control">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="index.php?route=hr/attendance" class="btn btn-light">Cancelar</a>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'hr/attendance/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
