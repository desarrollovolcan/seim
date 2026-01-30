<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de cotización',
    'source' => 'quotes/edit',
    'template' => 'informeIcargaInvoice.php',
]);
