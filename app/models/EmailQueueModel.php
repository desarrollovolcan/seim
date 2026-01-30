<?php

class EmailQueueModel extends Model
{
    protected string $table = 'email_queue';

    public function pending(?int $companyId = null): array
    {
        $companyId = $companyId ?? current_company_id();
        return $this->db->fetchAll(
            'SELECT * FROM email_queue WHERE status = "pending" AND company_id = :company_id ORDER BY scheduled_at ASC',
            ['company_id' => $companyId]
        );
    }
}
