<?php
$pageTitle = 'Productos con bajo stock';
$pageSubtitle = 'Reportes';
$pageDescription = 'Productos por debajo del stock mínimo.';
$moduleKey = 'reportes-productos-bajo-stock';
$moduleMode = 'report';
$moduleFields = [
    [
        'name' => 'bodega',
        'label' => 'Bodega',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Bodega central',
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
        'name' => 'stock_minimo',
        'label' => 'Stock mínimo',
        'type' => 'number',
        'required' => false,
        'placeholder' => '5',
        'col' => 'erp-field erp-field--third',
    ],
];
$moduleListColumns = [
    ['key' => 'bodega', 'label' => 'Bodega'],
    ['key' => 'categoria', 'label' => 'Categoría'],
    ['key' => 'producto', 'label' => 'Producto'],
    ['key' => 'stock_minimo', 'label' => 'Mínimo'],
    ['key' => 'stock_actual', 'label' => 'Stock actual'],
];

include('partials/generic-page.php');
