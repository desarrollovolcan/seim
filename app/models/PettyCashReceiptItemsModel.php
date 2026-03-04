<?php

class PettyCashReceiptItemsModel extends Model
{
    protected string $table = 'petty_cash_receipt_items';

    public function byReceipt(int $receiptId): array
    {
        return $this->db->fetchAll(
            'SELECT i.*, p.name AS product_name, p.unit_measure
             FROM petty_cash_receipt_items i
             LEFT JOIN petty_cash_products p ON p.id = i.product_id
             WHERE i.receipt_id = :receipt_id
             ORDER BY i.id ASC',
            ['receipt_id' => $receiptId]
        );
    }

    public function deleteByReceipt(int $receiptId): bool
    {
        return $this->db->execute(
            'DELETE FROM petty_cash_receipt_items WHERE receipt_id = :receipt_id',
            ['receipt_id' => $receiptId]
        );
    }
}
