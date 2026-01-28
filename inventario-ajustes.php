<?php
$pageTitle = 'Ajustes de inventario';
$pageSubtitle = 'Inventario';
$pageDescription = 'Ajustes por diferencias, mermas o correcciones.';
$moduleKey = 'inventario-ajustes';
$moduleTitleField = 'producto';
$moduleFields = [
    [
        'name' => 'fecha',
        'label' => 'Fecha',
        'type' => 'date',
        'required' => true,
        'default' => date('Y-m-d'),
    ],
    [
        'name' => 'producto',
        'label' => 'Producto',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'bodega',
        'label' => 'Bodega',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'tipo_ajuste',
        'label' => 'Tipo de ajuste',
        'type' => 'select',
        'required' => true,
        'options' => [
            'aumento' => 'Aumento',
            'disminucion' => 'Disminución',
        ],
    ],
    [
        'name' => 'cantidad',
        'label' => 'Cantidad',
        'type' => 'number',
        'required' => true,
        'step' => '1',
    ],
    [
        'name' => 'motivo',
        'label' => 'Motivo',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'usuario_responsable',
        'label' => 'Usuario responsable',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'observaciones',
        'label' => 'Observaciones',
        'type' => 'textarea',
        'required' => false,
        'rows' => 3,
    ],
];
$moduleListColumns = [
    ['key' => 'fecha', 'label' => 'Fecha'],
    ['key' => 'producto', 'label' => 'Producto'],
    ['key' => 'bodega', 'label' => 'Bodega'],
    ['key' => 'tipo_ajuste', 'label' => 'Tipo'],
    ['key' => 'cantidad', 'label' => 'Cantidad'],
    ['key' => 'usuario_responsable', 'label' => 'Responsable'],
];

include('partials/generic-page.php');
