<?php
require_once __DIR__ . '/app/bootstrap.php';

$pageTitle = 'Registrar venta';
$pageSubtitle = 'Ventas';
$pageDescription = 'Completa la cotización con datos generales, tributarios e ítems del servicio.';
$moduleKey = 'ventas-registrar';
$moduleTitleField = 'numero';
$hideModuleTable = true;
$hideModuleIntro = true;

$moduleFields = [
    [
        'name' => 'numero',
        'label' => 'Número',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'COT-000016',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'fecha_emision',
        'label' => 'Fecha emisión',
        'type' => 'date',
        'required' => true,
        'default' => date('Y-m-d'),
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'estado',
        'label' => 'Estado',
        'type' => 'select',
        'required' => true,
        'options' => [
            'Pendiente' => 'Pendiente',
            'Aprobada' => 'Aprobada',
            'Anulada' => 'Anulada',
        ],
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'cliente',
        'label' => 'Cliente',
        'type' => 'select',
        'required' => true,
        'options' => [
            'cliente_demo' => 'Cliente demo',
            'cliente_plus' => 'Cliente Plus',
        ],
        'col' => 'erp-field erp-field--two-thirds',
    ],
    [
        'name' => 'proyecto',
        'label' => 'Proyecto',
        'type' => 'select',
        'required' => true,
        'options' => [
            'proyecto_a' => 'Proyecto A',
            'proyecto_b' => 'Proyecto B',
        ],
        'col' => 'erp-field erp-field--third',
    ],
    [
        'type' => 'group_start',
        'label' => 'Datos tributarios (SII)',
        'description' => 'Estos datos se toman desde la ficha del cliente.',
    ],
    [
        'name' => 'tipo_documento',
        'label' => 'Tipo de documento',
        'type' => 'select',
        'required' => true,
        'options' => [
            'Factura electrónica' => 'Factura electrónica',
            'Boleta electrónica' => 'Boleta electrónica',
            'Nota de crédito' => 'Nota de crédito',
        ],
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'folio_documento',
        'label' => 'Folio / N° documento',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Ingresa el folio',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'tasa_impuesto',
        'label' => 'Tasa impuesto (%)',
        'type' => 'number',
        'required' => true,
        'default' => '19',
        'step' => '0.01',
        'col' => 'erp-field erp-field--sixth',
    ],
    [
        'name' => 'monto_exento',
        'label' => 'Monto exento',
        'type' => 'number',
        'required' => false,
        'step' => '0.01',
        'default' => '0',
        'col' => 'erp-field erp-field--sixth',
    ],
    [
        'name' => 'rut_receptor',
        'label' => 'RUT Receptor',
        'type' => 'text',
        'required' => true,
        'placeholder' => '12.345.678-9',
        'col' => 'erp-field erp-field--quarter',
    ],
    [
        'name' => 'razon_social',
        'label' => 'Razón social',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Razón social',
        'col' => 'erp-field erp-field--quarter',
    ],
    [
        'name' => 'giro',
        'label' => 'Giro',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Giro',
        'col' => 'erp-field erp-field--quarter',
    ],
    [
        'name' => 'codigo_actividad',
        'label' => 'Código actividad',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Código actividad',
        'col' => 'erp-field erp-field--quarter',
    ],
    [
        'name' => 'direccion',
        'label' => 'Dirección',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Dirección',
        'col' => 'erp-field erp-field--two-thirds',
    ],
    [
        'name' => 'comuna',
        'label' => 'Comuna',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Comuna',
        'col' => 'erp-field erp-field--quarter',
    ],
    [
        'name' => 'ciudad',
        'label' => 'Ciudad',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Ciudad',
        'col' => 'erp-field erp-field--quarter',
    ],
    [
        'type' => 'group_end',
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

$formAppendHtml = '
<div class="erp-form-card mt-2">
    <div class="erp-form-card__header">
        <div>
            <h6 class="mb-0">Items de cotización</h6>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm">Agregar item manual</button>
            <select class="form-select form-select-sm" style="min-width: 220px;">
                <option value="">Selecciona servicio</option>
                <option>Servicio de implementación</option>
                <option>Servicio de soporte</option>
                <option>Consultoría especializada</option>
            </select>
            <button type="button" class="btn btn-outline-primary btn-sm">Agregar servicio</button>
        </div>
    </div>
    <div class="erp-form-card__body">
        <div class="table-responsive">
            <table class="table erp-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th class="text-center" style="min-width: 120px;">Cantidad</th>
                        <th class="text-center" style="min-width: 150px;">Precio unitario</th>
                        <th class="text-center" style="min-width: 120px;">Impuesto %</th>
                        <th class="text-center" style="min-width: 120px;">Impuesto $</th>
                        <th class="text-center" style="min-width: 120px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" class="form-control" placeholder=""></td>
                        <td><input type="number" class="form-control text-center" value="1"></td>
                        <td><input type="number" class="form-control text-center" value="0"></td>
                        <td><input type="number" class="form-control text-center" value="0"></td>
                        <td><input type="number" class="form-control text-center" value="0.00"></td>
                        <td><input type="number" class="form-control text-center" value="0.00"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row g-3 mt-3">
    <div class="col-lg-3 col-md-6">
        <label class="form-label">Impuesto (%)</label>
        <input type="number" class="form-control" value="0">
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="impuestoAplicar">
            <label class="form-check-label small text-muted" for="impuestoAplicar">Aplicar impuesto</label>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <label class="form-label">Subtotal</label>
        <input type="number" class="form-control" value="0.00">
    </div>
    <div class="col-lg-3 col-md-6">
        <label class="form-label">Impuestos</label>
        <input type="number" class="form-control" value="0.00">
    </div>
    <div class="col-lg-3 col-md-6">
        <label class="form-label">Total</label>
        <input type="number" class="form-control" value="0.00">
    </div>
</div>';

$moduleListColumns = [
    ['key' => 'numero', 'label' => 'Número'],
    ['key' => 'fecha_emision', 'label' => 'Fecha'],
    ['key' => 'cliente', 'label' => 'Cliente'],
    ['key' => 'estado', 'label' => 'Estado'],
];

include('partials/generic-page.php');
