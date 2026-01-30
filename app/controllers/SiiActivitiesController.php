<?php

class SiiActivitiesController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        try {
            $activities = $this->db->fetchAll('SELECT id, code, name FROM sii_activity_codes ORDER BY code');
        } catch (Throwable $e) {
            log_message('error', 'Failed to load SII activities: ' . $e->getMessage());
            $activities = [];
        }
        $this->render('maintainers/sii-activities/index', [
            'title' => 'Actividades SII',
            'pageTitle' => 'Actividades SII',
            'activities' => $activities,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $this->render('maintainers/sii-activities/create', [
            'title' => 'Nueva actividad SII',
            'pageTitle' => 'Nueva actividad SII',
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        if ($code === '' || $name === '') {
            flash('error', 'Completa el código y la actividad.');
            $this->redirect('index.php?route=maintainers/sii-activities/create');
        }
        $duplicate = $this->db->fetch(
            'SELECT id FROM sii_activity_codes WHERE code = :code',
            ['code' => $code]
        );
        if ($duplicate) {
            flash('error', 'El código ya existe en el catálogo.');
            $this->redirect('index.php?route=maintainers/sii-activities/create');
        }
        try {
            $this->db->execute(
                'INSERT INTO sii_activity_codes (code, name) VALUES (:code, :name)',
                ['code' => $code, 'name' => $name]
            );
            audit($this->db, Auth::user()['id'], 'create', 'sii_activity_codes');
            flash('success', 'Actividad creada correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to create SII activity: ' . $e->getMessage());
            flash('error', 'No se pudo guardar la actividad.');
        }
        $this->redirect('index.php?route=maintainers/sii-activities');
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);
        $activity = $this->db->fetch(
            'SELECT id, code, name FROM sii_activity_codes WHERE id = :id',
            ['id' => $id]
        );
        if (!$activity) {
            $this->redirect('index.php?route=maintainers/sii-activities');
        }
        $this->render('maintainers/sii-activities/edit', [
            'title' => 'Editar actividad SII',
            'pageTitle' => 'Editar actividad SII',
            'activity' => $activity,
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $activity = $this->db->fetch(
            'SELECT id FROM sii_activity_codes WHERE id = :id',
            ['id' => $id]
        );
        if (!$activity) {
            $this->redirect('index.php?route=maintainers/sii-activities');
        }
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        if ($code === '' || $name === '') {
            flash('error', 'Completa el código y la actividad.');
            $this->redirect('index.php?route=maintainers/sii-activities/edit&id=' . $id);
        }
        $duplicate = $this->db->fetch(
            'SELECT id FROM sii_activity_codes WHERE code = :code AND id != :id',
            ['code' => $code, 'id' => $id]
        );
        if ($duplicate) {
            flash('error', 'El código ya está asignado a otra actividad.');
            $this->redirect('index.php?route=maintainers/sii-activities/edit&id=' . $id);
        }
        try {
            $this->db->execute(
                'UPDATE sii_activity_codes SET code = :code, name = :name WHERE id = :id',
                ['code' => $code, 'name' => $name, 'id' => $id]
            );
            audit($this->db, Auth::user()['id'], 'update', 'sii_activity_codes', $id);
            flash('success', 'Actividad actualizada correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to update SII activity: ' . $e->getMessage());
            flash('error', 'No se pudo actualizar la actividad.');
        }
        $this->redirect('index.php?route=maintainers/sii-activities');
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $activity = $this->db->fetch(
            'SELECT id FROM sii_activity_codes WHERE id = :id',
            ['id' => $id]
        );
        if (!$activity) {
            flash('error', 'Actividad no encontrada.');
            $this->redirect('index.php?route=maintainers/sii-activities');
        }
        try {
            $this->db->execute('DELETE FROM sii_activity_codes WHERE id = :id', ['id' => $id]);
            audit($this->db, Auth::user()['id'], 'delete', 'sii_activity_codes', $id);
            flash('success', 'Actividad eliminada correctamente.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to delete SII activity: ' . $e->getMessage());
            flash('error', 'No se pudo eliminar la actividad.');
        }
        $this->redirect('index.php?route=maintainers/sii-activities');
    }
}
