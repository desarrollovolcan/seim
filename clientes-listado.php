<?php
$pageTitle = 'Listado de clientes';
$pageSubtitle = 'Clientes';
$pageDescription = 'Administración del padrón de clientes.';
$moduleKey = 'clientes';
$moduleFields = [
    [
        'name' => 'nombre',
        'label' => 'Nombre o razón social',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Cliente',
    ],
    [
        'name' => 'rut',
        'label' => 'RUT / identificación',
        'type' => 'text',
        'required' => true,
        'placeholder' => '99.999.999-9',
    ],
    [
        'name' => 'telefono',
        'label' => 'Teléfono',
        'type' => 'tel',
        'required' => false,
        'placeholder' => '+56 9 1234 5678',
    ],
    [
        'name' => 'correo',
        'label' => 'Correo',
        'type' => 'email',
        'required' => false,
        'placeholder' => 'cliente@correo.cl',
    ],
    [
        'name' => 'direccion',
        'label' => 'Dirección',
        'type' => 'text',
        'required' => false,
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
    ],
    [
        'name' => 'fecha_registro',
        'label' => 'Fecha de registro',
        'type' => 'date',
        'required' => true,
        'default' => date('Y-m-d'),
    ],
];
$moduleListColumns = [
    ['key' => 'nombre', 'label' => 'Cliente'],
    ['key' => 'rut', 'label' => 'Identificación'],
    ['key' => 'correo', 'label' => 'Correo'],
    ['key' => 'telefono', 'label' => 'Teléfono'],
    ['key' => 'estado', 'label' => 'Estado'],
    ['key' => 'fecha_registro', 'label' => 'Registro'],
];

include('partials/generic-page.php');
