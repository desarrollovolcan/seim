<?php

class PurchasesModel extends Model
{
    protected string $table = 'purchases';

    public function listWithRelations(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, s.name AS supplier_name
             FROM purchases p
             LEFT JOIN suppliers s ON p.supplier_id = s.id
             WHERE p.company_id = :company_id AND p.deleted_at IS NULL
             ORDER BY p.purchase_date DESC, p.id DESC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT p.*, s.name AS supplier_name
             FROM purchases p
             LEFT JOIN suppliers s ON p.supplier_id = s.id
             WHERE p.id = :id AND p.company_id = :company_id AND p.deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
    }
}
