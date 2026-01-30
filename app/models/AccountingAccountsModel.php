<?php

class AccountingAccountsModel extends Model
{
    protected string $table = 'accounting_accounts';

    public function byCompany(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM accounting_accounts WHERE company_id = :company_id ORDER BY code ASC',
            ['company_id' => $companyId]
        );
    }

    public function byParent(int $companyId, int $parentId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM accounting_accounts WHERE company_id = :company_id AND parent_id = :parent_id ORDER BY code ASC',
            ['company_id' => $companyId, 'parent_id' => $parentId]
        );
    }

    public function findByCode(int $companyId, string $code, ?int $excludeId = null): ?array
    {
        $sql = 'SELECT * FROM accounting_accounts WHERE company_id = :company_id AND code = :code';
        $params = ['company_id' => $companyId, 'code' => $code];
        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude_id';
            $params['exclude_id'] = $excludeId;
        }
        return $this->db->fetch($sql, $params);
    }

    public function hasChildren(int $companyId, int $accountId): bool
    {
        $row = $this->db->fetch(
            'SELECT id FROM accounting_accounts WHERE company_id = :company_id AND parent_id = :parent_id LIMIT 1',
            ['company_id' => $companyId, 'parent_id' => $accountId]
        );
        return !empty($row);
    }

    public function hasJournalLines(int $companyId, int $accountId): bool
    {
        $row = $this->db->fetch(
            'SELECT ajl.id
             FROM accounting_journal_lines ajl
             JOIN accounting_journals aj ON ajl.journal_id = aj.id
             WHERE aj.company_id = :company_id AND ajl.account_id = :account_id
             LIMIT 1',
            ['company_id' => $companyId, 'account_id' => $accountId]
        );
        return !empty($row);
    }

    public function delete(int $accountId): bool
    {
        return $this->db->execute('DELETE FROM accounting_accounts WHERE id = :id', ['id' => $accountId]);
    }
}
