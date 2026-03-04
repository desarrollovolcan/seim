<?php

class PettyCashReceiptItemsModel extends Model
{
    protected string $table = 'petty_cash_receipt_items';

    public function byReceipt(int $receiptId): array
    {
        return $this->db->fetchAll(
            'SELECT i.*, p.name AS product_name
             FROM petty_cash_receipt_items i
             LEFT JOIN petty_cash_products p ON p.id = i.product_id
             WHERE i.receipt_id = :receipt_id
             ORDER BY i.id ASC',
            ['receipt_id' => $receiptId]
        );
    }
}
