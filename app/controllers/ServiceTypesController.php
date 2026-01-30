<?php

class ServiceTypesController extends Controller
{
    private ServiceTypesModel $serviceTypes;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->serviceTypes = new ServiceTypesModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $types = $this->serviceTypes->all('company_id = :company_id', ['company_id' => $companyId]);
        $this->render('maintainers/service-types/index', [
            'title' => 'Tipos de servicios',
            'pageTitle' => 'Tipos de servicios',
            'types' => $types,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $types = $this->serviceTypes->all('company_id = :company_id', ['company_id' => $companyId]);
        $this->render('maintainers/service-types/create', [
            'title' => 'Nuevo tipo de servicio',
            'pageTitle' => 'Nuevo tipo de servicio',
            'types' => $types,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $this->serviceTypes->create([
            'company_id' => $companyId,
            'name' => trim($_POST['name'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'create', 'service_types');
        flash('success', 'Tipo de servicio creado correctamente.');
        $this->redirect('index.php?route=maintainers/service-types');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $id = (int)($_GET['id'] ?? 0);
        $type = $this->db->fetch(
            'SELECT * FROM service_types WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$type) {
            $this->redirect('index.php?route=maintainers/service-types');
        }
        $this->render('maintainers/service-types/edit', [
            'title' => 'Editar tipo de servicio',
            'pageTitle' => 'Editar tipo de servicio',
            'type' => $type,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $id = (int)($_POST['id'] ?? 0);
        $type = $this->db->fetch(
            'SELECT id FROM service_types WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$type) {
            $this->redirect('index.php?route=maintainers/service-types');
        }
        $this->serviceTypes->update($id, [
            'name' => trim($_POST['name'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'update', 'service_types', $id);
        flash('success', 'Tipo de servicio actualizado correctamente.');
        $this->redirect('index.php?route=maintainers/service-types');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }
        $id = (int)($_POST['id'] ?? 0);
        $type = $this->db->fetch(
            'SELECT id FROM service_types WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$type) {
            $this->redirect('index.php?route=maintainers/service-types');
        }
        $linked = $this->db->fetch(
            'SELECT COUNT(*) as total FROM system_services WHERE service_type_id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!empty($linked['total'])) {
            flash('error', 'No se puede eliminar el tipo de servicio porque tiene servicios asociados.');
            $this->redirect('index.php?route=maintainers/service-types');
        }
        $this->db->execute(
            'DELETE FROM service_types WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        audit($this->db, Auth::user()['id'], 'delete', 'service_types', $id);
        flash('success', 'Tipo de servicio eliminado correctamente.');
        $this->redirect('index.php?route=maintainers/service-types');
    }
}
