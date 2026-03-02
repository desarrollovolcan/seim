<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Orden de compra #<?php echo (int)($order['id'] ?? 0); ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 24px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        h1 { margin: 0 0 6px; font-size: 24px; }
        .muted { color: #6b7280; font-size: 12px; }
        .grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 14px; }
        .box { border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; }
        .box strong { display: block; font-size: 12px; margin-bottom: 4px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; font-size: 12px; }
        th { background: #eff6ff; text-align: left; }
        .text-end { text-align: right; }
        .totals { margin-top: 12px; display: flex; justify-content: flex-end; }
        .total-box { min-width: 240px; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; }
        .total-line { display: flex; justify-content: space-between; margin: 4px 0; }
        .notes { margin-top: 12px; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; min-height: 62px; }
        .signatures { margin-top: 48px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
        .sign { text-align: center; font-size: 12px; }
        .sign .line { border-top: 1px solid #374151; margin-top: 40px; padding-top: 8px; }
        .print-actions { margin-top: 16px; }
        .print-actions button { padding: 8px 14px; border: 0; border-radius: 6px; background: #1d4ed8; color: #fff; cursor: pointer; }
        @media print { .print-actions { display: none; } body { margin: 0; padding: 14mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <div>
            <h1>Orden de Compra</h1>
            <div class="muted">N° <?php echo (int)($order['id'] ?? 0); ?><?php if (!empty($order['reference'])): ?> · Ref: <?php echo e($order['reference']); ?><?php endif; ?></div>
        </div>
        <div class="muted">Fecha emisión: <?php echo e(format_date($order['order_date'] ?? null)); ?></div>
    </div>

    <div class="grid">
        <div class="box">
            <strong>Proveedor</strong>
            <div><?php echo e($order['supplier_name'] ?? ''); ?></div>
        </div>
        <div class="box">
            <strong>Estado</strong>
            <div><?php echo e(ucfirst((string)($order['status'] ?? 'pendiente'))); ?></div>
        </div>
        <div class="box">
            <strong>Referencia</strong>
            <div><?php echo e($order['reference'] ?: 'Sin referencia'); ?></div>
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
            <div class="total-line"><span>Subtotal</span><strong><?php echo e(format_currency((float)($order['subtotal'] ?? 0))); ?></strong></div>
            <div class="total-line"><span>Total</span><strong><?php echo e(format_currency((float)($order['total'] ?? 0))); ?></strong></div>
        </div>
    </div>

    <div class="notes">
        <strong>Observaciones</strong>
        <div><?php echo nl2br(e((string)($order['notes'] ?: 'Sin observaciones.'))); ?></div>
    </div>

    <div class="signatures">
        <div class="sign"><div class="line">Solicitado por</div></div>
        <div class="sign"><div class="line">Aprobado por</div></div>
        <div class="sign"><div class="line">Proveedor</div></div>
    </div>

    <div class="print-actions">
        <button type="button" onclick="window.print()">Imprimir</button>
    </div>
</body>
</html>
