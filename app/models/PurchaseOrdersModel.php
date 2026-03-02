<?php

class PurchaseOrdersModel extends Model
{
    protected string $table = 'purchase_orders';

    public function listWithRelations(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT po.*, s.name AS supplier_name
             FROM purchase_orders po
             LEFT JOIN suppliers s ON po.supplier_id = s.id
             WHERE po.company_id = :company_id AND po.deleted_at IS NULL
             ORDER BY po.order_date DESC, po.id DESC',
            ['company_id' => $companyId]
        );
    }

    public function findForCompany(int $id, int $companyId): ?array
    {
        return $this->db->fetch(
            'SELECT po.*, s.name AS supplier_name,
                    s.code AS supplier_code,
                    s.tax_id AS supplier_tax_id,
                    s.contact_name AS supplier_contact_name,
                    s.email AS supplier_email,
                    s.phone AS supplier_phone,
                    s.address AS supplier_address,
                    s.commune AS supplier_commune,
                    s.giro AS supplier_giro,
                    s.website AS supplier_website
             FROM purchase_orders po
             LEFT JOIN suppliers s ON po.supplier_id = s.id
             WHERE po.id = :id AND po.company_id = :company_id AND po.deleted_at IS NULL',
            ['id' => $id, 'company_id' => $companyId]
        );
    }
}
