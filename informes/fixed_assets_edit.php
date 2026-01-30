<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de activo fijo',
    'source' => 'fixed-assets/edit',
    'template' => 'informeIcargaEspanol.php',
]);
