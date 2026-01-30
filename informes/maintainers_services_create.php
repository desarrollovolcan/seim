<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe servicio',
    'source' => 'maintainers/services/create',
    'template' => 'informeIcargaEspanol.php',
]);
