<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de contrato',
    'source' => 'hr/contracts/create',
    'template' => 'informeIcargaEspanol.php',
]);
