<?php
$pageTitle = 'Pérdidas y mermas';
$pageSubtitle = 'Reportes';
$pageDescription = 'Registro de pérdidas y mermas.';
$moduleKey = 'reportes-perdidas-mermas';
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
        'name' => 'motivo',
        'label' => 'Motivo',
        'type' => 'select',
        'required' => false,
        'options' => [
            'merma' => 'Merma',
            'perdida' => 'Pérdida',
            'ajuste' => 'Ajuste',
        ],
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'bodega',
        'label' => 'Bodega',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Bodega central',
        'col' => 'erp-field erp-field--third',
    ],
];
$moduleListColumns = [
    ['key' => 'fecha_desde', 'label' => 'Fecha'],
    ['key' => 'motivo', 'label' => 'Motivo'],
    ['key' => 'bodega', 'label' => 'Bodega'],
    ['key' => 'producto', 'label' => 'Producto'],
    ['key' => 'monto', 'label' => 'Monto'],
];

include('partials/generic-page.php');
