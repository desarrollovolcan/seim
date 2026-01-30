<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe familia de productos',
    'source' => 'maintainers/product-families/edit',
    'template' => 'informeIcargaEspanol.php',
]);
