<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe renovación',
    'source' => 'crm/renewals',
    'template' => 'informeIcargaEspanol.php',
]);
