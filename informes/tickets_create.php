<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de ticket',
    'source' => 'tickets/create',
    'template' => 'informeIcargaEspanol.php',
]);
