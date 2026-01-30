<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de asiento contable',
    'source' => 'accounting/journals-create',
    'template' => 'informeIcargaEspanol.php',
]);
