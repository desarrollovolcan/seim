<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de formulario</title>
    <style>
        @page { size: Letter; margin: 16mm; }
        :root { --azul:#1c3474; --gris:#6b7280; --gris-claro:#e5e7eb; }
        html, body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body { font-family:"Segoe UI", Arial, Helvetica, sans-serif; font-size:11px; color:#1f2933; margin:0; background:#f3f6fb; }
        .sheet { width:920px; max-width:100%; margin:16px auto; background:#fff; border-radius:8px; box-shadow:0 8px 24px rgba(15,23,42,.08); padding:18px; }
        .print-actions { text-align:right; margin-bottom:10px; }
        .print-actions button { background:var(--azul); color:#fff; border:0; border-radius:6px; padding:8px 12px; font-size:12px; cursor:pointer; }
        .header { display:table; width:100%; }
        .header .left, .header .right { display:table-cell; vertical-align:top; }
        .header .right { text-align:right; }
        .report-title { font-size:18px; font-weight:700; color:var(--azul); text-transform:uppercase; }
        .meta { font-size:11px; line-height:1.45; color:var(--gris); }
        hr { border:none; border-top:2px solid var(--azul); margin:10px 0; }
        .section-title { font-size:12px; font-weight:700; color:var(--azul); text-transform:uppercase; border-bottom:1px solid var(--gris-claro); padding-bottom:4px; margin-bottom:8px; }
        .note { background:#f8faff; border-left:4px solid var(--azul); padding:10px; color:#334155; margin-bottom:12px; }
        .table-items { width:100%; border-collapse:collapse; }
        .table-items th { background:var(--azul); color:#fff; text-align:left; padding:7px; font-size:11px; }
        .table-items td { border-bottom:1px solid var(--gris-claro); padding:7px; vertical-align:top; }
        .table-items tr:last-child td { border-bottom:2px solid var(--azul); }
        .footer { margin-top:18px; text-align:center; color:var(--gris); font-size:10px; border-top:1px solid var(--gris-claro); padding-top:8px; }
        @media print { body{background:#fff;} .sheet{box-shadow:none;border-radius:0;margin:0;width:100%;} .print-actions{display:none;} }
    </style>
</head>
<body onload="window.print()">
<div class="sheet">
    <div class="print-actions"><button type="button" onclick="window.print()">Imprimir</button></div>

    <div class="header">
        <div class="left">
            <div class="report-title">Informe</div>
            <div class="meta">Origen: <?php echo e($source); ?></div>
            <?php if ($template !== ''): ?><div class="meta">Plantilla: <?php echo e($template); ?></div><?php endif; ?>
        </div>
        <div class="right meta">
            <strong><?php echo e((string)($company['name'] ?? 'Empresa')); ?></strong><br>
            <?php if (!empty($company['rut'])): ?>RUT: <?php echo e((string)$company['rut']); ?><br><?php endif; ?>
            <?php if (!empty($company['giro'])): ?><?php echo e((string)$company['giro']); ?><br><?php endif; ?>
            <?php if (!empty($company['email'])): ?><?php echo e((string)$company['email']); ?><br><?php endif; ?>
            Fecha: <?php echo e(date('d/m/Y H:i')); ?>
        </div>
    </div>

    <hr>

    <div class="section-title">Datos del formulario</div>
    <div class="note">Documento generado automáticamente con la información registrada en el formulario.</div>

    <table class="table-items">
        <thead>
        <tr><th style="width:35%">Campo</th><th>Valor</th></tr>
        </thead>
        <tbody>
        <?php if (!empty($fields)): ?>
            <?php foreach ($fields as $field): ?>
                <tr>
                    <td><?php echo e((string)$field['label']); ?></td>
                    <td><?php echo nl2br(e((string)$field['value'])); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">No hay datos para imprimir desde este formulario.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">Documento generado electrónicamente · Formato Carta</div>
</div>
</body>
</html>
