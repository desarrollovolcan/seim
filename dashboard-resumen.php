<?php
$pageTitle = 'Resumen general';
$pageSubtitle = 'Dashboard';
$pageDescription = 'Indicadores clave del inventario, ventas y utilidades.';
$hideModuleCrud = true;
$pageSummaryCards = [
    [
        'title' => 'Saldo en caja',
        'value' => '$ 18.450.000',
        'meta' => '+4.2% vs. mes anterior',
        'icon' => 'ti ti-currency-dollar',
    ],
    [
        'title' => 'Cuentas por cobrar',
        'value' => '$ 6.320.000',
        'meta' => '32 facturas pendientes',
        'icon' => 'ti ti-file-invoice',
    ],
    [
        'title' => 'Cuentas por pagar',
        'value' => '$ 3.870.000',
        'meta' => '12 vencimientos próximos',
        'icon' => 'ti ti-report-money',
    ],
    [
        'title' => 'Utilidad neta',
        'value' => '$ 2.120.000',
        'meta' => 'Margen 18.4%',
        'icon' => 'ti ti-chart-line',
    ],
];
$pageActivityRows = [
    [
        'fecha' => 'Hoy 09:20',
        'referencia' => 'AS-2024-102',
        'descripcion' => 'Asiento de cierre diario',
        'monto' => '$ 245.000',
    ],
    [
        'fecha' => 'Ayer 16:45',
        'referencia' => 'FV-2941',
        'descripcion' => 'Factura venta mayorista',
        'monto' => '$ 1.320.000',
    ],
    [
        'fecha' => 'Ayer 12:10',
        'referencia' => 'RC-873',
        'descripcion' => 'Recibo de proveedor aprobado',
        'monto' => '$ 560.000',
    ],
    [
        'fecha' => 'Lun 10:15',
        'referencia' => 'NC-118',
        'descripcion' => 'Nota de crédito emitida',
        'monto' => '$ -95.000',
    ],
];

include('partials/generic-page.php');
