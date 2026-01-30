<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Registrar boleta de honorarios</h4>
        <a href="index.php?route=honorarios" class="btn btn-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=honorarios/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Prestador</label>
                    <input type="text" name="provider_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">RUT</label>
                    <input type="text" name="provider_rut" class="form-control" placeholder="12.345.678-9">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Documento</label>
                    <input type="text" name="document_number" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha emisión</label>
                    <input type="date" name="issue_date" class="form-control" value="<?php echo e($today); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Monto bruto</label>
                    <input type="number" step="0.01" name="gross_amount" class="form-control" value="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Retención (%)</label>
                    <input type="number" step="0.01" name="retention_rate" class="form-control" value="13">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="pendiente">Pendiente</option>
                        <option value="pagada">Pagada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha pago</label>
                    <input type="date" name="paid_at" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Notas</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'honorarios/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
