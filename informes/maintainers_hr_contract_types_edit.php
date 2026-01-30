<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe tipo de contrato',
    'source' => 'maintainers/hr-contract-types/edit',
    'template' => 'informeIcargaEspanol.php',
]);
