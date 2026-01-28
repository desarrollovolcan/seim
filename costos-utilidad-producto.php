<?php
$pageTitle = 'Utilidad por producto';
$pageSubtitle = 'Costos y Utilidades';
$pageDescription = 'Rentabilidad por producto en el tiempo.';
$moduleKey = 'costos-utilidades';
$moduleTitleField = 'producto';
$moduleFields = [
    [
        'name' => 'producto',
        'label' => 'Producto',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'costo_promedio',
        'label' => 'Costo promedio',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
    ],
    [
        'name' => 'precio_promedio_venta',
        'label' => 'Precio promedio de venta',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
    ],
    [
        'name' => 'utilidad_unitaria',
        'label' => 'Utilidad unitaria',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
    ],
    [
        'name' => 'utilidad_total',
        'label' => 'Utilidad total',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
    ],
    [
        'name' => 'periodo',
        'label' => 'Periodo analizado',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Ej: Ene 2024',
    ],
];
$moduleListColumns = [
    ['key' => 'producto', 'label' => 'Producto'],
    ['key' => 'costo_promedio', 'label' => 'Costo'],
    ['key' => 'precio_promedio_venta', 'label' => 'Precio promedio'],
    ['key' => 'utilidad_unitaria', 'label' => 'Utilidad unitaria'],
    ['key' => 'utilidad_total', 'label' => 'Utilidad total'],
    ['key' => 'periodo', 'label' => 'Periodo'],
];

include('partials/generic-page.php');
