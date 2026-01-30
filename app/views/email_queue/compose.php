<div class="card">
    <div class="card-body">
        <form method="post" action="index.php?route=email-queue/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Cliente</label>
                    <select name="client_id" class="form-select">
                        <option value="">Selecciona cliente</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['id']; ?>"><?php echo e($client['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Plantilla</label>
                    <select name="template_id" class="form-select">
                        <option value="">Sin plantilla</option>
                        <?php foreach ($templates as $template): ?>
                            <option value="<?php echo $template['id']; ?>"><?php echo e($template['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="type" class="form-select">
                        <option value="informativa">Informativa</option>
                        <option value="cobranza">Cobranza</option>
                        <option value="pago">Pago</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="pending">Pendiente</option>
                        <option value="sent">Enviado</option>
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
                <div class="col-md-6 mb-3">
                    <label class="form-label">Programar envío</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=email-queue" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
