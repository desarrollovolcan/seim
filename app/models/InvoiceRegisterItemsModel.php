<?php

class InvoiceRegisterItemsModel extends Model
{
    protected string $table = 'purchase_invoice_record_items';

    public function byInvoice(int $invoiceId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM purchase_invoice_record_items WHERE invoice_id = :invoice_id ORDER BY id ASC',
            ['invoice_id' => $invoiceId]
        );
    }

    public function deleteByInvoice(int $invoiceId): bool
    {
        return $this->db->execute(
            'DELETE FROM purchase_invoice_record_items WHERE invoice_id = :invoice_id',
            ['invoice_id' => $invoiceId]
        );
    }
}
