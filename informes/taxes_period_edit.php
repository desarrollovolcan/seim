<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de periodo tributario',
    'source' => 'taxes/period-edit',
    'template' => 'informeIcargaEspanol.php',
]);
