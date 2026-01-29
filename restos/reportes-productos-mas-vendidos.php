<?php
$pageTitle = 'Productos más vendidos';
$pageSubtitle = 'Reportes';
$pageDescription = 'Ranking de productos con mayor rotación.';
$moduleKey = 'reportes-productos-mas-vendidos';
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
        'name' => 'categoria',
        'label' => 'Categoría',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Categoría',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'top',
        'label' => 'Top',
        'type' => 'number',
        'required' => false,
        'placeholder' => '10',
        'col' => 'erp-field erp-field--third',
    ],
];
$moduleListColumns = [
    ['key' => 'categoria', 'label' => 'Categoría'],
    ['key' => 'fecha_desde', 'label' => 'Desde'],
    ['key' => 'fecha_hasta', 'label' => 'Hasta'],
    ['key' => 'top', 'label' => 'Ranking'],
    ['key' => 'producto', 'label' => 'Producto'],
];

include('partials/generic-page.php');
