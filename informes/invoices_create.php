<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de factura',
    'source' => 'invoices/create',
    'template' => 'informeIcargaInvoice.php',
]);
