<?php
$pageTitle = 'Parámetros de inventario';
$pageSubtitle = 'Configuración';
$pageDescription = 'Parámetros globales de inventario.';
$moduleKey = 'config-parametros-inventario';
$moduleTitleField = 'metodo_costeo';
$moduleFields = [
    [
        'name' => 'stock_minimo_default',
        'label' => 'Stock mínimo por defecto',
        'type' => 'number',
        'required' => true,
        'step' => '1',
    ],
    [
        'name' => 'stock_maximo_default',
        'label' => 'Stock máximo por defecto',
        'type' => 'number',
        'required' => false,
        'step' => '1',
    ],
    [
        'name' => 'metodo_costeo',
        'label' => 'Método de costeo',
        'type' => 'select',
        'required' => true,
        'options' => [
            'promedio' => 'Promedio ponderado',
            'fifo' => 'FIFO',
            'lifo' => 'LIFO',
        ],
    ],
    [
        'name' => 'parametros_costos',
        'label' => 'Parámetros de costos',
        'type' => 'textarea',
        'required' => false,
        'rows' => 3,
        'placeholder' => 'Reglas o consideraciones para costeo',
    ],
];
$moduleListColumns = [
    ['key' => 'stock_minimo_default', 'label' => 'Stock mínimo'],
    ['key' => 'stock_maximo_default', 'label' => 'Stock máximo'],
    ['key' => 'metodo_costeo', 'label' => 'Costeo'],
];

include('partials/generic-page.php');
