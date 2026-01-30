<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de honorarios',
    'source' => 'honorarios/create',
    'template' => 'informeIcargaEspanol.php',
]);
