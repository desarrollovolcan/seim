<?php

require_once __DIR__ . '/fpdf.php';

/**
 * Plantilla minimalista tipo Factura/Informe (FPDF)
 * - Secciones
 * - Grillas de campos
 * - Tablas
 * - “Cards” suaves
 * - Chips de estado
 * - Gráficos simples (barras/linea)
 *
 * Recomendación: usa Letter o A4. Esta plantilla está pensada para Letter.
 */
class InvoiceTemplatePDF extends FPDF
{
    // ====== BRAND / ESTILO ======
    public array $brand = [
        'primary' => [90, 43, 214],     // morado principal
        'ink' => [17, 24, 39],      // texto principal
        'muted' => [107, 114, 128],   // texto secundario
        'border' => [229, 231, 235],   // borde suave
        'bg' => [245, 246, 250],   // fondo página
        'card' => [255, 255, 255],   // tarjeta
        'soft' => [248, 250, 252],   // header tablas / blocks
    ];

    public string $docTitle = 'Factura';
    public string $docSubTitle = 'Documento Tributario / Informe';
    public string $brandName = 'Tu Empresa SpA';
    public string $brandRUT = '76.123.456-7';
    public string $brandAddress = 'Dirección, Comuna, Región';
    public string $brandContact = 'contacto@tuempresa.cl • +56 9 0000 0000 • tuweb.cl';

    public string $footerLeft = 'Documento generado automáticamente';
    public string $currencySymbol = '$';

    // Layout
    public float $mx = 14;       // margen X (mm)
    public float $topAfterHeader = 22;

    // ====== Helpers color ======
    private function fill(array $rgb): void
    {
        $this->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
    }

    private function draw(array $rgb): void
    {
        $this->SetDrawColor($rgb[0], $rgb[1], $rgb[2]);
    }

    private function text(array $rgb): void
    {
        $this->SetTextColor($rgb[0], $rgb[1], $rgb[2]);
    }

    // ====== HEADER / FOOTER ======
    public function Header(): void
    {
        // Fondo
        $this->fill($this->brand['bg']);
        $this->Rect(0, 0, $this->GetPageWidth(), $this->GetPageHeight(), 'F');

        // Barra superior minimal (delgada)
        $this->fill($this->brand['primary']);
        $this->Rect(0, 0, $this->GetPageWidth(), 12, 'F');

        // Cabecera contenido (zona blanca)
        $this->SetY(14);
        $this->SetX($this->mx);

        // Bloque empresa (izquierda)
        $this->SetFont('Helvetica', 'B', 11);
        $this->text($this->brand['ink']);
        $this->Cell(0, 5, $this->brandName, 0, 1, 'L');

        $this->SetFont('Helvetica', '', 8.5);
        $this->text($this->brand['muted']);
        $this->Cell(0, 4, "RUT: {$this->brandRUT}", 0, 1, 'L');
        $this->Cell(0, 4, $this->brandAddress, 0, 1, 'L');
        $this->Cell(0, 4, $this->brandContact, 0, 1, 'L');

        // Bloque documento (derecha)
        $rightW = 70;
        $xRight = $this->GetPageWidth() - $this->mx - $rightW;
        $yTop = 14;

        $this->Card($xRight, $yTop, $rightW, 26);

        $this->SetXY($xRight + 6, $yTop + 5);
        $this->SetFont('Helvetica', 'B', 12);
        $this->text($this->brand['ink']);
        $this->Cell($rightW - 12, 6, $this->docTitle, 0, 1, 'L');

        $this->SetFont('Helvetica', '', 8.5);
        $this->text($this->brand['muted']);
        $this->SetX($xRight + 6);
        $this->MultiCell($rightW - 12, 4, $this->docSubTitle, 0, 'L');

        $this->Ln(3);
        $this->SetY($this->topAfterHeader);
        $this->SetX($this->mx);
    }

    public function Footer(): void
    {
        $this->SetY(-12);
        $this->SetFont('Helvetica', '', 7.5);
        $this->text($this->brand['muted']);
        $this->SetX($this->mx);
        $this->Cell(0, 4, $this->footerLeft, 0, 0, 'L');

        $this->text($this->brand['primary']);
        $this->Cell(0, 4, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    // ====== COMPONENTES BASE ======

    /** Card minimalista */
    public function Card(float $x, float $y, float $w, float $h): void
    {
        $this->fill($this->brand['card']);
        $this->draw($this->brand['border']);
        $this->SetLineWidth(0.2);
        $this->Rect($x, $y, $w, $h, 'DF');
    }

    /** Separador */
    public function Divider(float $space = 5): void
    {
        $this->Ln($space);
        $this->draw($this->brand['border']);
        $y = $this->GetY();
        $this->Line($this->mx, $y, $this->GetPageWidth() - $this->mx, $y);
        $this->Ln($space);
    }

    /** Título de sección */
    public function Section(string $title, string $subtitle = ''): void
    {
        $this->SetFont('Helvetica', 'B', 10.5);
        $this->text($this->brand['ink']);
        $this->Cell(0, 6, $title, 0, 1, 'L');

        if ($subtitle !== '') {
            $this->SetFont('Helvetica', '', 8.5);
            $this->text($this->brand['muted']);
            $this->MultiCell(0, 4, $subtitle, 0, 'L');
        }

        $this->Ln(1);
    }

    /** Chip estado (pendiente/pagado/anulado/etc.) */
    public function StatusChip(string $text, array $rgbFill, array $rgbText, float $x, float $y): void
    {
        $w = 34;
        $h = 7;
        $this->fill($rgbFill);
        $this->draw($rgbFill);
        $this->Rect($x, $y, $w, $h, 'F');

        $this->SetXY($x, $y + 1.3);
        $this->SetFont('Helvetica', 'B', 8);
        $this->SetTextColor($rgbText[0], $rgbText[1], $rgbText[2]);
        $this->Cell($w, 4, $text, 0, 0, 'C');

        // restore
        $this->text($this->brand['ink']);
        $this->draw($this->brand['border']);
    }

    /**
     * Grilla de campos (muchos campos) en formato 2-3-4 columnas.
     * $fields: [
     *   ['label'=>'Folio', 'value'=>'123'],
     *   ...
     * ]
     */
    public function FieldGrid(array $fields, int $cols = 3, float $rowH = 10): void
    {
        $usableW = $this->GetPageWidth() - ($this->mx * 2);
        $colW = $usableW / max(1, $cols);
        $x0 = $this->mx;

        $this->SetFont('Helvetica', '', 8);

        $i = 0;
        $startY = $this->GetY();
        $rows = (int)ceil(count($fields) / $cols);
        $cardH = max(12, $rows * $rowH + 6);

        // Card contenedor
        $this->Card($x0, $startY, $usableW, $cardH);

        // Contenido
        $this->SetXY($x0 + 5, $startY + 5);

        foreach ($fields as $f) {
            $col = $i % $cols;
            $row = (int)floor($i / $cols);

            $x = $x0 + 5 + ($col * $colW);
            $y = $startY + 5 + ($row * $rowH);

            $this->SetXY($x, $y);
            $this->text($this->brand['muted']);
            $this->Cell($colW - 10, 4, $f['label'] ?? '', 0, 2, 'L');

            $this->text($this->brand['ink']);
            $this->SetFont('Helvetica', 'B', 9);
            $this->MultiCell($colW - 10, 4, (string)($f['value'] ?? ''), 0, 'L');

            $this->SetFont('Helvetica', '', 8);
            $i++;
        }

        $this->SetY($startY + $cardH + 6);
        $this->SetX($this->mx);
    }

    /** Tabla minimalista genérica */
    public function DataTable(array $headers, array $rows, array $widths, array $aligns = []): void
    {
        $usableW = $this->GetPageWidth() - ($this->mx * 2);
        $x0 = $this->mx;
        $y0 = $this->GetY();

        // Calcula altura aproximada (simple)
        $rowH = 8;
        $h = 14 + (count($rows) * $rowH) + 10;

        $this->Card($x0, $y0, $usableW, $h);

        $this->SetXY($x0 + 5, $y0 + 5);

        // Header tabla
        $this->fill($this->brand['soft']);
        $this->draw($this->brand['border']);
        $this->SetFont('Helvetica', 'B', 8.5);
        $this->text([55, 65, 81]);

        for ($i = 0; $i < count($headers); $i++) {
            $a = $aligns[$i] ?? (($i == 0) ? 'L' : 'R');
            $this->Cell($widths[$i], 8, $headers[$i], 0, 0, $a, true);
        }
        $this->Ln();

        // Rows
        $this->SetFont('Helvetica', '', 8.5);
        $this->text($this->brand['ink']);

        foreach ($rows as $rIndex => $r) {
            $zebra = ($rIndex % 2 === 0) ? [255, 255, 255] : [252, 252, 253];
            $this->SetFillColor($zebra[0], $zebra[1], $zebra[2]);

            for ($i = 0; $i < count($headers); $i++) {
                $a = $aligns[$i] ?? (($i == 0) ? 'L' : 'R');
                $val = $r[$i] ?? '';
                $this->Cell($widths[$i], $rowH, (string)$val, 0, 0, $a, true);
            }
            $this->Ln();

            // línea suave
            $this->draw([240, 242, 246]);
            $this->Line($x0 + 5, $this->GetY(), $x0 + $usableW - 5, $this->GetY());
        }

        $this->SetY($y0 + $h + 6);
        $this->SetX($this->mx);
    }

    /** Bloque de totales (tipo factura) */
    public function TotalsBlock(array $totals, float $width = 80): void
    {
        // $totals = [
        //   ['label'=>'Neto', 'value'=>'$100.000'],
        //   ['label'=>'IVA (19%)', 'value'=>'$19.000'],
        //   ['label'=>'Total', 'value'=>'$119.000', 'bold'=>true, 'big'=>true],
        // ];
        $usableW = $this->GetPageWidth() - ($this->mx * 2);
        $x = $this->GetPageWidth() - $this->mx - $width;
        $y = $this->GetY();

        $h = 8 + (count($totals) * 7) + 6;
        $this->Card($x, $y, $width, $h);

        $this->SetXY($x + 6, $y + 6);
        foreach ($totals as $t) {
            $isBold = !empty($t['bold']);
            $isBig = !empty($t['big']);

            $this->SetFont('Helvetica', $isBold ? 'B' : '', $isBig ? 11 : 9);
            $this->text($this->brand['muted']);
            $this->Cell($width * 0.55, 7, $t['label'], 0, 0, 'L');

            $this->text($this->brand['ink']);
            $this->Cell($width * 0.45 - 12, 7, (string)$t['value'], 0, 1, 'R');
        }

        $this->Ln(2);
    }

    /** Bloque texto / observaciones */
    public function NotesBlock(string $title, string $text, float $minH = 22): void
    {
        $usableW = $this->GetPageWidth() - ($this->mx * 2);
        $x0 = $this->mx;
        $y0 = $this->GetY();

        // Altura dinámica (aprox)
        $lines = max(1, (int)ceil(strlen($text) / 95));
        $h = max($minH, 12 + ($lines * 4.5));

        $this->Card($x0, $y0, $usableW, $h);

        $this->SetXY($x0 + 5, $y0 + 5);
        $this->SetFont('Helvetica', 'B', 9.5);
        $this->text($this->brand['ink']);
        $this->Cell(0, 5, $title, 0, 1, 'L');

        $this->SetFont('Helvetica', '', 8.5);
        $this->text($this->brand['muted']);
        $this->MultiCell(0, 4.5, $text, 0, 'L');

        $this->SetY($y0 + $h + 6);
        $this->SetX($this->mx);
    }

    // ====== GRÁFICOS SIMPLE (para informes) ======

    /**
     * Gráfico de barras simple.
     * $data = [
     *   ['label'=>'Ene', 'value'=>30],
     *   ['label'=>'Feb', 'value'=>50],
     * ]
     */
    public function BarChart(string $title, array $data, float $w = 0, float $h = 40): void
    {
        $usableW = $this->GetPageWidth() - ($this->mx * 2);
        $w = ($w <= 0) ? $usableW : $w;

        $x0 = $this->mx;
        $y0 = $this->GetY();

        $cardH = $h + 16;
        $this->Card($x0, $y0, $w, $cardH);

        // Title
        $this->SetXY($x0 + 5, $y0 + 5);
        $this->SetFont('Helvetica', 'B', 9.5);
        $this->text($this->brand['ink']);
        $this->Cell(0, 5, $title, 0, 1, 'L');

        // Chart area
        $chartX = $x0 + 7;
        $chartY = $y0 + 13;
        $chartW = $w - 14;
        $chartH = $h;

        // Axis baseline
        $this->draw([240, 242, 246]);
        $this->Line($chartX, $chartY + $chartH, $chartX + $chartW, $chartY + $chartH);

        if (empty($data)) {
            $this->SetXY($chartX, $chartY + 10);
            $this->SetFont('Helvetica', '', 8.5);
            $this->text($this->brand['muted']);
            $this->Cell($chartW, 5, 'Sin datos', 0, 1, 'C');
            $this->SetY($y0 + $cardH + 6);
            return;
        }

        $max = 0;
        foreach ($data as $d) {
            $max = max($max, (float)$d['value']);
        }
        $max = ($max <= 0) ? 1 : $max;

        $n = count($data);
        $gap = 3;
        $barW = ($chartW - (($n - 1) * $gap)) / $n;
        $barW = max(6, $barW);

        for ($i = 0; $i < $n; $i++) {
            $val = (float)$data[$i]['value'];
            $lab = (string)$data[$i]['label'];

            $bh = ($val / $max) * ($chartH - 10);
            $x = $chartX + ($i * ($barW + $gap));
            $y = ($chartY + $chartH) - $bh;

            // Bar (morado)
            $this->fill($this->brand['primary']);
            $this->Rect($x, $y, $barW, $bh, 'F');

            // Label
            $this->SetFont('Helvetica', '', 7);
            $this->text($this->brand['muted']);
            $this->SetXY($x, $chartY + $chartH + 1.5);
            $this->Cell($barW, 4, $lab, 0, 0, 'C');
        }

        $this->SetY($y0 + $cardH + 6);
        $this->SetX($this->mx);
    }
}
