<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de venta',
    'source' => 'sales/create',
    'template' => 'informeIcargaEspanol.php',
]);
