<?php

class ChileRegionsController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        try {
            $regions = $this->db->fetchAll('SELECT id, name FROM regions ORDER BY name');
        } catch (Throwable $e) {
            log_message('error', 'Failed to load Chile regions: ' . $e->getMessage());
            $regions = [];
        }
        $this->render('maintainers/chile-regions/index', [
            'title' => 'Regiones',
            'pageTitle' => 'Regiones',
            'regions' => $regions,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->render('maintainers/chile-regions/create', [
            'title' => 'Nueva región',
            'pageTitle' => 'Nueva región',
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'Completa el nombre de la región.');
            $this->redirect('index.php?route=maintainers/chile-regions/create');
        }
        $duplicate = $this->db->fetch(
            'SELECT id FROM regions WHERE name = :name',
            ['name' => $name]
        );
        if ($duplicate) {
            flash('error', 'La región ya existe en el listado.');
            $this->redirect('index.php?route=maintainers/chile-regions/create');
        }
        try {
            $this->db->execute(
                'INSERT INTO regions (name) VALUES (:name)',
                ['name' => $name]
            );
            $saved = $this->db->fetch(
                'SELECT id FROM regions WHERE name = :name',
                ['name' => $name]
            );
            if (!$saved) {
                flash('error', 'No se pudo confirmar el guardado de la región.');
            } else {
                audit($this->db, Auth::user()['id'], 'create', 'regions', (int)$saved['id']);
                flash('success', 'Región creada correctamente.');
            }
        } catch (Throwable $e) {
            log_message('error', 'Failed to create Chile region: ' . $e->getMessage());
            flash('error', 'No se pudo guardar la región.');
        }
        $this->redirect('index.php?route=maintainers/chile-regions');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $region = $this->db->fetch(
            'SELECT id, name FROM regions WHERE id = :id',
            ['id' => $id]
        );
        if (!$region) {
            $this->redirect('index.php?route=maintainers/chile-regions');
        }
        $this->render('maintainers/chile-regions/edit', [
            'title' => 'Editar región',
            'pageTitle' => 'Editar región',
            'region' => $region,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $region = $this->db->fetch(
            'SELECT id FROM regions WHERE id = :id',
            ['id' => $id]
        );
        if (!$region) {
            $this->redirect('index.php?route=maintainers/chile-regions');
        }
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            flash('error', 'Completa el nombre de la región.');
            $this->redirect('index.php?route=maintainers/chile-regions/edit&id=' . $id);
        }
        $duplicate = $this->db->fetch(
            'SELECT id FROM regions WHERE name = :name AND id != :id',
            ['name' => $name, 'id' => $id]
        );
        if ($duplicate) {
            flash('error', 'La región ya está asignada a otro registro.');
            $this->redirect('index.php?route=maintainers/chile-regions/edit&id=' . $id);
        }
        try {
            $this->db->execute(
                'UPDATE regions SET name = :name WHERE id = :id',
                ['name' => $name, 'id' => $id]
            );
            audit($this->db, Auth::user()['id'], 'update', 'regions', $id);
            flash('success', 'Región actualizada correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to update Chile region: ' . $e->getMessage());
            flash('error', 'No se pudo actualizar la región.');
        }
        $this->redirect('index.php?route=maintainers/chile-regions');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $region = $this->db->fetch(
            'SELECT id FROM regions WHERE id = :id',
            ['id' => $id]
        );
        if (!$region) {
            flash('error', 'Región no encontrada.');
            $this->redirect('index.php?route=maintainers/chile-regions');
        }
        $hasCommunes = $this->db->fetch(
            'SELECT id FROM communes WHERE region_id = :id LIMIT 1',
            ['id' => $id]
        );
        if ($hasCommunes) {
            flash('error', 'No se puede eliminar la región mientras tenga comunas asociadas.');
            $this->redirect('index.php?route=maintainers/chile-regions');
        }
        try {
            $this->db->execute('DELETE FROM regions WHERE id = :id', ['id' => $id]);
            audit($this->db, Auth::user()['id'], 'delete', 'regions', $id);
            flash('success', 'Región eliminada correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to delete Chile region: ' . $e->getMessage());
            flash('error', 'No se pudo eliminar la región.');
        }
        $this->redirect('index.php?route=maintainers/chile-regions');
    }
}
