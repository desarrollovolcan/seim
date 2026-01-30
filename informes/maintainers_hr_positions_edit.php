<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe cargo',
    'source' => 'maintainers/hr-positions/edit',
    'template' => 'informeIcargaEspanol.php',
]);
