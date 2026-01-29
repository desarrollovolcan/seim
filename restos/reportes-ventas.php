<?php
$pageTitle = 'Reporte de ventas';
$pageSubtitle = 'Reportes';
$pageDescription = 'Reporte detallado de ventas.';
$moduleKey = 'reportes-ventas';
$moduleMode = 'report';
$moduleFields = [
    [
        'name' => 'fecha_desde',
        'label' => 'Fecha desde',
        'type' => 'date',
        'required' => true,
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'fecha_hasta',
        'label' => 'Fecha hasta',
        'type' => 'date',
        'required' => true,
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'canal',
        'label' => 'Canal de venta',
        'type' => 'select',
        'required' => false,
        'options' => [
            'presencial' => 'Presencial',
            'online' => 'Online',
            'mayorista' => 'Mayorista',
        ],
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'vendedor',
        'label' => 'Vendedor',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Nombre del vendedor',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'estado',
        'label' => 'Estado',
        'type' => 'select',
        'required' => false,
        'options' => [
            'pagada' => 'Pagada',
            'pendiente' => 'Pendiente',
            'anulada' => 'Anulada',
        ],
        'col' => 'erp-field erp-field--third',
    ],
];
$moduleListColumns = [
    ['key' => 'fecha_desde', 'label' => 'Fecha'],
    ['key' => 'canal', 'label' => 'Canal'],
    ['key' => 'vendedor', 'label' => 'Vendedor'],
    ['key' => 'estado', 'label' => 'Estado'],
    ['key' => 'monto', 'label' => 'Total'],
];

include('partials/generic-page.php');
