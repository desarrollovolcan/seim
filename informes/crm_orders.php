<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe orden de venta',
    'source' => 'crm/orders',
    'template' => 'informeIcargaEspanol.php',
]);
