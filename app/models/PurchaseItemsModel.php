<?php

class PurchaseItemsModel extends Model
{
    protected string $table = 'purchase_items';

    public function hasPettyCashProductColumn(): bool
    {
        return $this->hasColumn('petty_cash_product_id');
    }

    public function hasUnitMeasureColumn(): bool
    {
        return $this->hasColumn('unit_measure');
    }
}
