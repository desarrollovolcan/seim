<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe departamento',
    'source' => 'maintainers/hr-departments/create',
    'template' => 'informeIcargaEspanol.php',
]);
