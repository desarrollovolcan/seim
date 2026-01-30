<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Editar contrato</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=hr/contracts/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)$contract['id']; ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Trabajador *</label>
                    <select name="employee_id" class="form-select" data-employee-select required>
                        <option value="">Selecciona</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?php echo (int)$employee['id']; ?>"
                                data-department-id="<?php echo (int)($employee['department_id'] ?? 0); ?>"
                                data-position-id="<?php echo (int)($employee['position_id'] ?? 0); ?>"
                                data-hire-date="<?php echo e($employee['hire_date'] ?? ''); ?>"
                                <?php echo ((int)$contract['employee_id'] === (int)$employee['id']) ? 'selected' : ''; ?>>
                                <?php echo e(trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? ''))); ?> - <?php echo e($employee['rut'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipo de contrato</label>
                    <select name="contract_type_id" class="form-select">
                        <option value="">Selecciona</option>
                        <?php foreach ($contractTypes as $type): ?>
                            <option value="<?php echo (int)$type['id']; ?>" <?php echo ((int)($contract['contract_type_id'] ?? 0) === (int)$type['id']) ? 'selected' : ''; ?>>
                                <?php echo e($type['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Departamento</label>
                    <select name="department_id" class="form-select" data-department-select>
                        <option value="">Selecciona</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?php echo (int)$department['id']; ?>" <?php echo ((int)($contract['department_id'] ?? 0) === (int)$department['id']) ? 'selected' : ''; ?>>
                                <?php echo e($department['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cargo</label>
                    <select name="position_id" class="form-select" data-position-select>
                        <option value="">Selecciona</option>
                        <?php foreach ($positions as $position): ?>
                            <option value="<?php echo (int)$position['id']; ?>" <?php echo ((int)($contract['position_id'] ?? 0) === (int)$position['id']) ? 'selected' : ''; ?>>
                                <?php echo e($position['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jornada</label>
                    <select name="schedule_id" class="form-select">
                        <option value="">Selecciona</option>
                        <?php foreach ($schedules as $schedule): ?>
                            <option value="<?php echo (int)$schedule['id']; ?>" <?php echo ((int)($contract['schedule_id'] ?? 0) === (int)$schedule['id']) ? 'selected' : ''; ?>>
                                <?php echo e($schedule['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha de inicio *</label>
                    <input type="date" name="start_date" class="form-control" data-start-date value="<?php echo e($contract['start_date'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha de término</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo e($contract['end_date'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sueldo base (CLP) *</label>
                    <input type="number" name="salary" class="form-control" min="0" step="0.01" value="<?php echo e($contract['salary'] ?? 0); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Horas semanales</label>
                    <input type="number" name="weekly_hours" class="form-control" min="1" max="45" value="<?php echo e($contract['weekly_hours'] ?? 45); ?>">
                    <div class="form-text">Máximo legal referencial: 45 horas semanales.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <?php $status = $contract['status'] ?? 'vigente'; ?>
                    <select name="status" class="form-select">
                        <option value="vigente" <?php echo $status === 'vigente' ? 'selected' : ''; ?>>Vigente</option>
                        <option value="suspendido" <?php echo $status === 'suspendido' ? 'selected' : ''; ?>>Suspendido</option>
                        <option value="terminado" <?php echo $status === 'terminado' ? 'selected' : ''; ?>>Terminado</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="index.php?route=hr/contracts" class="btn btn-light">Cancelar</a>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'hr/contracts/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>

<script>
    (function() {
        const employeeSelect = document.querySelector('[data-employee-select]');
        const departmentSelect = document.querySelector('[data-department-select]');
        const positionSelect = document.querySelector('[data-position-select]');
        const startDateInput = document.querySelector('[data-start-date]');

        const applyEmployeeDefaults = (option) => {
            if (!option) {
                return;
            }
            const departmentId = option.dataset.departmentId || '';
            const positionId = option.dataset.positionId || '';
            const hireDate = option.dataset.hireDate || '';
            if (departmentSelect && departmentId) {
                departmentSelect.value = departmentId;
            }
            if (positionSelect && positionId) {
                positionSelect.value = positionId;
            }
            if (startDateInput && hireDate && !startDateInput.value) {
                startDateInput.value = hireDate;
            }
        };

        employeeSelect?.addEventListener('change', (event) => {
            applyEmployeeDefaults(event.target.selectedOptions[0]);
        });
    })();
</script>
