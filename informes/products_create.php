<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de producto',
    'source' => 'products/create',
    'template' => 'informeIcargaEspanol.php',
]);
