<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de empleado',
    'source' => 'hr/employees/create',
    'template' => 'informeIcargaEspanol.php',
]);
