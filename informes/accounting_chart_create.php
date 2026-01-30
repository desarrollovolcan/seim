<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de plan de cuentas',
    'source' => 'accounting/chart-create',
    'template' => 'informeIcargaEspanol.php',
]);
