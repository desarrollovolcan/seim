<?php
$pageTitle = 'Historial de ventas';
$pageSubtitle = 'Ventas';
$pageDescription = 'Consulta de ventas realizadas.';
$moduleKey = 'ventas-historial';
$moduleTitleField = 'numero_venta';
$moduleFields = [
    [
        'name' => 'numero_venta',
        'label' => 'Número de venta',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'fecha',
        'label' => 'Fecha',
        'type' => 'date',
        'required' => true,
        'default' => date('Y-m-d'),
    ],
    [
        'name' => 'cliente',
        'label' => 'Cliente',
        'type' => 'text',
        'required' => false,
    ],
    [
        'name' => 'total_venta',
        'label' => 'Total venta',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
    ],
    [
        'name' => 'costo_total',
        'label' => 'Costo total',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
    ],
    [
        'name' => 'utilidad',
        'label' => 'Utilidad',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
    ],
    [
        'name' => 'estado',
        'label' => 'Estado',
        'type' => 'select',
        'required' => true,
        'options' => [
            'activa' => 'Activa',
            'anulada' => 'Anulada',
        ],
    ],
    [
        'name' => 'usuario_vendedor',
        'label' => 'Usuario vendedor',
        'type' => 'text',
        'required' => true,
    ],
];
$moduleListColumns = [
    ['key' => 'numero_venta', 'label' => 'Venta'],
    ['key' => 'fecha', 'label' => 'Fecha'],
    ['key' => 'cliente', 'label' => 'Cliente'],
    ['key' => 'total_venta', 'label' => 'Total'],
    ['key' => 'utilidad', 'label' => 'Utilidad'],
    ['key' => 'estado', 'label' => 'Estado'],
];

include('partials/generic-page.php');
