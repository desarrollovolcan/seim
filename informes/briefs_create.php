<?php

require_once __DIR__ . '/../api/fpdf/FormTemplatePDF.php';

$briefId = (int)($_GET['id'] ?? 0);
if ($briefId <= 0) {
    http_response_code(404);
    echo 'Informe no encontrado.';
    exit;
}

$db = $GLOBALS['db'] ?? null;
if (!$db) {
    http_response_code(500);
    echo 'No se pudo generar el informe.';
    exit;
}

$companyId = current_company_id();
$params = ['id' => $briefId];
$companyFilter = '';
if ($companyId) {
    $companyFilter = ' AND commercial_briefs.company_id = :company_id';
    $params['company_id'] = $companyId;
}

try {
    $brief = $db->fetch(
        'SELECT commercial_briefs.*, clients.name as client_name, clients.email as client_email, clients.phone as client_phone
         FROM commercial_briefs
         JOIN clients ON commercial_briefs.client_id = clients.id
         WHERE commercial_briefs.deleted_at IS NULL AND commercial_briefs.id = :id' . $companyFilter,
        $params
    );
} catch (Throwable $e) {
    http_response_code(500);
    echo 'No se pudo generar el informe.';
    exit;
}

if (!$brief) {
    http_response_code(404);
    echo 'Informe no encontrado.';
    exit;
}

$normalizeText = static function ($text): string {
    $text = (string)($text ?? '');
    $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
    return $converted !== false ? $converted : utf8_decode($text);
};
$formatCurrency = static function ($value): string {
    $amount = (float)($value ?? 0);
    return '$' . number_format($amount, 0, ',', '.');
};

$pdf = new FormTemplatePDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->docTitle = $normalizeText('Informe de Brief Comercial');
$pdf->docSubTitle = $normalizeText('Resumen detallado del brief');
$pdf->brandName = $normalizeText('GoCreative GES');
$pdf->brandRUT = $normalizeText('76.123.456-7');
$pdf->brandAddress = $normalizeText('Gestión Empresarial y Servicios');
$pdf->brandContact = $normalizeText('soporte@gocreative.cl • +56 9 0000 0000 • gocreative.cl');
$pdf->footerLeft = $normalizeText('Documento generado automáticamente');
$pdf->SetTitle($normalizeText('Informe de brief comercial'));
$pdf->AddPage();

$pdf->Section(
    $normalizeText('Resumen del brief'),
    $normalizeText('Información base del brief comercial registrado.')
);

$pdf->FieldGrid([
    ['label' => $normalizeText('ID Brief'), 'value' => $normalizeText('#' . ($brief['id'] ?? ''))],
    ['label' => $normalizeText('Cliente'), 'value' => $normalizeText($brief['client_name'] ?? '')],
    ['label' => $normalizeText('Estado'), 'value' => $normalizeText(str_replace('_', ' ', $brief['status'] ?? ''))],
    ['label' => $normalizeText('Servicio'), 'value' => $normalizeText($brief['service_summary'] ?? '')],
    ['label' => $normalizeText('Presupuesto'), 'value' => $normalizeText($formatCurrency($brief['expected_budget'] ?? 0))],
    ['label' => $normalizeText('Fecha deseada'), 'value' => $normalizeText($brief['desired_start_date'] ?? '-')],
], 2);

$pdf->Section(
    $normalizeText('Contacto del cliente'),
    $normalizeText('Datos de contacto asociados al brief.')
);

$pdf->FieldGrid([
    ['label' => $normalizeText('Contacto'), 'value' => $normalizeText($brief['contact_name'] ?? '')],
    ['label' => $normalizeText('Correo'), 'value' => $normalizeText($brief['contact_email'] ?? ($brief['client_email'] ?? ''))],
    ['label' => $normalizeText('Teléfono'), 'value' => $normalizeText($brief['contact_phone'] ?? ($brief['client_phone'] ?? ''))],
    ['label' => $normalizeText('Fecha creación'), 'value' => $normalizeText($brief['created_at'] ?? '')],
], 2);

$notes = trim((string)($brief['notes'] ?? ''));
if ($notes === '') {
    $notes = 'Sin observaciones registradas.';
}

$pdf->NotesBlock(
    $normalizeText('Notas comerciales'),
    $normalizeText($notes)
);

$filename = 'brief-comercial-' . preg_replace('/[^a-z0-9\\-]+/i', '-', (string)($brief['id'] ?? '')) . '.pdf';
$pdf->Output('D', $filename);
exit;
