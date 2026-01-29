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
            'clientes' => 0,
            'bajo_stock' => 0,
        ];

        try {
            $metrics['productos'] = (int) db()->query('SELECT COUNT(*) FROM inventario_productos')->fetchColumn();
            $metrics['categorias'] = (int) db()->query('SELECT COUNT(*) FROM inventario_categorias')->fetchColumn();
            $metrics['unidades'] = (int) db()->query('SELECT COUNT(*) FROM inventario_unidades')->fetchColumn();
            $metrics['movimientos'] = (int) db()->query('SELECT COUNT(*) FROM inventario_movimientos')->fetchColumn();
            $metrics['ventas'] = (int) db()->query('SELECT COUNT(*) FROM ventas')->fetchColumn();
            $metrics['clientes'] = (int) db()->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
            $metrics['bajo_stock'] = (int) db()->query('SELECT COUNT(*) FROM inventario_productos WHERE stock_actual <= stock_minimo')->fetchColumn();
        } catch (Exception $e) {
        }

        $recentSales = [];
        try {
            $hasClienteNombreColumn = column_exists('ventas', 'cliente_nombre');
            $hasClienteLegacyColumn = column_exists('ventas', 'cliente');
            $hasClienteIdColumn = column_exists('ventas', 'cliente_id');

            if ($hasClienteIdColumn) {
                if ($hasClienteNombreColumn) {
                    $clienteSelect = 'COALESCE(c.nombre, v.cliente_nombre) AS cliente';
                } elseif ($hasClienteLegacyColumn) {
                    $clienteSelect = 'COALESCE(c.nombre, v.cliente) AS cliente';
                } else {
                    $clienteSelect = 'c.nombre AS cliente';
                }

                $recentSales = db()->query(
                    sprintf(
                        'SELECT %s, v.fecha, v.total
                         FROM ventas v
                         LEFT JOIN clientes c ON c.id = v.cliente_id
                         ORDER BY v.created_at DESC LIMIT 5',
                        $clienteSelect
                    )
                )->fetchAll();
            } else {
                $clienteSelect = $hasClienteNombreColumn ? 'v.cliente_nombre AS cliente' : 'v.cliente AS cliente';
                $recentSales = db()->query(
                    sprintf(
                        'SELECT %s, v.fecha, v.total
                         FROM ventas v
                         ORDER BY v.created_at DESC LIMIT 5',
                        $clienteSelect
                    )
                )->fetchAll();
            }
        } catch (Exception $e) {
        }

        return [
            'metrics' => $metrics,
            'recentSales' => $recentSales,
        ];
    }
}
