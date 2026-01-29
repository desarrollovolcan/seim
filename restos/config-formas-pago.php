<?php
$pageTitle = 'Formas de pago';
$pageSubtitle = 'Configuración';
$pageDescription = 'Catálogo de formas de pago disponibles.';
$moduleKey = 'config-formas-pago';
$moduleFields = [
    [
        'name' => 'nombre',
        'label' => 'Nombre de la forma de pago',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'descripcion',
        'label' => 'Descripción',
        'type' => 'textarea',
        'required' => false,
        'rows' => 3,
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
    ['key' => 'nombre', 'label' => 'Forma de pago'],
    ['key' => 'estado', 'label' => 'Estado'],
    ['key' => 'descripcion', 'label' => 'Descripción'],
];

include('partials/generic-page.php');
