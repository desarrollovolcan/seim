<?php

class PaymentsModel extends Model
{
    protected string $table = 'payments';

    public function byInvoice(int $invoiceId): array
    {
        return $this->db->fetchAll('SELECT * FROM payments WHERE invoice_id = :invoice_id ORDER BY id DESC', ['invoice_id' => $invoiceId]);
    }
}
