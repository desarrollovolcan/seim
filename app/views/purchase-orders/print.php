<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Orden de compra #<?php echo (int)($order['id'] ?? 0); ?></title>
    <style>
        :root {
            --primary: #1e40af;
            --primary-2: #2563eb;
            --surface: #eef2f7;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --soft: #f4f7fb;
        }

        html, body, .invoice {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--surface);
            margin: 0;
            padding: 34px 0;
            color: var(--text);
        }

        .invoice {
            width: 100%;
            max-width: 920px;
            margin: auto;
            background: var(--card);
            padding: 30px 34px 34px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            box-sizing: border-box;
        }

        .print-actions {
            margin-bottom: 14px;
            text-align: right;
        }

        .print-actions button {
            background: var(--primary);
            color: #fff;
            border: 0;
            border-radius: 6px;
            padding: 8px 14px;
            cursor: pointer;
            font-weight: 600;
        }

        .top-bar {
            margin-top: 4px;
            background: var(--primary);
            color: white;
            padding: 18px 22px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .top-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .top-left img {
            width: 58px;
            height: 58px;
            object-fit: contain;
            border-radius: 6px;
            background: #fff;
            padding: 4px;
        }

        .top-bar h1 {
            margin: 0;
            font-size: 27px;
            letter-spacing: 2px;
            text-transform: uppercase;
            line-height: 1;
        }

        .company-data {
            font-size: 12px;
            text-align: right;
            line-height: 1.35;
        }

        .section {
            margin-top: 24px;
        }

        .section-title {
            margin: 0 0 10px;
            font-size: 14px;
            color: #1d3d9f;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .flex {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .box {
            background: var(--soft);
            padding: 16px;
            border-left: 5px solid var(--primary-2);
            border-radius: 6px;
            width: 100%;
            min-width: 0;
            font-size: 13px;
            line-height: 1.45;
        }

        .box strong.title {
            display: inline-block;
            margin-bottom: 6px;
            color: #1d3d9f;
            text-transform: uppercase;
            letter-spacing: .03em;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            font-size: 13px;
            table-layout: fixed;
        }

        thead {
            background: var(--primary-2);
            color: white;
        }

        th, td {
            padding: 10px;
            text-align: left;
            vertical-align: top;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        tbody tr {
            border-bottom: 1px solid #d6deea;
        }

        tbody tr:nth-child(even) {
            background: #f9fbff;
        }

        .text-end {
            text-align: right;
            white-space: nowrap;
        }

        .col-description { width: 44%; }
        .col-qty { width: 10%; }
        .col-unit { width: 16%; }
        .col-discount { width: 12%; }
        .col-total { width: 18%; }

        .summary {
            margin-top: 24px;
            width: 360px;
            margin-left: auto;
            font-size: 13px;
        }

        .summary div {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
        }

        .summary .total {
            font-weight: bold;
            font-size: 15px;
            background: var(--primary-2);
            color: white;
            padding: 10px 12px;
            border-radius: 4px;
            margin-top: 4px;
        }

        .bank-info, .notes {
            margin-top: 22px;
            font-size: 12px;
            background: var(--soft);
            padding: 16px;
            border-radius: 6px;
            line-height: 1.45;
        }


        .signatures {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            text-align: center;
            font-size: 11px;
            color: var(--muted);
        }

        .signatures .line {
            border-top: 1px solid #c7cfdd;
            margin-top: 30px;
            padding-top: 6px;
        }

        .footer {
            margin-top: 34px;
            text-align: center;
            font-size: 11px;
            color: var(--muted);
            border-top: 1px solid #d8dbe3;
            padding-top: 12px;
            line-height: 1.4;
        }

        @page {
            size: Letter portrait;
            margin: 10mm;
        }

        @media print {
            body {
                background: var(--surface);
                padding: 0;
                margin: 0;
            }

            .invoice {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 16px;
                box-shadow: none;
                border-radius: 0;
                break-inside: avoid;
            }

            table, tr, td, th {
                page-break-inside: avoid;
            }

            .top-bar {
                background: var(--primary) !important;
            }
            thead { background: var(--primary-2) !important; }
            .summary .total { background: var(--primary-2) !important; }

            .print-actions { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
<?php
$companyName = $company['name'] ?? 'Empresa';
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
$discount = 0.0;
$neto = max(0, $subtotal - $discount);
$ivaRate = 0.19;
$iva = $neto * $ivaRate;
$total = $neto + $iva;

$issueDate = format_date($order['order_date'] ?? null);
$dueDate = format_date(date('Y-m-d', strtotime((string)($order['order_date'] ?? date('Y-m-d')) . ' +10 days')));
$executive = Auth::user()['name'] ?? 'Ejecutivo asignado';
?>

<div class="invoice">
    <div class="print-actions">
        <button type="button" onclick="window.print()">Imprimir</button>
    </div>

    <div class="top-bar">
        <div class="top-left">
            <?php if ($logoData !== ''): ?>
                <img src="<?php echo e($logoData); ?>" alt="Logo empresa">
            <?php endif; ?>
            <div>
                <h1>Orden de compra</h1>
                <div>Folio Nº: <?php echo str_pad((string)(int)($order['id'] ?? 0), 6, '0', STR_PAD_LEFT); ?></div>
            </div>
        </div>
        <div class="company-data">
            <strong><?php echo e(strtoupper($companyName)); ?></strong><br>
            <?php if ($companyRut !== ''): ?>RUT: <?php echo e($companyRut); ?><br><?php endif; ?>
            <?php if ($companyGiro !== ''): ?>Giro: <?php echo e($companyGiro); ?><br><?php endif; ?>
            <?php if ($companyAddress !== ''): ?>Dirección: <?php echo e($companyAddress); ?><br><?php endif; ?>
            <?php if ($companyEmail !== ''): ?><?php echo e($companyEmail); ?><br><?php endif; ?>
            <?php if ($companyPhone !== ''): ?><?php echo e($companyPhone); ?><?php endif; ?>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Datos generales</h3>
        <div class="flex">
        <div class="box">
            <strong class="title">Facturar a</strong><br>
            <?php echo e($supplierName ?: 'Proveedor sin nombre'); ?><br>
            RUT: <?php echo e($supplierTax ?: '—'); ?><br>
            Giro: <?php echo e($supplierGiro ?: '—'); ?><br>
            Dirección: <?php echo e($supplierAddress ?: '—'); ?><br>
            Código proveedor: <?php echo e($supplierCode ?: '—'); ?><br>
            Contacto: <?php echo e($supplierContact ?: '—'); ?><br>
            Email: <?php echo e($supplierEmail ?: '—'); ?><br>
            Teléfono: <?php echo e($supplierPhone ?: '—'); ?>
        </div>

        <div class="box">
            <strong class="title">Información de Orden</strong><br>
            Fecha emisión: <?php echo e($issueDate); ?><br>
            Fecha vencimiento: <?php echo e($dueDate); ?><br>
            Forma de pago: Transferencia bancaria<br>
            Moneda: CLP<br>
            Orden de compra: <?php echo e((string)($order['reference'] ?: ('OC-' . (int)($order['id'] ?? 0)))); ?><br>
            Ejecutivo: <?php echo e($executive); ?><br>
            Estado: <?php echo e(ucfirst((string)($order['status'] ?? 'pendiente'))); ?>
        </div>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Detalle de productos</h3>
        <table>
            <thead>
            <tr>
                <th class="col-description">Descripción</th>
                <th class="text-end col-qty">Cant.</th>
                <th class="text-end col-unit">Valor Unitario</th>
                <th class="text-end col-discount">Desc.</th>
                <th class="text-end col-total">Total</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php echo e($item['product_name'] ?? 'Ítem'); ?>
                        <?php if (!empty($item['sku'])): ?>
                            <br><small style="color:#64748b;">SKU: <?php echo e($item['sku']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-end"><?php echo (int)($item['quantity'] ?? 0); ?></td>
                    <td class="text-end"><?php echo e(format_currency((float)($item['unit_cost'] ?? 0))); ?></td>
                    <td class="text-end"><?php echo e(format_currency(0)); ?></td>
                    <td class="text-end"><?php echo e(format_currency((float)($item['subtotal'] ?? 0))); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="summary">
        <div><span>Subtotal:</span><span><?php echo e(format_currency($subtotal)); ?></span></div>
        <div><span>Descuentos:</span><span>-<?php echo e(format_currency($discount)); ?></span></div>
        <div><span>Neto:</span><span><?php echo e(format_currency($neto)); ?></span></div>
        <div><span>IVA (19%):</span><span><?php echo e(format_currency($iva)); ?></span></div>
        <div class="total"><span>Total a pagar:</span><span><?php echo e(format_currency($total)); ?></span></div>
    </div>

    <div class="bank-info">
        <strong>Datos Bancarios:</strong><br><br>
        Banco: <?php echo e($companyBank ?: '—'); ?><br>
        Tipo de Cuenta: <?php echo e($companyAccountType ?: '—'); ?><br>
        Nº Cuenta: <?php echo e($companyAccountNumber ?: '—'); ?><br>
        RUT: <?php echo e($companyRut ?: '—'); ?><br>
        Email confirmación pago: <?php echo e($companyEmail ?: '—'); ?>
    </div>

    <div class="notes">
        <strong>Observaciones:</strong><br><br>
        <?php if ($conditions !== ''): ?>
            - <?php echo nl2br(e($conditions)); ?><br>
        <?php endif; ?>
        <?php if ($notesBody !== ''): ?>
            - <?php echo nl2br(e($notesBody)); ?><br>
        <?php else: ?>
            - Documento válido como respaldo contable interno.<br>
        <?php endif; ?>
        - Consulte con su ejecutivo para validar condiciones especiales.
    </div>


    <div class="signatures">
        <div><div class="line">Solicitado por</div></div>
        <div><div class="line">Revisado/Aprobado</div></div>
        <div><div class="line">Recepción proveedor</div></div>
    </div>

    <div class="footer">
        <?php echo e($companyName); ?> · Documento generado electrónicamente<br>
        <?php if ($companySignature !== ''): ?><?php echo nl2br(e($companySignature)); ?><br><?php endif; ?>
        No requiere firma manuscrita
    </div>
</div>

</body>
</html>
