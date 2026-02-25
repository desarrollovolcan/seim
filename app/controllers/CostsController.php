<?php

class CostsController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }

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
}
