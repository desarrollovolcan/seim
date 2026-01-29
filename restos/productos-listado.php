<?php
$pageTitle = 'Listado de productos';
$pageSubtitle = 'Productos';
$pageDescription = 'Catálogo general de productos registrados.';
$moduleKey = 'productos';
$currentUser = $_SESSION['user']['nombre'] ?? 'Usuario';
$currentUserLastName = $_SESSION['user']['apellido'] ?? '';
$usuarioCreador = trim($currentUser . ' ' . $currentUserLastName);
$moduleFields = [
    [
        'name' => 'nombre',
        'label' => 'Nombre del producto',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Ej: Harina 1kg',
    ],
    [
        'name' => 'codigo_interno',
        'label' => 'Código interno',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'SKU-001',
    ],
    [
        'name' => 'categoria',
        'label' => 'Categoría',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Abarrotes',
    ],
    [
        'name' => 'unidad_medida',
        'label' => 'Unidad de medida',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Unidad / Kg / Lt',
    ],
    [
        'name' => 'costo_base',
        'label' => 'Costo base',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
        'placeholder' => '0.00',
    ],
    [
        'name' => 'precio_venta',
        'label' => 'Precio de venta',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
        'placeholder' => '0.00',
    ],
    [
        'name' => 'stock_minimo',
        'label' => 'Stock mínimo',
        'type' => 'number',
        'required' => true,
        'step' => '1',
        'placeholder' => '0',
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
    [
        'name' => 'usuario_creador',
        'label' => 'Usuario creador',
        'type' => 'text',
        'required' => true,
        'default' => $usuarioCreador !== '' ? $usuarioCreador : 'Usuario',
        'readonly' => true,
    ],
];
$moduleListColumns = [
    ['key' => 'codigo_interno', 'label' => 'Código'],
    ['key' => 'nombre', 'label' => 'Producto'],
    ['key' => 'categoria', 'label' => 'Categoría'],
    ['key' => 'precio_venta', 'label' => 'Precio venta'],
    ['key' => 'stock_minimo', 'label' => 'Stock mínimo'],
    ['key' => 'estado', 'label' => 'Estado'],
    ['key' => 'fecha_creacion', 'label' => 'Creación'],
];

include('partials/generic-page.php');
