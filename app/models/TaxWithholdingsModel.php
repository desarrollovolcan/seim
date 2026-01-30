<?php

class TaxWithholdingsModel extends Model
{
    protected string $table = 'tax_withholdings';

    public function byPeriod(int $periodId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM tax_withholdings WHERE period_id = :period_id ORDER BY id DESC',
            ['period_id' => $periodId]
        );
    }
}
