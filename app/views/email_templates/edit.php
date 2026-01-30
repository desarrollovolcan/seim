<div class="card">
    <div class="card-body">
        <form method="post" action="index.php?route=email-templates/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo $template['id']; ?>">
            <div class="mb-3">
                <?php echo render_id_badge($template['id'] ?? null); ?>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($template['name']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="type" class="form-select">
                        <option value="informativa" <?php echo $template['type'] === 'informativa' ? 'selected' : ''; ?>>Informativa</option>
                        <option value="cobranza" <?php echo $template['type'] === 'cobranza' ? 'selected' : ''; ?>>Cobranza</option>
                        <option value="pago" <?php echo $template['type'] === 'pago' ? 'selected' : ''; ?>>Pago</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Asunto</label>
                    <input type="text" name="subject" class="form-control" value="<?php echo e($template['subject']); ?>" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Cuerpo HTML</label>
                    <textarea name="body_html" class="form-control" rows="8"><?php echo e($template['body_html']); ?></textarea>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=email-templates" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Vista previa</h4>
    </div>
    <div class="card-body">
        <form method="get" action="index.php" class="row g-3 align-items-end">
            <input type="hidden" name="route" value="email-templates/preview">
            <input type="hidden" name="template_id" value="<?php echo $template['id']; ?>">
            <div class="col-md-6">
                <label class="form-label">Ver como cliente</label>
                <select name="client_id" class="form-select">
                    <option value="">Selecciona cliente</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client['id']; ?>"><?php echo e($client['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary">Ver vista previa</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'email_templates/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
