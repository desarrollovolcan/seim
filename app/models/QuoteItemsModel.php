<?php

class QuoteItemsModel extends Model
{
    protected string $table = 'quote_items';

    public function byQuote(int $quoteId): array
    {
        return $this->db->fetchAll('SELECT * FROM quote_items WHERE quote_id = :quote_id', ['quote_id' => $quoteId]);
    }
}
