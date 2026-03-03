<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe de formulario</title>
    <style>
        :root { --primary:#1e40af; --soft:#eef4ff; --text:#1f2937; --muted:#6b7280; }
        @page { size: Letter; margin: 12mm; }
        html, body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body { font-family:'Segoe UI', Arial, sans-serif; margin:0; background:#eef2f7; color:var(--text); }
        .sheet { width: 900px; max-width: 100%; margin: 18px auto; background:#fff; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:24px; }
        .actions { text-align:right; margin-bottom:10px; }
        .actions button { background:var(--primary); color:#fff; border:0; border-radius:6px; padding:8px 12px; }
        .top { background:var(--primary); color:#fff; border-radius:8px; padding:16px 18px; display:flex; justify-content:space-between; gap:16px; }
        .title { font-size:26px; margin:0; text-transform:uppercase; }
        .meta { font-size:12px; text-align:right; line-height:1.35; }
        .box { margin-top:16px; background:#f4f7fb; border-left:5px solid #2563eb; border-radius:6px; padding:14px; font-size:13px; }
        table { width:100%; border-collapse:collapse; margin-top:16px; font-size:13px; }
        thead { background:#2563eb; color:#fff; }
        th,td { padding:10px; text-align:left; border-bottom:1px solid #d6deea; }
        tr:nth-child(even) td { background:#f9fbff; }
        .footer { margin-top:24px; text-align:center; color:var(--muted); font-size:11px; border-top:1px solid #d8dbe3; padding-top:10px; }
        @media print { body{background:#fff;} .sheet{box-shadow:none; margin:0; border-radius:0; width:100%;} .actions{display:none;} }
    </style>
</head>
<body onload="window.print()">
<div class="sheet">
    <div class="actions"><button onclick="window.print()">Imprimir</button></div>
    <div class="top">
        <div>
            <h1 class="title">Informe</h1>
            <div>Origen: <?php echo e($source); ?></div>
            <?php if ($template !== ''): ?><div>Plantilla: <?php echo e($template); ?></div><?php endif; ?>
        </div>
        <div class="meta">
            <strong><?php echo e((string)($company['name'] ?? 'Empresa')); ?></strong><br>
            <?php if (!empty($company['rut'])): ?>RUT: <?php echo e((string)$company['rut']); ?><br><?php endif; ?>
            <?php if (!empty($company['giro'])): ?><?php echo e((string)$company['giro']); ?><br><?php endif; ?>
            <?php if (!empty($company['email'])): ?><?php echo e((string)$company['email']); ?><br><?php endif; ?>
            Fecha: <?php echo e(date('d/m/Y H:i')); ?>
        </div>
    </div>

    <div class="box">
        Este informe se generó con los datos ingresados en el formulario seleccionado del menú.
    </div>

    <table>
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

    <div class="footer">
        Documento generado electrónicamente · Tamaño carta
    </div>
</div>
</body>
</html>
