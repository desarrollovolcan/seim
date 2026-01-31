<?php

class PosSessionWithdrawalsModel extends Model
{
    protected string $table = 'pos_session_withdrawals';

    public function totalBySession(int $sessionId): float
    {
        $row = $this->db->fetch(
            'SELECT SUM(amount) AS total FROM pos_session_withdrawals WHERE pos_session_id = :session_id',
            ['session_id' => $sessionId]
        );
        return (float)($row['total'] ?? 0);
    }
}
