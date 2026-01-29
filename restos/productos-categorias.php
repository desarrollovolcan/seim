<?php
$pageTitle = 'Categorías';
$pageSubtitle = 'Productos';
$pageDescription = 'Administración de categorías y familias de productos.';
$moduleKey = 'categorias-productos';
$moduleFields = [
    [
        'name' => 'nombre',
        'label' => 'Nombre de la categoría',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Ej: Abarrotes',
    ],
    [
        'name' => 'descripcion',
        'label' => 'Descripción',
        'type' => 'textarea',
        'required' => false,
        'placeholder' => 'Detalle general de la categoría',
        'rows' => 4,
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
        'name' => 'fecha_creacion',
        'label' => 'Fecha de creación',
        'type' => 'date',
        'required' => true,
        'default' => date('Y-m-d'),
    ],
];
$moduleListColumns = [
    ['key' => 'nombre', 'label' => 'Categoría'],
    ['key' => 'estado', 'label' => 'Estado'],
    ['key' => 'fecha_creacion', 'label' => 'Creación'],
];

include('partials/generic-page.php');
