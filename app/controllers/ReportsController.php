<?php

class ReportsController
{
    public function download(): void
    {
        $isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
        if ($isPost) {
            verify_csrf();
        }
        $template = $isPost
            ? (isset($_POST['report_template']) ? basename((string)$_POST['report_template']) : '')
            : (isset($_GET['template']) ? basename((string)$_GET['template']) : '');
        $source = $isPost
            ? (isset($_POST['report_source']) ? (string)$_POST['report_source'] : 'formulario')
            : (isset($_GET['source']) ? (string)$_GET['source'] : 'formulario');
        $templateByRoute = [
            'email_templates/create' => 'informeIcargaEspanol.php',
            'email_templates/edit' => 'informeIcargaEspanol.php',
            'purchases/create' => 'informeIcargaEspanol.php',
            'treasury/transaction-edit' => 'informeIcargaEspanol.php',
            'treasury/account-edit' => 'informeIcargaEspanol.php',
            'products/create' => 'informeIcargaEspanol.php',
            'products/edit' => 'informeIcargaEspanol.php',
            'taxes/period-edit' => 'informeIcargaEspanol.php',
            'taxes/withholding-edit' => 'informeIcargaEspanol.php',
            'roles/create' => 'informeIcargaEspanol.php',
            'roles/edit' => 'informeIcargaEspanol.php',
            'companies/create' => 'informeIcargaEspanol.php',
            'companies/edit' => 'informeIcargaEspanol.php',
            'services/create' => 'informeIcargaEspanol.php',
            'services/edit' => 'informeIcargaEspanol.php',
            'sales/create' => 'informeIcargaEspanol.php',
            'fixed-assets/create' => 'informeIcargaEspanol.php',
            'fixed-assets/edit' => 'informeIcargaEspanol.php',
            'quotes/create' => 'informeIcargaInvoice.php',
            'quotes/edit' => 'informeIcargaInvoice.php',
            'hr/payrolls/create' => 'informeIcargaEspanol.php',
            'hr/contracts/create' => 'informeIcargaEspanol.php',
            'hr/contracts/edit' => 'informeIcargaEspanol.php',
            'hr/attendance/create' => 'informeIcargaEspanol.php',
            'hr/employees/create' => 'informeIcargaEspanol.php',
            'hr/employees/edit' => 'informeIcargaEspanol.php',
            'tickets/create' => 'informeIcargaEspanol.php',
            'suppliers/create' => 'informeIcargaEspanol.php',
            'suppliers/edit' => 'informeIcargaEspanol.php',
            'users/create' => 'informeIcargaEspanol.php',
            'users/edit' => 'informeIcargaEspanol.php',
            'projects/create' => 'informeIcargaEspanol.php',
            'projects/edit' => 'informeIcargaEspanol.php',
            'inventory/movement-edit' => 'informeIcargaEspanol.php',
            'clients/create' => 'informeIcargaEspanol.php',
            'clients/edit' => 'informeIcargaEspanol.php',
            'crm/briefs' => 'informeFicha.php',
            'accounting/journals-create' => 'informeIcargaEspanol.php',
            'accounting/chart-create' => 'informeIcargaEspanol.php',
            'accounting/chart-edit' => 'informeIcargaEspanol.php',
            'honorarios/create' => 'informeIcargaEspanol.php',
            'invoices/create' => 'informeIcargaInvoice.php',
            'invoices/edit' => 'informeIcargaInvoice.php',
            'maintainers/hr-contract-types/create' => 'informeIcargaEspanol.php',
            'maintainers/hr-contract-types/edit' => 'informeIcargaEspanol.php',
            'maintainers/services/create' => 'informeIcargaEspanol.php',
            'maintainers/services/edit' => 'informeIcargaEspanol.php',
            'maintainers/hr-work-schedules/create' => 'informeIcargaEspanol.php',
            'maintainers/hr-work-schedules/edit' => 'informeIcargaEspanol.php',
            'maintainers/service-types/create' => 'informeIcargaEspanol.php',
            'maintainers/service-types/edit' => 'informeIcargaEspanol.php',
            'maintainers/hr-payroll-items/create' => 'informeIcargaEspanol.php',
            'maintainers/hr-payroll-items/edit' => 'informeIcargaEspanol.php',
            'maintainers/product-families/create' => 'informeIcargaEspanol.php',
            'maintainers/product-families/edit' => 'informeIcargaEspanol.php',
            'maintainers/hr-pension-funds/create' => 'informeIcargaEspanol.php',
            'maintainers/hr-pension-funds/edit' => 'informeIcargaEspanol.php',
            'maintainers/hr-health-providers/create' => 'informeIcargaEspanol.php',
            'maintainers/hr-health-providers/edit' => 'informeIcargaEspanol.php',
            'maintainers/hr-departments/create' => 'informeIcargaEspanol.php',
            'maintainers/hr-departments/edit' => 'informeIcargaEspanol.php',
            'maintainers/product-subfamilies/create' => 'informeIcargaEspanol.php',
            'maintainers/product-subfamilies/edit' => 'informeIcargaEspanol.php',
            'maintainers/hr-positions/create' => 'informeIcargaEspanol.php',
            'maintainers/hr-positions/edit' => 'informeIcargaEspanol.php',
            'crm/orders' => 'informeIcargaEspanol.php',
            'crm/renewals' => 'informeIcargaEspanol.php',
            'crm/hub/project' => 'informeIcargaEspanol.php',
            'crm/hub/service' => 'informeIcargaEspanol.php',
        ];
        $reportFileByRoute = [
            'email_templates/create' => 'email_templates_create.php',
            'email_templates/edit' => 'email_templates_edit.php',
            'purchases/create' => 'purchases_create.php',
            'treasury/transaction-edit' => 'treasury_transaction_edit.php',
            'treasury/account-edit' => 'treasury_account_edit.php',
            'products/create' => 'products_create.php',
            'products/edit' => 'products_edit.php',
            'taxes/period-edit' => 'taxes_period_edit.php',
            'taxes/withholding-edit' => 'taxes_withholding_edit.php',
            'roles/create' => 'roles_create.php',
            'roles/edit' => 'roles_edit.php',
            'companies/create' => 'companies_create.php',
            'companies/edit' => 'companies_edit.php',
            'services/create' => 'services_create.php',
            'services/edit' => 'services_edit.php',
            'sales/create' => 'sales_create.php',
            'fixed-assets/create' => 'fixed_assets_create.php',
            'fixed-assets/edit' => 'fixed_assets_edit.php',
            'quotes/create' => 'quotes_create.php',
            'quotes/edit' => 'quotes_edit.php',
            'hr/payrolls/create' => 'hr_payrolls_create.php',
            'hr/contracts/create' => 'hr_contracts_create.php',
            'hr/contracts/edit' => 'hr_contracts_edit.php',
            'hr/attendance/create' => 'hr_attendance_create.php',
            'hr/employees/create' => 'hr_employees_create.php',
            'hr/employees/edit' => 'hr_employees_edit.php',
            'tickets/create' => 'tickets_create.php',
            'suppliers/create' => 'suppliers_create.php',
            'suppliers/edit' => 'suppliers_edit.php',
            'users/create' => 'users_create.php',
            'users/edit' => 'users_edit.php',
            'projects/create' => 'projects_create.php',
            'projects/edit' => 'projects_edit.php',
            'inventory/movement-edit' => 'inventory_movement_edit.php',
            'clients/create' => 'clients_create.php',
            'clients/edit' => 'clients_edit.php',
            'crm/briefs' => 'briefs_create.php',
            'accounting/journals-create' => 'accounting_journals_create.php',
            'accounting/chart-create' => 'accounting_chart_create.php',
            'accounting/chart-edit' => 'accounting_chart_edit.php',
            'honorarios/create' => 'honorarios_create.php',
            'invoices/create' => 'invoices_create.php',
            'invoices/edit' => 'invoices_edit.php',
            'maintainers/hr-contract-types/create' => 'maintainers_hr_contract_types_create.php',
            'maintainers/hr-contract-types/edit' => 'maintainers_hr_contract_types_edit.php',
            'maintainers/services/create' => 'maintainers_services_create.php',
            'maintainers/services/edit' => 'maintainers_services_edit.php',
            'maintainers/hr-work-schedules/create' => 'maintainers_hr_work_schedules_create.php',
            'maintainers/hr-work-schedules/edit' => 'maintainers_hr_work_schedules_edit.php',
            'maintainers/service-types/create' => 'maintainers_service_types_create.php',
            'maintainers/service-types/edit' => 'maintainers_service_types_edit.php',
            'maintainers/hr-payroll-items/create' => 'maintainers_hr_payroll_items_create.php',
            'maintainers/hr-payroll-items/edit' => 'maintainers_hr_payroll_items_edit.php',
            'maintainers/product-families/create' => 'maintainers_product_families_create.php',
            'maintainers/product-families/edit' => 'maintainers_product_families_edit.php',
            'maintainers/hr-pension-funds/create' => 'maintainers_hr_pension_funds_create.php',
            'maintainers/hr-pension-funds/edit' => 'maintainers_hr_pension_funds_edit.php',
            'maintainers/hr-health-providers/create' => 'maintainers_hr_health_providers_create.php',
            'maintainers/hr-health-providers/edit' => 'maintainers_hr_health_providers_edit.php',
            'maintainers/hr-departments/create' => 'maintainers_hr_departments_create.php',
            'maintainers/hr-departments/edit' => 'maintainers_hr_departments_edit.php',
            'maintainers/product-subfamilies/create' => 'maintainers_product_subfamilies_create.php',
            'maintainers/product-subfamilies/edit' => 'maintainers_product_subfamilies_edit.php',
            'maintainers/hr-positions/create' => 'maintainers_hr_positions_create.php',
            'maintainers/hr-positions/edit' => 'maintainers_hr_positions_edit.php',
            'crm/orders' => 'crm_orders.php',
            'crm/renewals' => 'crm_renewals.php',
            'crm/hub/project' => 'crm_hub_project.php',
            'crm/hub/service' => 'crm_hub_service.php',
        ];

        $reportsDir = __DIR__ . '/../../documento';
        if ($template === '' || !is_dir($reportsDir)) {
            http_response_code(404);
            echo 'Plantilla no encontrada.';
            return;
        }

        if (!isset($templateByRoute[$source]) || $templateByRoute[$source] !== $template) {
            http_response_code(404);
            echo 'Plantilla no encontrada.';
            return;
        }

        $templatePath = $reportsDir . '/' . $template;
        if (!is_file($templatePath)) {
            http_response_code(404);
            echo 'Plantilla no encontrada.';
            return;
        }

        $reportFile = $reportFileByRoute[$source] ?? '';
        $reportsPath = __DIR__ . '/../../informes';
        if ($reportFile === '' || !is_dir($reportsPath)) {
            http_response_code(404);
            echo 'Informe no encontrado.';
            return;
        }

        $reportPath = $reportsPath . '/' . $reportFile;
        if (!is_file($reportPath)) {
            http_response_code(404);
            echo 'Informe no encontrado.';
            return;
        }

        $reportTemplate = $template;
        $reportSource = $source;
        require_once $reportPath;
        return;
    }
}
