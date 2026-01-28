<?php
$pageTitle = 'Impuestos';
$pageSubtitle = 'Configuración';
$pageDescription = 'Configuración de impuestos y tasas.';
$moduleKey = 'config-impuestos';
$moduleFields = [
    [
        'name' => 'nombre',
        'label' => 'Nombre del impuesto',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'porcentaje',
        'label' => 'Porcentaje',
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
            'activo' => 'Activo',
            'inactivo' => 'Inactivo',
        ],
    ],
];
$moduleListColumns = [
    ['key' => 'nombre', 'label' => 'Impuesto'],
    ['key' => 'porcentaje', 'label' => 'Porcentaje'],
    ['key' => 'estado', 'label' => 'Estado'],
];

include('partials/generic-page.php');
