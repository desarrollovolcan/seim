<?php
$pageTitle = 'Entradas de productos';
$pageSubtitle = 'Inventario';
$pageDescription = 'Registro de ingresos de stock por compras o ajustes.';
$moduleKey = 'inventario-entradas';
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
        'name' => 'tipo_entrada',
        'label' => 'Tipo de entrada',
        'type' => 'select',
        'required' => true,
        'options' => [
            'compra' => 'Compra',
            'ajuste' => 'Ajuste',
            'devolucion' => 'Devolución',
            'traslado' => 'Traslado',
        ],
    ],
    [
        'name' => 'proveedor',
        'label' => 'Proveedor',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Proveedor (opcional)',
    ],
    [
        'name' => 'documento_respaldo',
        'label' => 'Documento de respaldo',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Factura / Guía / Orden',
    ],
    [
        'name' => 'bodega_destino',
        'label' => 'Bodega destino',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Bodega principal',
    ],
    [
        'name' => 'producto',
        'label' => 'Producto',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Producto ingresado',
    ],
    [
        'name' => 'cantidad',
        'label' => 'Cantidad',
        'type' => 'number',
        'required' => true,
        'step' => '1',
        'placeholder' => '0',
    ],
    [
        'name' => 'costo_unitario',
        'label' => 'Costo unitario',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
        'placeholder' => '0.00',
    ],
    [
        'name' => 'costo_total',
        'label' => 'Costo total',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
        'placeholder' => '0.00',
        'help' => 'Calculado según cantidad y costo unitario.',
    ],
    [
        'name' => 'usuario_responsable',
        'label' => 'Usuario responsable',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Responsable de la entrada',
    ],
    [
        'name' => 'observaciones',
        'label' => 'Observaciones',
        'type' => 'textarea',
        'required' => false,
        'rows' => 3,
        'placeholder' => 'Notas adicionales',
    ],
];
$moduleListColumns = [
    ['key' => 'fecha', 'label' => 'Fecha'],
    ['key' => 'tipo_entrada', 'label' => 'Tipo'],
    ['key' => 'producto', 'label' => 'Producto'],
    ['key' => 'cantidad', 'label' => 'Cantidad'],
    ['key' => 'costo_total', 'label' => 'Costo total'],
    ['key' => 'bodega_destino', 'label' => 'Bodega'],
];

include('partials/generic-page.php');
