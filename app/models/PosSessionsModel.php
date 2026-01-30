<?php

class PosSessionsModel extends Model
{
    protected string $table = 'pos_sessions';

    public function openSession(int $companyId, int $userId, float $openingAmount): int
    {
        return $this->create([
            'company_id' => $companyId,
            'user_id' => $userId,
            'opening_amount' => $openingAmount,
            'status' => 'abierto',
            'opened_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function activeForUser(int $companyId, int $userId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM pos_sessions WHERE company_id = :company_id AND user_id = :user_id AND status = "abierto" ORDER BY id DESC LIMIT 1',
            ['company_id' => $companyId, 'user_id' => $userId]
        );
    }

    public function closeSession(int $id, float $closingAmount): bool
    {
        return $this->update($id, [
            'closing_amount' => $closingAmount,
            'status' => 'cerrado',
            'closed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
