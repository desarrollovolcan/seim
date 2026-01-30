<?php

class HonorariosController extends Controller
{
    private HonorariosModel $honorarios;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->honorarios = new HonorariosModel($db);
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
        $documents = $this->honorarios->byCompany($companyId);
        $this->render('honorarios/index', [
            'title' => 'Honorarios',
            'pageTitle' => 'Honorarios',
            'documents' => $documents,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireCompany();
        $this->render('honorarios/create', [
            'title' => 'Registrar honorarios',
            'pageTitle' => 'Registrar honorarios',
            'today' => date('Y-m-d'),
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $gross = (float)($_POST['gross_amount'] ?? 0);
        $rate = (float)($_POST['retention_rate'] ?? 13);
        $retention = $gross * ($rate / 100);
        $net = $gross - $retention;
        $this->honorarios->create([
            'company_id' => $companyId,
            'provider_name' => trim($_POST['provider_name'] ?? ''),
            'provider_rut' => normalize_rut($_POST['provider_rut'] ?? ''),
            'document_number' => trim($_POST['document_number'] ?? ''),
            'issue_date' => trim($_POST['issue_date'] ?? date('Y-m-d')),
            'gross_amount' => $gross,
            'retention_rate' => $rate,
            'retention_amount' => $retention,
            'net_amount' => $net,
            'status' => $_POST['status'] ?? 'pendiente',
            'paid_at' => $_POST['paid_at'] ?? null,
            'notes' => trim($_POST['notes'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Boleta de honorarios registrada.');
        $this->redirect('index.php?route=honorarios');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = $this->requireCompany();
        $id = (int)($_POST['id'] ?? 0);
        $document = $this->db->fetch(
            'SELECT id FROM honorarios_documents WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$document) {
            flash('error', 'Boleta no encontrada.');
            $this->redirect('index.php?route=honorarios');
        }
        try {
            $this->db->execute(
                'DELETE FROM honorarios_documents WHERE id = :id AND company_id = :company_id',
                ['id' => $id, 'company_id' => $companyId]
            );
            audit($this->db, Auth::user()['id'], 'delete', 'honorarios_documents', $id);
            flash('success', 'Boleta eliminada correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to delete honorarios document: ' . $e->getMessage());
            flash('error', 'No se pudo eliminar la boleta.');
        }
        $this->redirect('index.php?route=honorarios');
    }
}
