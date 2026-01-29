<?php
$pageTitle = 'Empresa';
$pageSubtitle = 'Datos de la empresa';
$pageDescription = 'Registra y actualiza la información principal de la empresa.';
$moduleKey = 'empresa';
$moduleFields = [
    [
        'name' => 'nombre',
        'label' => 'Nombre de la empresa',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Razón social',
    ],
    [
        'name' => 'ruc',
        'label' => 'RUC / NIT',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Documento tributario',
    ],
    [
        'name' => 'direccion',
        'label' => 'Dirección',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Dirección fiscal',
    ],
    [
        'name' => 'telefono',
        'label' => 'Teléfono',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Número de contacto',
    ],
    [
        'name' => 'estado',
        'label' => 'Estado',
        'type' => 'select',
        'required' => true,
        'options' => [
            'activa' => 'Activa',
            'inactiva' => 'Inactiva',
        ],
    ],
];
$moduleTitleField = 'nombre';
$moduleListColumns = [
    ['key' => 'nombre', 'label' => 'Empresa'],
    ['key' => 'ruc', 'label' => 'RUC/NIT'],
    ['key' => 'direccion', 'label' => 'Dirección'],
    ['key' => 'telefono', 'label' => 'Teléfono'],
    ['key' => 'estado', 'label' => 'Estado'],
    ['key' => 'created_at', 'label' => 'Creado'],
];

include('partials/generic-page.php');
