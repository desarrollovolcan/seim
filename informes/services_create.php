<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de servicio',
    'source' => 'services/create',
    'template' => 'informeIcargaEspanol.php',
]);
