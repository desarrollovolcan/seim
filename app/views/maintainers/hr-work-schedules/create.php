<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Nueva jornada</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=maintainers/hr-work-schedules/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Horas semanales</label>
                    <input type="number" name="weekly_hours" class="form-control" min="1" max="45" value="45">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Colación (min)</label>
                    <input type="number" name="lunch_break_minutes" class="form-control" min="0" value="60">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hora inicio</label>
                    <input type="time" name="start_time" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hora término</label>
                    <input type="time" name="end_time" class="form-control">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="index.php?route=maintainers/hr-work-schedules" class="btn btn-light">Cancelar</a>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'maintainers/hr-work-schedules/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
