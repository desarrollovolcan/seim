<?php

abstract class Model
{
    protected Database $db;
    protected string $table;
    private array $columnCache = [];

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
        $filtered = [];
        foreach ($data as $column => $value) {
            if ($this->hasColumn($column)) {
                $filtered[$column] = $value;
            }
        }
        if (empty($filtered)) {
            return 0;
        }
        $columns = implode(', ', array_keys($filtered));
        $placeholders = ':' . implode(', :', array_keys($filtered));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->db->execute($sql, $filtered);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $filtered = [];
        foreach ($data as $column => $value) {
            if ($column !== 'id' && $this->hasColumn($column)) {
                $filtered[$column] = $value;
            }
        }
        if (empty($filtered)) {
            return false;
        }
        $assignments = [];
        foreach ($filtered as $key => $value) {
            $assignments[] = "{$key} = :{$key}";
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $assignments) . " WHERE id = :id";
        $filtered['id'] = $id;
        return $this->db->execute($sql, $filtered);
    }

    public function softDelete(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }

    public function hasColumnNamed(string $column): bool
    {
        return $this->hasColumn($column);
    }

    public function isColumnNullable(string $column): bool
    {
        $row = $this->db->fetch(
            'SELECT IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column',
            ['table' => $this->table, 'column' => $column]
        );
        return strtoupper((string)($row['IS_NULLABLE'] ?? 'NO')) === 'YES';
    }

    protected function hasColumn(string $column): bool
    {
        if (array_key_exists($column, $this->columnCache)) {
            return $this->columnCache[$column];
        }

        $row = $this->db->fetch(
            'SELECT COUNT(*) AS count FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column',
            ['table' => $this->table, 'column' => $column]
        );
        $exists = (int)($row['count'] ?? 0) > 0;
        $this->columnCache[$column] = $exists;
        return $exists;
    }
}
