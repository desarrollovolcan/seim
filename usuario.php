<?php
$pageTitle = 'Usuario';
$pageSubtitle = 'Gestión de usuarios';
$pageDescription = 'Administra los usuarios del sistema.';
$moduleKey = 'usuario';
$moduleFields = [
    [
        'name' => 'nombre',
        'label' => 'Nombre completo',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Ingresa el nombre del usuario',
    ],
    [
        'name' => 'correo',
        'label' => 'Correo electrónico',
        'type' => 'email',
        'required' => true,
        'placeholder' => 'usuario@correo.com',
    ],
    [
        'name' => 'rol',
        'label' => 'Rol',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Administrador, Operador, etc.',
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
];
$moduleTitleField = 'nombre';
$moduleListColumns = [
    ['key' => 'nombre', 'label' => 'Nombre'],
    ['key' => 'correo', 'label' => 'Correo'],
    ['key' => 'rol', 'label' => 'Rol'],
    ['key' => 'estado', 'label' => 'Estado'],
    ['key' => 'created_at', 'label' => 'Creado'],
];

include('partials/generic-page.php');
