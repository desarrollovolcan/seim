<?php
$pageTitle = 'Datos de la empresa';
$pageSubtitle = 'Configuración';
$pageDescription = 'Configuración de datos corporativos.';
$moduleKey = 'config-empresa';
$moduleFields = [
    [
        'name' => 'nombre',
        'label' => 'Nombre de la empresa',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'razon_social',
        'label' => 'Razón social',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'ruc',
        'label' => 'RUT / RUC',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'telefono',
        'label' => 'Teléfono',
        'type' => 'tel',
        'required' => false,
    ],
    [
        'name' => 'correo',
        'label' => 'Correo',
        'type' => 'email',
        'required' => false,
    ],
    [
        'name' => 'direccion',
        'label' => 'Dirección',
        'type' => 'text',
        'required' => false,
    ],
    [
        'name' => 'moneda',
        'label' => 'Moneda',
        'type' => 'select',
        'required' => true,
        'options' => [
            'CLP' => 'Peso chileno (CLP)',
            'USD' => 'Dólar (USD)',
            'EUR' => 'Euro (EUR)',
        ],
    ],
];
$moduleListColumns = [
    ['key' => 'nombre', 'label' => 'Empresa'],
    ['key' => 'ruc', 'label' => 'RUT/RUC'],
    ['key' => 'correo', 'label' => 'Correo'],
    ['key' => 'moneda', 'label' => 'Moneda'],
];

include('partials/generic-page.php');
