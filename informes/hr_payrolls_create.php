<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de nómina',
    'source' => 'hr/payrolls/create',
    'template' => 'informeIcargaEspanol.php',
]);
