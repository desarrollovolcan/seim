<?php

class FormAuditController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        if (!current_company_id()) {
            flash('error', 'Selecciona una empresa.');
            $this->redirect('index.php?route=auth/switch-company');
        }

        $auditPath = __DIR__ . '/../FORM_AUDIT.md';
        $rows = [];
        if (is_readable($auditPath)) {
            $lines = file($auditPath, FILE_IGNORE_NEW_LINES) ?: [];
            foreach ($lines as $line) {
                if (!str_starts_with(trim($line), '|')) {
                    continue;
                }
                $parts = array_map('trim', explode('|', trim($line, '|')));
                if (count($parts) !== 3 || $parts[0] === 'Módulo') {
                    continue;
                }
                $rows[] = [
                    'module' => $parts[0],
                    'file' => trim($parts[1], '`'),
                    'status' => $parts[2],
                ];
            }
        }

        $statusCounts = [];
        foreach ($rows as $row) {
            $statusCounts[$row['status']] = ($statusCounts[$row['status']] ?? 0) + 1;
        }
        ksort($statusCounts);

        $this->render('maintainers/form-audit', [
            'title' => 'Auditoría de formularios',
            'pageTitle' => 'Auditoría de formularios',
            'rows' => $rows,
            'statusCounts' => $statusCounts,
            'auditMissing' => empty($rows),
        ]);
    }
}
