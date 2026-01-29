<?php

declare(strict_types=1);

class DashboardController
{
    public function show(): array
    {
        $empresaId = current_empresa_id();
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
            $stmt = db()->prepare('SELECT COUNT(*) FROM inventario_productos WHERE empresa_id = ? OR empresa_id IS NULL');
            $stmt->execute([$empresaId]);
            $metrics['productos'] = (int) $stmt->fetchColumn();
            $stmt = db()->prepare('SELECT COUNT(*) FROM inventario_categorias WHERE empresa_id = ? OR empresa_id IS NULL');
            $stmt->execute([$empresaId]);
            $metrics['categorias'] = (int) $stmt->fetchColumn();
            $stmt = db()->prepare('SELECT COUNT(*) FROM inventario_unidades WHERE empresa_id = ? OR empresa_id IS NULL');
            $stmt->execute([$empresaId]);
            $metrics['unidades'] = (int) $stmt->fetchColumn();
            $stmt = db()->prepare('SELECT COUNT(*) FROM inventario_movimientos WHERE empresa_id = ? OR empresa_id IS NULL');
            $stmt->execute([$empresaId]);
            $metrics['movimientos'] = (int) $stmt->fetchColumn();
            $stmt = db()->prepare('SELECT COUNT(*) FROM ventas WHERE empresa_id = ? OR empresa_id IS NULL');
            $stmt->execute([$empresaId]);
            $metrics['ventas'] = (int) $stmt->fetchColumn();
            $stmt = db()->prepare('SELECT COUNT(*) FROM clientes WHERE empresa_id = ? OR empresa_id IS NULL');
            $stmt->execute([$empresaId]);
            $metrics['clientes'] = (int) $stmt->fetchColumn();
            $stmt = db()->prepare('SELECT COUNT(*) FROM inventario_productos WHERE (empresa_id = ? OR empresa_id IS NULL) AND stock_actual <= stock_minimo');
            $stmt->execute([$empresaId]);
            $metrics['bajo_stock'] = (int) $stmt->fetchColumn();
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

                $stmt = db()->prepare(
                    sprintf(
                        'SELECT %s, v.fecha, v.total
                         FROM ventas v
                         LEFT JOIN clientes c ON c.id = v.cliente_id
                         WHERE v.empresa_id = ? OR v.empresa_id IS NULL
                         ORDER BY v.created_at DESC LIMIT 5',
                        $clienteSelect
                    )
                );
                $stmt->execute([$empresaId]);
                $recentSales = $stmt->fetchAll();
            } else {
                $clienteSelect = $hasClienteNombreColumn ? 'v.cliente_nombre AS cliente' : 'v.cliente AS cliente';
                $stmt = db()->prepare(
                    sprintf(
                        'SELECT %s, v.fecha, v.total
                         FROM ventas v
                         WHERE v.empresa_id = ? OR v.empresa_id IS NULL
                         ORDER BY v.created_at DESC LIMIT 5',
                        $clienteSelect
                    )
                );
                $stmt->execute([$empresaId]);
                $recentSales = $stmt->fetchAll();
            }
        } catch (Exception $e) {
        }

        $lowStock = [];
        try {
            $stmt = db()->prepare(
                'SELECT nombre, stock_actual, stock_minimo
                 FROM inventario_productos
                 WHERE (empresa_id = ? OR empresa_id IS NULL) AND stock_actual <= stock_minimo
                 ORDER BY stock_actual ASC LIMIT 5'
            );
            $stmt->execute([$empresaId]);
            $lowStock = $stmt->fetchAll();
        } catch (Exception $e) {
        }

        $profitMargins = [];
        try {
            $stmt = db()->prepare(
                'SELECT nombre, precio_compra, precio_venta,
                        ROUND(((precio_venta - precio_compra) / NULLIF(precio_compra, 0)) * 100, 2) AS margen
                 FROM inventario_productos
                 WHERE (empresa_id = ? OR empresa_id IS NULL)
                   AND precio_compra IS NOT NULL
                   AND precio_compra > 0
                   AND precio_venta IS NOT NULL
                 ORDER BY margen DESC
                 LIMIT 5'
            );
            $stmt->execute([$empresaId]);
            $profitMargins = $stmt->fetchAll();
        } catch (Exception $e) {
        }

        $ventasMensuales = [];
        try {
            $stmt = db()->prepare(
                'SELECT DATE_FORMAT(fecha, "%Y-%m") AS periodo, SUM(total) AS total
                 FROM ventas
                 WHERE (empresa_id = ? OR empresa_id IS NULL)
                 GROUP BY periodo
                 ORDER BY periodo DESC
                 LIMIT 6'
            );
            $stmt->execute([$empresaId]);
            $ventasMensuales = array_reverse($stmt->fetchAll());
        } catch (Exception $e) {
        }

        return [
            'metrics' => $metrics,
            'recentSales' => $recentSales,
            'lowStock' => $lowStock,
            'profitMargins' => $profitMargins,
            'ventasMensuales' => $ventasMensuales,
        ];
    }
}
