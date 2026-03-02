<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Orden de compra #<?php echo (int)($order['id'] ?? 0); ?></title>
    <style>
        :root {
            --primary: #1e40af;
            --primary-soft: #eff6ff;
            --line: #c7dafd;
            --text: #1f2937;
            --muted: #64748b;
        }

        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: var(--text); margin: 20px; }
        .sheet { border: 1px solid var(--line); border-radius: 10px; overflow: hidden; }
        .print-actions { margin-bottom: 12px; }
        .print-actions button { background: var(--primary); color:#fff; border:0; border-radius:6px; padding:8px 12px; cursor:pointer; }

        .header {
            border-bottom: 2px solid var(--primary);
            background: linear-gradient(180deg, #f8fbff 0%, #eff6ff 100%);
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            gap: 16px;
        }

        .brand { display: flex; gap: 12px; }
        .brand img { width: 72px; height: 72px; object-fit: contain; border: 1px solid var(--line); border-radius: 8px; background: #fff; }
        .brand h2 { margin: 0; font-size: 20px; color: var(--primary); }
        .meta { font-size: 12px; color: var(--muted); line-height: 1.4; margin-top: 4px; }

        .doc-box { min-width: 270px; border: 1px solid var(--line); background: #fff; border-radius: 8px; padding: 10px; }
        .doc-title { text-align: center; font-weight: 700; color: var(--primary); font-size: 18px; margin-bottom: 8px; }
        .doc-grid { width: 100%; border-collapse: collapse; }
        .doc-grid td { border: 1px solid #dbeafe; padding: 6px 8px; font-size: 12px; }
        .doc-grid td:first-child { background: var(--primary-soft); width: 42%; color: #1e3a8a; font-weight: 700; }

        .content { padding: 14px 16px 16px; }
        .block-title {
            margin: 0 0 6px;
            padding: 7px 10px;
            font-size: 13px;
            color: #1e3a8a;
            font-weight: 700;
            background: var(--primary-soft);
            border: 1px solid var(--line);
            border-radius: 7px;
        }

        .info-table, .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .info-table td, .detail-table th, .detail-table td { border: 1px solid var(--line); padding: 7px 8px; font-size: 12px; }
        .info-table td.label { width: 18%; font-weight: 700; color: #1e3a8a; background: #f8fbff; }

        .detail-table th { background: var(--primary-soft); color: #1e3a8a; font-weight: 700; text-align: left; }
        .text-end { text-align: right; }

        .summary { width: 320px; margin-left: auto; border: 1px solid var(--line); border-radius: 8px; overflow: hidden; }
        .summary .row { display: flex; justify-content: space-between; padding: 8px 10px; border-bottom: 1px solid #e5edff; font-size: 12px; }
        .summary .row:last-child { border-bottom: 0; background: var(--primary-soft); font-weight: 700; color: #1e3a8a; }

        .notes-box {
            margin-top: 10px;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 10px;
            font-size: 12px;
            min-height: 72px;
            line-height: 1.45;
        }

        .footer-grid {
            margin-top: 12px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .bank-box {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 10px;
            font-size: 11px;
            color: #475569;
        }

        .signatures {
            margin-top: 28px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
            text-align: center;
            font-size: 12px;
        }

        .sign-line { border-top: 1px solid #334155; margin-top: 38px; padding-top: 6px; }

        @media print {
            .print-actions { display: none; }
            body { margin: 0; padding: 8mm; }
        }
    </style>
</head>
<body onload="window.print()">
<?php
$companyName = $company['name'] ?? 'Nombre Empresa';
$companyRut = $company['rut'] ?? '';
$companyEmail = $company['email'] ?? '';
$companyPhone = $company['phone'] ?? '';
$companyAddress = trim(($company['address'] ?? '') . (empty($company['commune']) ? '' : ', ' . $company['commune']));
$companyGiro = $company['giro'] ?? '';
$companyBank = $company['bank'] ?? '';
$companyAccountType = $company['account_type'] ?? '';
$companyAccountNumber = $company['account_number'] ?? '';
$companyLogoColor = $company['logo_color'] ?? 'assets/images/logo.png';
$companySignature = $company['signature'] ?? '';

$supplierName = $order['supplier_name'] ?? '';
$supplierTax = $order['supplier_tax_id'] ?? '';
$supplierContact = $order['supplier_contact_name'] ?? '';
$supplierEmail = $order['supplier_email'] ?? '';
$supplierPhone = $order['supplier_phone'] ?? '';
$supplierAddress = trim((string)($order['supplier_address'] ?? '') . (empty($order['supplier_commune']) ? '' : ', ' . $order['supplier_commune']));
$supplierGiro = $order['supplier_giro'] ?? '';
$supplierCode = $order['supplier_code'] ?? '';

$logoData = '';
if ($companyLogoColor !== '') {
    $logoPath = __DIR__ . '/../../../' . ltrim($companyLogoColor, '/');
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
?>

<div class="print-actions">
    <button type="button" onclick="window.print()">Imprimir</button>
</div>

<div class="sheet">
    <div class="header">
        <div class="brand">
            <?php if ($logoData !== ''): ?><img src="<?php echo e($logoData); ?>" alt="Logo"><?php endif; ?>
            <div>
                <h2><?php echo e($companyName); ?></h2>
                <div class="meta">
                    <?php if ($companyRut !== ''): ?>RUT: <?php echo e($companyRut); ?><br><?php endif; ?>
                    <?php if ($companyGiro !== ''): ?>Giro: <?php echo e($companyGiro); ?><br><?php endif; ?>
                    <?php if ($companyAddress !== ''): ?><?php echo e($companyAddress); ?><br><?php endif; ?>
                    <?php echo e($companyEmail); ?><?php if ($companyPhone !== ''): ?> · <?php echo e($companyPhone); ?><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="doc-box">
            <div class="doc-title">ORDEN DE COMPRA</div>
            <table class="doc-grid">
                <tr><td>N° Orden</td><td><?php echo (int)($order['id'] ?? 0); ?></td></tr>
                <tr><td>Referencia</td><td><?php echo e((string)($order['reference'] ?: 'Sin referencia')); ?></td></tr>
                <tr><td>Fecha Emisión</td><td><?php echo e(format_date($order['order_date'] ?? null)); ?></td></tr>
                <tr><td>Estado</td><td><?php echo e(ucfirst((string)($order['status'] ?? 'pendiente'))); ?></td></tr>
            </table>
        </div>
    </div>

    <div class="content">
        <div class="block-title">Datos del Proveedor / Cliente</div>
        <table class="info-table">
            <tr>
                <td class="label">Razón Social</td><td><?php echo e($supplierName); ?></td>
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

        <div class="block-title">Detalle de Productos</div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:37%">Descripción</th>
                    <th style="width:10%">Cant.</th>
                    <th style="width:8%">UM</th>
                    <th style="width:14%" class="text-end">Neto</th>
                    <th style="width:10%" class="text-end">% Desc</th>
                    <th style="width:16%" class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td><?php echo (int)$index + 1; ?></td>
                        <td>
                            <strong><?php echo e($item['product_name'] ?? ''); ?></strong>
                            <?php if (!empty($item['sku'])): ?><br><span style="color:#64748b; font-size:11px;">SKU: <?php echo e($item['sku']); ?></span><?php endif; ?>
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

        <div class="summary">
            <div class="row"><span>Descuento</span><span><?php echo e(format_currency($discount)); ?></span></div>
            <div class="row"><span>Neto</span><span><?php echo e(format_currency($subtotal)); ?></span></div>
            <div class="row"><span>Total</span><span><?php echo e(format_currency($total)); ?></span></div>
        </div>

        <div class="block-title" style="margin-top:12px;">Condiciones y Observaciones</div>
        <div class="notes-box">
            <strong>Condiciones:</strong><br>
            <?php echo nl2br(e($conditions !== '' ? $conditions : 'Entrega y forma de pago según acuerdo comercial vigente.')); ?>
            <br><br>
            <strong>Observaciones:</strong><br>
            <?php echo nl2br(e($notesBody !== '' ? $notesBody : 'Sin observaciones adicionales.')); ?>
        </div>

        <div class="footer-grid">
            <div class="bank-box">
                <strong style="color:#1e3a8a;">Datos Bancarios Empresa</strong><br>
                Banco: <?php echo e($companyBank ?: '—'); ?><br>
                Tipo de cuenta: <?php echo e($companyAccountType ?: '—'); ?><br>
                N° cuenta: <?php echo e($companyAccountNumber ?: '—'); ?><br>
                Email: <?php echo e($companyEmail ?: '—'); ?>
            </div>
            <div class="bank-box">
                <strong style="color:#1e3a8a;">Notas de recepción</strong><br>
                1) Verificar cantidades y estado al momento de entrega.<br>
                2) Informar diferencias dentro de 24 horas.
            </div>
        </div>

        <div class="signatures">
            <div><div class="sign-line">Solicitado por</div></div>
            <div><div class="sign-line">Aprobado por</div></div>
            <div><div class="sign-line">Aceptado por proveedor</div></div>
        </div>

        <?php if ($companySignature !== ''): ?>
            <div style="font-size:11px; color:#64748b; text-align:center; margin-top:10px;"><?php echo nl2br(e($companySignature)); ?></div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
