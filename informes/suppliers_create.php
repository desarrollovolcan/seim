<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de proveedor',
    'source' => 'suppliers/create',
    'template' => 'informeIcargaEspanol.php',
]);
