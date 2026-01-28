<?php
$pageTitle = 'Registrar venta';
$pageSubtitle = 'Ventas';
$pageDescription = 'Registro de ventas y emisión de comprobantes.';
$moduleKey = 'ventas-registrar';
$moduleTitleField = 'numero_venta';
$moduleFields = [
    [
        'type' => 'section',
        'label' => 'Datos de venta',
        'description' => 'Información general de la venta.',
    ],
    [
        'name' => 'fecha',
        'label' => 'Fecha',
        'type' => 'date',
        'required' => true,
        'default' => date('Y-m-d'),
    ],
    [
        'name' => 'numero_venta',
        'label' => 'Número de venta',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'VTA-0001',
    ],
    [
        'name' => 'cliente',
        'label' => 'Cliente',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Cliente (opcional)',
    ],
    [
        'type' => 'section',
        'label' => 'Datos del producto',
        'description' => 'Detalle del producto vendido.',
    ],
    [
        'name' => 'producto',
        'label' => 'Producto',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'cantidad',
        'label' => 'Cantidad',
        'type' => 'number',
        'required' => true,
        'step' => '1',
    ],
    [
        'name' => 'precio_unitario',
        'label' => 'Precio unitario',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
    ],
    [
        'name' => 'descuento',
        'label' => 'Descuento',
        'type' => 'number',
        'required' => false,
        'step' => '0.01',
        'placeholder' => '0.00',
    ],
    [
        'name' => 'subtotal',
        'label' => 'Subtotal',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
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
        'name' => 'utilidad_venta',
        'label' => 'Utilidad de la venta',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
    ],
    [
        'name' => 'forma_pago',
        'label' => 'Forma de pago',
        'type' => 'text',
        'required' => true,
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
    ['key' => 'utilidad_venta', 'label' => 'Utilidad'],
    ['key' => 'usuario_vendedor', 'label' => 'Vendedor'],
];

include('partials/generic-page.php');
