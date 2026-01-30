<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de transacción',
    'source' => 'treasury/transaction-edit',
    'template' => 'informeIcargaEspanol.php',
]);
