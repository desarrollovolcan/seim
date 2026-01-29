<?php
$pageTitle = 'Asientos contables';
$pageSubtitle = 'Contabilidad';
$pageDescription = 'Registro de asientos con detalle de cuentas y montos.';
$moduleKey = 'asientos-contables';
$moduleTitleField = 'numero_asiento';
$moduleFields = [
    [
        'name' => 'numero_asiento',
        'label' => 'Número de asiento',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'AS-2024-001',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'fecha',
        'label' => 'Fecha',
        'type' => 'date',
        'required' => true,
        'default' => date('Y-m-d'),
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'comprobante',
        'label' => 'Comprobante',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'COMP-044',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'glosa',
        'label' => 'Glosa',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Registro de ingresos diarios',
        'col' => 'erp-field erp-field--two-thirds',
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
        'name' => 'moneda',
        'label' => 'Moneda',
        'type' => 'select',
        'required' => true,
        'options' => [
            'CLP' => 'CLP',
            'USD' => 'USD',
            'EUR' => 'EUR',
        ],
        'col' => 'erp-field erp-field--third',
    ],
];

$formAppendHtml = '
<div class="border rounded-3 p-3 bg-light-subtle">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <div>
            <h6 class="mb-0">Detalle del asiento</h6>
            <small class="text-muted">Agrega cuentas de débito y crédito.</small>
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm">Agregar línea</button>
    </div>
    <div class="table-responsive">
        <table class="table erp-table table-sm align-middle mb-0">
            <thead>
                <tr>
                    <th>Cuenta</th>
                    <th>Descripción</th>
                    <th class="text-end">Débito</th>
                    <th class="text-end">Crédito</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1.01.001</td>
                    <td>Caja general</td>
                    <td class="text-end">$ 450.000</td>
                    <td class="text-end">-</td>
                </tr>
                <tr>
                    <td>4.01.010</td>
                    <td>Ingresos por ventas</td>
                    <td class="text-end">-</td>
                    <td class="text-end">$ 450.000</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end mt-2">
        <div class="text-end">
            <div class="text-muted small">Balanceado</div>
            <strong>$ 450.000</strong>
        </div>
    </div>
</div>';

$moduleListColumns = [
    ['key' => 'numero_asiento', 'label' => 'Asiento'],
    ['key' => 'fecha', 'label' => 'Fecha'],
    ['key' => 'comprobante', 'label' => 'Comprobante'],
    ['key' => 'glosa', 'label' => 'Glosa'],
    ['key' => 'centro_costo', 'label' => 'Centro'],
    ['key' => 'moneda', 'label' => 'Moneda'],
];

include('partials/generic-page.php');
