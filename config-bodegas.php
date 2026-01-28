<?php
$pageTitle = 'Bodegas / sucursales';
$pageSubtitle = 'Configuración';
$pageDescription = 'Gestión de bodegas y sucursales.';
$moduleKey = 'config-bodegas';
$moduleFields = [
    [
        'name' => 'nombre',
        'label' => 'Nombre de la bodega',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'direccion',
        'label' => 'Dirección',
        'type' => 'text',
        'required' => false,
    ],
    [
        'name' => 'ciudad',
        'label' => 'Ciudad',
        'type' => 'text',
        'required' => false,
    ],
    [
        'name' => 'telefono',
        'label' => 'Teléfono',
        'type' => 'tel',
        'required' => false,
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
    ['key' => 'nombre', 'label' => 'Bodega'],
    ['key' => 'ciudad', 'label' => 'Ciudad'],
    ['key' => 'telefono', 'label' => 'Teléfono'],
    ['key' => 'estado', 'label' => 'Estado'],
];

include('partials/generic-page.php');
