<?php

class HrPayrollsController extends Controller
{
    private HrPayrollsModel $payrolls;
    private HrEmployeesModel $employees;
    private HrContractsModel $contracts;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->payrolls = new HrPayrollsModel($db);
        $this->employees = new HrEmployeesModel($db);
        $this->contracts = new HrContractsModel($db);
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

        $this->render('hr/payrolls/index', [
            'title' => 'Remuneraciones',
            'pageTitle' => 'Remuneraciones y nómina',
            'payrolls' => $this->payrolls->byCompany($companyId),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();

        $this->render('hr/payrolls/create', [
            'title' => 'Nueva remuneración',
            'pageTitle' => 'Nueva remuneración',
            'employees' => $this->employees->active($companyId),
        ]);
    }

    private function buildPayrollBreakdown(array $employee, float $baseSalary, float $bonuses, float $otherEarnings, float $otherDeductions): array
    {
        $pensionRate = (float)($employee['pension_rate'] ?? 10);
        $healthRate = (float)($employee['health_rate'] ?? 7);
        $unemploymentRate = (float)($employee['unemployment_rate'] ?? 0.6);

        $taxableIncome = $baseSalary + $bonuses + $otherEarnings;
        $pensionDeduction = round($taxableIncome * ($pensionRate / 100), 2);
        $healthDeduction = round($taxableIncome * ($healthRate / 100), 2);
        $unemploymentDeduction = round($taxableIncome * ($unemploymentRate / 100), 2);
        $totalDeductions = $pensionDeduction + $healthDeduction + $unemploymentDeduction + $otherDeductions;
        $netPay = $taxableIncome - $totalDeductions;

        return [
            'taxable_income' => $taxableIncome,
            'pension_deduction' => $pensionDeduction,
            'health_deduction' => $healthDeduction,
            'unemployment_deduction' => $unemploymentDeduction,
            'total_deductions' => $totalDeductions,
            'net_pay' => $netPay,
        ];
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $employeeId = (int)($_POST['employee_id'] ?? 0);
        $periodStart = trim($_POST['period_start'] ?? '');
        $periodEnd = trim($_POST['period_end'] ?? '');
        if ($employeeId === 0 || $periodStart === '' || $periodEnd === '') {
            flash('error', 'Selecciona trabajador y período.');
            $this->redirect('index.php?route=hr/payrolls/create');
        }

        $employee = $this->employees->findForCompany($employeeId, $companyId);
        if (!$employee) {
            flash('error', 'Trabajador no válido.');
            $this->redirect('index.php?route=hr/payrolls/create');
        }

        $baseSalary = (float)($_POST['base_salary'] ?? 0);
        $bonuses = (float)($_POST['bonuses'] ?? 0);
        $otherEarnings = (float)($_POST['other_earnings'] ?? 0);
        $otherDeductions = (float)($_POST['other_deductions'] ?? 0);
        $breakdown = $this->buildPayrollBreakdown($employee, $baseSalary, $bonuses, $otherEarnings, $otherDeductions);

        $this->payrolls->create([
            'company_id' => $companyId,
            'employee_id' => $employeeId,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'base_salary' => $baseSalary,
            'bonuses' => $bonuses,
            'other_earnings' => $otherEarnings,
            'other_deductions' => $otherDeductions,
            'taxable_income' => $breakdown['taxable_income'],
            'pension_deduction' => $breakdown['pension_deduction'],
            'health_deduction' => $breakdown['health_deduction'],
            'unemployment_deduction' => $breakdown['unemployment_deduction'],
            'total_deductions' => $breakdown['total_deductions'],
            'net_pay' => $breakdown['net_pay'],
            'status' => $_POST['status'] ?? 'borrador',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'hr_payrolls');
        flash('success', 'Remuneración creada correctamente.');
        $this->redirect('index.php?route=hr/payrolls');
    }

    public function bulkCreate(): void
    {
        $this->requireLogin();
        $this->requireCompany();

        $this->render('hr/payrolls/bulk', [
            'title' => 'Liquidaciones masivas',
            'pageTitle' => 'Generación masiva de liquidaciones',
        ]);
    }

    public function bulkStore(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $periodStart = trim($_POST['period_start'] ?? '');
        $periodEnd = trim($_POST['period_end'] ?? '');
        if ($periodStart === '' || $periodEnd === '') {
            flash('error', 'Completa el período.');
            $this->redirect('index.php?route=hr/payrolls/bulk');
        }

        $contracts = $this->contracts->activeForPayroll($companyId, $periodStart, $periodEnd);
        if (!$contracts) {
            flash('error', 'No hay contratos vigentes para el período seleccionado.');
            $this->redirect('index.php?route=hr/payrolls/bulk');
        }

        foreach ($contracts as $contract) {
            $employee = $this->employees->findForCompany((int)$contract['employee_id'], $companyId);
            if (!$employee) {
                continue;
            }
            $baseSalary = (float)($contract['salary'] ?? 0);
            $bonuses = (float)($_POST['bonuses'] ?? 0);
            $otherEarnings = (float)($_POST['other_earnings'] ?? 0);
            $otherDeductions = (float)($_POST['other_deductions'] ?? 0);
            $breakdown = $this->buildPayrollBreakdown($employee, $baseSalary, $bonuses, $otherEarnings, $otherDeductions);

            $this->payrolls->create([
                'company_id' => $companyId,
                'employee_id' => (int)$contract['employee_id'],
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'base_salary' => $baseSalary,
                'bonuses' => $bonuses,
                'other_earnings' => $otherEarnings,
                'other_deductions' => $otherDeductions,
                'taxable_income' => $breakdown['taxable_income'],
                'pension_deduction' => $breakdown['pension_deduction'],
                'health_deduction' => $breakdown['health_deduction'],
                'unemployment_deduction' => $breakdown['unemployment_deduction'],
                'total_deductions' => $breakdown['total_deductions'],
                'net_pay' => $breakdown['net_pay'],
                'status' => $_POST['status'] ?? 'borrador',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        audit($this->db, Auth::user()['id'], 'create', 'hr_payrolls_bulk');
        flash('success', 'Liquidaciones generadas correctamente.');
        $this->redirect('index.php?route=hr/payrolls');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $payroll = $this->payrolls->findForCompany($id, $companyId);
        if (!$payroll) {
            $this->redirect('index.php?route=hr/payrolls');
        }

        $this->db->execute('DELETE FROM hr_payrolls WHERE id = :id AND company_id = :company_id', [
            'id' => $id,
            'company_id' => $companyId,
        ]);
        audit($this->db, Auth::user()['id'], 'delete', 'hr_payrolls', $id);
        flash('success', 'Remuneración eliminada correctamente.');
        $this->redirect('index.php?route=hr/payrolls');
    }
}
