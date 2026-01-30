<?php

class TreasuryController extends Controller
{
    private BankAccountsModel $accounts;
    private BankTransactionsModel $transactions;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->accounts = new BankAccountsModel($db);
        $this->transactions = new BankTransactionsModel($db);
    }

    private function requireCompany(): int
    {
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        return (int)$companyId;
    }

    public function accounts(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $accounts = $this->accounts->byCompany($companyId);
        $this->render('treasury/accounts', [
            'title' => 'Cuentas bancarias',
            'pageTitle' => 'Cuentas bancarias',
            'accounts' => $accounts,
        ]);
    }

    public function showAccount(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $accountId = (int)($_GET['id'] ?? 0);
        $account = $this->db->fetch(
            'SELECT * FROM bank_accounts WHERE id = :id AND company_id = :company_id',
            ['id' => $accountId, 'company_id' => $companyId]
        );
        if (!$account) {
            flash('error', 'Cuenta bancaria no encontrada.');
            $this->redirect('index.php?route=treasury/accounts');
        }
        $transactions = $this->db->fetchAll(
            'SELECT * FROM bank_transactions WHERE bank_account_id = :account_id AND company_id = :company_id ORDER BY transaction_date DESC, id DESC',
            ['account_id' => $accountId, 'company_id' => $companyId]
        );
        $this->render('treasury/account-show', [
            'title' => 'Detalle cuenta bancaria',
            'pageTitle' => 'Detalle cuenta bancaria',
            'account' => $account,
            'transactions' => $transactions,
        ]);
    }

    public function storeAccount(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $this->accounts->create([
            'company_id' => $companyId,
            'name' => trim($_POST['name'] ?? ''),
            'bank_name' => trim($_POST['bank_name'] ?? ''),
            'account_number' => trim($_POST['account_number'] ?? ''),
            'currency' => $_POST['currency'] ?? 'CLP',
            'current_balance' => (float)($_POST['current_balance'] ?? 0),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Cuenta bancaria creada.');
        $this->redirect('index.php?route=treasury/accounts');
    }

    public function editAccount(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $accountId = (int)($_GET['id'] ?? 0);
        $account = $this->db->fetch(
            'SELECT * FROM bank_accounts WHERE id = :id AND company_id = :company_id',
            ['id' => $accountId, 'company_id' => $companyId]
        );
        if (!$account) {
            flash('error', 'Cuenta bancaria no encontrada.');
            $this->redirect('index.php?route=treasury/accounts');
        }
        $this->render('treasury/account-edit', [
            'title' => 'Editar cuenta bancaria',
            'pageTitle' => 'Editar cuenta bancaria',
            'account' => $account,
        ]);
    }

    public function updateAccount(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $accountId = (int)($_POST['id'] ?? 0);
        $account = $this->db->fetch(
            'SELECT id FROM bank_accounts WHERE id = :id AND company_id = :company_id',
            ['id' => $accountId, 'company_id' => $companyId]
        );
        if (!$account) {
            flash('error', 'Cuenta bancaria no encontrada.');
            $this->redirect('index.php?route=treasury/accounts');
        }
        $this->accounts->update($accountId, [
            'name' => trim($_POST['name'] ?? ''),
            'bank_name' => trim($_POST['bank_name'] ?? ''),
            'account_number' => trim($_POST['account_number'] ?? ''),
            'currency' => $_POST['currency'] ?? 'CLP',
            'current_balance' => (float)($_POST['current_balance'] ?? 0),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Cuenta bancaria actualizada.');
        $this->redirect('index.php?route=treasury/accounts');
    }

    public function transactions(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $accounts = $this->accounts->byCompany($companyId);
        $transactions = $this->transactions->byCompany($companyId);
        $this->render('treasury/transactions', [
            'title' => 'Movimientos bancarios',
            'pageTitle' => 'Movimientos bancarios',
            'accounts' => $accounts,
            'transactions' => $transactions,
            'today' => date('Y-m-d'),
        ]);
    }

    public function showTransaction(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $transactionId = (int)($_GET['id'] ?? 0);
        $transaction = $this->db->fetch(
            'SELECT bt.*, ba.name as account_name
             FROM bank_transactions bt
             JOIN bank_accounts ba ON bt.bank_account_id = ba.id
             WHERE bt.id = :id AND bt.company_id = :company_id',
            ['id' => $transactionId, 'company_id' => $companyId]
        );
        if (!$transaction) {
            flash('error', 'Movimiento bancario no encontrado.');
            $this->redirect('index.php?route=treasury/transactions');
        }
        $this->render('treasury/transaction-show', [
            'title' => 'Detalle movimiento bancario',
            'pageTitle' => 'Detalle movimiento bancario',
            'transaction' => $transaction,
        ]);
    }

    public function storeTransaction(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $accountId = (int)($_POST['bank_account_id'] ?? 0);
        $account = $this->accounts->find($accountId);
        if (!$account || (int)$account['company_id'] !== $companyId) {
            flash('error', 'Cuenta bancaria no válida.');
            $this->redirect('index.php?route=treasury/transactions');
        }
        $type = $_POST['type'] ?? 'deposito';
        $amount = (float)($_POST['amount'] ?? 0);
        $currentBalance = (float)($account['current_balance'] ?? 0);
        $newBalance = $type === 'retiro' ? $currentBalance - $amount : $currentBalance + $amount;
        $this->transactions->create([
            'company_id' => $companyId,
            'bank_account_id' => $accountId,
            'transaction_date' => trim($_POST['transaction_date'] ?? date('Y-m-d')),
            'description' => trim($_POST['description'] ?? ''),
            'type' => $type,
            'amount' => $amount,
            'balance' => $newBalance,
            'reference' => trim($_POST['reference'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->accounts->update($accountId, [
            'current_balance' => $newBalance,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Movimiento bancario registrado.');
        $this->redirect('index.php?route=treasury/transactions');
    }

    public function editTransaction(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $transactionId = (int)($_GET['id'] ?? 0);
        $transaction = $this->db->fetch(
            'SELECT bt.*, ba.name as account_name
             FROM bank_transactions bt
             JOIN bank_accounts ba ON bt.bank_account_id = ba.id
             WHERE bt.id = :id AND bt.company_id = :company_id',
            ['id' => $transactionId, 'company_id' => $companyId]
        );
        if (!$transaction) {
            flash('error', 'Movimiento bancario no encontrado.');
            $this->redirect('index.php?route=treasury/transactions');
        }
        $this->render('treasury/transaction-edit', [
            'title' => 'Editar movimiento bancario',
            'pageTitle' => 'Editar movimiento bancario',
            'transaction' => $transaction,
        ]);
    }

    public function updateTransaction(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $transactionId = (int)($_POST['id'] ?? 0);
        $transaction = $this->db->fetch(
            'SELECT id FROM bank_transactions WHERE id = :id AND company_id = :company_id',
            ['id' => $transactionId, 'company_id' => $companyId]
        );
        if (!$transaction) {
            flash('error', 'Movimiento bancario no encontrado.');
            $this->redirect('index.php?route=treasury/transactions');
        }
        $this->transactions->update($transactionId, [
            'reference' => trim($_POST['reference'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Movimiento bancario actualizado.');
        $this->redirect('index.php?route=treasury/transactions');
    }
}
