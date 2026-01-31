<?php

class ChileCommunesController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        try {
            $communes = $this->db->fetchAll(
                'SELECT communes.id, communes.name AS commune, regions.name AS region
                FROM communes
                JOIN regions ON regions.id = communes.region_id
                ORDER BY communes.name'
            );
        } catch (Throwable $e) {
            log_message('error', 'Failed to load Chile communes: ' . $e->getMessage());
            $communes = [];
        }
        $this->render('maintainers/chile-communes/index', [
            'title' => 'Comunas',
            'pageTitle' => 'Comunas',
            'communes' => $communes,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->render('maintainers/chile-communes/create', [
            'title' => 'Nueva comuna',
            'pageTitle' => 'Nueva comuna',
            'regions' => $this->loadRegions(),
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $commune = trim($_POST['commune'] ?? '');
        $regionId = (int)($_POST['region_id'] ?? 0);
        if ($commune === '' || $regionId === 0) {
            flash('error', 'Completa comuna y región.');
            $this->redirect('index.php?route=maintainers/chile-communes/create');
        }
        $duplicate = $this->db->fetch(
            'SELECT id FROM communes WHERE name = :commune AND region_id = :region_id',
            ['commune' => $commune, 'region_id' => $regionId]
        );
        if ($duplicate) {
            flash('error', 'La comuna ya existe en la región seleccionada.');
            $this->redirect('index.php?route=maintainers/chile-communes/create');
        }
        try {
            $this->db->execute(
                'INSERT INTO communes (name, region_id) VALUES (:commune, :region_id)',
                ['commune' => $commune, 'region_id' => $regionId]
            );
            $saved = $this->db->fetch(
                'SELECT id FROM communes WHERE name = :commune AND region_id = :region_id',
                ['commune' => $commune, 'region_id' => $regionId]
            );
            if (!$saved) {
                flash('error', 'No se pudo confirmar el guardado de la comuna.');
            } else {
                audit($this->db, Auth::user()['id'], 'create', 'chile_communes', (int)$saved['id']);
                flash('success', 'Comuna creada correctamente.');
            }
        } catch (Throwable $e) {
            log_message('error', 'Failed to create Chile commune: ' . $e->getMessage());
            flash('error', 'No se pudo guardar la comuna.');
        }
        $this->redirect('index.php?route=maintainers/chile-communes');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $commune = $this->db->fetch(
            'SELECT communes.id, communes.name AS commune, communes.region_id, regions.name AS region
            FROM communes
            JOIN regions ON regions.id = communes.region_id
            WHERE communes.id = :id',
            ['id' => $id]
        );
        if (!$commune) {
            $this->redirect('index.php?route=maintainers/chile-communes');
        }
        $this->render('maintainers/chile-communes/edit', [
            'title' => 'Editar comuna',
            'pageTitle' => 'Editar comuna',
            'commune' => $commune,
            'regions' => $this->loadRegions(),
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $commune = $this->db->fetch(
            'SELECT id FROM communes WHERE id = :id',
            ['id' => $id]
        );
        if (!$commune) {
            $this->redirect('index.php?route=maintainers/chile-communes');
        }
        $communeName = trim($_POST['commune'] ?? '');
        $regionId = (int)($_POST['region_id'] ?? 0);
        if ($communeName === '' || $regionId === 0) {
            flash('error', 'Completa comuna y región.');
            $this->redirect('index.php?route=maintainers/chile-communes/edit&id=' . $id);
        }
        $duplicate = $this->db->fetch(
            'SELECT id FROM communes WHERE name = :commune AND region_id = :region_id AND id != :id',
            ['commune' => $communeName, 'region_id' => $regionId, 'id' => $id]
        );
        if ($duplicate) {
            flash('error', 'La comuna ya está asignada a otra región.');
            $this->redirect('index.php?route=maintainers/chile-communes/edit&id=' . $id);
        }
        try {
            $this->db->execute(
                'UPDATE communes SET name = :commune, region_id = :region_id WHERE id = :id',
                ['commune' => $communeName, 'region_id' => $regionId, 'id' => $id]
            );
            audit($this->db, Auth::user()['id'], 'update', 'chile_communes', $id);
            flash('success', 'Comuna actualizada correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to update Chile commune: ' . $e->getMessage());
            flash('error', 'No se pudo actualizar la comuna.');
        }
        $this->redirect('index.php?route=maintainers/chile-communes');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $commune = $this->db->fetch(
            'SELECT id FROM communes WHERE id = :id',
            ['id' => $id]
        );
        if (!$commune) {
            flash('error', 'Comuna no encontrada.');
            $this->redirect('index.php?route=maintainers/chile-communes');
        }
        try {
            $this->db->execute('DELETE FROM communes WHERE id = :id', ['id' => $id]);
            audit($this->db, Auth::user()['id'], 'delete', 'chile_communes', $id);
            flash('success', 'Comuna eliminada correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to delete Chile commune: ' . $e->getMessage());
            flash('error', 'No se pudo eliminar la comuna.');
        }
        $this->redirect('index.php?route=maintainers/chile-communes');
    }

    private function loadRegions(): array
    {
        try {
            return $this->db->fetchAll('SELECT id, name FROM regions ORDER BY name');
        } catch (Throwable $e) {
            log_message('error', 'Failed to load Chile regions: ' . $e->getMessage());
            return [];
        }
    }
}
