<?php
$pageTitle = 'Reporte de inventario';
$pageSubtitle = 'Reportes';
$pageDescription = 'Reporte consolidado de inventario.';
$moduleKey = 'reportes';
$moduleMode = 'report';
$moduleTitleField = 'tipo_reporte';
$moduleFields = [
    [
        'name' => 'tipo_reporte',
        'label' => 'Tipo de reporte',
        'type' => 'select',
        'required' => true,
        'options' => [
            'inventario_valorizado' => 'Inventario valorizado',
            'kardex_producto' => 'Kardex por producto',
            'ventas_periodo' => 'Ventas por período',
            'utilidad_producto' => 'Utilidad por producto',
            'productos_mas_vendidos' => 'Productos más vendidos',
            'perdidas_mermas' => 'Pérdidas y mermas',
        ],
    ],
    [
        'name' => 'fecha_desde',
        'label' => 'Fecha desde',
        'type' => 'date',
        'required' => true,
    ],
    [
        'name' => 'fecha_hasta',
        'label' => 'Fecha hasta',
        'type' => 'date',
        'required' => true,
    ],
    [
        'name' => 'producto',
        'label' => 'Producto',
        'type' => 'text',
        'required' => false,
    ],
    [
        'name' => 'bodega',
        'label' => 'Bodega',
        'type' => 'text',
        'required' => false,
    ],
];
$moduleListColumns = [
    ['key' => 'tipo_reporte', 'label' => 'Reporte'],
    ['key' => 'fecha_desde', 'label' => 'Desde'],
    ['key' => 'fecha_hasta', 'label' => 'Hasta'],
    ['key' => 'producto', 'label' => 'Producto'],
    ['key' => 'bodega', 'label' => 'Bodega'],
];

include('partials/generic-page.php');
