<?php
require_once __DIR__ . '/app/bootstrap.php';

$pageTitle = 'Registrar venta';
$pageSubtitle = 'Ventas';
$pageDescription = 'Registro de ventas con cabecera y detalle de productos.';
$moduleKey = 'ventas-registrar';
$moduleTitleField = 'numero_guia';

$currentUser = $_SESSION['user']['nombre'] ?? 'Usuario';
$currentUserLastName = $_SESSION['user']['apellido'] ?? '';
$usuarioVendedor = trim($currentUser . ' ' . $currentUserLastName);

$productosDisponibles = [];
$productoPrecios = [];
try {
    $stmt = db()->prepare('SELECT id, nombre, data FROM module_records WHERE module_key = ? ORDER BY created_at DESC');
    $stmt->execute(['productos']);
    $productos = $stmt->fetchAll();
    foreach ($productos as $producto) {
        $decoded = [];
        if (!empty($producto['data'])) {
            $decoded = json_decode((string) $producto['data'], true);
        }
        $precioVenta = isset($decoded['precio_venta']) ? (float) $decoded['precio_venta'] : 0.0;
        $nombre = (string) ($producto['nombre'] ?? 'Producto');
        $productosDisponibles[(string) $producto['id']] = sprintf('%s - Precio: %0.2f', $nombre, $precioVenta);
        $productoPrecios[(string) $producto['id']] = $precioVenta;
    }
} catch (Exception $e) {
    $productosDisponibles = [];
    $productoPrecios = [];
} catch (Error $e) {
    $productosDisponibles = [];
    $productoPrecios = [];
}

$moduleFields = [
    [
        'type' => 'section',
        'label' => 'Cabecera de la guía',
        'description' => 'Datos generales de la guía de venta.',
    ],
    [
        'name' => 'fecha_guia',
        'label' => 'Fecha de guía',
        'type' => 'date',
        'required' => true,
        'default' => date('Y-m-d'),
    ],
    [
        'name' => 'numero_guia',
        'label' => 'Número de guía',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'GUIA-0001',
    ],
    [
        'name' => 'cliente',
        'label' => 'Cliente',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Nombre o razón social',
    ],
    [
        'name' => 'direccion_entrega',
        'label' => 'Dirección de entrega',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Dirección o punto de entrega',
    ],
    [
        'name' => 'forma_pago',
        'label' => 'Forma de pago',
        'type' => 'text',
        'required' => true,
    ],
    [
        'name' => 'usuario_vendedor',
        'label' => 'Usuario vendedor',
        'type' => 'text',
        'required' => true,
        'default' => $usuarioVendedor !== '' ? $usuarioVendedor : 'Usuario',
        'readonly' => true,
    ],
    [
        'type' => 'section',
        'label' => 'Detalle de la guía',
        'description' => 'Selecciona productos ya creados y define cantidades.',
    ],
    [
        'name' => 'producto_id',
        'label' => 'Producto',
        'type' => 'select',
        'required' => true,
        'options' => $productosDisponibles,
        'help' => 'Se listan los productos creados en el catálogo.',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'cantidad',
        'label' => 'Cantidad',
        'type' => 'number',
        'required' => true,
        'step' => '1',
        'default' => 1,
        'col' => 'erp-field erp-field--sixth',
    ],
    [
        'name' => 'precio_venta',
        'label' => 'Precio de venta',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
        'readonly' => true,
        'placeholder' => '0.00',
        'help' => 'Se completa con el precio de venta del producto seleccionado.',
        'col' => 'erp-field erp-field--sixth',
    ],
    [
        'name' => 'subtotal',
        'label' => 'Subtotal',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
        'readonly' => true,
        'placeholder' => '0.00',
        'col' => 'erp-field erp-field--sixth',
    ],
    [
        'name' => 'total_venta',
        'label' => 'Total venta',
        'type' => 'number',
        'required' => true,
        'step' => '0.01',
        'readonly' => true,
        'placeholder' => '0.00',
        'col' => 'erp-field erp-field--sixth',
    ],
    [
        'name' => 'observaciones',
        'label' => 'Observaciones',
        'type' => 'textarea',
        'required' => false,
        'rows' => 3,
        'placeholder' => 'Notas adicionales para la guía.',
    ],
];
$moduleListColumns = [
    ['key' => 'numero_guia', 'label' => 'Guía'],
    ['key' => 'fecha_guia', 'label' => 'Fecha'],
    ['key' => 'cliente', 'label' => 'Cliente'],
    ['key' => 'producto_id', 'label' => 'Producto'],
    ['key' => 'cantidad', 'label' => 'Cantidad'],
    ['key' => 'total_venta', 'label' => 'Total'],
    ['key' => 'usuario_vendedor', 'label' => 'Vendedor'],
];

$pageInlineScript = sprintf(
    <<<'SCRIPT'
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const priceMap = %s;
        const productSelect = document.getElementById('module-producto_id');
        const cantidadInput = document.getElementById('module-cantidad');
        const precioInput = document.getElementById('module-precio_venta');
        const subtotalInput = document.getElementById('module-subtotal');
        const totalInput = document.getElementById('module-total_venta');

        const updateTotals = () => {
            if (!productSelect || !cantidadInput) {
                return;
            }
            const selectedId = productSelect.value;
            const priceValue = selectedId && priceMap[selectedId] ? parseFloat(priceMap[selectedId]) : null;
            if (priceValue !== null && precioInput) {
                precioInput.value = priceValue.toFixed(2);
            }

            const cantidad = cantidadInput.value !== '' ? parseFloat(cantidadInput.value) : null;
            if (priceValue !== null && cantidad !== null && !Number.isNaN(cantidad)) {
                const subtotal = priceValue * cantidad;
                if (subtotalInput) {
                    subtotalInput.value = subtotal.toFixed(2);
                }
                if (totalInput) {
                    totalInput.value = subtotal.toFixed(2);
                }
            }
        };

        if (productSelect) {
            productSelect.addEventListener('change', updateTotals);
        }
        if (cantidadInput) {
            cantidadInput.addEventListener('input', updateTotals);
        }
        updateTotals();
    });
</script>
SCRIPT,
    json_encode($productoPrecios, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)
);

include('partials/generic-page.php');
