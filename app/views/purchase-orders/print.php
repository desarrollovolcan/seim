<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Orden de compra #<?php echo (int)($order['id'] ?? 0); ?></title>
    <style>
        :root {
            --primary: #1e40af;
            --primary-soft: #eaf1ff;
            --line: #9fb8f5;
            --text: #1f2937;
            --muted: #64748b;
        }

        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: var(--text); margin: 14px; background: #fff; }

        .print-actions { margin-bottom: 8px; }
        .print-actions button { background: var(--primary); color: #fff; border: 0; border-radius: 4px; padding: 7px 12px; cursor: pointer; }

        .doc {
            width: 100%;
            max-width: 860px;
            margin: 0 auto;
            border: 1px solid var(--line);
        }

        .header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid var(--line);
        }

        .header-left, .header-right { padding: 8px; min-height: 88px; }
        .header-right { border-left: 1px solid var(--line); }

        .brand { display: grid; grid-template-columns: 66px 1fr; gap: 8px; }
        .brand img { width: 62px; height: 62px; object-fit: contain; border: 1px solid var(--line); padding: 2px; }
        .company-title { font-size: 22px; font-weight: 700; letter-spacing: .02em; color: var(--primary); margin: 0 0 3px; text-transform: uppercase; }
        .company-meta { font-size: 10px; line-height: 1.25; color: var(--muted); }

        .doc-title { font-size: 32px; line-height: 1; margin: 2px 0 8px; color: var(--primary); font-weight: 800; text-transform: uppercase; text-align: right; }
        .doc-info { width: 100%; border-collapse: collapse; }
        .doc-info td { border: 1px solid var(--line); padding: 4px 6px; font-size: 10px; }
        .doc-info td.label { background: var(--primary-soft); color: #1d3d9f; font-weight: 700; width: 42%; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0; border-bottom: 1px solid var(--line); }
        .panel { border-right: 1px solid var(--line); }
        .panel:last-child { border-right: 0; }

        .panel-title {
            background: var(--primary);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 4px 6px;
        }

        .panel table { width: 100%; border-collapse: collapse; }
        .panel td { border-top: 1px solid var(--line); padding: 4px 6px; font-size: 10px; }
        .panel td.label { width: 35%; background: #f7faff; color: #1d3d9f; font-weight: 700; }

        .triplet {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            border-bottom: 1px solid var(--line);
        }
        .triplet > div { border-right: 1px solid var(--line); }
        .triplet > div:last-child { border-right: 0; }

        .detail { border-bottom: 1px solid var(--line); }
        .detail table { width: 100%; border-collapse: collapse; }
        .detail th, .detail td { border: 1px solid var(--line); padding: 4px 5px; font-size: 10px; }
        .detail th { background: var(--primary-soft); color: #1d3d9f; text-transform: uppercase; font-weight: 700; }
        .text-end { text-align: right; }

        .bottom {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 0;
        }
        .notes { border-right: 1px solid var(--line); }
        .notes-box { min-height: 126px; padding: 6px; font-size: 10px; line-height: 1.35; }

        .totals table { width: 100%; border-collapse: collapse; }
        .totals td { border: 1px solid var(--line); padding: 4px 6px; font-size: 10px; }
        .totals td.label { background: #f7faff; color: #1d3d9f; font-weight: 700; }
        .totals tr.total td { background: var(--primary-soft); font-size: 11px; font-weight: 700; }

        .signatures { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; padding: 10px 8px 12px; border-top: 1px solid var(--line); }
        .signatures .line { border-top: 1px solid #334155; margin-top: 24px; text-align: center; font-size: 10px; padding-top: 4px; }

        .footer { font-size: 9px; color: var(--muted); text-align: center; padding: 0 8px 8px; }

        @media print {
            .print-actions { display: none; }
            body { margin: 0; padding: 5mm; }
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
$total = (float)($order['total'] ?? 0);
$discount = 0.0;
$neto = max(0, $subtotal - $discount);
?>

<div class="print-actions">
    <button type="button" onclick="window.print()">Imprimir</button>
</div>

<div class="doc">
    <div class="header">
        <div class="header-left">
            <div class="brand">
                <div><?php if ($logoData !== ''): ?><img src="<?php echo e($logoData); ?>" alt="logo"><?php endif; ?></div>
                <div>
                    <h1 class="company-title"><?php echo e($companyName); ?></h1>
                    <div class="company-meta">
                        <?php if ($companyRut !== ''): ?>RUT: <?php echo e($companyRut); ?><br><?php endif; ?>
                        <?php if ($companyGiro !== ''): ?>Giro: <?php echo e($companyGiro); ?><br><?php endif; ?>
                        <?php if ($companyAddress !== ''): ?><?php echo e($companyAddress); ?><br><?php endif; ?>
                        <?php if ($companyEmail !== ''): ?><?php echo e($companyEmail); ?><?php endif; ?>
                        <?php if ($companyPhone !== ''): ?> · <?php echo e($companyPhone); ?><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">ORDEN DE COMPRA</div>
            <table class="doc-info">
                <tr><td class="label">N° OC</td><td><?php echo (int)($order['id'] ?? 0); ?></td></tr>
                <tr><td class="label">Referencia</td><td><?php echo e((string)($order['reference'] ?: 'Sin referencia')); ?></td></tr>
                <tr><td class="label">Fecha</td><td><?php echo e(format_date($order['order_date'] ?? null)); ?></td></tr>
                <tr><td class="label">Estado</td><td><?php echo e(ucfirst((string)($order['status'] ?? 'pendiente'))); ?></td></tr>
            </table>
        </div>
    </div>

    <div class="grid-2">
        <div class="panel">
            <div class="panel-title">Proveedor</div>
            <table>
                <tr><td class="label">Razón social</td><td><?php echo e($supplierName ?: '—'); ?></td></tr>
                <tr><td class="label">RUT</td><td><?php echo e($supplierTax ?: '—'); ?></td></tr>
                <tr><td class="label">Código</td><td><?php echo e($supplierCode ?: '—'); ?></td></tr>
                <tr><td class="label">Dirección</td><td><?php echo e($supplierAddress ?: '—'); ?></td></tr>
            </table>
        </div>
        <div class="panel">
            <div class="panel-title">Enviar a</div>
            <table>
                <tr><td class="label">Contacto</td><td><?php echo e($supplierContact ?: '—'); ?></td></tr>
                <tr><td class="label">Email</td><td><?php echo e($supplierEmail ?: '—'); ?></td></tr>
                <tr><td class="label">Teléfono</td><td><?php echo e($supplierPhone ?: '—'); ?></td></tr>
                <tr><td class="label">Giro</td><td><?php echo e($supplierGiro ?: '—'); ?></td></tr>
            </table>
        </div>
    </div>

    <div class="triplet">
        <div><div class="panel-title">Solicitante</div><div style="padding:6px;font-size:10px;"><?php echo e($companyName); ?></div></div>
        <div><div class="panel-title">Enviar vía</div><div style="padding:6px;font-size:10px;">Correo electrónico</div></div>
        <div><div class="panel-title">Condiciones de envío</div><div style="padding:6px;font-size:10px;">Según acuerdo comercial vigente</div></div>
    </div>

    <div class="detail">
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width:6%;">#</th>
                    <th style="width:42%;">Descripción</th>
                    <th style="width:8%;" class="text-end">Cant.</th>
                    <th style="width:10%;">Precio Unit.</th>
                    <th style="width:8%;" class="text-end">% Dcto</th>
                    <th style="width:16%;" class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td><?php echo (int)$index + 1; ?></td>
                        <td><?php echo e($item['product_name'] ?? ''); ?><?php if (!empty($item['sku'])): ?><br><span style="color:#64748b;">SKU: <?php echo e($item['sku']); ?></span><?php endif; ?></td>
                        <td class="text-end"><?php echo (int)($item['quantity'] ?? 0); ?></td>
                        <td class="text-end"><?php echo e(format_currency((float)($item['unit_cost'] ?? 0))); ?></td>
                        <td class="text-end">0%</td>
                        <td class="text-end"><?php echo e(format_currency((float)($item['subtotal'] ?? 0))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="bottom">
        <div class="notes">
            <div class="panel-title">Comentarios o instrucciones especiales</div>
            <div class="notes-box">
                <strong>Condiciones:</strong><br>
                <?php echo nl2br(e($conditions !== '' ? $conditions : 'Entrega y pago según acuerdo comercial.')); ?>
                <br><br>
                <strong>Observaciones:</strong><br>
                <?php echo nl2br(e($notesBody !== '' ? $notesBody : 'Sin observaciones adicionales.')); ?>
            </div>
        </div>
        <div class="totals">
            <table>
                <tr><td class="label">Sub-total</td><td class="text-end"><?php echo e(format_currency($subtotal)); ?></td></tr>
                <tr><td class="label">Descuento</td><td class="text-end"><?php echo e(format_currency($discount)); ?></td></tr>
                <tr><td class="label">Neto</td><td class="text-end"><?php echo e(format_currency($neto)); ?></td></tr>
                <tr><td class="label">Impuesto</td><td class="text-end">0%</td></tr>
                <tr><td class="label">Total</td><td class="text-end"><?php echo e(format_currency($total)); ?></td></tr>
                <tr><td class="label">Banco</td><td><?php echo e($companyBank ?: '—'); ?></td></tr>
                <tr><td class="label">Tipo Cta.</td><td><?php echo e($companyAccountType ?: '—'); ?></td></tr>
                <tr><td class="label">N° Cuenta</td><td><?php echo e($companyAccountNumber ?: '—'); ?></td></tr>
            </table>
        </div>
    </div>

    <div class="signatures">
        <div><div class="line">Solicitado por</div></div>
        <div><div class="line">Aprobado por</div></div>
        <div><div class="line">Aceptado por proveedor</div></div>
    </div>

    <div class="footer">
        <?php if ($companySignature !== ''): ?><?php echo nl2br(e($companySignature)); ?><br><?php endif; ?>
        Si tiene alguna consulta sobre esta orden, contáctenos por email o teléfono.
    </div>
</div>
</body>
</html>
