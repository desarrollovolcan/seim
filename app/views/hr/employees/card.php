<?php
$companyName = $currentCompany['name'] ?? 'Empresa';
$employeeName = trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? ''));
$employeeRut = $employee['rut'] ?? '';
$qrToken = $employee['qr_token'] ?? '';
?>
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Credencial de trabajador</h4>
                <div class="text-muted"><?php echo e($companyName); ?></div>
            </div>
            <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
        </div>
        <div class="border rounded p-3 d-flex align-items-center gap-4" style="max-width: 520px;">
            <div>
                <div class="fw-semibold"><?php echo e($employeeName); ?></div>
                <div class="text-muted">RUT: <?php echo e($employeeRut); ?></div>
                <div class="text-muted">Cargo: <?php echo e($employee['position_name'] ?? ''); ?></div>
                <div class="text-muted">Departamento: <?php echo e($employee['department_name'] ?? ''); ?></div>
            </div>
            <div class="text-center">
                <div id="qr-code"></div>
                <div class="text-muted mt-2">QR de marcación</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById('qr-code'), {
        text: <?php echo json_encode($qrToken); ?>,
        width: 140,
        height: 140
    });
</script>
