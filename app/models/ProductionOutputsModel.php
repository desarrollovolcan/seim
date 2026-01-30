<?php

class ProductionOutputsModel extends Model
{
    protected string $table = 'production_outputs';

    public function outputProductColumn(): string
    {
        if ($this->hasColumn('produced_product_id')) {
            return 'produced_product_id';
        }

        if ($this->hasColumn('product_id')) {
            return 'product_id';
        }

        return 'produced_product_id';
    }
}
