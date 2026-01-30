<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de cliente',
    'source' => 'clients/create',
    'template' => 'informeIcargaEspanol.php',
]);
