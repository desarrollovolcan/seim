<?php

class SearchController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        $term = trim($_GET['q'] ?? '');
        $clients = [];
        $projects = [];
        $services = [];
        $invoices = [];

        if ($term !== '') {
            $like = '%' . $term . '%';
            $companyId = current_company_id();
            $clients = $this->db->fetchAll(
                'SELECT id, name, email FROM clients WHERE deleted_at IS NULL AND company_id = :company_id AND (name LIKE :term OR email LIKE :term)',
                ['term' => $like, 'company_id' => $companyId]
            );
            $projects = $this->db->fetchAll(
                'SELECT id, name, status FROM projects WHERE deleted_at IS NULL AND company_id = :company_id AND name LIKE :term',
                ['term' => $like, 'company_id' => $companyId]
            );
            $services = $this->db->fetchAll(
                'SELECT id, name, service_type FROM services WHERE deleted_at IS NULL AND company_id = :company_id AND name LIKE :term',
                ['term' => $like, 'company_id' => $companyId]
            );
            $invoices = $this->db->fetchAll(
                'SELECT id, numero, estado FROM invoices WHERE deleted_at IS NULL AND company_id = :company_id AND numero LIKE :term',
                ['term' => $like, 'company_id' => $companyId]
            );
        }

        $this->render('search/index', [
            'title' => 'Buscador',
            'pageTitle' => 'Buscador',
            'term' => $term,
            'clients' => $clients,
            'projects' => $projects,
            'services' => $services,
            'invoices' => $invoices,
        ]);
    }
}
