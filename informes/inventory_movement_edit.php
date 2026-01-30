<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe de movimiento de inventario',
    'source' => 'inventory/movement-edit',
    'template' => 'informeIcargaEspanol.php',
]);
