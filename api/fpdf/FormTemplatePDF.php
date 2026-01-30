<?php

require_once __DIR__ . '/fpdf.php';

/**
 * Plantilla moderna para formularios/reportes (FPDF).
 * Pensada para reutilizar en todo el proyecto.
 */
class FormTemplatePDF extends FPDF
{
    public array $palette = [
        'primary' => [63, 81, 181],
        'accent' => [90, 77, 225],
        'ink' => [17, 24, 39],
        'muted' => [100, 116, 139],
        'border' => [226, 232, 240],
        'bg' => [248, 250, 252],
        'card' => [255, 255, 255],
        'soft' => [241, 245, 249],
    ];

    public string $docTitle = 'Reporte';
    public string $docSubTitle = '';
    public string $brandName = 'GoCreative GES';
    public string $brandRUT = '';
    public string $brandAddress = '';
    public string $brandContact = '';
    public string $footerLeft = 'Documento generado automáticamente';

    public float $mx = 14;
    public float $topAfterHeader = 26;

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

    public function Header(): void
    {
        $this->fill($this->palette['bg']);
        $this->Rect(0, 0, $this->GetPageWidth(), $this->GetPageHeight(), 'F');

        $this->fill($this->palette['primary']);
        $this->Rect(0, 0, $this->GetPageWidth(), 14, 'F');

        $this->SetY(18);
        $this->SetX($this->mx);

        $this->SetFont('Helvetica', 'B', 12);
        $this->text($this->palette['ink']);
        $this->Cell(0, 6, $this->brandName, 0, 1, 'L');

        $this->SetFont('Helvetica', '', 8.5);
        $this->text($this->palette['muted']);
        if ($this->brandRUT !== '') {
            $this->Cell(0, 4, "RUT: {$this->brandRUT}", 0, 1, 'L');
        }
        if ($this->brandAddress !== '') {
            $this->Cell(0, 4, $this->brandAddress, 0, 1, 'L');
        }
        if ($this->brandContact !== '') {
            $this->Cell(0, 4, $this->brandContact, 0, 1, 'L');
        }

        $rightW = 78;
        $xRight = $this->GetPageWidth() - $this->mx - $rightW;
        $yTop = 18;

        $this->Card($xRight, $yTop, $rightW, 24);
        $this->SetXY($xRight + 6, $yTop + 5);
        $this->SetFont('Helvetica', 'B', 12);
        $this->text($this->palette['ink']);
        $this->MultiCell($rightW - 12, 5, $this->docTitle, 0, 'L');

        if ($this->docSubTitle !== '') {
            $this->SetFont('Helvetica', '', 8.5);
            $this->text($this->palette['muted']);
            $this->SetX($xRight + 6);
            $this->MultiCell($rightW - 12, 4, $this->docSubTitle, 0, 'L');
        }

        $this->Ln(3);
        $this->SetY($this->topAfterHeader);
        $this->SetX($this->mx);
    }

    public function Footer(): void
    {
        $this->SetY(-12);
        $this->SetFont('Helvetica', '', 7.5);
        $this->text($this->palette['muted']);
        $this->SetX($this->mx);
        $this->Cell(0, 4, $this->footerLeft, 0, 0, 'L');

        $this->text($this->palette['accent']);
        $this->Cell(0, 4, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    public function Card(float $x, float $y, float $w, float $h): void
    {
        $this->fill($this->palette['card']);
        $this->draw($this->palette['border']);
        $this->SetLineWidth(0.2);
        $this->Rect($x, $y, $w, $h, 'DF');
    }

    public function Section(string $title, string $subtitle = ''): void
    {
        $this->SetFont('Helvetica', 'B', 10.5);
        $this->text($this->palette['ink']);
        $this->Cell(0, 6, $title, 0, 1, 'L');

        if ($subtitle !== '') {
            $this->SetFont('Helvetica', '', 8.5);
            $this->text($this->palette['muted']);
            $this->MultiCell(0, 4, $subtitle, 0, 'L');
        }

        $this->Ln(1);
    }

    public function FieldGrid(array $fields, int $cols = 2, float $rowH = 10): void
    {
        $usableW = $this->GetPageWidth() - ($this->mx * 2);
        $colW = $usableW / max(1, $cols);
        $x0 = $this->mx;

        $this->SetFont('Helvetica', '', 8.5);

        $rows = (int)ceil(count($fields) / $cols);
        $cardH = max(14, $rows * $rowH + 8);
        $startY = $this->GetY();

        $this->Card($x0, $startY, $usableW, $cardH);
        $this->SetXY($x0 + 5, $startY + 5);

        $i = 0;
        foreach ($fields as $field) {
            $col = $i % $cols;
            $row = (int)floor($i / $cols);
            $x = $x0 + 5 + ($col * $colW);
            $y = $startY + 5 + ($row * $rowH);

            $this->SetXY($x, $y);
            $this->SetFont('Helvetica', 'B', 8);
            $this->text($this->palette['muted']);
            $this->Cell($colW - 10, 4, (string)($field['label'] ?? ''), 0, 2, 'L');

            $this->SetFont('Helvetica', '', 9);
            $this->text($this->palette['ink']);
            $value = (string)($field['value'] ?? '');
            $this->MultiCell($colW - 10, 4, $value, 0, 'L');

            $i++;
        }

        $this->SetY($startY + $cardH + 4);
    }

    public function NotesBlock(string $title, string $content): void
    {
        $usableW = $this->GetPageWidth() - ($this->mx * 2);
        $x0 = $this->mx;
        $y0 = $this->GetY();

        $this->SetFont('Helvetica', 'B', 9);
        $this->text($this->palette['ink']);
        $this->Cell(0, 5, $title, 0, 1, 'L');

        $this->SetFont('Helvetica', '', 9);
        $this->text($this->palette['ink']);
        $startY = $this->GetY();

        $this->fill($this->palette['soft']);
        $this->draw($this->palette['border']);
        $this->SetXY($x0, $startY);
        $this->MultiCell($usableW, 5, $content, 1, 'L', true);
        $this->SetY(max($this->GetY(), $y0 + 12));
    }
}
