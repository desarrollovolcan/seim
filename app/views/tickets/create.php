<div class="card">
    <div class="card-body">
        <form method="post" action="index.php?route=tickets/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Cliente</label>
                    <select name="client_id" class="form-select" required>
                        <option value="">Selecciona un cliente</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo (int)$client['id']; ?>" <?php echo (int)($selectedClientId ?? 0) === (int)$client['id'] ? 'selected' : ''; ?>>
                                <?php echo e($client['name'] ?? ''); ?> · <?php echo e($client['email'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Prioridad</label>
                    <select name="priority" class="form-select">
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Asignar a</label>
                    <select name="assigned_user_id" class="form-select">
                        <option value="">Sin asignar</option>
                        <?php foreach ($users ?? [] as $user): ?>
                            <option value="<?php echo (int)$user['id']; ?>"><?php echo e($user['name'] ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Asunto</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="index.php?route=tickets" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Crear ticket</button>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'tickets/create';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>
