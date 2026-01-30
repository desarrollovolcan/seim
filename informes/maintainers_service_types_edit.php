<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe tipo de servicio',
    'source' => 'maintainers/service-types/edit',
    'template' => 'informeIcargaEspanol.php',
]);
