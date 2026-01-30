<?php

abstract class Model
{
    protected Database $db;
    protected string $table;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function all(string $where = '1=1', array $params = []): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY id DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function find(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->db->execute($sql, $data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $assignments = [];
        foreach ($data as $key => $value) {
            $assignments[] = "{$key} = :{$key}";
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $assignments) . " WHERE id = :id";
        $data['id'] = $id;
        return $this->db->execute($sql, $data);
    }

    public function softDelete(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }
}
