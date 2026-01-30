<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de empresa',
    'source' => 'companies/edit',
    'template' => 'informeIcargaEspanol.php',
]);
