<?php
$pageTitle = 'Proveedores';
$pageSubtitle = 'Compras';
$pageDescription = 'Gestión de proveedores y condiciones comerciales.';
$moduleKey = 'proveedores';
$moduleFields = [
    [
        'name' => 'nombre',
        'label' => 'Proveedor',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Proveedor ABC',
        'col' => 'erp-field erp-field--two-thirds',
    ],
    [
        'name' => 'rut',
        'label' => 'RUT / Identificación',
        'type' => 'text',
        'required' => true,
        'placeholder' => '99.999.999-9',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'correo',
        'label' => 'Correo',
        'type' => 'email',
        'required' => false,
        'placeholder' => 'proveedor@correo.cl',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'telefono',
        'label' => 'Teléfono',
        'type' => 'tel',
        'required' => false,
        'placeholder' => '+56 9 1234 5678',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'condicion_pago',
        'label' => 'Condición de pago',
        'type' => 'select',
        'required' => true,
        'options' => [
            'contado' => 'Contado',
            '30_dias' => '30 días',
            '60_dias' => '60 días',
        ],
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'direccion',
        'label' => 'Dirección',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Dirección comercial',
        'col' => 'erp-field erp-field--two-thirds',
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
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'observaciones',
        'label' => 'Observaciones',
        'type' => 'textarea',
        'required' => false,
        'placeholder' => 'Notas internas',
        'rows' => 3,
        'col' => 'erp-field erp-field--full',
    ],
];
$moduleListColumns = [
    ['key' => 'nombre', 'label' => 'Proveedor'],
    ['key' => 'rut', 'label' => 'Identificación'],
    ['key' => 'correo', 'label' => 'Correo'],
    ['key' => 'telefono', 'label' => 'Teléfono'],
    ['key' => 'condicion_pago', 'label' => 'Condición de pago'],
    ['key' => 'estado', 'label' => 'Estado'],
];

include('partials/generic-page.php');
