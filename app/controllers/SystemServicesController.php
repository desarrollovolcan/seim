<?php

class SystemServicesController extends Controller
{
    private SystemServicesModel $services;
    private ServiceTypesModel $serviceTypes;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->services = new SystemServicesModel($db);
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
        $services = $this->services->allWithType($companyId);
        $this->render('maintainers/services/index', [
            'title' => 'Servicios',
            'pageTitle' => 'Servicios',
            'services' => $services,
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
        $services = $this->services->allWithType($companyId);
        $this->render('maintainers/services/create', [
            'title' => 'Nuevo servicio',
            'pageTitle' => 'Nuevo servicio',
            'types' => $types,
            'services' => $services,
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
        $serviceTypeId = (int)($_POST['service_type_id'] ?? 0);
        $serviceType = $this->db->fetch(
            'SELECT id FROM service_types WHERE id = :id AND company_id = :company_id',
            ['id' => $serviceTypeId, 'company_id' => $companyId]
        );
        if (!$serviceType) {
            flash('error', 'Tipo de servicio no encontrado para esta empresa.');
            $this->redirect('index.php?route=maintainers/services/create');
        }
        $this->services->create([
            'company_id' => $companyId,
            'service_type_id' => $serviceTypeId,
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'cost' => (float)($_POST['cost'] ?? 0),
            'currency' => trim($_POST['currency'] ?? 'CLP'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'create', 'system_services');
        flash('success', 'Servicio creado correctamente.');
        $this->redirect('index.php?route=maintainers/services');
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
        $service = $this->db->fetch(
            'SELECT * FROM system_services WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$service) {
            $this->redirect('index.php?route=maintainers/services');
        }
        $types = $this->serviceTypes->all('company_id = :company_id', ['company_id' => $companyId]);
        $this->render('maintainers/services/edit', [
            'title' => 'Editar servicio',
            'pageTitle' => 'Editar servicio',
            'service' => $service,
            'types' => $types,
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
        $service = $this->db->fetch(
            'SELECT id FROM system_services WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$service) {
            $this->redirect('index.php?route=maintainers/services');
        }
        $serviceTypeId = (int)($_POST['service_type_id'] ?? 0);
        $serviceType = $this->db->fetch(
            'SELECT id FROM service_types WHERE id = :id AND company_id = :company_id',
            ['id' => $serviceTypeId, 'company_id' => $companyId]
        );
        if (!$serviceType) {
            flash('error', 'Tipo de servicio no encontrado para esta empresa.');
            $this->redirect('index.php?route=maintainers/services/edit&id=' . $id);
        }
        $this->services->update($id, [
            'service_type_id' => $serviceTypeId,
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'cost' => (float)($_POST['cost'] ?? 0),
            'currency' => trim($_POST['currency'] ?? 'CLP'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'update', 'system_services', $id);
        flash('success', 'Servicio actualizado correctamente.');
        $this->redirect('index.php?route=maintainers/services');
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
        $service = $this->db->fetch(
            'SELECT id FROM system_services WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$service) {
            $this->redirect('index.php?route=maintainers/services');
        }
        $linked = $this->db->fetch(
            'SELECT COUNT(*) as total FROM quotes WHERE system_service_id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!empty($linked['total'])) {
            flash('error', 'No se puede eliminar el servicio porque tiene cotizaciones asociadas.');
            $this->redirect('index.php?route=maintainers/services');
        }
        $this->db->execute(
            'DELETE FROM system_services WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        audit($this->db, Auth::user()['id'], 'delete', 'system_services', $id);
        flash('success', 'Servicio eliminado correctamente.');
        $this->redirect('index.php?route=maintainers/services');
    }
}
