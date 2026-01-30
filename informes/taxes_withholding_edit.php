<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de retención',
    'source' => 'taxes/withholding-edit',
    'template' => 'informeIcargaEspanol.php',
]);
