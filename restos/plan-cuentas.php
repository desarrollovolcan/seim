<?php
$pageTitle = 'Plan de cuentas';
$pageSubtitle = 'Contabilidad';
$pageDescription = 'Estructura contable con clasificación por niveles y estado.';
$moduleKey = 'plan-cuentas';
$moduleTitleField = 'codigo';
$moduleFields = [
    [
        'name' => 'codigo',
        'label' => 'Código contable',
        'type' => 'text',
        'required' => true,
        'placeholder' => '1.01.001',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'nombre_cuenta',
        'label' => 'Nombre de la cuenta',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Caja general',
        'col' => 'erp-field erp-field--two-thirds',
    ],
    [
        'name' => 'tipo',
        'label' => 'Tipo de cuenta',
        'type' => 'select',
        'required' => true,
        'options' => [
            'activo' => 'Activo',
            'pasivo' => 'Pasivo',
            'patrimonio' => 'Patrimonio',
            'ingreso' => 'Ingreso',
            'gasto' => 'Gasto',
        ],
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'nivel',
        'label' => 'Nivel',
        'type' => 'number',
        'required' => true,
        'placeholder' => '1',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'cuenta_padre',
        'label' => 'Cuenta padre',
        'type' => 'text',
        'required' => false,
        'placeholder' => '1.01',
        'col' => 'erp-field erp-field--third',
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
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'descripcion',
        'label' => 'Descripción',
        'type' => 'textarea',
        'required' => false,
        'placeholder' => 'Notas sobre la cuenta',
        'rows' => 3,
        'col' => 'erp-field erp-field--full',
    ],
];
$moduleListColumns = [
    ['key' => 'codigo', 'label' => 'Código'],
    ['key' => 'nombre_cuenta', 'label' => 'Cuenta'],
    ['key' => 'tipo', 'label' => 'Tipo'],
    ['key' => 'nivel', 'label' => 'Nivel'],
    ['key' => 'cuenta_padre', 'label' => 'Cuenta padre'],
    ['key' => 'estado', 'label' => 'Estado'],
];

include('partials/generic-page.php');
