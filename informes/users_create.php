<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de usuario',
    'source' => 'users/create',
    'template' => 'informeIcargaEspanol.php',
]);
