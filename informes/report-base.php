<?php

require_once __DIR__ . '/../api/fpdf/InvoiceTemplatePDF.php';

function generate_form_report(array $config): void
{
    $title = $config['title'] ?? 'Informe de formulario';
    $source = $config['source'] ?? 'formulario';
    $template = $config['template'] ?? 'plantilla';
    $data = $config['data'] ?? $_POST;

    $normalizeText = static function ($text): string {
        $text = (string)($text ?? '');
        $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
        return $converted !== false ? $converted : utf8_decode($text);
    };
    $formatValue = static function ($value): string {
        if (is_bool($value)) {
            return $value ? 'Sí' : 'No';
        }
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        return trim((string)$value);
    };
    $shouldSkipField = static function (string $key): bool {
        $lower = strtolower($key);
        if ($lower === 'csrf_token' || $lower === 'report_template' || $lower === 'report_source') {
            return true;
        }
        return str_contains($lower, 'password');
    };
    $buildFieldRows = static function (array $payload) use ($formatValue, $shouldSkipField): array {
        $fields = [];
        foreach ($payload as $key => $value) {
            if ($shouldSkipField((string)$key)) {
                continue;
            }
            if (is_array($value)) {
                continue;
            }
            $label = ucwords(str_replace(['_', '-'], ' ', (string)$key));
            $fields[] = [
                'label' => $label,
                'value' => $formatValue($value),
            ];
        }
        return $fields;
    };

    $pdf = new InvoiceTemplatePDF('P', 'mm', 'Letter');
    $pdf->AliasNbPages();
    $pdf->docTitle = $normalizeText($title);
    $pdf->docSubTitle = $normalizeText('Informe generado para el formulario solicitado');
    $pdf->brandName = $normalizeText('GoCreative GES');
    $pdf->brandRUT = $normalizeText('76.123.456-7');
    $pdf->brandAddress = $normalizeText('Gestión Empresarial y Servicios');
    $pdf->brandContact = $normalizeText('soporte@gocreative.cl • +56 9 0000 0000 • gocreative.cl');
    $pdf->footerLeft = $normalizeText('Documento generado automáticamente');
    $pdf->SetTitle($normalizeText($title));
    $pdf->AddPage();

    $actionLabel = 'Detalle';
    if (strpos($source, 'create') !== false) {
        $actionLabel = 'Creación';
    } elseif (strpos($source, 'edit') !== false) {
        $actionLabel = 'Actualización';
    }

    $pdf->Section(
        $normalizeText('Resumen del formulario'),
        $normalizeText('Información base del reporte vinculado al formulario.')
    );
    $pdf->FieldGrid([
        ['label' => $normalizeText('Formulario'), 'value' => $normalizeText($source)],
        ['label' => $normalizeText('Acción'), 'value' => $normalizeText($actionLabel)],
        ['label' => $normalizeText('Plantilla base'), 'value' => $normalizeText(pathinfo($template, PATHINFO_FILENAME))],
        ['label' => $normalizeText('Fecha de generación'), 'value' => $normalizeText(date('d/m/Y H:i'))],
    ], 2);

    $fields = $buildFieldRows($data);
    if ($fields) {
        $pdf->Section(
            $normalizeText('Datos del formulario'),
            $normalizeText('Resumen de la información enviada desde el formulario.')
        );
        $pdf->FieldGrid(array_map(static function (array $field) use ($normalizeText): array {
            return [
                'label' => $normalizeText($field['label']),
                'value' => $normalizeText($field['value']),
            ];
        }, $fields), 2);
    }

    if (!empty($data['items']) && is_array($data['items'])) {
        $rows = [];
        foreach ($data['items'] as $item) {
            if (!is_array($item)) {
                continue;
            }
            $rows[] = [
                $item['descripcion'] ?? ($item['detalle'] ?? ''),
                $item['cantidad'] ?? '',
                $item['precio_unitario'] ?? ($item['precio'] ?? ''),
                $item['total'] ?? '',
            ];
        }
        if ($rows) {
            $pdf->Section(
                $normalizeText('Detalle de ítems'),
                $normalizeText('Listado de líneas asociadas al formulario.')
            );
            $pdf->DataTable(
                [
                    $normalizeText('Detalle'),
                    $normalizeText('Cantidad'),
                    $normalizeText('Precio'),
                    $normalizeText('Total'),
                ],
                array_map(static function (array $row) use ($normalizeText, $formatValue): array {
                    return [
                        $normalizeText($formatValue($row[0] ?? '')),
                        $normalizeText($formatValue($row[1] ?? '')),
                        $normalizeText($formatValue($row[2] ?? '')),
                        $normalizeText($formatValue($row[3] ?? '')),
                    ];
                }, $rows),
                [80, 30, 40, 40],
                ['L', 'C', 'R', 'R']
            );
        }
    }

    $pdf->Section(
        $normalizeText('Detalle del informe'),
        $normalizeText('Cada formulario tiene su propio informe generado con la plantilla de diseño.')
    );
    $pdf->NotesBlock(
        $normalizeText('Observaciones'),
        $normalizeText('Este documento fue generado con FPDF usando la plantilla de informes y corresponde al formulario solicitado.')
    );

    $filename = 'informe-' . preg_replace('/[^a-z0-9\\-]+/i', '-', pathinfo($template, PATHINFO_FILENAME)) . '.pdf';
    $pdf->Output('D', $filename);
    exit;
}
