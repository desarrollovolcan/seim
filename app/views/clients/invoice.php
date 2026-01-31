<?php
$companyName = $company['name'] ?? 'Empresa';
$companyRut = $company['rut'] ?? '';
$companyEmail = $company['email'] ?? '';
$companyLogoColor = $company['logo_color'] ?? 'assets/images/logo.png';
$companyLogoBlack = $company['logo_black'] ?? 'assets/images/logo-black.png';
$companyLogoDataUri = '';
if ($companyLogoColor !== '') {
    $logoFilePath = __DIR__ . '/../../../' . ltrim($companyLogoColor, '/');
    if (is_file($logoFilePath)) {
        $logoContents = @file_get_contents($logoFilePath);
        if ($logoContents !== false) {
            $mimeType = function_exists('mime_content_type')
                ? (mime_content_type($logoFilePath) ?: 'image/png')
                : 'image/png';
            $companyLogoDataUri = 'data:' . $mimeType . ';base64,' . base64_encode($logoContents);
        }
    }
}
$invoiceStatus = $invoice['estado'] ?? 'pendiente';
$invoiceNumber = $invoice['numero'] ?? '';
$issueDate = $invoice['fecha_emision'] ?? '';
$dueDate = $invoice['fecha_vencimiento'] ?? '';
$subtotal = $invoice['subtotal'] ?? 0;
$taxes = $invoice['impuestos'] ?? 0;
$total = $invoice['total'] ?? 0;
$clientName = $client['name'] ?? '';
$clientAddress = $client['address'] ?? '';
$clientPhone = $client['phone'] ?? '';
$clientEmail = $client['email'] ?? '';
$badgeClass = $invoiceStatus === 'pagada' ? 'success' : ($invoiceStatus === 'vencida' ? 'danger' : 'warning');
$itemsData = [];
foreach ($items as $item) {
    $itemsData[] = [
        'descripcion' => $item['descripcion'] ?? '',
        'cantidad' => $item['cantidad'] ?? '',
        'precio_unitario_formatted' => format_currency((float)($item['precio_unitario'] ?? 0)),
        'total_formatted' => format_currency((float)($item['total'] ?? 0)),
    ];
}

$invoiceData = [
    'invoice' => [
        'numero' => $invoiceNumber,
        'fecha_emision' => $issueDate,
        'fecha_vencimiento' => $dueDate,
        'subtotal_formatted' => format_currency((float)$subtotal),
        'impuestos_formatted' => format_currency((float)$taxes),
        'total_formatted' => format_currency((float)$total),
    ],
    'client' => [
        'name' => $clientName,
        'address' => $clientAddress,
        'email' => $clientEmail,
    ],
    'company' => [
        'name' => $companyName,
        'rut' => $companyRut,
        'email' => $companyEmail,
        'logo' => $companyLogoDataUri,
    ],
    'items' => $itemsData,
];
$portalToken = $client['portal_token'] ?? '';
?>

<div class="row justify-content-center">
    <div class="col-xxl-12">
        <div class="row">
            <div class="col-xl-9">
                <div class="card" id="invoice-document">
                    <div class="card-body px-4">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                            <div class="auth-brand mb-0">
                                <a href="index.php" class="logo-dark">
                                    <img src="<?php echo e($companyLogoBlack); ?>" alt="logo" height="24">
                                </a>
                                <a href="index.php" class="logo-light">
                                    <img src="<?php echo e($companyLogoColor); ?>" alt="logo" height="24">
                                </a>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-<?php echo $badgeClass; ?>-subtle text-<?php echo $badgeClass; ?> mb-2 fs-xs px-2 py-1">
                                    <?php echo e(ucfirst($invoiceStatus)); ?>
                                </span>
                                <h4 class="fw-bold text-dark m-0">Factura #<?php echo e($invoiceNumber); ?></h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <h6 class="text-uppercase text-muted mb-2">Emisor</h6>
                                <p class="mb-1 fw-semibold"><?php echo e($companyName); ?></p>
                                <?php if ($companyRut !== ''): ?>
                                    <p class="text-muted mb-1">RUT: <?php echo e($companyRut); ?></p>
                                <?php endif; ?>
                                <?php if ($companyEmail !== ''): ?>
                                    <p class="text-muted mb-0">Email: <?php echo e($companyEmail); ?></p>
                                <?php endif; ?>
                                <div class="mt-4">
                                    <h6 class="text-uppercase text-muted">Fecha emisión</h6>
                                    <p class="mb-0 fw-medium"><?php echo e($issueDate); ?></p>
                                </div>
                            </div>

                            <div class="col-4">
                                <h6 class="text-uppercase text-muted mb-2">Cliente</h6>
                                <p class="mb-1 fw-semibold"><?php echo e($clientName); ?></p>
                                <?php if ($clientAddress !== ''): ?>
                                    <p class="text-muted mb-1"><?php echo e($clientAddress); ?></p>
                                <?php endif; ?>
                                <?php if ($clientPhone !== ''): ?>
                                    <p class="text-muted mb-1">Tel: <?php echo e($clientPhone); ?></p>
                                <?php endif; ?>
                                <?php if ($clientEmail !== ''): ?>
                                    <p class="text-muted mb-0">Email: <?php echo e($clientEmail); ?></p>
                                <?php endif; ?>
                                <div class="mt-4">
                                    <h6 class="text-uppercase text-muted">Fecha vencimiento</h6>
                                    <p class="mb-0 fw-medium"><?php echo e($dueDate); ?></p>
                                </div>
                            </div>

                            <div class="col-4 text-end"></div>
                        </div>

                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-nowrap text-center align-middle">
                                <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                    <tr class="text-uppercase fs-xxs">
                                        <th style="width: 50px;">#</th>
                                        <th class="text-start">Detalle</th>
                                        <th>Qty</th>
                                        <th>Precio unitario</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr>
                                            <td><?php echo sprintf('%02d', $index + 1); ?></td>
                                            <td class="text-start">
                                                <strong><?php echo e($item['descripcion'] ?? ''); ?></strong>
                                            </td>
                                            <td><?php echo e($item['cantidad'] ?? ''); ?></td>
                                            <td><?php echo e(format_currency((float)($item['precio_unitario'] ?? 0))); ?></td>
                                            <td class="text-end"><?php echo e(format_currency((float)($item['total'] ?? 0))); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end">
                            <table class="table w-auto table-borderless text-end">
                                <tbody>
                                    <tr>
                                        <td class="fw-medium">Subtotal</td>
                                        <td><?php echo e(format_currency((float)$subtotal)); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Impuestos</td>
                                        <td><?php echo e(format_currency((float)$taxes)); ?></td>
                                    </tr>
                                    <tr class="border-top pt-2 fs-5 fw-bold">
                                        <td>Total</td>
                                        <td><?php echo e(format_currency((float)$total)); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-lg-4 mt-2 bg-light bg-opacity-50 rounded px-3 py-2">
                            <p class="mb-0 text-muted">
                                <strong>Nota:</strong> Gracias por tu preferencia. Para consultas escribe a
                                <a href="mailto:<?php echo e($companyEmail); ?>" class="fw-medium"><?php echo e($companyEmail); ?></a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 d-print-none">
                <div class="card card-top-sticky">
                    <div class="card-body">
                        <div class="justify-content-center d-flex flex-column gap-2">
                            <a href="index.php?route=clients/portal&token=<?php echo urlencode($portalToken); ?>" class="btn btn-light">
                                <i class="ti ti-arrow-left me-1"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
