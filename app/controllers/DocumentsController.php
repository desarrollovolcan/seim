<?php

class DocumentsController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para ver documentos.');
            $this->redirect('index.php?route=dashboard');
        }
        $filter = (string)($_GET['filter'] ?? 'all');
        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $user = Auth::user();
        $userId = isset($user['id']) ? (int)$user['id'] : null;
        $documents = $this->listDocuments((int)$companyId, $filter, $categoryId, $userId);
        if (!empty($documents)) {
            $documents = $this->attachSharesToDocuments($documents, (int)$companyId);
        }
        $categories = $this->listCategories((int)$companyId);
        $counts = $this->documentCounts((int)$companyId, $userId);
        $usersModel = new UsersModel($this->db);
        $users = $usersModel->allActive((int)$companyId);
        $this->render('documents/index', [
            'title' => 'Documentos',
            'pageTitle' => 'Documentos',
            'documents' => $documents,
            'categories' => $categories,
            'counts' => $counts,
            'activeFilter' => $filter,
            'activeCategoryId' => $categoryId,
            'users' => $users,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para cargar documentos.');
            $this->redirect('index.php?route=dashboard');
        }
        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        if ($categoryId && !$this->categoryExists((int)$companyId, $categoryId)) {
            $categoryId = null;
        }
        $files = $this->normalizeFiles($_FILES['documents'] ?? $_FILES['document'] ?? null);
        if (empty($files)) {
            flash('error', 'Selecciona uno o más archivos para subir.');
            $this->redirect('index.php?route=documents');
        }
        $directory = $this->documentsDirectory((int)$companyId);
        $directoryError = ensure_upload_directory($directory);
        if ($directoryError !== null) {
            flash('error', $directoryError);
            $this->redirect('index.php?route=documents');
        }
        $uploaded = 0;
        $errors = [];
        foreach ($files as $file) {
            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                $errors[] = 'No pudimos cargar uno de los archivos.';
                continue;
            }
            if (($file['size'] ?? 0) > 10 * 1024 * 1024) {
                $errors[] = 'Uno de los archivos supera el tamaño máximo de 10MB.';
                continue;
            }
            $originalName = basename((string)($file['name'] ?? 'documento'));
            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
            $uniquePrefix = date('YmdHis') . '-' . bin2hex(random_bytes(4));
            $storedName = $uniquePrefix . '__' . $safeName;
            $destination = $directory . '/' . $storedName;
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                $errors[] = 'No pudimos guardar uno de los archivos en el servidor.';
                continue;
            }
            $mime = $this->guessMime($destination);
            $size = filesize($destination) ?: 0;
            $this->db->execute(
                'INSERT INTO documents (company_id, category_id, filename, original_name, mime_type, file_size, created_at, updated_at)
                 VALUES (:company_id, :category_id, :filename, :original_name, :mime_type, :file_size, NOW(), NOW())',
                [
                    'company_id' => (int)$companyId,
                    'category_id' => $categoryId,
                    'filename' => $storedName,
                    'original_name' => $originalName,
                    'mime_type' => $mime,
                    'file_size' => $size,
                ]
            );
            $uploaded++;
        }
        if ($uploaded > 0) {
            flash('success', 'Documentos cargados correctamente.');
        }
        if (!empty($errors)) {
            flash('warning', implode(' ', array_unique($errors)));
        }
        if ($uploaded === 0) {
            flash('error', 'No pudimos cargar los documentos.');
        }
        $this->redirect('index.php?route=documents');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para eliminar documentos.');
            $this->redirect('index.php?route=dashboard');
        }
        $documentId = (int)($_POST['id'] ?? 0);
        if ($documentId <= 0) {
            flash('error', 'Documento no encontrado.');
            $this->redirect('index.php?route=documents');
        }
        $document = $this->db->fetch(
            'SELECT id, filename, deleted_at FROM documents WHERE id = :id AND company_id = :company_id',
            ['id' => $documentId, 'company_id' => (int)$companyId]
        );
        if (!$document) {
            flash('error', 'Documento no encontrado.');
            $this->redirect('index.php?route=documents');
        }
        $this->db->execute(
            'UPDATE documents SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id AND company_id = :company_id',
            ['id' => $documentId, 'company_id' => (int)$companyId]
        );
        flash('success', 'Documento enviado a la papelera.');
        $this->redirect('index.php?route=documents');
    }

    public function restore(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para restaurar documentos.');
            $this->redirect('index.php?route=dashboard');
        }
        $documentId = (int)($_POST['id'] ?? 0);
        if ($documentId <= 0) {
            flash('error', 'Documento no encontrado.');
            $this->redirect('index.php?route=documents&filter=trash');
        }
        $this->db->execute(
            'UPDATE documents SET deleted_at = NULL, updated_at = NOW() WHERE id = :id AND company_id = :company_id',
            ['id' => $documentId, 'company_id' => (int)$companyId]
        );
        flash('success', 'Documento restaurado.');
        $this->redirect('index.php?route=documents&filter=trash');
    }

    public function purge(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para eliminar documentos.');
            $this->redirect('index.php?route=dashboard');
        }
        $documentId = (int)($_POST['id'] ?? 0);
        if ($documentId <= 0) {
            flash('error', 'Documento no encontrado.');
            $this->redirect('index.php?route=documents&filter=trash');
        }
        $document = $this->db->fetch(
            'SELECT id, filename FROM documents WHERE id = :id AND company_id = :company_id',
            ['id' => $documentId, 'company_id' => (int)$companyId]
        );
        if (!$document) {
            flash('error', 'Documento no encontrado.');
            $this->redirect('index.php?route=documents&filter=trash');
        }
        $directory = $this->documentsDirectory((int)$companyId);
        $target = $this->safeDocumentPath($directory, (string)$document['filename']);
        if ($target !== null && is_file($target) && !unlink($target)) {
            flash('error', 'No pudimos eliminar el documento.');
            $this->redirect('index.php?route=documents&filter=trash');
        }
        $this->db->execute(
            'DELETE FROM documents WHERE id = :id AND company_id = :company_id',
            ['id' => $documentId, 'company_id' => (int)$companyId]
        );
        flash('success', 'Documento eliminado definitivamente.');
        $this->redirect('index.php?route=documents&filter=trash');
    }

    public function toggleFavorite(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para actualizar favoritos.');
            $this->redirect('index.php?route=dashboard');
        }
        $documentId = (int)($_POST['id'] ?? 0);
        if ($documentId <= 0) {
            flash('error', 'Documento no encontrado.');
            $this->redirect('index.php?route=documents');
        }
        $document = $this->db->fetch(
            'SELECT id, is_favorite FROM documents WHERE id = :id AND company_id = :company_id',
            ['id' => $documentId, 'company_id' => (int)$companyId]
        );
        if (!$document) {
            flash('error', 'Documento no encontrado.');
            $this->redirect('index.php?route=documents');
        }
        $newValue = ((int)($document['is_favorite'] ?? 0)) === 1 ? 0 : 1;
        $this->db->execute(
            'UPDATE documents SET is_favorite = :favorite, updated_at = NOW() WHERE id = :id AND company_id = :company_id',
            ['favorite' => $newValue, 'id' => $documentId, 'company_id' => (int)$companyId]
        );
        flash('success', $newValue ? 'Documento marcado como favorito.' : 'Documento removido de favoritos.');
        $this->redirect($this->documentsRedirectUrl());
    }

    public function share(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        $user = Auth::user();
        $userId = isset($user['id']) ? (int)$user['id'] : null;
        if (!$companyId || !$userId) {
            flash('error', 'Selecciona una empresa para compartir documentos.');
            $this->redirect('index.php?route=dashboard');
        }
        $documentId = (int)($_POST['id'] ?? 0);
        $shareUserId = (int)($_POST['user_id'] ?? 0);
        if ($documentId <= 0 || $shareUserId <= 0) {
            flash('error', 'Selecciona un usuario para compartir.');
            $this->redirect($this->documentsRedirectUrl());
        }
        $document = $this->db->fetch(
            'SELECT id FROM documents WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $documentId, 'company_id' => (int)$companyId]
        );
        if (!$document) {
            flash('error', 'Documento no encontrado.');
            $this->redirect($this->documentsRedirectUrl());
        }
        $userExists = $this->db->fetch(
            'SELECT id FROM users WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $shareUserId, 'company_id' => (int)$companyId]
        );
        if (!$userExists) {
            flash('error', 'Usuario no encontrado.');
            $this->redirect($this->documentsRedirectUrl());
        }
        $existingShare = $this->db->fetch(
            'SELECT id FROM document_shares WHERE document_id = :document_id AND user_id = :user_id',
            ['document_id' => $documentId, 'user_id' => $shareUserId]
        );
        if (!$existingShare) {
            $this->db->execute(
                'INSERT INTO document_shares (document_id, user_id, shared_by_user_id, created_at)
                 VALUES (:document_id, :user_id, :shared_by_user_id, NOW())',
                [
                    'document_id' => $documentId,
                    'user_id' => $shareUserId,
                    'shared_by_user_id' => $userId,
                ]
            );
        }
        flash('success', 'Documento compartido correctamente.');
        $this->redirect($this->documentsRedirectUrl());
    }

    public function unshare(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para dejar de compartir documentos.');
            $this->redirect('index.php?route=dashboard');
        }
        $documentId = (int)($_POST['id'] ?? 0);
        $shareUserId = (int)($_POST['user_id'] ?? 0);
        if ($documentId <= 0 || $shareUserId <= 0) {
            flash('error', 'Selecciona un usuario para dejar de compartir.');
            $this->redirect($this->documentsRedirectUrl());
        }
        $share = $this->db->fetch(
            'SELECT ds.id
             FROM document_shares ds
             INNER JOIN documents d ON d.id = ds.document_id
             WHERE ds.document_id = :document_id
               AND ds.user_id = :user_id
               AND d.company_id = :company_id',
            ['document_id' => $documentId, 'user_id' => $shareUserId, 'company_id' => (int)$companyId]
        );
        if (!$share) {
            flash('error', 'No encontramos el permiso de compartición.');
            $this->redirect($this->documentsRedirectUrl());
        }
        $this->db->execute(
            'DELETE FROM document_shares WHERE document_id = :document_id AND user_id = :user_id',
            ['document_id' => $documentId, 'user_id' => $shareUserId]
        );
        flash('success', 'Se dejó de compartir el documento.');
        $this->redirect($this->documentsRedirectUrl());
    }

    public function storeCategory(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para crear categorías.');
            $this->redirect('index.php?route=dashboard');
        }
        $name = trim((string)($_POST['name'] ?? ''));
        $color = trim((string)($_POST['color'] ?? '#6c757d'));
        if ($name === '') {
            flash('error', 'Ingresa un nombre para la categoría.');
            $this->redirect($this->documentsRedirectUrl());
        }
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $color = '#6c757d';
        }
        $this->db->execute(
            'INSERT INTO document_categories (company_id, name, color, created_at, updated_at)
             VALUES (:company_id, :name, :color, NOW(), NOW())',
            [
                'company_id' => (int)$companyId,
                'name' => $name,
                'color' => $color,
            ]
        );
        flash('success', 'Categoría creada.');
        $this->redirect($this->documentsRedirectUrl());
    }

    public function deleteCategory(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para eliminar categorías.');
            $this->redirect('index.php?route=dashboard');
        }
        $categoryId = (int)($_POST['id'] ?? 0);
        if ($categoryId <= 0) {
            flash('error', 'Categoría no encontrada.');
            $this->redirect($this->documentsRedirectUrl());
        }
        $category = $this->db->fetch(
            'SELECT id FROM document_categories WHERE id = :id AND company_id = :company_id',
            ['id' => $categoryId, 'company_id' => (int)$companyId]
        );
        if (!$category) {
            flash('error', 'Categoría no encontrada.');
            $this->redirect($this->documentsRedirectUrl());
        }
        $this->db->execute(
            'UPDATE documents SET category_id = NULL, updated_at = NOW()
             WHERE category_id = :category_id AND company_id = :company_id',
            ['category_id' => $categoryId, 'company_id' => (int)$companyId]
        );
        $this->db->execute(
            'DELETE FROM document_categories WHERE id = :id AND company_id = :company_id',
            ['id' => $categoryId, 'company_id' => (int)$companyId]
        );
        flash('success', 'Categoría eliminada.');
        $this->redirect($this->documentsRedirectUrl());
    }

    public function assignCategory(): void
    {
        $this->requireLogin();
        verify_csrf();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para actualizar categorías.');
            $this->redirect('index.php?route=dashboard');
        }
        $documentId = (int)($_POST['id'] ?? 0);
        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        if ($documentId <= 0) {
            flash('error', 'Documento no encontrado.');
            $this->redirect($this->documentsRedirectUrl());
        }
        if ($categoryId && !$this->categoryExists((int)$companyId, $categoryId)) {
            $categoryId = null;
        }
        $this->db->execute(
            'UPDATE documents SET category_id = :category_id, updated_at = NOW() WHERE id = :id AND company_id = :company_id',
            ['category_id' => $categoryId, 'id' => $documentId, 'company_id' => (int)$companyId]
        );
        flash('success', 'Categoría actualizada.');
        $this->redirect($this->documentsRedirectUrl());
    }

    public function download(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        if (!$companyId) {
            http_response_code(403);
            echo 'Empresa no válida';
            return;
        }
        $documentId = (int)($_GET['id'] ?? 0);
        if ($documentId <= 0) {
            http_response_code(404);
            echo 'Documento no encontrado';
            return;
        }
        $document = $this->db->fetch(
            'SELECT filename, original_name FROM documents WHERE id = :id AND company_id = :company_id AND deleted_at IS NULL',
            ['id' => $documentId, 'company_id' => (int)$companyId]
        );
        if (!$document) {
            http_response_code(404);
            echo 'Documento no encontrado';
            return;
        }
        $this->db->execute(
            'UPDATE documents SET download_count = download_count + 1, last_downloaded_at = NOW() WHERE id = :id AND company_id = :company_id',
            ['id' => $documentId, 'company_id' => (int)$companyId]
        );
        $directory = $this->documentsDirectory((int)$companyId);
        $target = $this->safeDocumentPath($directory, (string)$document['filename']);
        if ($target === null || !is_file($target)) {
            http_response_code(404);
            echo 'Documento no encontrado';
            return;
        }
        $displayName = $document['original_name'] ?: $this->displayName((string)$document['filename']);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $displayName . '"');
        header('Content-Length: ' . filesize($target));
        readfile($target);
        exit;
    }

    private function documentsDirectory(int $companyId): string
    {
        return dirname(__DIR__) . '/../storage/uploads/documents/' . $companyId;
    }

    private function listDocuments(int $companyId, string $filter = 'all', ?int $categoryId = null, ?int $userId = null): array
    {
        $filter = $filter !== '' ? $filter : 'all';
        $joins = 'LEFT JOIN document_categories dc ON dc.id = documents.category_id';
        $conditions = ['documents.company_id = :company_id'];
        $params = ['company_id' => $companyId];

        switch ($filter) {
            case 'favorites':
                $conditions[] = 'documents.is_favorite = 1';
                $conditions[] = 'documents.deleted_at IS NULL';
                break;
            case 'recent':
                $conditions[] = 'documents.deleted_at IS NULL';
                $conditions[] = 'documents.updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
                break;
            case 'downloads':
                $conditions[] = 'documents.deleted_at IS NULL';
                $conditions[] = 'documents.download_count > 0';
                break;
            case 'trash':
                $conditions[] = 'documents.deleted_at IS NOT NULL';
                break;
            case 'shared':
                if (!$userId) {
                    return [];
                }
                $joins .= ' INNER JOIN document_shares ds ON ds.document_id = documents.id AND ds.user_id = :shared_user_id';
                $params['shared_user_id'] = $userId;
                $conditions[] = 'documents.deleted_at IS NULL';
                break;
            case 'all':
            default:
                $conditions[] = 'documents.deleted_at IS NULL';
                break;
        }

        if ($categoryId) {
            $conditions[] = 'documents.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        $rows = $this->db->fetchAll(
            'SELECT documents.id, documents.filename, documents.original_name, documents.mime_type, documents.file_size,
                    documents.updated_at, documents.is_favorite, documents.download_count, documents.deleted_at,
                    dc.name AS category_name, dc.color AS category_color
             FROM documents
             ' . $joins . '
             WHERE ' . implode(' AND ', $conditions) . '
             ORDER BY documents.id DESC',
            $params
        );
        $documents = [];
        foreach ($rows as $row) {
            $filename = (string)$row['filename'];
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $documents[] = [
                'id' => (int)$row['id'],
                'filename' => $filename,
                'name' => $row['original_name'] ?: $this->displayName($filename),
                'extension' => $extension !== '' ? strtoupper($extension) : 'Archivo',
                'size' => (int)($row['file_size'] ?? 0),
                'updated_at' => $row['updated_at']
                    ? date('d/m/Y', strtotime((string)$row['updated_at']))
                    : date('d/m/Y'),
                'download_url' => 'index.php?route=documents/download&id=' . urlencode((string)$row['id']),
                'is_favorite' => (int)($row['is_favorite'] ?? 0) === 1,
                'download_count' => (int)($row['download_count'] ?? 0),
                'deleted_at' => $row['deleted_at'],
                'category' => [
                    'name' => $row['category_name'] ?? null,
                    'color' => $row['category_color'] ?? null,
                ],
            ];
        }
        return $documents;
    }

    private function listCategories(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT dc.id, dc.name, dc.color, COUNT(d.id) AS total
             FROM document_categories dc
             LEFT JOIN documents d ON d.category_id = dc.id AND d.deleted_at IS NULL
             WHERE dc.company_id = :company_id
             GROUP BY dc.id
             ORDER BY dc.name ASC',
            ['company_id' => $companyId]
        );
    }

    private function documentCounts(int $companyId, ?int $userId): array
    {
        $counts = [
            'all' => 0,
            'favorites' => 0,
            'recent' => 0,
            'downloads' => 0,
            'trash' => 0,
            'shared' => 0,
        ];
        $counts['all'] = (int)($this->db->fetch(
            'SELECT COUNT(*) AS total FROM documents WHERE company_id = :company_id AND deleted_at IS NULL',
            ['company_id' => $companyId]
        )['total'] ?? 0);
        $counts['favorites'] = (int)($this->db->fetch(
            'SELECT COUNT(*) AS total FROM documents WHERE company_id = :company_id AND deleted_at IS NULL AND is_favorite = 1',
            ['company_id' => $companyId]
        )['total'] ?? 0);
        $counts['recent'] = (int)($this->db->fetch(
            'SELECT COUNT(*) AS total FROM documents WHERE company_id = :company_id AND deleted_at IS NULL AND updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)',
            ['company_id' => $companyId]
        )['total'] ?? 0);
        $counts['downloads'] = (int)($this->db->fetch(
            'SELECT COUNT(*) AS total FROM documents WHERE company_id = :company_id AND deleted_at IS NULL AND download_count > 0',
            ['company_id' => $companyId]
        )['total'] ?? 0);
        $counts['trash'] = (int)($this->db->fetch(
            'SELECT COUNT(*) AS total FROM documents WHERE company_id = :company_id AND deleted_at IS NOT NULL',
            ['company_id' => $companyId]
        )['total'] ?? 0);
        if ($userId) {
            $counts['shared'] = (int)($this->db->fetch(
                'SELECT COUNT(*) AS total
                 FROM documents
                 INNER JOIN document_shares ds ON ds.document_id = documents.id AND ds.user_id = :user_id
                 WHERE documents.company_id = :company_id AND documents.deleted_at IS NULL',
                ['company_id' => $companyId, 'user_id' => $userId]
            )['total'] ?? 0);
        }
        return $counts;
    }

    private function categoryExists(int $companyId, int $categoryId): bool
    {
        return (bool)$this->db->fetch(
            'SELECT id FROM document_categories WHERE id = :id AND company_id = :company_id',
            ['id' => $categoryId, 'company_id' => $companyId]
        );
    }

    private function attachSharesToDocuments(array $documents, int $companyId): array
    {
        $documentIds = array_values(array_filter(array_map(
            static fn(array $document): int => (int)($document['id'] ?? 0),
            $documents
        )));
        if (empty($documentIds)) {
            return $documents;
        }
        $placeholders = implode(',', array_fill(0, count($documentIds), '?'));
        $params = $documentIds;
        $params[] = $companyId;
        $rows = $this->db->fetchAll(
            'SELECT ds.document_id, u.id, u.name, u.avatar_path
             FROM document_shares ds
             INNER JOIN users u ON u.id = ds.user_id
             WHERE ds.document_id IN (' . $placeholders . ')
               AND u.company_id = ?
               AND u.deleted_at IS NULL
             ORDER BY ds.document_id ASC, u.name ASC',
            $params
        );
        $sharesByDocument = [];
        foreach ($rows as $row) {
            $docId = (int)$row['document_id'];
            $sharesByDocument[$docId][] = [
                'id' => (int)$row['id'],
                'name' => $row['name'] ?? '',
                'avatar_path' => $row['avatar_path'] ?? '',
            ];
        }
        foreach ($documents as &$document) {
            $docId = (int)($document['id'] ?? 0);
            $document['shared_with'] = $sharesByDocument[$docId] ?? [];
        }
        unset($document);
        return $documents;
    }

    private function documentsRedirectUrl(): string
    {
        $filter = (string)($_POST['redirect_filter'] ?? $_GET['filter'] ?? '');
        $categoryId = (string)($_POST['redirect_category'] ?? $_GET['category'] ?? '');
        $parts = ['index.php?route=documents'];
        if ($filter !== '') {
            $parts[] = 'filter=' . urlencode($filter);
        }
        if ($categoryId !== '') {
            $parts[] = 'category=' . urlencode($categoryId);
        }
        return implode('&', $parts);
    }

    private function safeDocumentPath(string $directory, string $filename): ?string
    {
        $safe = basename($filename);
        if ($safe === '') {
            return null;
        }
        $path = $directory . '/' . $safe;
        $realDirectory = realpath($directory);
        $realPath = realpath($path);
        if ($realDirectory === false || $realPath === false) {
            return null;
        }
        if (!str_starts_with($realPath, $realDirectory)) {
            return null;
        }
        return $realPath;
    }

    private function displayName(string $filename): string
    {
        $parts = explode('__', $filename, 2);
        return $parts[1] ?? $filename;
    }

    private function guessMime(string $path): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return 'application/octet-stream';
        }
        $mime = finfo_file($finfo, $path) ?: 'application/octet-stream';
        finfo_close($finfo);
        return $mime;
    }

    private function normalizeFiles(?array $files): array
    {
        if ($files === null || empty($files['name'])) {
            return [];
        }
        if (is_array($files['name'])) {
            $normalized = [];
            $count = count($files['name']);
            for ($index = 0; $index < $count; $index++) {
                if (($files['error'][$index] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                $normalized[] = [
                    'name' => $files['name'][$index] ?? '',
                    'type' => $files['type'][$index] ?? '',
                    'tmp_name' => $files['tmp_name'][$index] ?? '',
                    'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                    'size' => $files['size'][$index] ?? 0,
                ];
            }
            return $normalized;
        }
        if (($files['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_NO_FILE) {
            return [];
        }
        return [$files];
    }
}
