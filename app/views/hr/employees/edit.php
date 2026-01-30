<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Editar trabajador</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=hr/employees/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)$employee['id']; ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">RUT *</label>
                    <input type="text" name="rut" class="form-control" value="<?php echo e($employee['rut'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo e($employee['first_name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apellido *</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo e($employee['last_name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nacionalidad</label>
                    <input type="text" name="nationality" class="form-control" value="<?php echo e($employee['nationality'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="birth_date" class="form-control" value="<?php echo e($employee['birth_date'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado civil</label>
                    <?php $civilStatus = $employee['civil_status'] ?? ''; ?>
                    <select name="civil_status" class="form-select">
                        <option value="" <?php echo $civilStatus === '' ? 'selected' : ''; ?>>Selecciona</option>
                        <option value="soltero" <?php echo $civilStatus === 'soltero' ? 'selected' : ''; ?>>Soltero(a)</option>
                        <option value="casado" <?php echo $civilStatus === 'casado' ? 'selected' : ''; ?>>Casado(a)</option>
                        <option value="conviviente" <?php echo $civilStatus === 'conviviente' ? 'selected' : ''; ?>>Conviviente</option>
                        <option value="divorciado" <?php echo $civilStatus === 'divorciado' ? 'selected' : ''; ?>>Divorciado(a)</option>
                        <option value="viudo" <?php echo $civilStatus === 'viudo' ? 'selected' : ''; ?>>Viudo(a)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e($employee['email'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo e($employee['phone'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="address" class="form-control" value="<?php echo e($employee['address'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Dependientes</label>
                    <input type="number" name="dependents_count" class="form-control" min="0" value="<?php echo e($employee['dependents_count'] ?? 0); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Departamento</label>
                    <select name="department_id" class="form-select">
                        <option value="">Selecciona</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?php echo (int)$department['id']; ?>" <?php echo ((int)($employee['department_id'] ?? 0) === (int)$department['id']) ? 'selected' : ''; ?>>
                                <?php echo e($department['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cargo</label>
                    <select name="position_id" class="form-select">
                        <option value="">Selecciona</option>
                        <?php foreach ($positions as $position): ?>
                            <option value="<?php echo (int)$position['id']; ?>" <?php echo ((int)($employee['position_id'] ?? 0) === (int)$position['id']) ? 'selected' : ''; ?>>
                                <?php echo e($position['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha de ingreso *</label>
                    <input type="date" name="hire_date" class="form-control" value="<?php echo e($employee['hire_date'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha de término</label>
                    <input type="date" name="termination_date" class="form-control" value="<?php echo e($employee['termination_date'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">AFP</label>
                    <select name="pension_fund_id" class="form-select">
                        <option value="">Selecciona</option>
                        <?php foreach ($pensionFunds as $fund): ?>
                            <option value="<?php echo (int)$fund['id']; ?>" <?php echo ((int)($employee['pension_fund_id'] ?? 0) === (int)$fund['id']) ? 'selected' : ''; ?>>
                                <?php echo e($fund['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tasa AFP (%)</label>
                    <input type="number" name="pension_rate" class="form-control" min="0" step="0.01" value="<?php echo e($employee['pension_rate'] ?? 10); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Salud</label>
                    <select name="health_provider_id" class="form-select">
                        <option value="">Selecciona</option>
                        <?php foreach ($healthProviders as $provider): ?>
                            <option value="<?php echo (int)$provider['id']; ?>" <?php echo ((int)($employee['health_provider_id'] ?? 0) === (int)$provider['id']) ? 'selected' : ''; ?>>
                                <?php echo e($provider['name']); ?> (<?php echo e($provider['provider_type']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Plan de salud</label>
                    <input type="text" name="health_plan" class="form-control" value="<?php echo e($employee['health_plan'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tasa Salud (%)</label>
                    <input type="number" name="health_rate" class="form-control" min="0" step="0.01" value="<?php echo e($employee['health_rate'] ?? 7); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Seguro cesantía (%)</label>
                    <input type="number" name="unemployment_rate" class="form-control" min="0" step="0.01" value="<?php echo e($employee['unemployment_rate'] ?? 0.6); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Método de pago</label>
                    <?php $paymentMethod = $employee['payment_method'] ?? ''; ?>
                    <select name="payment_method" class="form-select">
                        <option value="" <?php echo $paymentMethod === '' ? 'selected' : ''; ?>>Selecciona</option>
                        <option value="transferencia" <?php echo $paymentMethod === 'transferencia' ? 'selected' : ''; ?>>Transferencia</option>
                        <option value="efectivo" <?php echo $paymentMethod === 'efectivo' ? 'selected' : ''; ?>>Efectivo</option>
                        <option value="cheque" <?php echo $paymentMethod === 'cheque' ? 'selected' : ''; ?>>Cheque</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Banco</label>
                    <input type="text" name="bank_name" class="form-control" value="<?php echo e($employee['bank_name'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo de cuenta</label>
                    <input type="text" name="bank_account_type" class="form-control" value="<?php echo e($employee['bank_account_type'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">N° de cuenta</label>
                    <input type="text" name="bank_account_number" class="form-control" value="<?php echo e($employee['bank_account_number'] ?? ''); ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Enrolamiento facial</label>
                    <input type="hidden" name="face_descriptor" id="face-descriptor" value="<?php echo e($employee['face_descriptor'] ?? ''); ?>">
                    <div class="border rounded p-3">
                        <div class="fw-semibold mb-2">Captura facial</div>
                        <video id="face-video" width="320" height="240" autoplay muted class="border rounded"></video>
                        <div class="mt-2 d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary" id="face-capture">Actualizar rostro</button>
                            <span class="text-muted" id="face-status"><?php echo !empty($employee['face_descriptor']) ? 'Rostro registrado' : 'Sin captura'; ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <?php $status = $employee['status'] ?? 'activo'; ?>
                        <option value="activo" <?php echo $status === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo $status === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                        <option value="suspendido" <?php echo $status === 'suspendido' ? 'selected' : ''; ?>>Suspendido</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="index.php?route=hr/employees" class="btn btn-light">Cancelar</a>
            </div>
        
    <?php
    $reportTemplate = 'informeIcargaEspanol.php';
    $reportSource = 'hr/employees/edit';
    include __DIR__ . '/../partials/report-download.php';
    ?>
</form>
    </div>
</div>

<script src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    const faceVideo = document.getElementById('face-video');
    const faceCapture = document.getElementById('face-capture');
    const faceDescriptorInput = document.getElementById('face-descriptor');
    const faceStatus = document.getElementById('face-status');
    const faceModelsUrl = 'https://justadudewhohacks.github.io/face-api.js/models';

    async function loadFaceModels() {
        await faceapi.nets.tinyFaceDetector.loadFromUri(faceModelsUrl);
        await faceapi.nets.faceLandmark68Net.loadFromUri(faceModelsUrl);
        await faceapi.nets.faceRecognitionNet.loadFromUri(faceModelsUrl);
    }

    async function startFaceCamera() {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        faceVideo.srcObject = stream;
    }

    async function captureFaceDescriptor() {
        const detection = await faceapi
            .detectSingleFace(faceVideo, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (!detection) {
            faceStatus.textContent = 'No se detectó rostro.';
            return;
        }

        faceDescriptorInput.value = JSON.stringify(Array.from(detection.descriptor));
        faceStatus.textContent = 'Rostro actualizado.';
    }

    loadFaceModels()
        .then(startFaceCamera)
        .catch(() => {
            faceStatus.textContent = 'No se pudo cargar el módulo facial.';
        });

    faceCapture?.addEventListener('click', captureFaceDescriptor);
</script>
