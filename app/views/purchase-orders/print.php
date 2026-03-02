<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Orden de compra #<?php echo (int)($order['id'] ?? 0); ?></title>
    <style>
        :root {
            --doc-blue: #1e40af;
            --doc-blue-soft: #eaf1ff;
            --doc-line: #b9cdf8;
            --doc-text: #1f2937;
            --doc-muted: #64748b;
        }

        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: var(--doc-text); margin: 16px; }
        .print-actions { margin-bottom: 10px; }
        .print-actions button { border: 0; background: var(--doc-blue); color:#fff; border-radius: 6px; padding: 8px 12px; cursor:pointer; }

        .sheet {
            border: 1px solid var(--doc-line);
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
            background: #fff;
        }

        .top {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            border-bottom: 1px solid var(--doc-line);
        }

        .company {
            padding: 12px;
            display: grid;
            grid-template-columns: 80px 1fr;
            gap: 10px;
            align-items: start;
        }

        .company img {
            width: 74px;
            height: 74px;
            object-fit: contain;
            border: 1px solid var(--doc-line);
            padding: 4px;
            background: #fff;
        }

        .company h1 {
            margin: 0;
            color: var(--doc-blue);
            font-size: 20px;
            line-height: 1.1;
        }

        .company .meta {
            margin-top: 4px;
            font-size: 11px;
            color: var(--doc-muted);
            line-height: 1.35;
        }

        .doc-box {
            border-left: 1px solid var(--doc-line);
            padding: 10px;
        }

        .doc-box h2 {
            margin: 0 0 8px;
            text-align: center;
            color: var(--doc-blue);
            font-size: 18px;
            letter-spacing: .03em;
        }

        .doc-table,
        .info-table,
        .detail-table,
        .totals-table,
        .bank-table {
            width: 100%;
            border-collapse: collapse;
        }

        .doc-table td,
        .info-table td,
        .detail-table th,
        .detail-table td,
        .totals-table td,
        .bank-table td {
            border: 1px solid var(--doc-line);
            padding: 6px 7px;
            font-size: 11px;
            vertical-align: top;
        }

        .label { background: var(--doc-blue-soft); color: #1e3a8a; font-weight: 700; width: 18%; }
        .doc-table .label { width: 42%; }

        .section {
            margin: 10px;
        }

        .section-title {
            margin: 0 0 6px;
            padding: 6px 8px;
            font-size: 12px;
            font-weight: 700;
            color: #1e3a8a;
            border: 1px solid var(--doc-line);
            background: var(--doc-blue-soft);
        }

        .detail-table th {
            background: var(--doc-blue-soft);
            color: #1e3a8a;
            font-weight: 700;
            text-align: left;
        }

        .text-end { text-align: right; }
        .totals-wrap { display: flex; justify-content: flex-end; margin-top: 8px; }
        .totals-table { width: 320px; }
        .totals-table tr:last-child td { background: var(--doc-blue-soft); color: #1e3a8a; font-weight: 700; }

        .bottom-grid {
            margin: 10px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .notes-box {
            border: 1px solid var(--doc-line);
            padding: 8px;
            min-height: 110px;
            font-size: 11px;
            line-height: 1.45;
        }

        .signatures {
            margin: 18px 10px 12px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            text-align: center;
            font-size: 11px;
        }

        .sign-line {
            margin-top: 34px;
            border-top: 1px solid #334155;
            padding-top: 6px;
        }

        .footer-note {
            margin: 0 10px 10px;
            color: var(--doc-muted);
            font-size: 10px;
            text-align: center;
        }

        @media print {
            .print-actions { display: none; }
            body { margin: 0; padding: 6mm; }
        }
    </style>
</head>
<body onload="window.print()">
<?php
$companyName = $company['name'] ?? 'Nombre Empresa';
$companyRut = $company['rut'] ?? '';
$companyEmail = $company['email'] ?? '';
$companyPhone = $company['phone'] ?? '';
$companyAddress = trim((string)($company['address'] ?? '') . (empty($company['commune']) ? '' : ', ' . $company['commune']));
$companyGiro = $company['giro'] ?? '';
$companyBank = $company['bank'] ?? '';
$companyAccountType = $company['account_type'] ?? '';
$companyAccountNumber = $company['account_number'] ?? '';
$companySignature = $company['signature'] ?? '';

$supplierName = $order['supplier_name'] ?? '';
$supplierTax = $order['supplier_tax_id'] ?? '';
$supplierContact = $order['supplier_contact_name'] ?? '';
$supplierEmail = $order['supplier_email'] ?? '';
$supplierPhone = $order['supplier_phone'] ?? '';
$supplierAddress = trim((string)($order['supplier_address'] ?? '') . (empty($order['supplier_commune']) ? '' : ', ' . $order['supplier_commune']));
$supplierGiro = $order['supplier_giro'] ?? '';
$supplierCode = $order['supplier_code'] ?? '';

$logoPathValue = $company['logo_color'] ?? 'assets/images/logo.png';
$logoData = '';
if ($logoPathValue !== '') {
    $logoPath = __DIR__ . '/../../../' . ltrim($logoPathValue, '/');
    if (is_file($logoPath)) {
        $raw = @file_get_contents($logoPath);
        if ($raw !== false) {
            $mime = function_exists('mime_content_type') ? (mime_content_type($logoPath) ?: 'image/png') : 'image/png';
            $logoData = 'data:' . $mime . ';base64,' . base64_encode($raw);
        }
    }
}

$rawNotes = (string)($order['notes'] ?? '');
$conditions = '';
$notesBody = $rawNotes;
$marker = "Condiciones de la orden:\n";
if (strpos($rawNotes, $marker) !== false) {
    [$notesBody, $conditions] = explode($marker, $rawNotes, 2);
    $notesBody = trim($notesBody);
    $conditions = trim($conditions);
}

$subtotal = (float)($order['subtotal'] ?? 0);
$total = (float)($order['total'] ?? 0);
$discount = 0.0;
$neto = max(0, $subtotal - $discount);
?>

<div class="print-actions">
    <button type="button" onclick="window.print()">Imprimir</button>
</div>

<div class="sheet">
    <div class="top">
        <div class="company">
            <div>
                <?php if ($logoData !== ''): ?>
                    <img src="<?php echo e($logoData); ?>" alt="Logo empresa">
                <?php endif; ?>
            </div>
            <div>
                <h1><?php echo e($companyName); ?></h1>
                <div class="meta">
                    <?php if ($companyRut !== ''): ?>RUT: <?php echo e($companyRut); ?><br><?php endif; ?>
                    <?php if ($companyGiro !== ''): ?>Giro: <?php echo e($companyGiro); ?><br><?php endif; ?>
                    <?php if ($companyAddress !== ''): ?>Dirección: <?php echo e($companyAddress); ?><br><?php endif; ?>
                    <?php if ($companyEmail !== ''): ?>Email: <?php echo e($companyEmail); ?><?php endif; ?>
                    <?php if ($companyPhone !== ''): ?> · Tel: <?php echo e($companyPhone); ?><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="doc-box">
            <h2>ORDEN DE COMPRA</h2>
            <table class="doc-table">
                <tr><td class="label">N° Orden</td><td><?php echo (int)($order['id'] ?? 0); ?></td></tr>
                <tr><td class="label">Referencia</td><td><?php echo e((string)($order['reference'] ?: 'Sin referencia')); ?></td></tr>
                <tr><td class="label">Fecha Emisión</td><td><?php echo e(format_date($order['order_date'] ?? null)); ?></td></tr>
                <tr><td class="label">Estado</td><td><?php echo e(ucfirst((string)($order['status'] ?? 'pendiente'))); ?></td></tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">DATOS PROVEEDOR</div>
        <table class="info-table">
            <tr>
                <td class="label">Razón Social</td><td><?php echo e($supplierName ?: '—'); ?></td>
                <td class="label">Código</td><td><?php echo e($supplierCode ?: '—'); ?></td>
            </tr>
            <tr>
                <td class="label">RUT</td><td><?php echo e($supplierTax ?: '—'); ?></td>
                <td class="label">Giro</td><td><?php echo e($supplierGiro ?: '—'); ?></td>
            </tr>
            <tr>
                <td class="label">Contacto</td><td><?php echo e($supplierContact ?: '—'); ?></td>
                <td class="label">Email</td><td><?php echo e($supplierEmail ?: '—'); ?></td>
            </tr>
            <tr>
                <td class="label">Teléfono</td><td><?php echo e($supplierPhone ?: '—'); ?></td>
                <td class="label">Dirección</td><td><?php echo e($supplierAddress ?: '—'); ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">DETALLE DE PRODUCTOS / SERVICIOS</div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th style="width:39%;">Detalle</th>
                    <th style="width:9%;" class="text-end">Cant.</th>
                    <th style="width:8%;">UM</th>
                    <th style="width:14%;" class="text-end">Valor Unit.</th>
                    <th style="width:10%;" class="text-end">% Desc.</th>
                    <th style="width:15%;" class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td><?php echo (int)$index + 1; ?></td>
                        <td>
                            <?php echo e($item['product_name'] ?? ''); ?>
                            <?php if (!empty($item['sku'])): ?><br><span style="color:#64748b;">SKU: <?php echo e($item['sku']); ?></span><?php endif; ?>
                        </td>
                        <td class="text-end"><?php echo (int)($item['quantity'] ?? 0); ?></td>
                        <td>UNID</td>
                        <td class="text-end"><?php echo e(format_currency((float)($item['unit_cost'] ?? 0))); ?></td>
                        <td class="text-end">0%</td>
                        <td class="text-end"><?php echo e(format_currency((float)($item['subtotal'] ?? 0))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals-wrap">
            <table class="totals-table">
                <tr><td class="label">Descuento</td><td class="text-end"><?php echo e(format_currency($discount)); ?></td></tr>
                <tr><td class="label">Neto</td><td class="text-end"><?php echo e(format_currency($neto)); ?></td></tr>
                <tr><td class="label">TOTAL</td><td class="text-end"><?php echo e(format_currency($total)); ?></td></tr>
            </table>
        </div>
    </div>

    <div class="bottom-grid">
        <div>
            <div class="section-title">CONDICIONES Y OBSERVACIONES</div>
            <div class="notes-box">
                <strong>Condiciones:</strong><br>
                <?php echo nl2br(e($conditions !== '' ? $conditions : 'Entrega y pago según acuerdo comercial.')); ?>
                <br><br>
                <strong>Observaciones:</strong><br>
                <?php echo nl2br(e($notesBody !== '' ? $notesBody : 'Sin observaciones adicionales.')); ?>
            </div>
        </div>
        <div>
            <div class="section-title">DATOS BANCARIOS EMPRESA</div>
            <table class="bank-table">
                <tr><td class="label">Banco</td><td><?php echo e($companyBank ?: '—'); ?></td></tr>
                <tr><td class="label">Tipo Cuenta</td><td><?php echo e($companyAccountType ?: '—'); ?></td></tr>
                <tr><td class="label">N° Cuenta</td><td><?php echo e($companyAccountNumber ?: '—'); ?></td></tr>
                <tr><td class="label">Email pago</td><td><?php echo e($companyEmail ?: '—'); ?></td></tr>
            </table>
        </div>
    </div>

    <div class="signatures">
        <div><div class="sign-line">Solicitado por</div></div>
        <div><div class="sign-line">Aprobado por</div></div>
        <div><div class="sign-line">Recibido/Aceptado proveedor</div></div>
    </div>

    <?php if ($companySignature !== ''): ?>
        <div class="footer-note"><?php echo nl2br(e($companySignature)); ?></div>
    <?php endif; ?>
</div>
</body>
</html>
