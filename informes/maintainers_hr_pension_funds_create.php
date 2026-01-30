<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe AFP',
    'source' => 'maintainers/hr-pension-funds/create',
    'template' => 'informeIcargaEspanol.php',
]);
