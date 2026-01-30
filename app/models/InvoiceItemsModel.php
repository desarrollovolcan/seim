<?php

class InvoiceItemsModel extends Model
{
    protected string $table = 'invoice_items';

    public function byInvoice(int $invoiceId): array
    {
        return $this->db->fetchAll('SELECT * FROM invoice_items WHERE invoice_id = :invoice_id', ['invoice_id' => $invoiceId]);
    }
}
