<?php

declare(strict_types=1);

class DashboardController
{
    public function show(): array
    {
        $metrics = [
            'productos' => 0,
            'categorias' => 0,
            'unidades' => 0,
            'movimientos' => 0,
            'ventas' => 0,
            'bajo_stock' => 0,
        ];

        try {
            $metrics['productos'] = (int) db()->query('SELECT COUNT(*) FROM inventario_productos')->fetchColumn();
            $metrics['categorias'] = (int) db()->query('SELECT COUNT(*) FROM inventario_categorias')->fetchColumn();
            $metrics['unidades'] = (int) db()->query('SELECT COUNT(*) FROM inventario_unidades')->fetchColumn();
            $metrics['movimientos'] = (int) db()->query('SELECT COUNT(*) FROM inventario_movimientos')->fetchColumn();
            $metrics['ventas'] = (int) db()->query('SELECT COUNT(*) FROM ventas')->fetchColumn();
            $metrics['bajo_stock'] = (int) db()->query('SELECT COUNT(*) FROM inventario_productos WHERE stock_actual <= stock_minimo')->fetchColumn();
        } catch (Exception $e) {
        }

        $recentSales = [];
        try {
            $recentSales = db()->query('SELECT cliente, fecha, total FROM ventas ORDER BY created_at DESC LIMIT 5')->fetchAll();
        } catch (Exception $e) {
        }

        return [
            'metrics' => $metrics,
            'recentSales' => $recentSales,
        ];
    }
}
