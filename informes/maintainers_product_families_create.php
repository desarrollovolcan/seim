<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe familia de productos',
    'source' => 'maintainers/product-families/create',
    'template' => 'informeIcargaEspanol.php',
]);
