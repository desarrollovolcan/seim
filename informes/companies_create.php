<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de empresa',
    'source' => 'companies/create',
    'template' => 'informeIcargaEspanol.php',
]);
