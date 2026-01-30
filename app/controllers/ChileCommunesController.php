<?php

class ChileCommunesController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        try {
            $communes = $this->db->fetchAll(
                'SELECT communes.id, communes.name AS commune, cities.name AS city, regions.name AS region
                FROM communes
                JOIN cities ON cities.id = communes.city_id
                JOIN regions ON regions.id = cities.region_id
                ORDER BY communes.name, cities.name'
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
        $cities = $this->loadCities();
        $this->render('maintainers/chile-communes/create', [
            'title' => 'Nueva comuna',
            'pageTitle' => 'Nueva comuna',
            'cities' => $cities,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $commune = trim($_POST['commune'] ?? '');
        $cityId = (int)($_POST['city_id'] ?? 0);
        if ($commune === '' || $cityId === 0) {
            flash('error', 'Completa comuna y ciudad.');
            $this->redirect('index.php?route=maintainers/chile-communes/create');
        }
        $duplicate = $this->db->fetch(
            'SELECT id FROM communes WHERE name = :commune AND city_id = :city_id',
            ['commune' => $commune, 'city_id' => $cityId]
        );
        if ($duplicate) {
            flash('error', 'La comuna ya existe en la ciudad seleccionada.');
            $this->redirect('index.php?route=maintainers/chile-communes/create');
        }
        try {
            $this->db->execute(
                'INSERT INTO communes (name, city_id) VALUES (:commune, :city_id)',
                ['commune' => $commune, 'city_id' => $cityId]
            );
            audit($this->db, Auth::user()['id'], 'create', 'chile_communes');
            flash('success', 'Comuna creada correctamente.');
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
            'SELECT communes.id, communes.name AS commune, communes.city_id, cities.name AS city, regions.name AS region
            FROM communes
            JOIN cities ON cities.id = communes.city_id
            JOIN regions ON regions.id = cities.region_id
            WHERE communes.id = :id',
            ['id' => $id]
        );
        if (!$commune) {
            $this->redirect('index.php?route=maintainers/chile-communes');
        }
        $cities = $this->loadCities();
        $this->render('maintainers/chile-communes/edit', [
            'title' => 'Editar comuna',
            'pageTitle' => 'Editar comuna',
            'commune' => $commune,
            'cities' => $cities,
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
        $cityId = (int)($_POST['city_id'] ?? 0);
        if ($communeName === '' || $cityId === 0) {
            flash('error', 'Completa comuna y ciudad.');
            $this->redirect('index.php?route=maintainers/chile-communes/edit&id=' . $id);
        }
        $duplicate = $this->db->fetch(
            'SELECT id FROM communes WHERE name = :commune AND city_id = :city_id AND id != :id',
            ['commune' => $communeName, 'city_id' => $cityId, 'id' => $id]
        );
        if ($duplicate) {
            flash('error', 'La comuna ya está asignada a otra ciudad.');
            $this->redirect('index.php?route=maintainers/chile-communes/edit&id=' . $id);
        }
        try {
            $this->db->execute(
                'UPDATE communes SET name = :commune, city_id = :city_id WHERE id = :id',
                ['commune' => $communeName, 'city_id' => $cityId, 'id' => $id]
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

    private function loadCities(): array
    {
        try {
            return $this->db->fetchAll(
                'SELECT cities.id, cities.name, regions.name AS region
                FROM cities
                JOIN regions ON regions.id = cities.region_id
                ORDER BY regions.name, cities.name'
            );
        } catch (Throwable $e) {
            log_message('error', 'Failed to load Chile cities: ' . $e->getMessage());
            return [];
        }
    }
}
