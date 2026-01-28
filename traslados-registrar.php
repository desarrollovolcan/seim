<?php
$pageTitle = 'Registrar traslado';
$pageSubtitle = 'Traslados';
$pageDescription = 'Registro de transferencias entre bodegas o sucursales.';
$moduleKey = 'traslados';
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
        'name' => 'bodega_origen',
        'label' => 'Bodega origen',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'bodega_destino',
        'label' => 'Bodega destino',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'producto',
        'label' => 'Producto',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'cantidad_productos',
        'label' => 'Cantidad de productos',
        'type' => 'number',
        'required' => true,
        'step' => '1',
        'help' => 'Total de ítems incluidos en el traslado.',
    ],
    [
        'name' => 'detalle_productos',
        'label' => 'Detalle de productos',
        'type' => 'textarea',
        'required' => true,
        'rows' => 4,
        'placeholder' => 'Ej: Producto A x 3, Producto B x 2',
    ],
    [
        'name' => 'cantidad',
        'label' => 'Cantidad',
        'type' => 'number',
        'required' => true,
        'step' => '1',
    ],
    [
        'name' => 'estado',
        'label' => 'Estado',
        'type' => 'select',
        'required' => true,
        'options' => [
            'pendiente' => 'Pendiente',
            'confirmado' => 'Confirmado',
        ],
    ],
    [
        'name' => 'usuario_envia',
        'label' => 'Usuario que envía',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'usuario_recibe',
        'label' => 'Usuario que recibe',
        'type' => 'text',
        'required' => false,
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
    ['key' => 'bodega_origen', 'label' => 'Origen'],
    ['key' => 'bodega_destino', 'label' => 'Destino'],
    ['key' => 'producto', 'label' => 'Producto'],
    ['key' => 'cantidad_productos', 'label' => 'Ítems'],
    ['key' => 'estado', 'label' => 'Estado'],
];

include('partials/generic-page.php');
