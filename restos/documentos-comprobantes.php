<?php
$pageTitle = 'Documentos y comprobantes';
$pageSubtitle = 'Contabilidad';
$pageDescription = 'Control de documentos emitidos y recibidos.';
$moduleKey = 'documentos-comprobantes';
$moduleTitleField = 'numero';
$moduleFields = [
    [
        'name' => 'tipo_documento',
        'label' => 'Tipo de documento',
        'type' => 'select',
        'required' => true,
        'options' => [
            'factura' => 'Factura',
            'boleta' => 'Boleta',
            'nota_credito' => 'Nota de crédito',
            'nota_debito' => 'Nota de débito',
        ],
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'serie',
        'label' => 'Serie',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'A',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'numero',
        'label' => 'Número',
        'type' => 'text',
        'required' => true,
        'placeholder' => '000123',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'fecha_emision',
        'label' => 'Fecha de emisión',
        'type' => 'date',
        'required' => true,
        'default' => date('Y-m-d'),
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'razon_social',
        'label' => 'Cliente/Proveedor',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Nombre o razón social',
        'col' => 'erp-field erp-field--two-thirds',
    ],
    [
        'name' => 'monto_total',
        'label' => 'Monto total',
        'type' => 'text',
        'required' => true,
        'placeholder' => '$ 0',
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'estado',
        'label' => 'Estado',
        'type' => 'select',
        'required' => true,
        'options' => [
            'emitido' => 'Emitido',
            'recibido' => 'Recibido',
            'anulado' => 'Anulado',
        ],
        'col' => 'erp-field erp-field--third',
    ],
    [
        'name' => 'observaciones',
        'label' => 'Observaciones',
        'type' => 'textarea',
        'required' => false,
        'placeholder' => 'Notas adicionales',
        'rows' => 3,
        'col' => 'erp-field erp-field--full',
    ],
];
$moduleListColumns = [
    ['key' => 'tipo_documento', 'label' => 'Tipo'],
    ['key' => 'serie', 'label' => 'Serie'],
    ['key' => 'numero', 'label' => 'Número'],
    ['key' => 'razon_social', 'label' => 'Cliente/Proveedor'],
    ['key' => 'fecha_emision', 'label' => 'Emisión'],
    ['key' => 'monto_total', 'label' => 'Total'],
    ['key' => 'estado', 'label' => 'Estado'],
];

include('partials/generic-page.php');
