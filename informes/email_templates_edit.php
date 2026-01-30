<?php

require_once __DIR__ . '/report-base.php';

generate_form_report([
    'title' => 'Informe plantilla de correo',
    'source' => 'email_templates/edit',
    'template' => 'informeIcargaEspanol.php',
]);
