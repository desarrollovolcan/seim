<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe jornada laboral',
    'source' => 'maintainers/hr-work-schedules/edit',
    'template' => 'informeIcargaEspanol.php',
]);
