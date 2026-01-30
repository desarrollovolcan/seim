<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe servicio',
    'source' => 'maintainers/services/edit',
    'template' => 'informeIcargaEspanol.php',
]);
