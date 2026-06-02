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

            $inventoryProducts = $this->db->fetchAll(
                'SELECT id, name, stock, cost
                 FROM products
                 WHERE deleted_at IS NULL' . $companyFilter . '
                 ORDER BY stock DESC, name ASC
                 LIMIT 8',
                $companyParams
            );
            $salesByProduct = $this->db->fetchAll(
                'SELECT CASE
                            WHEN si.produced_product_id IS NOT NULL THEN CONCAT("produced-", si.produced_product_id)
                            ELSE CONCAT("product-", si.product_id)
                        END AS product_key,
                        COALESCE(pp.name, p.name) AS name,
                        SUM(si.quantity) as quantity,
                        SUM(si.subtotal) as total
                 FROM sale_items si
                 JOIN sales s ON s.id = si.sale_id
                 LEFT JOIN products p ON p.id = si.product_id
                 LEFT JOIN produced_products pp ON pp.id = si.produced_product_id
                 WHERE 1=1' . ($companyId ? ' AND s.company_id = :company_id' : '') . '
                 GROUP BY product_key, name
                 ORDER BY total DESC, name ASC
                 LIMIT 8',
                $companyParams
            );
            $profitByProduct = $this->db->fetchAll(
                'SELECT CASE
                            WHEN si.produced_product_id IS NOT NULL THEN CONCAT("produced-", si.produced_product_id)
                            ELSE CONCAT("product-", si.product_id)
                        END AS product_key,
                        COALESCE(pp.name, p.name) AS name,
                        SUM(si.subtotal) as total,
                        SUM(si.quantity * COALESCE(pp.cost, p.cost)) as total_cost,
                        SUM(si.subtotal - (si.quantity * COALESCE(pp.cost, p.cost))) as profit
                 FROM sale_items si
                 JOIN sales s ON s.id = si.sale_id
                 LEFT JOIN products p ON p.id = si.product_id
                 LEFT JOIN produced_products pp ON pp.id = si.produced_product_id
                 WHERE 1=1' . ($companyId ? ' AND s.company_id = :company_id' : '') . '
                 GROUP BY product_key, name
                 ORDER BY profit DESC, name ASC
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
        } catch (Throwable $e) {
            log_message('error', 'Failed to load dashboard metrics: ' . $e->getMessage());
            $inventoryProducts = [];
            $salesByProduct = [];
            $profitByProduct = [];
            $lowStockProducts = [];
        }

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'pageTitle' => 'Dashboard',
            'inventoryProducts' => $inventoryProducts,
            'salesByProduct' => $salesByProduct,
            'profitByProduct' => $profitByProduct,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}
