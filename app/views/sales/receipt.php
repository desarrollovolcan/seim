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
        .meta {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-size: 10px;
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
        <div class="center">
            <strong><?php echo e($company['name'] ?? ''); ?></strong><br>
            <?php if (!empty($company['rut'])): ?>
                RUT: <?php echo e($company['rut']); ?><br>
            <?php endif; ?>
            <?php if (!empty($company['address'])): ?>
                <?php echo e($company['address']); ?><br>
            <?php endif; ?>
        </div>
        <div class="divider"></div>
        <div class="meta">
            <div>
                <div>Venta: <?php echo e($sale['numero'] ?? ''); ?></div>
                <div>Fecha: <?php echo e(format_date($sale['sale_date'] ?? null)); ?></div>
            </div>
            <div>
                <div>Canal: <?php echo e(strtoupper($sale['channel'] ?? 'POS')); ?></div>
                <div>Estado: <?php echo e($sale['status'] ?? 'pagado'); ?></div>
            </div>
        </div>
        <div class="divider"></div>
        <div><strong>Cliente:</strong> <?php echo e($sale['client_name'] ?? 'Consumidor final'); ?></div>
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
        <div class="center">Gracias por su compra</div>
    </div>
<?php endfor; ?>
</body>
</html>
