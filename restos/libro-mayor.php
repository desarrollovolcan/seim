<?php
$pageTitle = 'Libro mayor';
$pageSubtitle = 'Contabilidad';
$pageDescription = 'Saldo y movimientos por cuenta contable.';
$moduleKey = 'libro-mayor';
$moduleMode = 'report';
$moduleFields = [
    [
        'name' => 'fecha_desde',
        'label' => 'Fecha desde',
        'type' => 'date',
        'required' => true,
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'fecha_hasta',
        'label' => 'Fecha hasta',
        'type' => 'date',
        'required' => true,
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'cuenta',
        'label' => 'Cuenta',
        'type' => 'text',
        'required' => true,
        'placeholder' => '1.01.001',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'nivel',
        'label' => 'Nivel',
        'type' => 'number',
        'required' => false,
        'placeholder' => '2',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'moneda',
        'label' => 'Moneda',
        'type' => 'select',
        'required' => false,
        'options' => [
            'CLP' => 'CLP',
            'USD' => 'USD',
            'EUR' => 'EUR',
        ],
        'col' => 'erp-field erp-field--third',
    ],
];
$moduleListColumns = [
    ['key' => 'cuenta', 'label' => 'Cuenta'],
    ['key' => 'descripcion', 'label' => 'Descripción'],
    ['key' => 'debe', 'label' => 'Debe'],
    ['key' => 'haber', 'label' => 'Haber'],
    ['key' => 'saldo', 'label' => 'Saldo'],
];

include('partials/generic-page.php');
