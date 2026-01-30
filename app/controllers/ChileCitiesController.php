<?php

class ChileCitiesController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        try {
            $cities = $this->db->fetchAll(
                'SELECT cities.id, cities.name, regions.name AS region
                FROM cities
                JOIN regions ON regions.id = cities.region_id
                ORDER BY regions.name, cities.name'
            );
        } catch (Throwable $e) {
            log_message('error', 'Failed to load Chile cities: ' . $e->getMessage());
            $cities = [];
        }
        $this->render('maintainers/chile-cities/index', [
            'title' => 'Ciudades',
            'pageTitle' => 'Ciudades',
            'cities' => $cities,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $regions = $this->loadRegions();
        $this->render('maintainers/chile-cities/create', [
            'title' => 'Nueva ciudad',
            'pageTitle' => 'Nueva ciudad',
            'regions' => $regions,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        $regionId = (int)($_POST['region_id'] ?? 0);
        if ($name === '' || $regionId === 0) {
            flash('error', 'Completa ciudad y región.');
            $this->redirect('index.php?route=maintainers/chile-cities/create');
        }
        $duplicate = $this->db->fetch(
            'SELECT id FROM cities WHERE name = :name AND region_id = :region_id',
            ['name' => $name, 'region_id' => $regionId]
        );
        if ($duplicate) {
            flash('error', 'La ciudad ya existe en la región seleccionada.');
            $this->redirect('index.php?route=maintainers/chile-cities/create');
        }
        try {
            $this->db->execute(
                'INSERT INTO cities (name, region_id) VALUES (:name, :region_id)',
                ['name' => $name, 'region_id' => $regionId]
            );
            audit($this->db, Auth::user()['id'], 'create', 'cities');
            flash('success', 'Ciudad creada correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to create Chile city: ' . $e->getMessage());
            flash('error', 'No se pudo guardar la ciudad.');
        }
        $this->redirect('index.php?route=maintainers/chile-cities');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);
        $city = $this->db->fetch(
            'SELECT id, name, region_id FROM cities WHERE id = :id',
            ['id' => $id]
        );
        if (!$city) {
            $this->redirect('index.php?route=maintainers/chile-cities');
        }
        $regions = $this->loadRegions();
        $this->render('maintainers/chile-cities/edit', [
            'title' => 'Editar ciudad',
            'pageTitle' => 'Editar ciudad',
            'city' => $city,
            'regions' => $regions,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $city = $this->db->fetch(
            'SELECT id FROM cities WHERE id = :id',
            ['id' => $id]
        );
        if (!$city) {
            $this->redirect('index.php?route=maintainers/chile-cities');
        }
        $name = trim($_POST['name'] ?? '');
        $regionId = (int)($_POST['region_id'] ?? 0);
        if ($name === '' || $regionId === 0) {
            flash('error', 'Completa ciudad y región.');
            $this->redirect('index.php?route=maintainers/chile-cities/edit&id=' . $id);
        }
        $duplicate = $this->db->fetch(
            'SELECT id FROM cities WHERE name = :name AND region_id = :region_id AND id != :id',
            ['name' => $name, 'region_id' => $regionId, 'id' => $id]
        );
        if ($duplicate) {
            flash('error', 'La ciudad ya está asignada a otra región.');
            $this->redirect('index.php?route=maintainers/chile-cities/edit&id=' . $id);
        }
        try {
            $this->db->execute(
                'UPDATE cities SET name = :name, region_id = :region_id WHERE id = :id',
                ['name' => $name, 'region_id' => $regionId, 'id' => $id]
            );
            audit($this->db, Auth::user()['id'], 'update', 'cities', $id);
            flash('success', 'Ciudad actualizada correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to update Chile city: ' . $e->getMessage());
            flash('error', 'No se pudo actualizar la ciudad.');
        }
        $this->redirect('index.php?route=maintainers/chile-cities');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $city = $this->db->fetch(
            'SELECT id FROM cities WHERE id = :id',
            ['id' => $id]
        );
        if (!$city) {
            flash('error', 'Ciudad no encontrada.');
            $this->redirect('index.php?route=maintainers/chile-cities');
        }
        $hasCommunes = $this->db->fetch(
            'SELECT id FROM communes WHERE city_id = :id LIMIT 1',
            ['id' => $id]
        );
        if ($hasCommunes) {
            flash('error', 'No se puede eliminar la ciudad mientras tenga comunas asociadas.');
            $this->redirect('index.php?route=maintainers/chile-cities');
        }
        try {
            $this->db->execute('DELETE FROM cities WHERE id = :id', ['id' => $id]);
            audit($this->db, Auth::user()['id'], 'delete', 'cities', $id);
            flash('success', 'Ciudad eliminada correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to delete Chile city: ' . $e->getMessage());
            flash('error', 'No se pudo eliminar la ciudad.');
        }
        $this->redirect('index.php?route=maintainers/chile-cities');
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
