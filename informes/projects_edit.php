<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de proyecto',
    'source' => 'projects/edit',
    'template' => 'informeIcargaEspanol.php',
]);
