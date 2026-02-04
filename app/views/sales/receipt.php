<?php
$companyLogo = $company['logo_black'] ?? $company['logo_color'] ?? '';
$companyLogoDataUri = '';
if ($companyLogo !== '') {
    $logoFilePath = __DIR__ . '/../../../' . ltrim($companyLogo, '/');
    if (is_file($logoFilePath)) {
        $logoContents = @file_get_contents($logoFilePath);
        if ($logoContents !== false) {
            $mimeType = mime_content_type($logoFilePath) ?: 'image/png';
            $companyLogoDataUri = 'data:' . $mimeType . ';base64,' . base64_encode($logoContents);
        }
    }
}
$totalUnits = array_sum(array_map(static fn(array $item) => (int)($item['quantity'] ?? 0), $items));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Voucher de venta</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 6mm;
        }
        * {
            box-sizing: border-box;
            color: #000 !important;
        }
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            background: #fff;
        }
        .receipt {
            width: 72mm;
            margin: 0 auto;
        }
        .receipt + .receipt {
            margin-top: 10mm;
        }
        .center {
            text-align: center;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #000;
            margin-bottom: 6px;
        }
        .logo {
            width: 30mm;
            text-align: left;
        }
        .logo img {
            max-width: 100%;
            max-height: 22mm;
            object-fit: contain;
            filter: grayscale(100%);
        }
        .brand-info {
            flex: 1;
            font-size: 10px;
            line-height: 1.3;
        }
        .title {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px 12px;
            font-size: 10px;
            margin-bottom: 6px;
        }
        .meta span {
            display: block;
            word-break: break-word;
        }
        .meta span {
            display: block;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
        }
        .items th,
        .items td {
            padding: 4px 0;
            vertical-align: top;
        }
        .items th {
            text-align: left;
            border-bottom: 1px solid #000;
        }
        .items td:last-child,
        .items th:last-child {
            text-align: right;
        }
        .totals {
            width: 100%;
            margin-top: 6px;
        }
        .totals td {
            padding: 2px 0;
        }
        .totals td:last-child {
            text-align: right;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            gap: 8px;
        }
        .copy-label {
            font-size: 10px;
            text-align: right;
        }
        .page-break {
            page-break-after: always;
        }
        @media print {
            .page-break:last-child {
                page-break-after: auto;
            }
        }
    </style>
</head>
<body onload="window.print()">
<?php for ($copy = 1; $copy <= $copies; $copy++): ?>
    <div class="receipt<?php echo $copy < $copies ? ' page-break' : ''; ?>">
        <div class="copy-label">Copia <?php echo $copy; ?> de <?php echo $copies; ?></div>
        <div class="brand">
            <?php if ($companyLogoDataUri !== ''): ?>
                <div class="logo">
                    <img src="<?php echo e($companyLogoDataUri); ?>" alt="Logo">
                </div>
            <?php endif; ?>
            <div class="brand-info">
                <div class="title">COMPROBANTE</div>
                <strong><?php echo e($company['name'] ?? ''); ?></strong>
                <?php if (!empty($company['rut'])): ?>
                    <div>RUT: <?php echo e($company['rut']); ?></div>
                <?php endif; ?>
                <?php if (!empty($company['giro'])): ?>
                    <div><?php echo e($company['giro']); ?></div>
                <?php endif; ?>
                <?php if (!empty($company['address']) || !empty($company['commune'])): ?>
                    <div>
                        <?php echo e($company['address'] ?? ''); ?>
                        <?php if (!empty($company['commune'])): ?>
                            <?php echo $company['address'] ? ' · ' : ''; ?><?php echo e($company['commune']); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($company['phone']) || !empty($company['email'])): ?>
                    <div>
                        <?php echo e($company['phone'] ?? ''); ?>
                        <?php if (!empty($company['phone']) && !empty($company['email'])): ?>
                            ·
                        <?php endif; ?>
                        <?php echo e($company['email'] ?? ''); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="meta">
            <span><strong>N° venta:</strong> <?php echo e($sale['numero'] ?? ''); ?></span>
            <span><strong>Fecha:</strong> <?php echo e(format_date($sale['sale_date'] ?? null)); ?></span>
            <span><strong>Canal:</strong> <?php echo e(strtoupper($sale['channel'] ?? 'POS')); ?></span>
            <span><strong>Estado:</strong> <?php echo e($sale['status'] ?? 'pagado'); ?></span>
            <span><strong>Cliente:</strong> <?php echo e($sale['client_name'] ?? 'Consumidor final'); ?></span>
            <span><strong>Ítems:</strong> <?php echo count($items); ?> (<?php echo $totalUnits; ?> uds)</span>
        </div>
        <div class="divider"></div>
        <table class="items">
            <thead>
                <tr>
                    <th>Detalle</th>
                    <th>Cant.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php echo e($item['product_name'] ?? 'Producto'); ?>
                            <?php if (!empty($item['sku'])): ?>
                                <br><small>#<?php echo e($item['sku']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo (int)($item['quantity'] ?? 0); ?></td>
                        <td><?php echo e(format_currency((float)($item['subtotal'] ?? 0))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="divider"></div>
        <table class="totals">
            <tr>
                <td>Subtotal</td>
                <td><?php echo e(format_currency((float)($sale['subtotal'] ?? 0))); ?></td>
            </tr>
            <tr>
                <td>Descuento</td>
                <td><?php echo e(format_currency((float)($sale['discount_total'] ?? 0))); ?></td>
            </tr>
            <tr>
                <td>Impuestos</td>
                <td><?php echo e(format_currency((float)($sale['tax'] ?? 0))); ?></td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong><?php echo e(format_currency((float)($sale['total'] ?? 0))); ?></strong></td>
            </tr>
        </table>
        <?php if (!empty($sale['notes'])): ?>
            <div class="divider"></div>
            <div><strong>Notas:</strong> <?php echo nl2br(e($sale['notes'] ?? '')); ?></div>
        <?php endif; ?>
        <div class="divider"></div>
        <div class="summary">
            <span>Gracias por su compra.</span>
            <span><?php echo e(date('H:i')); ?></span>
        </div>
    </div>
<?php endfor; ?>
</body>
</html>
