<?php

class ProjectsController extends Controller
{
    private ProjectsModel $projects;
    private ClientsModel $clients;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->projects = new ProjectsModel($db);
        $this->clients = new ClientsModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        $projectColumns = $this->getTableColumns('projects');
        $clientColumns = $this->getTableColumns('clients');
        $hasProjectColumn = static fn (string $column): bool => in_array($column, $projectColumns, true);
        $conditions = [];
        $params = [];
        if ($hasProjectColumn('deleted_at')) {
            $conditions[] = 'projects.deleted_at IS NULL';
        }
        if ($companyId && $hasProjectColumn('company_id')) {
            $conditions[] = 'projects.company_id = :company_id';
            $params['company_id'] = $companyId;
        }
        $clientId = (int)($_GET['client_id'] ?? 0);
        if ($clientId > 0 && $hasProjectColumn('client_id')) {
            $conditions[] = 'projects.client_id = :client_id';
            $params['client_id'] = $clientId;
        }
        $status = trim($_GET['status'] ?? '');
        if ($status !== '' && $hasProjectColumn('status')) {
            $conditions[] = 'projects.status = :status';
            $params['status'] = $status;
        }
        $mandante = trim($_GET['mandante'] ?? '');
        if ($mandante !== '' && $hasProjectColumn('mandante_name')) {
            $conditions[] = 'projects.mandante_name LIKE :mandante';
            $params['mandante'] = '%' . $mandante . '%';
        }
        $name = trim($_GET['name'] ?? '');
        if ($name !== '' && $hasProjectColumn('name')) {
            $conditions[] = 'projects.name LIKE :name';
            $params['name'] = '%' . $name . '%';
        }
        $where = $conditions ? implode(' AND ', $conditions) : '1=1';
        $useClientJoin = $hasProjectColumn('client_id')
            && in_array('id', $clientColumns, true)
            && in_array('name', $clientColumns, true);
        $tasksJoin = 'LEFT JOIN (SELECT project_id, COUNT(*) AS incomplete_tasks FROM project_tasks WHERE completed = 0 GROUP BY project_id) AS task_counts ON task_counts.project_id = projects.id';
        $select = 'SELECT projects.*';
        if ($useClientJoin) {
            $select .= ', clients.name as client_name';
        }
        $select .= ', COALESCE(task_counts.incomplete_tasks, 0) as incomplete_tasks';
        $from = ' FROM projects';
        if ($useClientJoin) {
            $from .= ' LEFT JOIN clients ON projects.client_id = clients.id';
        }
        $from .= ' ' . $tasksJoin;
        $projectsQuery = $select . $from . " WHERE {$where} ORDER BY projects.id DESC";
        try {
            $projects = $this->db->fetchAll($projectsQuery, $params);
        } catch (PDOException $e) {
            log_message('error', 'Failed to load projects list: ' . $e->getMessage());
            $projects = [];
        }
        $tasksByProject = [];
        $projectIds = array_column($projects, 'id');
        if (!empty($projectIds)) {
            $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
            try {
                $tasks = $this->db->fetchAll(
                    "SELECT project_id, title, progress_percent, start_date, end_date FROM project_tasks WHERE project_id IN ({$placeholders}) ORDER BY project_id ASC, id ASC",
                    $projectIds
                );
                foreach ($tasks as $task) {
                    $tasksByProject[$task['project_id']][] = $task;
                }
            } catch (PDOException $e) {
                log_message('error', 'Failed to load tasks for projects list: ' . $e->getMessage());
            }
        }
        foreach ($projects as &$project) {
            if (!array_key_exists('client_name', $project)) {
                $project['client_name'] = '-';
            }
        }
        unset($project);
        $clients = [];
        if (!empty($clientColumns)) {
            $clientConditions = [];
            $clientParams = [];
            if (in_array('deleted_at', $clientColumns, true)) {
                $clientConditions[] = 'deleted_at IS NULL';
            }
            if ($companyId && in_array('company_id', $clientColumns, true)) {
                $clientConditions[] = 'company_id = :company_id';
                $clientParams['company_id'] = $companyId;
            }
            $clientWhere = $clientConditions ? implode(' AND ', $clientConditions) : '1=1';
            try {
                $clients = $this->db->fetchAll(
                    "SELECT * FROM clients WHERE {$clientWhere} ORDER BY id DESC",
                    $clientParams
                );
            } catch (PDOException $e) {
                log_message('error', 'Failed to load clients list for projects: ' . $e->getMessage());
                $clients = [];
            }
        }
        $this->render('projects/index', [
            'title' => 'Proyectos',
            'pageTitle' => 'Proyectos',
            'projects' => $projects,
            'clients' => $clients,
            'tasksByProject' => $tasksByProject,
            'filters' => [
                'client_id' => $clientId,
                'status' => $status,
                'mandante' => $mandante,
                'name' => $name,
            ],
        ]);
    }

    private function getTableColumns(string $table): array
    {
        try {
            $rows = $this->db->fetchAll("SHOW COLUMNS FROM {$table}");
        } catch (PDOException $e) {
            log_message('error', 'Failed to inspect table ' . $table . ': ' . $e->getMessage());
            return [];
        }
        $columns = [];
        foreach ($rows as $row) {
            if (isset($row['Field'])) {
                $columns[] = $row['Field'];
            }
        }
        return $columns;
    }

    public function create(): void
    {
        $this->requireLogin();
        $clients = $this->clients->active(current_company_id());
        $selectedClientId = (int)($_GET['client_id'] ?? 0);
        $this->render('projects/create', [
            'title' => 'Nuevo Proyecto',
            'pageTitle' => 'Nuevo Proyecto',
            'clients' => $clients,
            'selectedClientId' => $selectedClientId,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $startDate = trim($_POST['start_date'] ?? '');
        $deliveryDate = trim($_POST['delivery_date'] ?? '');
        $value = trim($_POST['value'] ?? '');
        $companyId = current_company_id();
        $clientId = (int)($_POST['client_id'] ?? 0);
        $client = $this->db->fetch(
            'SELECT id FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $clientId, 'company_id' => $companyId]
        );
        if (!$client) {
            flash('error', 'Cliente no encontrado para esta empresa.');
            $this->redirect('index.php?route=projects/create');
        }
        $data = [
            'company_id' => $companyId,
            'client_id' => $clientId,
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status' => $_POST['status'] ?? 'cotizado',
            'start_date' => $startDate !== '' ? $startDate : null,
            'delivery_date' => $deliveryDate !== '' ? $deliveryDate : null,
            'value' => $value !== '' ? $value : null,
            'mandante_name' => trim($_POST['mandante_name'] ?? ''),
            'mandante_rut' => trim($_POST['mandante_rut'] ?? ''),
            'mandante_phone' => trim($_POST['mandante_phone'] ?? ''),
            'mandante_email' => trim($_POST['mandante_email'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $projectId = $this->projects->create($data);
        create_notification(
            $this->db,
            $companyId,
            'Nuevo proyecto',
            'Se creó el proyecto "' . $data['name'] . '".',
            'success'
        );
        audit($this->db, Auth::user()['id'], 'create', 'projects');
        flash('success', 'Proyecto creado correctamente.');
        $this->redirect('index.php?route=projects');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $project = $this->db->fetch(
            'SELECT * FROM projects WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => current_company_id()]
        );
        if (!$project) {
            $this->redirect('index.php?route=projects');
        }
        $clients = $this->clients->active(current_company_id());
        $this->render('projects/edit', [
            'title' => 'Editar Proyecto',
            'pageTitle' => 'Editar Proyecto',
            'project' => $project,
            'clients' => $clients,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $project = $this->db->fetch(
            'SELECT id FROM projects WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => current_company_id()]
        );
        if (!$project) {
            flash('error', 'Proyecto no encontrado para esta empresa.');
            $this->redirect('index.php?route=projects');
        }
        $startDate = trim($_POST['start_date'] ?? '');
        $deliveryDate = trim($_POST['delivery_date'] ?? '');
        $value = trim($_POST['value'] ?? '');
        $data = [
            'client_id' => (int)($_POST['client_id'] ?? 0),
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status' => $_POST['status'] ?? 'cotizado',
            'start_date' => $startDate !== '' ? $startDate : null,
            'delivery_date' => $deliveryDate !== '' ? $deliveryDate : null,
            'value' => $value !== '' ? $value : null,
            'mandante_name' => trim($_POST['mandante_name'] ?? ''),
            'mandante_rut' => trim($_POST['mandante_rut'] ?? ''),
            'mandante_phone' => trim($_POST['mandante_phone'] ?? ''),
            'mandante_email' => trim($_POST['mandante_email'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->projects->update($id, $data);
        audit($this->db, Auth::user()['id'], 'update', 'projects', $id);
        flash('success', 'Proyecto actualizado correctamente.');
        $this->redirect('index.php?route=projects');
    }

    public function show(): void
    {
        $this->requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $companyId = current_company_id();
        $project = $this->db->fetch(
            'SELECT * FROM projects WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => $companyId]
        );
        if (!$project) {
            $this->redirect('index.php?route=projects');
        }
        $checklist = $this->db->fetchAll('SELECT * FROM project_tasks WHERE project_id = :id ORDER BY id ASC', ['id' => $id]);
        $client = $this->db->fetch(
            'SELECT * FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $project['client_id'], 'company_id' => $companyId]
        );
        $invoices = $this->db->fetchAll(
            'SELECT * FROM invoices WHERE project_id = :id AND deleted_at IS NULL AND company_id = :company_id ORDER BY id DESC',
            ['id' => $id, 'company_id' => $companyId]
        );
        $quotes = $this->db->fetchAll(
            'SELECT * FROM quotes WHERE project_id = :id AND company_id = :company_id ORDER BY id DESC',
            ['id' => $id, 'company_id' => $companyId]
        );
        $services = $this->db->fetchAll(
            'SELECT * FROM services WHERE client_id = :id AND deleted_at IS NULL AND company_id = :company_id ORDER BY id DESC',
            ['id' => $project['client_id'], 'company_id' => $companyId]
        );
        $tickets = $this->db->fetchAll(
            'SELECT * FROM support_tickets WHERE client_id = :id AND company_id = :company_id ORDER BY id DESC',
            ['id' => $project['client_id'], 'company_id' => $companyId]
        );
        $this->render('projects/show', [
            'title' => 'Detalle Proyecto',
            'pageTitle' => 'Detalle Proyecto',
            'project' => $project,
            'checklist' => $checklist,
            'client' => $client,
            'invoices' => $invoices,
            'quotes' => $quotes,
            'services' => $services,
            'tickets' => $tickets,
        ]);
    }

    public function storeTask(): void
    {
        $this->requireLogin();
        verify_csrf();
        $projectId = (int)($_POST['project_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $progressPercent = (int)($_POST['progress_percent'] ?? 0);
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $progressPercent = max(0, min(100, $progressPercent));
        if ($projectId <= 0 || $title === '') {
            $this->redirect('index.php?route=projects/show&id=' . $projectId);
        }
        $this->db->execute(
            'INSERT INTO project_tasks (project_id, title, start_date, end_date, progress_percent, completed, created_at, updated_at) VALUES (:project_id, :title, :start_date, :end_date, :progress_percent, :completed, :created_at, :updated_at)',
            [
                'project_id' => $projectId,
                'title' => $title,
                'start_date' => $startDate !== '' ? $startDate : null,
                'end_date' => $endDate !== '' ? $endDate : null,
                'progress_percent' => $progressPercent,
                'completed' => $progressPercent >= 100 ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        flash('success', 'Tarea agregada al proyecto.');
        $this->redirect('index.php?route=projects/show&id=' . $projectId);
    }

    public function updateTask(): void
    {
        $this->requireLogin();
        verify_csrf();
        $taskId = (int)($_POST['task_id'] ?? 0);
        $projectId = (int)($_POST['project_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $progressPercent = (int)($_POST['progress_percent'] ?? 0);
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $progressPercent = max(0, min(100, $progressPercent));
        if ($taskId <= 0 || $projectId <= 0 || $title === '') {
            $this->redirect('index.php?route=projects/show&id=' . $projectId);
        }
        $this->db->execute(
            'UPDATE project_tasks SET title = :title, start_date = :start_date, end_date = :end_date, progress_percent = :progress_percent, completed = :completed, updated_at = :updated_at WHERE id = :id AND project_id = :project_id',
            [
                'id' => $taskId,
                'project_id' => $projectId,
                'title' => $title,
                'start_date' => $startDate !== '' ? $startDate : null,
                'end_date' => $endDate !== '' ? $endDate : null,
                'progress_percent' => $progressPercent,
                'completed' => $progressPercent >= 100 ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        flash('success', 'Tarea actualizada correctamente.');
        $this->redirect('index.php?route=projects/show&id=' . $projectId);
    }

    public function deleteTask(): void
    {
        $this->requireLogin();
        verify_csrf();
        $taskId = (int)($_POST['task_id'] ?? 0);
        $projectId = (int)($_POST['project_id'] ?? 0);
        if ($taskId > 0 && $projectId > 0) {
            $this->db->execute('DELETE FROM project_tasks WHERE id = :id AND project_id = :project_id', [
                'id' => $taskId,
                'project_id' => $projectId,
            ]);
            flash('success', 'Tarea eliminada correctamente.');
        }
        $this->redirect('index.php?route=projects/show&id=' . $projectId);
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $companyId = current_company_id();
        $project = $this->db->fetch(
            'SELECT id FROM projects WHERE id = :id AND deleted_at IS NULL' . ($companyId ? ' AND company_id = :company_id' : ''),
            $companyId ? ['id' => $id, 'company_id' => $companyId] : ['id' => $id]
        );
        if (!$project) {
            flash('error', 'Proyecto no encontrado.');
            $this->redirect('index.php?route=projects');
        }
        $taskCount = $this->db->fetch('SELECT COUNT(*) as total FROM project_tasks WHERE project_id = :id', ['id' => $id]);
        $invoiceCount = $this->db->fetch('SELECT COUNT(*) as total FROM invoices WHERE project_id = :id AND deleted_at IS NULL', ['id' => $id]);
        $quoteCount = $this->db->fetch('SELECT COUNT(*) as total FROM quotes WHERE project_id = :id', ['id' => $id]);
        $blocked = [];
        if (!empty($taskCount['total'])) {
            $blocked[] = 'tareas';
        }
        if (!empty($invoiceCount['total'])) {
            $blocked[] = 'facturas';
        }
        if (!empty($quoteCount['total'])) {
            $blocked[] = 'cotizaciones';
        }
        if (!empty($blocked)) {
            flash('error', 'No se puede eliminar el proyecto porque tiene registros asociados: ' . implode(', ', $blocked) . '.');
            $this->redirect('index.php?route=projects');
        }
        $this->projects->softDelete($id);
        audit($this->db, Auth::user()['id'], 'delete', 'projects', $id);
        flash('success', 'Proyecto eliminado correctamente.');
        $this->redirect('index.php?route=projects');
    }
}
