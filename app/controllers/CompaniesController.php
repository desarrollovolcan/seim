<?php

class CompaniesController extends Controller
{
    private CompaniesModel $companies;
    private SettingsModel $settings;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->companies = new CompaniesModel($db);
        $this->settings = new SettingsModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $this->requirePermission('companies');
        $companies = $this->companies->active();
        $this->render('companies/index', [
            'title' => 'Empresas',
            'pageTitle' => 'Empresas',
            'companies' => $companies,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requirePermission('companies/create');
        $this->render('companies/create', [
            'title' => 'Nueva Empresa',
            'pageTitle' => 'Nueva Empresa',
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->requirePermission('companies/store');
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=companies/create');
        }
        $data = [
            'name' => $name,
            'rut' => trim($_POST['rut'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'giro' => trim($_POST['giro'] ?? ''),
            'commune' => trim($_POST['commune'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $companyId = $this->companies->create($data);
        audit($this->db, Auth::user()['id'], 'create', 'companies', $companyId);
        flash('success', 'Empresa creada correctamente.');
        $this->redirect('index.php?route=companies');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requirePermission('companies/edit');
        $id = (int)($_GET['id'] ?? 0);
        $company = $this->companies->find($id);
        if (!$company) {
            $this->redirect('index.php?route=companies');
        }
        $companySettings = $this->settings->get('company', [], $id);
        $this->render('companies/edit', [
            'title' => 'Editar Empresa',
            'pageTitle' => 'Editar Empresa',
            'company' => $company,
            'companySettings' => is_array($companySettings) ? $companySettings : [],
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requirePermission('companies/edit');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $company = $this->companies->find($id);
        if (!$company) {
            flash('error', 'Empresa no encontrada.');
            $this->redirect('index.php?route=companies');
        }
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=companies/edit&id=' . $id);
        }
        $rut = trim($_POST['rut'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $giro = trim($_POST['giro'] ?? '');
        $commune = trim($_POST['commune'] ?? '');

        $companySettings = $this->settings->get('company', [], $id);
        $companySettings = is_array($companySettings) ? $companySettings : [];

        $logoColorResult = upload_company_logo($_FILES['logo_color'] ?? null, 'logo-color');
        if (!empty($logoColorResult['error'])) {
            flash('error', $logoColorResult['error']);
            $this->redirect('index.php?route=companies/edit&id=' . $id);
        }
        $logoBlackResult = upload_company_logo($_FILES['logo_black'] ?? null, 'logo-black');
        if (!empty($logoBlackResult['error'])) {
            flash('error', $logoBlackResult['error']);
            $this->redirect('index.php?route=companies/edit&id=' . $id);
        }

        $companySettingsData = array_merge($companySettings, [
            'name' => $name,
            'rut' => $rut,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'giro' => $giro,
            'commune' => $commune,
            'logo_color' => $companySettings['logo_color'] ?? null,
            'logo_black' => $companySettings['logo_black'] ?? null,
        ]);
        if (!empty($logoColorResult['path'])) {
            $companySettingsData['logo_color'] = $logoColorResult['path'];
        }
        if (!empty($logoBlackResult['path'])) {
            $companySettingsData['logo_black'] = $logoBlackResult['path'];
        }
        $this->companies->update($id, [
            'name' => $name,
            'rut' => $rut,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'giro' => $giro,
            'commune' => $commune,
            'logo_color' => $companySettingsData['logo_color'] ?? null,
            'logo_black' => $companySettingsData['logo_black'] ?? null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->settings->set('company', $companySettingsData, $id);
        audit($this->db, Auth::user()['id'], 'update', 'companies', $id);
        flash('success', 'Empresa actualizada correctamente.');
        $this->redirect('index.php?route=companies');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requirePermission('companies/delete');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $company = $this->companies->find($id);
        if (!$company) {
            flash('error', 'Empresa no encontrada.');
            $this->redirect('index.php?route=companies');
        }
        try {
            $this->db->beginTransaction();
            
            // 1. Usuarios y asignaciones
            $this->db->execute('UPDATE users SET company_id = NULL WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM user_companies WHERE company_id = :id', ['id' => $id]);
            
            // 2. Ventas y POS
            $this->db->execute('DELETE FROM sale_payments WHERE sale_id IN (SELECT id FROM sales WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM sale_items WHERE sale_id IN (SELECT id FROM sales WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM pos_session_withdrawals WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM pos_sessions WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM sales WHERE company_id = :id', ['id' => $id]);
            
            // 3. Facturación
            $this->db->execute('DELETE FROM invoice_items WHERE invoice_id IN (SELECT id FROM invoices WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM invoice_comments WHERE invoice_id IN (SELECT id FROM invoices WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM invoice_sii_logs WHERE invoice_id IN (SELECT id FROM invoices WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM payments WHERE invoice_id IN (SELECT id FROM invoices WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM invoices WHERE company_id = :id', ['id' => $id]);
            
            // 4. Cotizaciones
            $this->db->execute('DELETE FROM quote_items WHERE quote_id IN (SELECT id FROM quotes WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM quote_approvals WHERE quote_id IN (SELECT id FROM quotes WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM quotes WHERE company_id = :id', ['id' => $id]);
            
            // 5. Productos y Catálogos
            $this->db->execute('DELETE FROM produced_product_materials WHERE produced_product_id IN (SELECT id FROM produced_products WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM produced_products WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM products WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM product_subfamilies WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM product_families WHERE company_id = :id', ['id' => $id]);
            
            // 6. Clientes
            $this->db->execute('DELETE FROM client_contacts WHERE client_id IN (SELECT id FROM clients WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM clients WHERE company_id = :id', ['id' => $id]);
            
            // 7. Proveedores y Compras
            $this->db->execute('DELETE FROM purchase_items WHERE purchase_id IN (SELECT id FROM purchases WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM purchases WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM suppliers WHERE company_id = :id', ['id' => $id]);
            
            // 8. Tesorería
            $this->db->execute('DELETE FROM cash_movements WHERE cash_box_id IN (SELECT id FROM cash_boxes WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM cash_boxes WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM bank_movements WHERE bank_account_id IN (SELECT id FROM bank_accounts WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM bank_accounts WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM fixed_assets WHERE company_id = :id', ['id' => $id]);
            
            // 9. Impuestos y RRHH
            $this->db->execute('DELETE FROM tax_withholdings WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM tax_periods WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM hr_payrolls WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM hr_attendance WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM hr_contracts WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM hr_employees WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM hr_departments WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM hr_positions WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM hr_work_schedules WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM hr_contract_types WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM hr_pension_funds WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM hr_health_providers WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM hr_payroll_items WHERE company_id = :id', ['id' => $id]);
            
            // 10. Tickets, Documentos, Notificaciones, Configuración
            $this->db->execute('DELETE FROM ticket_attachments WHERE message_id IN (SELECT id FROM ticket_messages WHERE ticket_id IN (SELECT id FROM support_tickets WHERE company_id = :id))', ['id' => $id]);
            $this->db->execute('DELETE FROM ticket_messages WHERE ticket_id IN (SELECT id FROM support_tickets WHERE company_id = :id)', ['id' => $id]);
            $this->db->execute('DELETE FROM support_tickets WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM documents WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM document_categories WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM calendar_events WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM notifications WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM email_logs WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM email_queue WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM commercial_briefs WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM sales_orders WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM service_renewals WHERE company_id = :id', ['id' => $id]);
            $this->db->execute('DELETE FROM settings WHERE company_id = :id', ['id' => $id]);
            
            // 11. Eliminar la Empresa
            $this->db->execute('DELETE FROM companies WHERE id = :id', ['id' => $id]);
            $this->db->commit();
            
            audit($this->db, Auth::user()['id'], 'delete', 'companies', $id);
            flash('success', 'Empresa y todos sus registros asociados eliminados correctamente.');
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            log_message('error', 'Failed to delete company: ' . $e->getMessage());
            flash('error', 'No se pudo eliminar la empresa.');
        }
        $this->redirect('index.php?route=companies');
    }
}
