<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe solicitud de servicio',
    'source' => 'crm/hub/service',
    'template' => 'informeIcargaEspanol.php',
]);
