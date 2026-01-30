<?php

class TaxesController extends Controller
{
    private TaxPeriodsModel $periods;
    private TaxWithholdingsModel $withholdings;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->periods = new TaxPeriodsModel($db);
        $this->withholdings = new TaxWithholdingsModel($db);
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

    public function index(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $periods = $this->periods->byCompany($companyId);
        $selectedPeriodId = (int)($_GET['period_id'] ?? ($periods[0]['id'] ?? 0));
        $withholdings = $selectedPeriodId > 0 ? $this->withholdings->byPeriod($selectedPeriodId) : [];
        $this->render('taxes/index', [
            'title' => 'Impuestos',
            'pageTitle' => 'Impuestos',
            'periods' => $periods,
            'withholdings' => $withholdings,
            'selectedPeriodId' => $selectedPeriodId,
        ]);
    }

    public function storePeriod(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $period = trim($_POST['period'] ?? '');
        if ($period === '') {
            flash('error', 'Indica el período tributario.');
            $this->redirect('index.php?route=taxes');
        }
        $this->periods->create([
            'company_id' => $companyId,
            'period' => $period,
            'iva_debito' => (float)($_POST['iva_debito'] ?? 0),
            'iva_credito' => (float)($_POST['iva_credito'] ?? 0),
            'remanente' => (float)($_POST['remanente'] ?? 0),
            'total_retenciones' => (float)($_POST['total_retenciones'] ?? 0),
            'impuesto_unico' => (float)($_POST['impuesto_unico'] ?? 0),
            'status' => $_POST['status'] ?? 'pendiente',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Período tributario creado.');
        $this->redirect('index.php?route=taxes');
    }

    public function editPeriod(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $periodId = (int)($_GET['id'] ?? 0);
        $period = $this->db->fetch(
            'SELECT * FROM tax_periods WHERE id = :id AND company_id = :company_id',
            ['id' => $periodId, 'company_id' => $companyId]
        );
        if (!$period) {
            flash('error', 'Período tributario no encontrado.');
            $this->redirect('index.php?route=taxes');
        }
        $this->render('taxes/period-edit', [
            'title' => 'Editar período tributario',
            'pageTitle' => 'Editar período tributario',
            'period' => $period,
        ]);
    }

    public function updatePeriod(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $periodId = (int)($_POST['id'] ?? 0);
        $period = $this->db->fetch(
            'SELECT id FROM tax_periods WHERE id = :id AND company_id = :company_id',
            ['id' => $periodId, 'company_id' => $companyId]
        );
        if (!$period) {
            flash('error', 'Período tributario no encontrado.');
            $this->redirect('index.php?route=taxes');
        }
        $this->periods->update($periodId, [
            'period' => trim($_POST['period'] ?? ''),
            'iva_debito' => (float)($_POST['iva_debito'] ?? 0),
            'iva_credito' => (float)($_POST['iva_credito'] ?? 0),
            'remanente' => (float)($_POST['remanente'] ?? 0),
            'impuesto_unico' => (float)($_POST['impuesto_unico'] ?? 0),
            'status' => $_POST['status'] ?? 'pendiente',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Período tributario actualizado.');
        $this->redirect('index.php?route=taxes');
    }

    public function storeWithholding(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $periodId = (int)($_POST['period_id'] ?? 0);
        $type = trim($_POST['type'] ?? '');
        $baseAmount = (float)($_POST['base_amount'] ?? 0);
        $rate = (float)($_POST['rate'] ?? 0);
        $amount = $baseAmount * ($rate / 100);
        if ($periodId <= 0 || $type === '') {
            flash('error', 'Selecciona un período y tipo de retención.');
            $this->redirect('index.php?route=taxes');
        }
        $this->withholdings->create([
            'company_id' => $companyId,
            'period_id' => $periodId,
            'type' => $type,
            'base_amount' => $baseAmount,
            'rate' => $rate,
            'amount' => $amount,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->db->execute(
            'UPDATE tax_periods SET total_retenciones = total_retenciones + :amount, updated_at = NOW() WHERE id = :id AND company_id = :company_id',
            [
                'amount' => $amount,
                'id' => $periodId,
                'company_id' => $companyId,
            ]
        );
        flash('success', 'Retención registrada.');
        $this->redirect('index.php?route=taxes&period_id=' . $periodId);
    }

    public function editWithholding(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $withholdingId = (int)($_GET['id'] ?? 0);
        $withholding = $this->db->fetch(
            'SELECT * FROM tax_withholdings WHERE id = :id AND company_id = :company_id',
            ['id' => $withholdingId, 'company_id' => $companyId]
        );
        if (!$withholding) {
            flash('error', 'Retención no encontrada.');
            $this->redirect('index.php?route=taxes');
        }
        $this->render('taxes/withholding-edit', [
            'title' => 'Editar retención',
            'pageTitle' => 'Editar retención',
            'withholding' => $withholding,
        ]);
    }

    public function updateWithholding(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $withholdingId = (int)($_POST['id'] ?? 0);
        $withholding = $this->db->fetch(
            'SELECT id, period_id FROM tax_withholdings WHERE id = :id AND company_id = :company_id',
            ['id' => $withholdingId, 'company_id' => $companyId]
        );
        if (!$withholding) {
            flash('error', 'Retención no encontrada.');
            $this->redirect('index.php?route=taxes');
        }
        $baseAmount = (float)($_POST['base_amount'] ?? 0);
        $rate = (float)($_POST['rate'] ?? 0);
        $amount = $baseAmount * ($rate / 100);
        $this->withholdings->update($withholdingId, [
            'type' => trim($_POST['type'] ?? ''),
            'base_amount' => $baseAmount,
            'rate' => $rate,
            'amount' => $amount,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Retención actualizada.');
        $this->redirect('index.php?route=taxes&period_id=' . (int)$withholding['period_id']);
    }

    public function deletePeriod(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $periodId = (int)($_POST['id'] ?? 0);
        $period = $this->db->fetch(
            'SELECT id FROM tax_periods WHERE id = :id AND company_id = :company_id',
            ['id' => $periodId, 'company_id' => $companyId]
        );
        if (!$period) {
            flash('error', 'Período tributario no encontrado.');
            $this->redirect('index.php?route=taxes');
        }
        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();
            $this->db->execute(
                'DELETE FROM tax_withholdings WHERE period_id = :period_id AND company_id = :company_id',
                ['period_id' => $periodId, 'company_id' => $companyId]
            );
            $this->db->execute(
                'DELETE FROM tax_periods WHERE id = :id AND company_id = :company_id',
                ['id' => $periodId, 'company_id' => $companyId]
            );
            audit($this->db, Auth::user()['id'], 'delete', 'tax_periods', $periodId);
            $pdo->commit();
            flash('success', 'Período tributario eliminado.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Failed to delete tax period: ' . $e->getMessage());
            flash('error', 'No se pudo eliminar el período.');
        }
        $this->redirect('index.php?route=taxes');
    }

    public function deleteWithholding(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $withholdingId = (int)($_POST['id'] ?? 0);
        $withholding = $this->db->fetch(
            'SELECT id, period_id, amount FROM tax_withholdings WHERE id = :id AND company_id = :company_id',
            ['id' => $withholdingId, 'company_id' => $companyId]
        );
        if (!$withholding) {
            flash('error', 'Retención no encontrada.');
            $this->redirect('index.php?route=taxes');
        }
        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();
            $this->db->execute(
                'DELETE FROM tax_withholdings WHERE id = :id AND company_id = :company_id',
                ['id' => $withholdingId, 'company_id' => $companyId]
            );
            $this->db->execute(
                'UPDATE tax_periods SET total_retenciones = GREATEST(total_retenciones - :amount, 0), updated_at = NOW() WHERE id = :period_id AND company_id = :company_id',
                [
                    'amount' => (float)($withholding['amount'] ?? 0),
                    'period_id' => (int)$withholding['period_id'],
                    'company_id' => $companyId,
                ]
            );
            audit($this->db, Auth::user()['id'], 'delete', 'tax_withholdings', $withholdingId);
            $pdo->commit();
            flash('success', 'Retención eliminada.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Failed to delete tax withholding: ' . $e->getMessage());
            flash('error', 'No se pudo eliminar la retención.');
        }
        $this->redirect('index.php?route=taxes&period_id=' . (int)$withholding['period_id']);
    }
}
