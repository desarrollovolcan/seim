<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de compra',
    'source' => 'purchases/create',
    'template' => 'informeIcargaEspanol.php',
]);
