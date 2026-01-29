<?php
$pageTitle = 'Libro diario';
$pageSubtitle = 'Contabilidad';
$pageDescription = 'Detalle cronológico de operaciones contables.';
$moduleKey = 'libro-diario';
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
        'label' => 'Cuenta contable',
        'type' => 'text',
        'required' => false,
        'placeholder' => '1.01.001',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'centro_costo',
        'label' => 'Centro de costo',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Administración',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'tipo_asiento',
        'label' => 'Tipo de asiento',
        'type' => 'select',
        'required' => false,
        'options' => [
            'ingreso' => 'Ingreso',
            'egreso' => 'Egreso',
            'ajuste' => 'Ajuste',
        ],
        'col' => 'erp-field erp-field--third',
    ],
];
$moduleListColumns = [
    ['key' => 'fecha_desde', 'label' => 'Fecha'],
    ['key' => 'tipo_asiento', 'label' => 'Tipo'],
    ['key' => 'cuenta', 'label' => 'Cuenta'],
    ['key' => 'descripcion', 'label' => 'Detalle'],
    ['key' => 'debe', 'label' => 'Debe'],
    ['key' => 'haber', 'label' => 'Haber'],
];

include('partials/generic-page.php');
