<?php

class CostsController extends Controller
{
    private function requireCompany(): int
    {
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }

        return (int)$companyId;
    }

    private function fetchAccountingPurchases(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT p.id, p.reference, p.purchase_date, p.status, p.subtotal, p.tax, p.total,
                    p.sii_document_type, p.sii_document_number, p.sii_tax_rate, p.sii_exempt_amount,
                    p.notes, p.created_at,
                    s.name AS supplier_name
             FROM purchases p
             LEFT JOIN suppliers s ON s.id = p.supplier_id
             WHERE p.company_id = :company_id
               AND p.deleted_at IS NULL
             ORDER BY p.purchase_date ASC, p.id ASC',
            ['company_id' => $companyId]
        );
    }

    private function fetchCashMovements(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT bt.id, bt.transaction_date, bt.reference, bt.description, bt.type, bt.amount, bt.balance,
                    bt.created_at,
                    ba.name AS account_name, ba.bank_name, ba.account_number, ba.currency
             FROM bank_transactions bt
             INNER JOIN bank_accounts ba ON ba.id = bt.bank_account_id
             WHERE bt.company_id = :company_id
             ORDER BY bt.transaction_date ASC, bt.id ASC',
            ['company_id' => $companyId]
        );
    }

    private function buildCashAnalysis(array $cashMovements): array
    {
        $entryAmount = 0.0;
        $exitAmount = 0.0;

        foreach ($cashMovements as &$movement) {
            $isExit = ($movement['type'] ?? '') === 'retiro';
            $amount = (float)($movement['amount'] ?? 0);
            $movement['entry_amount'] = $isExit ? 0.0 : $amount;
            $movement['exit_amount'] = $isExit ? $amount : 0.0;
            $entryAmount += $movement['entry_amount'];
            $exitAmount += $movement['exit_amount'];
        }
        unset($movement);

        return [
            'movements' => $cashMovements,
            'summary' => [
                'entries' => $entryAmount,
                'exits' => $exitAmount,
                'net' => $entryAmount - $exitAmount,
                'movements_count' => count($cashMovements),
            ],
        ];
    }

    public function index(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $params = [
            'company_id' => (int)$companyId,
            'month_start' => date('Y-m-01'),
            'month_end' => date('Y-m-t'),
        ];

        try {
            $summary = $this->db->fetch(
                'SELECT
                    COUNT(*) AS documents_count,
                    COALESCE(SUM(total), 0) AS total_amount,
                    COALESCE(SUM(CASE WHEN status = "pendiente" THEN total ELSE 0 END), 0) AS pending_amount
                 FROM purchases
                 WHERE company_id = :company_id
                   AND deleted_at IS NULL
                   AND purchase_date BETWEEN :month_start AND :month_end',
                $params
            ) ?: [];

            $recentPurchases = $this->db->fetchAll(
                'SELECT p.id, p.reference, p.purchase_date, p.total, p.status, s.name AS supplier_name
                 FROM purchases p
                 LEFT JOIN suppliers s ON s.id = p.supplier_id
                 WHERE p.company_id = :company_id AND p.deleted_at IS NULL
                 ORDER BY p.purchase_date DESC, p.id DESC
                 LIMIT 8',
                ['company_id' => (int)$companyId]
            );
        } catch (Throwable $e) {
            log_message('error', 'No se pudo cargar panel de costos: ' . $e->getMessage());
            $summary = [];
            $recentPurchases = [];
        }

        $this->render('costs/index', [
            'title' => 'Costos',
            'pageTitle' => 'Panel de costos y gastos',
            'summary' => $summary,
            'recentPurchases' => $recentPurchases,
        ]);
    }

    public function accountantList(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $purchases = $this->fetchAccountingPurchases($companyId);

        $this->render('costs/accountant-list', [
            'title' => 'Listado contable de costos',
            'pageTitle' => 'Listado contable de costos',
            'purchases' => $purchases,
        ]);
    }

    public function cashFlowAnalysis(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $cashData = $this->buildCashAnalysis($this->fetchCashMovements($companyId));

        $this->render('costs/cash-flow-analysis', [
            'title' => 'Análisis contable de caja',
            'pageTitle' => 'Análisis contable de entradas y salidas',
            'cashMovements' => $cashData['movements'],
            'cashSummary' => $cashData['summary'],
        ]);
    }

    public function exportPurchasesExcel(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $purchases = $this->fetchAccountingPurchases($companyId);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="listado_contable_costos_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'wb');
        if ($output === false) {
            return;
        }

        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, ['ID', 'Documento', 'Tipo DTE', 'Nro DTE', 'Proveedor', 'Fecha compra', 'Estado', 'Subtotal', 'Impuesto', 'Tasa IVA', 'Exento', 'Total', 'Notas', 'Creado']);

        foreach ($purchases as $purchase) {
            fputcsv($output, [
                (int)($purchase['id'] ?? 0),
                $purchase['reference'] ?: ('Compra #' . (int)($purchase['id'] ?? 0)),
                $purchase['sii_document_type'] ?? '',
                $purchase['sii_document_number'] ?? '',
                $purchase['supplier_name'] ?? '-',
                $purchase['purchase_date'] ?? '',
                $purchase['status'] ?? 'pendiente',
                (float)($purchase['subtotal'] ?? 0),
                (float)($purchase['tax'] ?? 0),
                (float)($purchase['sii_tax_rate'] ?? 0),
                (float)($purchase['sii_exempt_amount'] ?? 0),
                (float)($purchase['total'] ?? 0),
                $purchase['notes'] ?? '',
                $purchase['created_at'] ?? '',
            ]);
        }

        fclose($output);
        exit;
    }

    public function exportCashFlowExcel(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $cashData = $this->buildCashAnalysis($this->fetchCashMovements($companyId));

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="analisis_caja_contable_' . date('Ymd_His') . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<html><head><meta charset="UTF-8">';
        echo '<style>';
        echo 'table{border-collapse:collapse;font-family:Arial,sans-serif;font-size:12px;}';
        echo 'th,td{border:1px solid #777;padding:6px 8px;}';
        echo 'th{background:#e6e6e6;font-weight:bold;text-align:center;}';
        echo '.text-right{text-align:right;}';
        echo '.text-center{text-align:center;}';
        echo '.title{font-size:14px;font-weight:bold;margin-bottom:8px;}';
        echo '.summary-label{background:#f5f5f5;font-weight:bold;}';
        echo '</style>';
        echo '</head><body>';

        echo '<div class="title">Análisis contable de caja y bancos</div>';
        echo '<table>';
        echo '<thead><tr>';
        echo '<th>ID</th>';
        echo '<th>Fecha</th>';
        echo '<th>Cuenta</th>';
        echo '<th>Banco</th>';
        echo '<th>Nro cuenta</th>';
        echo '<th>Moneda</th>';
        echo '<th>Tipo</th>';
        echo '<th>Referencia</th>';
        echo '<th>Descripción</th>';
        echo '<th>Entrada</th>';
        echo '<th>Salida</th>';
        echo '<th>Saldo</th>';
        echo '<th>Creado</th>';
        echo '</tr></thead><tbody>';

        foreach ($cashData['movements'] as $movement) {
            $id = (int)($movement['id'] ?? 0);
            $date = htmlspecialchars((string)($movement['transaction_date'] ?? ''), ENT_QUOTES, 'UTF-8');
            $accountName = htmlspecialchars((string)($movement['account_name'] ?? '-'), ENT_QUOTES, 'UTF-8');
            $bankName = htmlspecialchars((string)($movement['bank_name'] ?? '-'), ENT_QUOTES, 'UTF-8');
            $accountNumber = htmlspecialchars((string)($movement['account_number'] ?? '-'), ENT_QUOTES, 'UTF-8');
            $currency = htmlspecialchars((string)($movement['currency'] ?? 'CLP'), ENT_QUOTES, 'UTF-8');
            $type = htmlspecialchars((string)($movement['type'] ?? ''), ENT_QUOTES, 'UTF-8');
            $reference = htmlspecialchars((string)($movement['reference'] ?? ''), ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars((string)($movement['description'] ?? ''), ENT_QUOTES, 'UTF-8');
            $entryAmount = number_format((float)($movement['entry_amount'] ?? 0), 2, ',', '.');
            $exitAmount = number_format((float)($movement['exit_amount'] ?? 0), 2, ',', '.');
            $balance = number_format((float)($movement['balance'] ?? 0), 2, ',', '.');
            $createdAt = htmlspecialchars((string)($movement['created_at'] ?? ''), ENT_QUOTES, 'UTF-8');

            echo '<tr>';
            echo '<td class="text-center">' . $id . '</td>';
            echo '<td>' . $date . '</td>';
            echo '<td>' . $accountName . '</td>';
            echo '<td>' . $bankName . '</td>';
            echo '<td>' . $accountNumber . '</td>';
            echo '<td class="text-center">' . $currency . '</td>';
            echo '<td class="text-center">' . $type . '</td>';
            echo '<td>' . $reference . '</td>';
            echo '<td>' . $description . '</td>';
            echo '<td class="text-right">' . $entryAmount . '</td>';
            echo '<td class="text-right">' . $exitAmount . '</td>';
            echo '<td class="text-right">' . $balance . '</td>';
            echo '<td>' . $createdAt . '</td>';
            echo '</tr>';
        }

        $entries = number_format((float)($cashData['summary']['entries'] ?? 0), 2, ',', '.');
        $exits = number_format((float)($cashData['summary']['exits'] ?? 0), 2, ',', '.');
        $net = number_format((float)($cashData['summary']['net'] ?? 0), 2, ',', '.');

        echo '<tr><td colspan="13" style="border:0;height:8px;"></td></tr>';
        echo '<tr>';
        echo '<td class="summary-label text-center" colspan="9">RESUMEN</td>';
        echo '<td class="summary-label text-right">Entradas</td>';
        echo '<td class="summary-label text-right">Salidas</td>';
        echo '<td class="summary-label text-right">Neto</td>';
        echo '<td class="summary-label"></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="9"></td>';
        echo '<td class="text-right">' . $entries . '</td>';
        echo '<td class="text-right">' . $exits . '</td>';
        echo '<td class="text-right">' . $net . '</td>';
        echo '<td></td>';
        echo '</tr>';

        echo '</tbody></table>';
        echo '</body></html>';
        exit;
    }
}
