<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Generación masiva de contratos</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=hr/contracts/bulk-store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tipo de contrato</label>
                    <select name="contract_type_id" class="form-select">
                        <option value="">Selecciona</option>
                        <?php foreach ($contractTypes as $type): ?>
                            <option value="<?php echo (int)$type['id']; ?>"><?php echo e($type['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Departamento</label>
                    <select name="department_id" class="form-select">
                        <option value="">Selecciona</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?php echo (int)$department['id']; ?>"><?php echo e($department['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cargo</label>
                    <select name="position_id" class="form-select">
                        <option value="">Selecciona</option>
                        <?php foreach ($positions as $position): ?>
                            <option value="<?php echo (int)$position['id']; ?>"><?php echo e($position['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jornada</label>
                    <select name="schedule_id" class="form-select">
                        <option value="">Selecciona</option>
                        <?php foreach ($schedules as $schedule): ?>
                            <option value="<?php echo (int)$schedule['id']; ?>"><?php echo e($schedule['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha de inicio *</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha de término</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sueldo base (CLP) *</label>
                    <input type="number" name="salary" class="form-control" min="0" step="0.01" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Horas semanales</label>
                    <input type="number" name="weekly_hours" class="form-control" min="1" max="45" value="45">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="vigente">Vigente</option>
                        <option value="suspendido">Suspendido</option>
                        <option value="terminado">Terminado</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <h5 class="mb-3">Selecciona trabajadores</h5>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Seleccionar</th>
                                <th>Trabajador</th>
                                <th>RUT</th>
                                <th>Departamento</th>
                                <th>Cargo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="employee_ids[]" value="<?php echo (int)$employee['id']; ?>" class="form-check-input">
                                    </td>
                                    <td><?php echo e(trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? ''))); ?></td>
                                    <td><?php echo e($employee['rut'] ?? ''); ?></td>
                                    <td><?php echo e($employee['department_name'] ?? ''); ?></td>
                                    <td><?php echo e($employee['position_name'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Generar contratos</button>
                <a href="index.php?route=hr/contracts" class="btn btn-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>
