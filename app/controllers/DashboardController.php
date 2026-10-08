<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        try {
            $companyId = current_company_id() ?? (int)($_SESSION['company_id'] ?? 0);
            $companyFilter = $companyId ? ' AND company_id = :company_id' : '';
            $companyParams = $companyId ? ['company_id' => $companyId] : [];

            // 1. Métricas de Ventas y Financieras Generales
            $salesSummary = $this->db->fetch(
                'SELECT 
                    COUNT(*) as total_orders,
                    COALESCE(SUM(total), 0) as total_sales,
                    COALESCE(AVG(total), 0) as avg_ticket
                 FROM sales
                 WHERE 1=1' . ($companyId ? ' AND company_id = :company_id' : ''),
                $companyParams
            ) ?: [];

            // Ventas del mes actual
            $salesThisMonth = $this->db->fetch(
                'SELECT 
                    COUNT(*) as count,
                    COALESCE(SUM(total), 0) as total
                 FROM sales
                 WHERE MONTH(sale_date) = MONTH(CURRENT_DATE()) 
                   AND YEAR(sale_date) = YEAR(CURRENT_DATE())' . ($companyId ? ' AND company_id = :company_id' : ''),
                $companyParams
            ) ?: [];

            // Ventas del mes anterior para comparar crecimiento
            $salesLastMonth = $this->db->fetch(
                'SELECT COALESCE(SUM(total), 0) as total
                 FROM sales
                 WHERE sale_date >= DATE_SUB(DATE_FORMAT(CURRENT_DATE(), "%Y-%m-01"), INTERVAL 1 MONTH)
                   AND sale_date < DATE_FORMAT(CURRENT_DATE(), "%Y-%m-01")' . ($companyId ? ' AND company_id = :company_id' : ''),
                $companyParams
            ) ?: [];

            // Rentabilidad y Costos
            $profitRow = $this->db->fetch(
                'SELECT 
                    COALESCE(SUM(si.subtotal), 0) as total_subtotal,
                    COALESCE(SUM(si.quantity * COALESCE(pp.cost, p.cost, 0)), 0) as total_cost,
                    COALESCE(SUM(si.subtotal - (si.quantity * COALESCE(pp.cost, p.cost, 0))), 0) as gross_profit
                 FROM sale_items si
                 JOIN sales s ON s.id = si.sale_id
                 LEFT JOIN products p ON p.id = si.product_id
                 LEFT JOIN produced_products pp ON pp.id = si.produced_product_id
                 WHERE 1=1' . ($companyId ? ' AND s.company_id = :company_id' : ''),
                $companyParams
            ) ?: [];

            // 2. Métricas de Cotizaciones y Pipeline
            $quotesSummary = $this->db->fetch(
                'SELECT 
                    COUNT(*) as total_quotes,
                    COALESCE(SUM(total), 0) as total_amount,
                    COALESCE(SUM(CASE WHEN estado = "aprobada" THEN total ELSE 0 END), 0) as approved_amount,
                    COALESCE(SUM(CASE WHEN estado = "aprobada" THEN 1 ELSE 0 END), 0) as approved_count,
                    COALESCE(SUM(CASE WHEN estado IN ("en_curso", "enviada", "creada") THEN total ELSE 0 END), 0) as pipeline_amount,
                    COALESCE(SUM(CASE WHEN estado IN ("en_curso", "enviada", "creada") THEN 1 ELSE 0 END), 0) as pipeline_count
                 FROM quotes
                 WHERE 1=1' . ($companyId ? ' AND company_id = :company_id' : ''),
                $companyParams
            ) ?: [];

            // Conteo por estado de cotización
            $quotesByStatus = $this->db->fetchAll(
                'SELECT estado, COUNT(*) as count, COALESCE(SUM(total), 0) as total
                 FROM quotes
                 WHERE 1=1' . ($companyId ? ' AND company_id = :company_id' : '') . '
                 GROUP BY estado',
                $companyParams
            );

            // 3. Métricas de Inventario y Productos
            $inventorySummary = $this->db->fetch(
                'SELECT 
                    COUNT(*) as total_products,
                    COALESCE(SUM(stock * cost), 0) as valuation,
                    COALESCE(SUM(CASE WHEN stock <= stock_min THEN 1 ELSE 0 END), 0) as low_stock_count,
                    COALESCE(SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END), 0) as out_of_stock_count
                 FROM products
                 WHERE deleted_at IS NULL' . $companyFilter,
                $companyParams
            ) ?: [];

            // 4. Evolución Mensual de Ventas (Últimos 6 meses)
            $monthlySales = $this->db->fetchAll(
                'SELECT 
                    DATE_FORMAT(s.sale_date, "%Y-%m") as ym,
                    DATE_FORMAT(s.sale_date, "%b %Y") as month_name,
                    COALESCE(SUM(s.total), 0) as sales_total,
                    COALESCE(SUM(si.quantity * COALESCE(pp.cost, p.cost, 0)), 0) as cost_total,
                    COALESCE(SUM(s.total - (si.quantity * COALESCE(pp.cost, p.cost, 0))), 0) as profit_total
                 FROM sales s
                 LEFT JOIN sale_items si ON si.sale_id = s.id
                 LEFT JOIN products p ON p.id = si.product_id
                 LEFT JOIN produced_products pp ON pp.id = si.produced_product_id
                 WHERE s.sale_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)' . ($companyId ? ' AND s.company_id = :company_id' : '') . '
                 GROUP BY ym, month_name
                 ORDER BY ym ASC',
                $companyParams
            );

            // 5. Ventas por Familia / Categoría
            $salesByFamily = $this->db->fetchAll(
                'SELECT 
                    COALESCE(pf.name, "Sin Categoría") as family_name,
                    COALESCE(SUM(si.subtotal), 0) as total_amount
                 FROM sale_items si
                 JOIN sales s ON s.id = si.sale_id
                 LEFT JOIN products p ON p.id = si.product_id
                 LEFT JOIN product_families pf ON pf.id = p.family_id
                 WHERE 1=1' . ($companyId ? ' AND s.company_id = :company_id' : '') . '
                 GROUP BY family_name
                 ORDER BY total_amount DESC
                 LIMIT 5',
                $companyParams
            );

            // 6. Top Productos Más Vendidos
            $topProducts = $this->db->fetchAll(
                'SELECT 
                    COALESCE(pp.name, p.name) AS name,
                    COALESCE(p.sku, pp.sku, "") as sku,
                    SUM(si.quantity) as quantity,
                    SUM(si.subtotal) as total,
                    SUM(si.subtotal - (si.quantity * COALESCE(pp.cost, p.cost, 0))) as profit
                 FROM sale_items si
                 JOIN sales s ON s.id = si.sale_id
                 LEFT JOIN products p ON p.id = si.product_id
                 LEFT JOIN produced_products pp ON pp.id = si.produced_product_id
                 WHERE 1=1' . ($companyId ? ' AND s.company_id = :company_id' : '') . '
                 GROUP BY name, sku
                 ORDER BY total DESC
                 LIMIT 5',
                $companyParams
            );

            // 7. Listado: Últimas Ventas
            $recentSales = $this->db->fetchAll(
                'SELECT s.id, s.numero, s.sale_date, s.total, s.status, COALESCE(c.name, "Consumidor final") as client_name
                 FROM sales s
                 LEFT JOIN clients c ON c.id = s.client_id
                 WHERE 1=1' . ($companyId ? ' AND s.company_id = :company_id' : '') . '
                 ORDER BY s.id DESC
                 LIMIT 5',
                $companyParams
            );

            // 8. Listado: Cotizaciones Recientes y Seguimiento
            $actionableQuotes = $this->db->fetchAll(
                'SELECT q.id, q.numero, q.total, q.estado, q.next_action_date, q.next_action_note, COALESCE(c.name, "Sin cliente") as client_name
                 FROM quotes q
                 LEFT JOIN clients c ON c.id = q.client_id
                 WHERE 1=1' . ($companyId ? ' AND q.company_id = :company_id' : '') . '
                 ORDER BY q.id DESC
                 LIMIT 5',
                $companyParams
            );

            // 9. Listado: Productos con Stock Crítico
            $criticalStock = $this->db->fetchAll(
                'SELECT id, name, sku, stock, stock_min, cost
                 FROM products
                 WHERE deleted_at IS NULL AND stock <= stock_min' . $companyFilter . '
                 ORDER BY stock ASC, name ASC
                 LIMIT 5',
                $companyParams
            );

        } catch (Throwable $e) {
            log_message('error', 'Failed to load dashboard metrics: ' . $e->getMessage());
            $salesSummary = [];
            $salesThisMonth = [];
            $salesLastMonth = [];
            $profitRow = [];
            $quotesSummary = [];
            $quotesByStatus = [];
            $inventorySummary = [];
            $monthlySales = [];
            $salesByFamily = [];
            $topProducts = [];
            $recentSales = [];
            $actionableQuotes = [];
            $criticalStock = [];
        }

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'pageTitle' => 'Panel de Control Ejecutivo',
            'salesSummary' => $salesSummary,
            'salesThisMonth' => $salesThisMonth,
            'salesLastMonth' => $salesLastMonth,
            'profitRow' => $profitRow,
            'quotesSummary' => $quotesSummary,
            'quotesByStatus' => $quotesByStatus,
            'inventorySummary' => $inventorySummary,
            'monthlySales' => $monthlySales,
            'salesByFamily' => $salesByFamily,
            'topProducts' => $topProducts,
            'recentSales' => $recentSales,
            'actionableQuotes' => $actionableQuotes,
            'criticalStock' => $criticalStock,
        ]);
    }
}

