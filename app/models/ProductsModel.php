<?php

class ProductsModel extends Model
{
    protected string $table = 'products';

    public function active(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT *
             FROM products
             WHERE company_id = :company_id
             ORDER BY name ASC',
            ['company_id' => $companyId]
        );
    }

    public function filtered(int $companyId, array $filters = []): array
    {
        $where = ['p.company_id = :company_id'];
        $params = ['company_id' => $companyId];

        $search = trim((string)($filters['search'] ?? ''));
        if ($search !== '') {
            $where[] = '(p.name LIKE :search OR p.sku LIKE :search OR p.description LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $familyId = (int)($filters['family_id'] ?? 0);
        if ($familyId > 0) {
            $where[] = 'p.family_id = :family_id';
            $params['family_id'] = $familyId;
        }

        $subfamilyId = (int)($filters['subfamily_id'] ?? 0);
        if ($subfamilyId > 0) {
            $where[] = 'p.subfamily_id = :subfamily_id';
            $params['subfamily_id'] = $subfamilyId;
        }

        $supplierId = (int)($filters['supplier_id'] ?? 0);
        if ($supplierId > 0) {
            $where[] = 'p.supplier_id = :supplier_id';
            $params['supplier_id'] = $supplierId;
        }

        return $this->db->fetchAll(
            'SELECT p.*,
                    f.name AS family_name,
                    sf.name AS subfamily_name,
                    s.name AS supplier_name
             FROM products p
             LEFT JOIN product_families f ON f.id = p.family_id
             LEFT JOIN product_subfamilies sf ON sf.id = p.subfamily_id
             LEFT JOIN suppliers s ON s.id = p.supplier_id
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY p.name ASC',
            $params
        );
    }

    public function bulkAssign(int $companyId, array $productIds, ?int $familyId, ?int $subfamilyId, ?int $supplierId): int
    {
        if ($productIds === []) {
            return 0;
        }

        $updates = [];
        $params = [];
        if ($familyId !== null) {
            $updates[] = 'family_id = ?';
            $params[] = $familyId;
        }
        if ($subfamilyId !== null) {
            $updates[] = 'subfamily_id = ?';
            $params[] = $subfamilyId;
        }
        if ($supplierId !== null) {
            $updates[] = 'supplier_id = ?';
            $params[] = $supplierId;
        }
        if ($updates === []) {
            return 0;
        }

        $updates[] = 'updated_at = NOW()';
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $sql = 'UPDATE products
                SET ' . implode(', ', $updates) . "
                WHERE company_id = ? AND id IN ({$placeholders})";
        $params = [...$params, $companyId, ...$productIds];
        return $this->db->execute($sql, $params);
    }

    public function filteredIds(int $companyId, array $filters = []): array
    {
        return array_map(
            static fn(array $product): int => (int)$product['id'],
            $this->filtered($companyId, $filters)
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM products WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
    }

    public function adjustStock(int $id, int $difference): void
    {
        $this->db->execute(
            'UPDATE products SET stock = GREATEST(0, stock + :difference), updated_at = NOW() WHERE id = :id',
            [
                'difference' => $difference,
                'id' => $id,
            ]
        );
    }

    public function updateCost(int $id, float $cost): void
    {
        $this->db->execute(
            'UPDATE products SET cost = :cost, updated_at = NOW() WHERE id = :id',
            [
                'cost' => $cost,
                'id' => $id,
            ]
        );
    }

    public function latestCompetitionCode(int $companyId, string $prefix, ?int $excludeId = null): ?string
    {
        $params = [
            'company_id' => $companyId,
            'prefix' => $prefix . '%',
        ];
        $where = 'company_id = :company_id AND competition_code LIKE :prefix';
        if ($excludeId) {
            $where .= ' AND id <> :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $row = $this->db->fetch(
            "SELECT competition_code FROM products WHERE {$where} ORDER BY competition_code DESC LIMIT 1",
            $params
        );

        return $row['competition_code'] ?? null;
    }

    public function latestSupplierCode(int $companyId, string $prefix, ?int $excludeId = null): ?string
    {
        $params = [
            'company_id' => $companyId,
            'prefix' => $prefix . '%',
        ];
        $where = 'company_id = :company_id AND supplier_code LIKE :prefix';
        if ($excludeId) {
            $where .= ' AND id <> :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $row = $this->db->fetch(
            "SELECT supplier_code FROM products WHERE {$where} ORDER BY supplier_code DESC LIMIT 1",
            $params
        );

        return $row['supplier_code'] ?? null;
    }
}
