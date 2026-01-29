<?php

declare(strict_types=1);

class DashboardController
{
    public function show(): array
    {
        $empresaId = current_empresa_id();
        $resumen = [
            'ventas_total' => 0.0,
            'costo_total' => 0.0,
            'ganancia_total' => 0.0,
            'margen_total' => 0.0,
            'productos_bajo_stock' => 0,
        ];

        try {
            $stmt = db()->prepare(
                'SELECT COUNT(*)
                 FROM inventario_productos
                 WHERE (empresa_id = ? OR empresa_id IS NULL)
                   AND stock_actual <= stock_minimo'
            );
            $stmt->execute([$empresaId]);
            $resumen['productos_bajo_stock'] = (int) $stmt->fetchColumn();
        } catch (Exception $e) {
        }

        try {
            $stmt = db()->prepare(
                'SELECT SUM(vi.total) AS ventas_total,
                        SUM(vi.cantidad * COALESCE(p.precio_compra, 0)) AS costo_total
                 FROM venta_items vi
                 INNER JOIN ventas v ON v.id = vi.venta_id
                 INNER JOIN inventario_productos p ON p.id = vi.producto_id
                 WHERE (v.empresa_id = ? OR v.empresa_id IS NULL)'
            );
            $stmt->execute([$empresaId]);
            $totales = $stmt->fetch();
            $resumen['ventas_total'] = (float) ($totales['ventas_total'] ?? 0);
            $resumen['costo_total'] = (float) ($totales['costo_total'] ?? 0);
            $resumen['ganancia_total'] = $resumen['ventas_total'] - $resumen['costo_total'];
            $resumen['margen_total'] = $resumen['ventas_total'] > 0
                ? round(($resumen['ganancia_total'] / $resumen['ventas_total']) * 100, 2)
                : 0.0;
        } catch (Exception $e) {
        }

        $lowStock = [];
        try {
            $stmt = db()->prepare(
                'SELECT nombre, stock_actual, stock_minimo, precio_compra, precio_venta
                 FROM inventario_productos
                 WHERE (empresa_id = ? OR empresa_id IS NULL) AND stock_actual <= stock_minimo
                 ORDER BY stock_actual ASC LIMIT 8'
            );
            $stmt->execute([$empresaId]);
            $lowStock = $stmt->fetchAll();
        } catch (Exception $e) {
        }

        $ventasProductos = [];
        try {
            $stmt = db()->prepare(
                'SELECT p.id,
                        p.nombre,
                        p.precio_compra,
                        p.precio_venta,
                        SUM(vi.cantidad) AS unidades,
                        SUM(vi.total) AS ventas_total,
                        ROUND(AVG(vi.precio_unitario), 2) AS precio_promedio
                 FROM venta_items vi
                 INNER JOIN ventas v ON v.id = vi.venta_id
                 INNER JOIN inventario_productos p ON p.id = vi.producto_id
                 WHERE (v.empresa_id = ? OR v.empresa_id IS NULL)
                 GROUP BY p.id, p.nombre, p.precio_compra, p.precio_venta
                 ORDER BY ventas_total DESC
                 LIMIT 8'
            );
            $stmt->execute([$empresaId]);
            $ventasProductos = $stmt->fetchAll();
        } catch (Exception $e) {
        }

        $ventasMensuales = [];
        try {
            $stmt = db()->prepare(
                'SELECT DATE_FORMAT(v.fecha, "%Y-%m") AS periodo,
                        SUM(vi.total) AS ventas_total,
                        SUM(vi.cantidad * COALESCE(p.precio_compra, 0)) AS costo_total,
                        SUM(vi.total - (vi.cantidad * COALESCE(p.precio_compra, 0))) AS ganancia_total
                 FROM ventas v
                 INNER JOIN venta_items vi ON vi.venta_id = v.id
                 INNER JOIN inventario_productos p ON p.id = vi.producto_id
                 WHERE (v.empresa_id = ? OR v.empresa_id IS NULL)
                 GROUP BY periodo
                 ORDER BY periodo ASC
                 LIMIT 12'
            );
            $stmt->execute([$empresaId]);
            $ventasMensuales = $stmt->fetchAll();
        } catch (Exception $e) {
        }

        $gananciaAcumulada = [];
        $acumulado = 0.0;
        foreach ($ventasMensuales as $item) {
            $acumulado += (float) ($item['ganancia_total'] ?? 0);
            $gananciaAcumulada[] = [
                'periodo' => $item['periodo'] ?? '',
                'ganancia_acumulada' => $acumulado,
                'ventas_total' => (float) ($item['ventas_total'] ?? 0),
                'costo_total' => (float) ($item['costo_total'] ?? 0),
            ];
        }

        return [
            'resumen' => $resumen,
            'lowStock' => $lowStock,
            'ventasProductos' => $ventasProductos,
            'gananciaAcumulada' => $gananciaAcumulada,
        ];
    }
}
