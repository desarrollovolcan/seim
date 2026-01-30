<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización</title>

    <style>
        /* ===== CONFIG IMPRESIÓN ===== */
        @page {
            size: Letter;
            margin: 20mm;
        }

        body {
            font-family: "Segoe UI", Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1f2933;
            margin: 0;
        }

        /* ===== COLORES ===== */
        :root {
            --azul: #1c3474;
            --gris: #6b7280;
            --gris-claro: #e5e7eb;
        }

        /* ===== ESTRUCTURA ===== */
        .page {
            width: 100%;
        }

        hr {
            border: none;
            border-top: 2px solid var(--azul);
            margin: 14px 0;
        }

        /* ===== HEADER ===== */
        .header {
            display: table;
            width: 100%;
        }

        .header .left,
        .header .right {
            display: table-cell;
            vertical-align: top;
        }

        .header .right {
            text-align: right;
        }

        .logo {
            font-size: 16px;
            font-weight: 700;
            color: var(--azul);
        }

        .company-data {
            font-size: 11px;
            line-height: 1.4;
            color: var(--gris);
        }

        .quote-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--azul);
        }

        .quote-number {
            font-size: 13px;
            font-weight: 600;
            color: #000;
        }

        /* ===== SECCIONES ===== */
        .section {
            margin-top: 16px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--azul);
            border-bottom: 1px solid var(--gris-claro);
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        /* ===== TABLA INFO ===== */
        .table-info {
            width: 100%;
            border-collapse: collapse;
        }

        .table-info td {
            padding: 5px 6px;
        }

        .label {
            width: 150px;
            font-weight: 600;
            color: #000;
        }

        /* ===== TABLA ITEMS ===== */
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .table-items th {
            background: var(--azul);
            color: #fff;
            font-weight: 600;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        .table-items td {
            border-bottom: 1px solid var(--gris-claro);
            padding: 8px;
        }

        .table-items tr:last-child td {
            border-bottom: 2px solid var(--azul);
        }

        .right {
            text-align: right;
        }

        /* ===== TOTALES ===== */
        .totals {
            width: 100%;
            margin-top: 14px;
        }

        .totals td {
            padding: 6px;
        }

        .totals .label {
            text-align: right;
            font-weight: 600;
        }

        .total-final {
            font-size: 14px;
            font-weight: 700;
            color: var(--azul);
            border-top: 2px solid var(--azul);
        }

        /* ===== CONDICIONES ===== */
        ul {
            margin: 6px 0 0 16px;
            padding: 0;
        }

        ul li {
            margin-bottom: 4px;
            color: var(--gris);
        }

        /* ===== FOOTER ===== */
        .footer {
            position: fixed;
            bottom: 15mm;
            left: 20mm;
            right: 20mm;
            font-size: 10px;
            text-align: center;
            color: var(--gris);
            border-top: 1px solid var(--gris-claro);
            padding-top: 6px;
        }

        .print-actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 12px;
        }

        .print-actions button {
            background: var(--azul);
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
        }

        @media print {
            .print-actions {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
<?php
$companyName = $company['name'] ?? 'Nombre Empresa';
$companyRut = $company['rut'] ?? '';
$companyEmail = $company['email'] ?? '';
$companyPhone = $company['phone'] ?? '';
$companyAddress = $company['address'] ?? '';
$companyGiro = $company['giro'] ?? '';
$companyCity = $company['city'] ?? '';
$clientRut = $client['rut'] ?? '';
$clientContact = $client['contact_name'] ?? '';
$clientGiro = $client['giro'] ?? '';
$clientPhone = $client['phone'] ?? '';
$clientAddress = $client['address'] ?? '';
$clientCommune = $client['commune'] ?? '';
$clientCity = $client['city'] ?? '';
$validUntil = $quote['valid_until'] ?? '';
?>

<div class="page">
    <div class="print-actions">
        <button type="button" onclick="window.print()">Imprimir</button>
    </div>
    <div class="header">
        <div class="left">
            <div class="logo"><?php echo e($companyName); ?></div>
            <div class="company-data">
                <?php if ($companyRut !== ''): ?>
                    RUT: <?php echo e($companyRut); ?><br>
                <?php endif; ?>
                <?php if ($companyGiro !== ''): ?>
                    Giro: <?php echo e($companyGiro); ?><br>
                <?php endif; ?>
                <?php echo e($companyAddress); ?>
                <?php if ($companyCity !== ''): ?>
                    , <?php echo e($companyCity); ?>
                <?php endif; ?>
                <br>
                <?php echo e($companyEmail); ?>
                <?php if ($companyPhone !== ''): ?>
                    · <?php echo e($companyPhone); ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="right">
            <div class="quote-title">COTIZACIÓN</div>
            <div class="quote-number">N° <?php echo e($quote['numero']); ?></div>
            <div>Fecha: <?php echo e($quote['fecha_emision']); ?></div>
            <?php if ($validUntil !== ''): ?>
                <div>Válida hasta: <?php echo e($validUntil); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <hr>

    <div class="section">
        <div class="section-title">Datos del Cliente</div>
        <table class="table-info">
            <tr>
                <td class="label">Razón Social</td>
                <td><?php echo e($client['name'] ?? ''); ?></td>
            </tr>
            <?php if ($clientRut !== ''): ?>
                <tr>
                    <td class="label">RUT</td>
                    <td><?php echo e($clientRut); ?></td>
                </tr>
            <?php endif; ?>
            <?php if ($clientGiro !== ''): ?>
                <tr>
                    <td class="label">Giro</td>
                    <td><?php echo e($clientGiro); ?></td>
                </tr>
            <?php endif; ?>
            <?php if ($clientAddress !== ''): ?>
                <tr>
                    <td class="label">Dirección</td>
                    <td>
                        <?php echo e($clientAddress); ?>
                        <?php if ($clientCommune !== '' || $clientCity !== ''): ?>
                            <?php echo e(trim($clientCommune . ' ' . $clientCity)); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ($clientContact !== ''): ?>
                <tr>
                    <td class="label">Contacto</td>
                    <td><?php echo e($clientContact); ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td class="label">Email</td>
                <td><?php echo e($client['email'] ?? ''); ?></td>
            </tr>
            <?php if ($clientPhone !== ''): ?>
                <tr>
                    <td class="label">Teléfono</td>
                    <td><?php echo e($clientPhone); ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Información de la Cotización</div>
        <table class="table-info">
            <tr>
                <td class="label">Estado</td>
                <td><?php echo e(ucfirst($quote['estado'] ?? 'pendiente')); ?></td>
            </tr>
            <?php if (!empty($quote['sii_document_type'])): ?>
                <tr>
                    <td class="label">Documento SII</td>
                    <td><?php echo e($quote['sii_document_type']); ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($quote['sii_document_number'])): ?>
                <tr>
                    <td class="label">Folio</td>
                    <td><?php echo e($quote['sii_document_number']); ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($quote['sii_receiver_rut'])): ?>
                <tr>
                    <td class="label">RUT receptor</td>
                    <td><?php echo e($quote['sii_receiver_rut']); ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($quote['sii_receiver_name'])): ?>
                <tr>
                    <td class="label">Razón social</td>
                    <td><?php echo e($quote['sii_receiver_name']); ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($quote['sii_receiver_giro'])): ?>
                <tr>
                    <td class="label">Giro</td>
                    <td><?php echo e($quote['sii_receiver_giro']); ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($quote['sii_receiver_address'])): ?>
                <tr>
                    <td class="label">Dirección SII</td>
                    <td><?php echo e(trim(($quote['sii_receiver_address'] ?? '') . ' ' . ($quote['sii_receiver_commune'] ?? '') . ' ' . ($quote['sii_receiver_city'] ?? ''))); ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($quote['notas'])): ?>
                <tr>
                    <td class="label">Notas</td>
                    <td><?php echo nl2br(e($quote['notas'] ?? '')); ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalle de la Cotización</div>

        <table class="table-items">
            <thead>
                <tr>
                    <th width="40">#</th>
                    <th>Descripción</th>
                    <th width="60">Cant.</th>
                    <th width="90">Precio</th>
                    <th width="100">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo e($item['descripcion']); ?></td>
                        <td class="right"><?php echo e($item['cantidad']); ?></td>
                        <td class="right"><?php echo e(format_currency((float)($item['precio_unitario'] ?? 0))); ?></td>
                        <td class="right"><?php echo e(format_currency((float)($item['total'] ?? 0))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <table class="totals">
        <tr>
            <td class="label">Neto</td>
            <td class="right"><?php echo e(format_currency((float)($quote['subtotal'] ?? 0))); ?></td>
        </tr>
        <tr>
            <td class="label">IVA</td>
            <td class="right"><?php echo e(format_currency((float)($quote['impuestos'] ?? 0))); ?></td>
        </tr>
        <tr>
            <td class="label total-final">TOTAL</td>
            <td class="right total-final"><?php echo e(format_currency((float)($quote['total'] ?? 0))); ?></td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Condiciones Comerciales</div>
        <ul>
            <li>Valores expresados en pesos chilenos.</li>
            <li>Pago según acuerdo comercial.</li>
            <li>Plazo de entrega sujeto a entrega de información.</li>
            <li>Vigencia de la cotización: 15 días.</li>
        </ul>
    </div>
</div>

<div class="footer">
    Cotización generada automáticamente · <?php echo e($companyName); ?> · <?php echo e($companyEmail); ?>
</div>

</body>
</html>
