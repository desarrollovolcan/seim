<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        try {
            $isAdmin = (Auth::user()['role'] ?? '') === 'admin';
            $companyId = $isAdmin ? null : current_company_id();
            $companyFilter = $companyId ? ' AND company_id = :company_id' : '';
            $companyParams = $companyId ? ['company_id' => $companyId] : [];

            $productionCompanyFilter = $companyId ? ' AND o.company_id = :company_id' : '';
            $producedProducts = $this->db->fetchAll(
                'SELECT p.id, p.name, p.stock, p.cost, COALESCE(SUM(po.quantity), 0) AS produced_quantity
                 FROM production_outputs po
                 JOIN production_orders o ON o.id = po.production_id
                 JOIN products p ON p.id = po.product_id
                 WHERE 1=1' . $productionCompanyFilter . '
                 GROUP BY p.id, p.name, p.stock, p.cost
                 ORDER BY produced_quantity DESC, p.name ASC
                 LIMIT 8',
                $companyParams
            );
            $salesByProduct = $this->db->fetchAll(
                'SELECT p.id, p.name, SUM(si.quantity) as quantity, SUM(si.subtotal) as total
                 FROM sale_items si
                 JOIN sales s ON s.id = si.sale_id
                 JOIN products p ON p.id = si.product_id
                 WHERE 1=1' . ($companyId ? ' AND s.company_id = :company_id' : '') . '
                 GROUP BY p.id, p.name
                 ORDER BY total DESC, p.name ASC
                 LIMIT 8',
                $companyParams
            );
            $profitByProduct = $this->db->fetchAll(
                'SELECT p.id, p.name,
                        SUM(si.subtotal) as total,
                        SUM(si.quantity * p.cost) as total_cost,
                        SUM(si.subtotal - (si.quantity * p.cost)) as profit
                 FROM sale_items si
                 JOIN sales s ON s.id = si.sale_id
                 JOIN products p ON p.id = si.product_id
                 WHERE 1=1' . ($companyId ? ' AND s.company_id = :company_id' : '') . '
                 GROUP BY p.id, p.name
                 ORDER BY profit DESC, p.name ASC
                 LIMIT 8',
                $companyParams
            );
            $lowStockProducts = $this->db->fetchAll(
                'SELECT id, name, stock, stock_min
                 FROM products
                 WHERE deleted_at IS NULL AND stock <= stock_min' . $companyFilter . '
                 ORDER BY stock ASC, name ASC
                 LIMIT 8',
                $companyParams
            );
        } catch (PDOException $e) {
            log_message('error', 'Failed to load dashboard metrics: ' . $e->getMessage());
            $producedProducts = [];
            $salesByProduct = [];
            $profitByProduct = [];
            $lowStockProducts = [];
        }

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'pageTitle' => 'Dashboard',
            'producedProducts' => $producedProducts,
            'salesByProduct' => $salesByProduct,
            'profitByProduct' => $profitByProduct,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}
