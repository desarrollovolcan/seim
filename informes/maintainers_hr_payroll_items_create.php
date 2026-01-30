<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe ítem remuneración',
    'source' => 'maintainers/hr-payroll-items/create',
    'template' => 'informeIcargaEspanol.php',
]);
