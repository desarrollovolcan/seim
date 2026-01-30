<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe institución de salud',
    'source' => 'maintainers/hr-health-providers/edit',
    'template' => 'informeIcargaEspanol.php',
]);
