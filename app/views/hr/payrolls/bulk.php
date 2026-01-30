<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Generación masiva de liquidaciones</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=hr/payrolls/bulk-store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Inicio período *</label>
                    <input type="date" name="period_start" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fin período *</label>
                    <input type="date" name="period_end" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Haberes generales</label>
                    <input type="number" name="bonuses" class="form-control" min="0" step="0.01" value="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Otros haberes</label>
                    <input type="number" name="other_earnings" class="form-control" min="0" step="0.01" value="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Otros descuentos</label>
                    <input type="number" name="other_deductions" class="form-control" min="0" step="0.01" value="0">
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
            <div class="alert alert-info mt-4">
                Se generará una liquidación por cada contrato vigente en el período. Las cotizaciones se calculan con las tasas
                registradas en cada trabajador (AFP, Salud, Seguro de Cesantía).
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Generar liquidaciones</button>
                <a href="index.php?route=hr/payrolls" class="btn btn-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>
