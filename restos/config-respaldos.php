<?php
$pageTitle = 'Respaldos del sistema';
$pageSubtitle = 'Configuración';
$pageDescription = 'Gestión de respaldos y restauración.';
$moduleKey = 'config-respaldos';
$moduleFields = [
    [
        'name' => 'nombre',
        'label' => 'Nombre del respaldo',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'fecha_respaldo',
        'label' => 'Fecha del respaldo',
        'type' => 'date',
        'required' => true,
        'default' => date('Y-m-d'),
    ],
    [
        'name' => 'tipo_respaldo',
        'label' => 'Tipo de respaldo',
        'type' => 'select',
        'required' => true,
        'options' => [
            'completo' => 'Completo',
            'incremental' => 'Incremental',
        ],
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
    ['key' => 'nombre', 'label' => 'Respaldo'],
    ['key' => 'tipo_respaldo', 'label' => 'Tipo'],
    ['key' => 'fecha_respaldo', 'label' => 'Fecha'],
];

include('partials/generic-page.php');
