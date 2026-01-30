<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de asistencia',
    'source' => 'hr/attendance/create',
    'template' => 'informeIcargaEspanol.php',
]);
