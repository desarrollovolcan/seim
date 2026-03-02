<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Orden de compra #<?php echo (int)($order['id'] ?? 0); ?></title>
    <style>
        :root { --brand-primary:#1e40af; --brand-soft:#eff6ff; --brand-muted:#6b7280; }
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 24px; }
        .page { border: 1px solid #dbe7ff; border-radius: 10px; overflow: hidden; }
        .header { background: var(--brand-soft); border-bottom: 2px solid var(--brand-primary); padding: 16px 18px; display: flex; justify-content: space-between; gap: 16px; }
        .brand { display:flex; gap:12px; align-items:flex-start; }
        .brand img { width: 76px; height: 76px; object-fit: contain; border-radius: 8px; background:#fff; border:1px solid #dbeafe; }
        .brand h1 { margin:0; font-size: 22px; color: var(--brand-primary); }
        .muted { color: var(--brand-muted); font-size: 12px; line-height: 1.45; }
        .doc-box { text-align:right; min-width: 280px; }
        .doc-title { font-size: 19px; font-weight: 700; color: var(--brand-primary); letter-spacing: .02em; }
        .doc-id { font-size: 13px; margin-top: 4px; }
        .content { padding: 16px 18px; }
        .grid { display:grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap:10px; margin-bottom: 12px; }
        .box { border: 1px solid #dbeafe; border-radius: 8px; padding: 10px; }
        .box strong { display:block; font-size:12px; color:#475569; margin-bottom:4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #dbeafe; padding: 8px; font-size: 12px; }
        th { background: var(--brand-soft); text-align: left; color:#1e3a8a; }
        .text-end { text-align:right; }
        .totals { margin-top: 12px; display:flex; justify-content:flex-end; }
        .total-box { width: 300px; border:1px solid #dbeafe; border-radius: 8px; }
        .total-line { display:flex; justify-content:space-between; padding:8px 10px; border-bottom: 1px solid #e5edff; }
        .total-line:last-child { border-bottom:0; font-weight:700; color:#1e3a8a; }
        .notes { margin-top: 12px; border: 1px solid #dbeafe; border-radius: 8px; padding: 10px; min-height: 54px; }
        .notes h4 { margin: 0 0 6px; color:#1e3a8a; font-size: 13px; }
        .signatures { margin: 44px 0 16px; display:grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 24px; }
        .sign { text-align:center; font-size:12px; }
        .sign .line { border-top: 1px solid #334155; margin-top: 42px; padding-top: 6px; }
        .print-actions { margin: 10px 0; }
        .print-actions button { padding: 8px 14px; border: 0; border-radius: 6px; background: var(--brand-primary); color: #fff; cursor: pointer; }
        @media print { .print-actions { display:none; } body { margin:0; padding:8mm; } }
    </style>
</head>
<body onload="window.print()">
<?php
$companyName = $company['name'] ?? ($order['company_name'] ?? 'Nombre Empresa');
$companyRut = $company['rut'] ?? '';
$companyEmail = $company['email'] ?? '';
$companyPhone = $company['phone'] ?? '';
$companyAddress = $company['address'] ?? '';
$companyGiro = $company['giro'] ?? '';
$companyLogoColor = $company['logo_color'] ?? 'assets/images/logo.png';
$companyLogoDataUri = '';
if ($companyLogoColor !== '') {
    $logoFilePath = __DIR__ . '/../../../' . ltrim($companyLogoColor, '/');
    if (is_file($logoFilePath)) {
        $logoContents = @file_get_contents($logoFilePath);
        if ($logoContents !== false) {
            $mimeType = function_exists('mime_content_type') ? (mime_content_type($logoFilePath) ?: 'image/png') : 'image/png';
            $companyLogoDataUri = 'data:' . $mimeType . ';base64,' . base64_encode($logoContents);
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
?>
<div class="print-actions">
    <button type="button" onclick="window.print()">Imprimir</button>
</div>
<div class="page">
    <div class="header">
        <div class="brand">
            <?php if ($companyLogoDataUri !== ''): ?>
                <img src="<?php echo e($companyLogoDataUri); ?>" alt="Logo empresa">
            <?php endif; ?>
            <div>
                <h1><?php echo e($companyName); ?></h1>
                <div class="muted">
                    <?php if ($companyRut !== ''): ?>RUT: <?php echo e($companyRut); ?><br><?php endif; ?>
                    <?php if ($companyGiro !== ''): ?>Giro: <?php echo e($companyGiro); ?><br><?php endif; ?>
                    <?php if ($companyAddress !== ''): ?><?php echo e($companyAddress); ?><br><?php endif; ?>
                    <?php if ($companyEmail !== ''): ?><?php echo e($companyEmail); ?><?php endif; ?>
                    <?php if ($companyPhone !== ''): ?> · <?php echo e($companyPhone); ?><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="doc-box">
            <div class="doc-title">ORDEN DE COMPRA</div>
            <div class="doc-id">N° <?php echo (int)($order['id'] ?? 0); ?></div>
            <div class="muted">Fecha emisión: <?php echo e(format_date($order['order_date'] ?? null)); ?></div>
            <div class="muted">Estado: <?php echo e(ucfirst((string)($order['status'] ?? 'pendiente'))); ?></div>
            <div class="muted">Referencia: <?php echo e((string)($order['reference'] ?: 'Sin referencia')); ?></div>
        </div>
    </div>

    <div class="content">
        <div class="grid">
            <div class="box">
                <strong>Proveedor</strong>
                <div><?php echo e($order['supplier_name'] ?? ''); ?></div>
            </div>
            <div class="box">
                <strong>Observación breve</strong>
                <div><?php echo e($notesBody !== '' ? $notesBody : 'Sin observaciones.'); ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:7%">#</th>
                    <th>Producto</th>
                    <th style="width:14%" class="text-end">Cantidad</th>
                    <th style="width:18%" class="text-end">Costo unitario</th>
                    <th style="width:18%" class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td><?php echo (int)$index + 1; ?></td>
                        <td><?php echo e($item['product_name'] ?? ''); ?></td>
                        <td class="text-end"><?php echo (int)($item['quantity'] ?? 0); ?></td>
                        <td class="text-end"><?php echo e(format_currency((float)($item['unit_cost'] ?? 0))); ?></td>
                        <td class="text-end"><?php echo e(format_currency((float)($item['subtotal'] ?? 0))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <div class="total-box">
                <div class="total-line"><span>Subtotal</span><span><?php echo e(format_currency((float)($order['subtotal'] ?? 0))); ?></span></div>
                <div class="total-line"><span>Total</span><span><?php echo e(format_currency((float)($order['total'] ?? 0))); ?></span></div>
            </div>
        </div>

        <div class="notes">
            <h4>Condiciones de la orden</h4>
            <div><?php echo nl2br(e($conditions !== '' ? $conditions : 'Plazo de entrega y forma de pago según acuerdo comercial vigente.')); ?></div>
        </div>

        <div class="signatures">
            <div class="sign"><div class="line">Solicitado por</div></div>
            <div class="sign"><div class="line">Aprobado por</div></div>
            <div class="sign"><div class="line">Proveedor</div></div>
        </div>
    </div>
</div>
</body>
</html>
