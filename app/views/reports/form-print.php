<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe - <?php echo e((string)($company['name'] ?? 'SEIM Energía')); ?></title>
    <style>
        @page { size: Letter; margin: 14mm; }
        :root { --seim-blue: #0f172a; --seim-accent: #2563eb; --gris: #64748b; --borde: #e2e8f0; }
        html, body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 0; background: #f8fafc; }
        .sheet { width: 100%; max-width: 860px; margin: 16px auto; background: #fff; border-radius: 8px; box-shadow: 0 4px 16px rgba(15,23,42,.06); padding: 24px; box-sizing: border-box; border: 1px solid var(--borde); }
        .print-actions { display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 16px; }
        .print-actions button { background: var(--seim-accent); color: #fff; border: 0; border-radius: 6px; padding: 7px 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.15s; }
        .print-actions button.btn-close-view { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .print-actions button:hover { opacity: 0.9; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 16px; }
        .header .brand-area img { max-height: 44px; max-width: 180px; object-fit: contain; }
        .header .meta { font-size: 11px; line-height: 1.45; color: var(--gris); text-align: right; }
        .report-title { font-size: 16px; font-weight: 700; color: #0f172a; text-transform: uppercase; margin-top: 6px; }
        hr { border: none; border-top: 1.5px solid var(--borde); margin: 12px 0 16px; }
        .section-title { font-size: 12px; font-weight: 700; color: var(--seim-blue); text-transform: uppercase; border-bottom: 1.5px solid var(--seim-accent); padding-bottom: 4px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .table-items { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 16px; }
        .table-items th { background: #f8fafc; color: #475569; text-align: left; padding: 8px 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; border: 1px solid var(--borde); letter-spacing: 0.04em; }
        .table-items td { border: 1px solid var(--borde); padding: 8px 10px; vertical-align: top; word-break: break-word; overflow-wrap: anywhere; font-size: 11px; }
        .table-items tr:nth-child(even) td { background: #fafbfc; }
        .table-items td.label-cell { font-weight: 600; color: #334155; width: 32%; background: #f8fafc; }
        .footer { margin-top: 24px; text-align: center; color: var(--gris); font-size: 10px; border-top: 1px solid var(--borde); padding-top: 10px; }
        @media print {
            body { background: #fff; }
            .sheet { box-shadow: none; border: none; border-radius: 0; margin: 0; width: 100%; max-width: 100%; padding: 0; }
            .print-actions { display: none !important; }
        }
    </style>
</head>
<body onload="setTimeout(() => window.print(), 300)">
<div class="sheet">
    <div class="print-actions">
        <button type="button" class="btn-close-view" onclick="window.close()">Cerrar</button>
        <button type="button" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>

    <div class="header">
        <div class="brand-area">
            <img src="assets/images/seim-logo.png" alt="Logo" onerror="this.style.display='none';">
            <div class="report-title">Resumen de Formulario</div>
            <div style="color: var(--gris); font-size: 10px; margin-top: 2px;">Módulo: <?php echo e($source); ?></div>
        </div>
        <div class="meta">
            <strong style="color: #0f172a; font-size: 12px;"><?php echo e((string)($company['name'] ?? 'SEIM Energía')); ?></strong><br>
            <?php if (!empty($company['rut'])): ?>RUT: <?php echo e((string)$company['rut']); ?><br><?php endif; ?>
            <?php if (!empty($company['giro'])): ?><?php echo e((string)$company['giro']); ?><br><?php endif; ?>
            <?php if (!empty($company['email'])): ?><?php echo e((string)$company['email']); ?><br><?php endif; ?>
            Fecha de emisión: <?php echo e(date('d/m/Y H:i')); ?>
        </div>
    </div>

    <hr>

    <div class="section-title">
        <span>Información registrada</span>
        <span style="font-weight: normal; font-size: 10px; color: var(--gris);">Valores actuales</span>
    </div>

    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 32%;">Campo</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($fields)): ?>
            <?php foreach ($fields as $field): ?>
                <tr>
                    <td class="label-cell"><?php echo e((string)$field['label']); ?></td>
                    <td><?php echo nl2br(e((string)$field['value'])); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2" style="text-align: center; color: var(--gris); padding: 16px;">No hay datos para mostrar en este informe.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Documento generado electrónicamente por sistema SEIM · Formato Carta
    </div>
</div>
</body>
</html>
