<div class="card">
    <div class="card-body">
        <form method="post" action="index.php?route=email-templates/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="type" class="form-select">
                        <option value="informativa">Informativa</option>
                        <option value="cobranza">Cobranza</option>
                        <option value="pago">Pago</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Asunto</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Cuerpo HTML</label>
                    <textarea name="body_html" class="form-control" rows="8"></textarea>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=email-templates" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'email_templates/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
