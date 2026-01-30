<?php

class SettingsModel extends Model
{
    protected string $table = 'settings';

    public function get(string $key, $default = null, ?int $companyId = null)
    {
        $companyId = $companyId ?? current_company_id();
        if ($companyId) {
            $row = $this->db->fetch('SELECT value FROM settings WHERE `key` = :key AND company_id = :company_id', [
                'key' => $key,
                'company_id' => $companyId,
            ]);
            if (!$row) {
                $row = $this->db->fetch('SELECT value FROM settings WHERE `key` = :key AND company_id IS NULL', ['key' => $key]);
            }
        } else {
            $row = $this->db->fetch('SELECT value FROM settings WHERE `key` = :key AND company_id IS NULL', ['key' => $key]);
        }
        if (!$row) {
            return $default;
        }
        $value = json_decode($row['value'], true);
        return $value === null ? $row['value'] : $value;
    }

    public function set(string $key, $value, ?int $companyId = null): void
    {
        $payload = is_array($value) ? json_encode($value) : $value;
        $companyId = $companyId ?? current_company_id();
        $exists = $this->db->fetch('SELECT id FROM settings WHERE `key` = :key AND company_id <=> :company_id', [
            'key' => $key,
            'company_id' => $companyId,
        ]);
        if ($exists) {
            $this->db->execute('UPDATE settings SET value = :value, updated_at = NOW() WHERE `key` = :key AND company_id <=> :company_id', [
                'value' => $payload,
                'key' => $key,
                'company_id' => $companyId,
            ]);
        } else {
            $this->db->execute('INSERT INTO settings (`key`, company_id, value, created_at, updated_at) VALUES (:key, :company_id, :value, NOW(), NOW())', [
                'key' => $key,
                'company_id' => $companyId,
                'value' => $payload,
            ]);
        }
    }
}
