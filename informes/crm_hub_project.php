<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe registro rápido de proyecto',
    'source' => 'crm/hub/project',
    'template' => 'informeIcargaEspanol.php',
]);
