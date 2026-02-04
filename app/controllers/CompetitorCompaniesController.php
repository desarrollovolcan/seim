<?php

class CompetitorCompaniesController extends Controller
{
    private CompetitorCompaniesModel $competitors;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->competitors = new CompetitorCompaniesModel($db);
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

        $this->render('maintainers/competitor-companies/index', [
            'title' => 'Empresas competencia',
            'pageTitle' => 'Empresas competencia',
            'competitors' => $this->competitors->active($companyId),
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireCompany();

        $this->render('maintainers/competitor-companies/create', [
            'title' => 'Nueva empresa competencia',
            'pageTitle' => 'Nueva empresa competencia',
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();

        $name = trim($_POST['name'] ?? '');
        $code = strtoupper(trim($_POST['code'] ?? ''));

        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/competitor-companies/create');
        }

        if ($code === '') {
            flash('error', 'El código es obligatorio.');
            $this->redirect('index.php?route=maintainers/competitor-companies/create');
        }

        $this->competitors->create([
            'company_id' => $companyId,
            'name' => $name,
            'code' => $code,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'create', 'competitor_companies');
        flash('success', 'Empresa competencia creada correctamente.');
        $this->redirect('index.php?route=maintainers/competitor-companies');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $companyId = $this->requireCompany();
        $id = (int)($_GET['id'] ?? 0);
        $competitor = $this->competitors->findForCompany($id, $companyId);
        if (!$competitor) {
            $this->redirect('index.php?route=maintainers/competitor-companies');
        }

        $this->render('maintainers/competitor-companies/edit', [
            'title' => 'Editar empresa competencia',
            'pageTitle' => 'Editar empresa competencia',
            'competitor' => $competitor,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $competitor = $this->competitors->findForCompany($id, $companyId);
        if (!$competitor) {
            $this->redirect('index.php?route=maintainers/competitor-companies');
        }

        $name = trim($_POST['name'] ?? '');
        $code = strtoupper(trim($_POST['code'] ?? ''));

        if ($name === '') {
            flash('error', 'El nombre es obligatorio.');
            $this->redirect('index.php?route=maintainers/competitor-companies/edit&id=' . $id);
        }

        if ($code === '') {
            flash('error', 'El código es obligatorio.');
            $this->redirect('index.php?route=maintainers/competitor-companies/edit&id=' . $id);
        }

        $this->competitors->update($id, [
            'name' => $name,
            'code' => $code,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit($this->db, Auth::user()['id'], 'update', 'competitor_companies', $id);
        flash('success', 'Empresa competencia actualizada correctamente.');
        $this->redirect('index.php?route=maintainers/competitor-companies');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $competitor = $this->competitors->findForCompany($id, $companyId);
        if (!$competitor) {
            $this->redirect('index.php?route=maintainers/competitor-companies');
        }

        $this->db->execute('DELETE FROM competitor_companies WHERE id = :id AND company_id = :company_id', [
            'id' => $id,
            'company_id' => $companyId,
        ]);
        audit($this->db, Auth::user()['id'], 'delete', 'competitor_companies', $id);
        flash('success', 'Empresa competencia eliminada correctamente.');
        $this->redirect('index.php?route=maintainers/competitor-companies');
    }
}
