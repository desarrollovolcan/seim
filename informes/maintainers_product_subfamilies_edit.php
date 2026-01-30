<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe subfamilia de productos',
    'source' => 'maintainers/product-subfamilies/edit',
    'template' => 'informeIcargaEspanol.php',
]);
