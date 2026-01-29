<?php
$pageTitle = 'Stock actual';
$pageSubtitle = 'Inventario';
$pageDescription = 'Visibilidad del stock disponible por producto y bodega.';
$moduleKey = 'inventario-stock-actual';
$moduleTitleField = 'producto';
$moduleFields = [
    [
        'name' => 'producto',
        'label' => 'Producto',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Producto',
    ],
    [
        'name' => 'bodega',
        'label' => 'Bodega',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Bodega',
    ],
    [
        'name' => 'stock_disponible',
        'label' => 'Stock disponible',
        'type' => 'number',
        'required' => true,
        'step' => '1',
    ],
    [
        'name' => 'stock_comprometido',
        'label' => 'Stock comprometido',
        'type' => 'number',
        'required' => false,
        'step' => '1',
        'placeholder' => '0',
    ],
    [
        'name' => 'stock_minimo',
        'label' => 'Stock mínimo',
        'type' => 'number',
        'required' => true,
        'step' => '1',
    ],
    [
        'name' => 'costo_promedio',
        'label' => 'Costo promedio',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
    ],
    [
        'name' => 'valor_total',
        'label' => 'Valor total en stock',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
    ],
    [
        'name' => 'estado_stock',
        'label' => 'Estado de stock',
        'type' => 'select',
        'required' => true,
        'options' => [
            'normal' => 'Normal',
            'bajo' => 'Bajo',
        ],
    ],
];
$moduleListColumns = [
    ['key' => 'producto', 'label' => 'Producto'],
    ['key' => 'bodega', 'label' => 'Bodega'],
    ['key' => 'stock_disponible', 'label' => 'Stock'],
    ['key' => 'stock_minimo', 'label' => 'Mínimo'],
    ['key' => 'estado_stock', 'label' => 'Estado'],
    ['key' => 'valor_total', 'label' => 'Valor total'],
];

include('partials/generic-page.php');
