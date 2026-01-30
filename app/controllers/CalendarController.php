<?php

class CalendarController extends Controller
{
    private const ALLOWED_CLASSNAMES = [
        'bg-primary-subtle text-primary',
        'bg-secondary-subtle text-secondary',
        'bg-success-subtle text-success',
        'bg-info-subtle text-info',
        'bg-warning-subtle text-warning',
        'bg-danger-subtle text-danger',
        'bg-dark-subtle text-dark',
    ];

    public function index(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        if (!$companyId) {
            flash('error', 'Selecciona una empresa para usar el calendario.');
            $this->redirect('index.php?route=dashboard');
        }
        $this->ensureCalendarTables();
        $calendarModel = new CalendarModel($this->db);
        $documents = $calendarModel->listDocuments((int)$companyId);
        $eventTypes = $calendarModel->listEventTypes((int)$companyId);
        $usersModel = new UsersModel($this->db);
        $users = $usersModel->allActive((int)$companyId);

        $this->render('calendar/index', [
            'title' => 'Calendario',
            'pageTitle' => 'Calendario',
            'documents' => $documents,
            'eventTypes' => $eventTypes,
            'users' => $users,
        ]);
    }

    public function events(): void
    {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');

        $companyId = current_company_id();
        if (!$companyId) {
            echo json_encode([], JSON_UNESCAPED_UNICODE);
            return;
        }
        $this->ensureCalendarTables();
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $calendarModel = new CalendarModel($this->db);
        $events = $calendarModel->listEvents((int)$companyId, $start, $end);
        echo json_encode($events, JSON_UNESCAPED_UNICODE);
    }

    public function store(): void
    {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');

        $companyId = current_company_id();
        if (!$companyId) {
            http_response_code(403);
            echo json_encode(['message' => 'Empresa no válida.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $this->ensureCalendarTables();
        $data = $this->requestData();
        $this->verifyToken($data['csrf_token'] ?? null);

        $title = trim((string)($data['title'] ?? ''));
        $typeId = (int)($data['type_id'] ?? 0);
        $location = trim((string)($data['location'] ?? ''));
        $description = trim((string)($data['description'] ?? ''));
        $allDay = !empty($data['all_day']);
        $reminderMinutes = isset($data['reminder_minutes']) && $data['reminder_minutes'] !== ''
            ? (int)$data['reminder_minutes']
            : null;

        $start = $this->parseDateTime($data['start'] ?? null);
        $end = $this->parseDateTime($data['end'] ?? null);
        if ($title === '' || !$start) {
            http_response_code(422);
            echo json_encode(['message' => 'Completa el título y la fecha de inicio.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        if ($typeId <= 0) {
            http_response_code(422);
            echo json_encode(['message' => 'Selecciona un tipo de evento.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        if ($end && $end < $start) {
            http_response_code(422);
            echo json_encode(['message' => 'La fecha de término no puede ser anterior a la fecha de inicio.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        if ($reminderMinutes !== null && $reminderMinutes < 0) {
            $reminderMinutes = null;
        }

        $user = Auth::user();
        $userId = (int)($user['id'] ?? 0);
        if ($userId <= 0) {
            http_response_code(403);
            echo json_encode(['message' => 'Usuario no válido.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $calendarModel = new CalendarModel($this->db);
        $eventType = $calendarModel->findEventType((int)$companyId, $typeId);
        if (!$eventType) {
            http_response_code(422);
            echo json_encode(['message' => 'El tipo de evento seleccionado no existe.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $eventId = (int)($data['id'] ?? 0);
        if ($eventId > 0) {
            $existing = $this->db->fetch(
                'SELECT id FROM calendar_events WHERE id = :id AND company_id = :company_id',
                ['id' => $eventId, 'company_id' => (int)$companyId]
            );
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['message' => 'Evento no encontrado.'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $this->db->execute(
                'UPDATE calendar_events
                 SET title = :title,
                     description = :description,
                     event_type = :event_type,
                     event_type_id = :event_type_id,
                     location = :location,
                     start_at = :start_at,
                     end_at = :end_at,
                     all_day = :all_day,
                     reminder_minutes = :reminder_minutes,
                     class_name = :class_name,
                     updated_at = NOW()
                 WHERE id = :id AND company_id = :company_id',
                [
                    'title' => $title,
                    'description' => $description !== '' ? $description : null,
                    'event_type' => $eventType['name'],
                    'event_type_id' => $eventType['id'],
                    'location' => $location !== '' ? $location : null,
                    'start_at' => $start->format('Y-m-d H:i:s'),
                    'end_at' => $end ? $end->format('Y-m-d H:i:s') : null,
                    'all_day' => $allDay ? 1 : 0,
                    'reminder_minutes' => $reminderMinutes,
                    'class_name' => $eventType['class_name'],
                    'id' => $eventId,
                    'company_id' => (int)$companyId,
                ]
            );
        } else {
            $this->db->execute(
                'INSERT INTO calendar_events
                    (company_id, created_by_user_id, title, description, event_type, event_type_id, location, start_at, end_at, all_day, reminder_minutes, class_name, created_at, updated_at)
                 VALUES
                    (:company_id, :created_by, :title, :description, :event_type, :event_type_id, :location, :start_at, :end_at, :all_day, :reminder_minutes, :class_name, NOW(), NOW())',
                [
                    'company_id' => (int)$companyId,
                    'created_by' => $userId,
                    'title' => $title,
                    'description' => $description !== '' ? $description : null,
                    'event_type' => $eventType['name'],
                    'event_type_id' => $eventType['id'],
                    'location' => $location !== '' ? $location : null,
                    'start_at' => $start->format('Y-m-d H:i:s'),
                    'end_at' => $end ? $end->format('Y-m-d H:i:s') : null,
                    'all_day' => $allDay ? 1 : 0,
                    'reminder_minutes' => $reminderMinutes,
                    'class_name' => $eventType['class_name'],
                ]
            );
            $eventId = (int)$this->db->lastInsertId();
        }

        $documentIds = $this->sanitizeDocumentIds($data['documents'] ?? []);
        $this->syncEventDocuments($eventId, (int)$companyId, $documentIds);
        $attendeeIds = $this->sanitizeDocumentIds($data['attendees'] ?? []);
        $this->syncEventAttendees($eventId, (int)$companyId, $attendeeIds);

        echo json_encode(['success' => true, 'id' => $eventId], JSON_UNESCAPED_UNICODE);
    }

    public function delete(): void
    {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');

        $companyId = current_company_id();
        if (!$companyId) {
            http_response_code(403);
            echo json_encode(['message' => 'Empresa no válida.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $this->ensureCalendarTables();
        $data = $this->requestData();
        $this->verifyToken($data['csrf_token'] ?? null);

        $eventId = (int)($data['id'] ?? 0);
        if ($eventId <= 0) {
            http_response_code(404);
            echo json_encode(['message' => 'Evento no encontrado.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $existing = $this->db->fetch(
            'SELECT id FROM calendar_events WHERE id = :id AND company_id = :company_id',
            ['id' => $eventId, 'company_id' => (int)$companyId]
        );
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['message' => 'Evento no encontrado.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $this->db->execute('DELETE FROM calendar_events WHERE id = :id AND company_id = :company_id', [
            'id' => $eventId,
            'company_id' => (int)$companyId,
        ]);
        echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
    }

    public function types(): void
    {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');

        $companyId = current_company_id();
        if (!$companyId) {
            echo json_encode([], JSON_UNESCAPED_UNICODE);
            return;
        }
        $this->ensureCalendarTables();
        $calendarModel = new CalendarModel($this->db);
        $types = $calendarModel->listEventTypes((int)$companyId);
        echo json_encode($types, JSON_UNESCAPED_UNICODE);
    }

    public function storeType(): void
    {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');

        $companyId = current_company_id();
        if (!$companyId) {
            http_response_code(403);
            echo json_encode(['message' => 'Empresa no válida.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $this->ensureCalendarTables();
        $data = $this->requestData();
        $this->verifyToken($data['csrf_token'] ?? null);

        $name = trim((string)($data['name'] ?? ''));
        $className = (string)($data['class_name'] ?? '');
        if ($name === '') {
            http_response_code(422);
            echo json_encode(['message' => 'Ingresa un nombre para el tipo de evento.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (!in_array($className, self::ALLOWED_CLASSNAMES, true)) {
            http_response_code(422);
            echo json_encode(['message' => 'Selecciona un color válido.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $existing = $this->db->fetch(
            'SELECT id FROM calendar_event_types WHERE company_id = :company_id AND LOWER(name) = LOWER(:name)',
            ['company_id' => (int)$companyId, 'name' => $name]
        );
        if ($existing) {
            http_response_code(409);
            echo json_encode(['message' => 'Ya existe un tipo con ese nombre.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $user = Auth::user();
        $userId = (int)($user['id'] ?? 0);
        if ($userId <= 0) {
            http_response_code(403);
            echo json_encode(['message' => 'Usuario no válido.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $calendarModel = new CalendarModel($this->db);
        $typeId = $calendarModel->createEventType((int)$companyId, $userId, $name, $className);
        echo json_encode([
            'success' => true,
            'type' => [
                'id' => $typeId,
                'name' => $name,
                'class_name' => $className,
            ],
        ], JSON_UNESCAPED_UNICODE);
    }

    public function updateType(): void
    {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');

        $companyId = current_company_id();
        if (!$companyId) {
            http_response_code(403);
            echo json_encode(['message' => 'Empresa no válida.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $this->ensureCalendarTables();
        $data = $this->requestData();
        $this->verifyToken($data['csrf_token'] ?? null);

        $typeId = (int)($data['id'] ?? 0);
        $name = trim((string)($data['name'] ?? ''));
        $className = (string)($data['class_name'] ?? '');
        if ($typeId <= 0) {
            http_response_code(404);
            echo json_encode(['message' => 'Tipo de evento no encontrado.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        if ($name === '') {
            http_response_code(422);
            echo json_encode(['message' => 'Ingresa un nombre para el tipo de evento.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (!in_array($className, self::ALLOWED_CLASSNAMES, true)) {
            http_response_code(422);
            echo json_encode(['message' => 'Selecciona un color válido.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $calendarModel = new CalendarModel($this->db);
        $existing = $calendarModel->findEventType((int)$companyId, $typeId);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['message' => 'Tipo de evento no encontrado.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $duplicate = $this->db->fetch(
            'SELECT id FROM calendar_event_types
             WHERE company_id = :company_id AND LOWER(name) = LOWER(:name) AND id != :id',
            ['company_id' => (int)$companyId, 'name' => $name, 'id' => $typeId]
        );
        if ($duplicate) {
            http_response_code(409);
            echo json_encode(['message' => 'Ya existe un tipo con ese nombre.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $calendarModel->updateEventType((int)$companyId, $typeId, $name, $className);
        $calendarModel->syncEventTypeAppearance((int)$companyId, $typeId, $name, $className);
        echo json_encode([
            'success' => true,
            'type' => [
                'id' => $typeId,
                'name' => $name,
                'class_name' => $className,
            ],
        ], JSON_UNESCAPED_UNICODE);
    }

    public function deleteType(): void
    {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');

        $companyId = current_company_id();
        if (!$companyId) {
            http_response_code(403);
            echo json_encode(['message' => 'Empresa no válida.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $this->ensureCalendarTables();
        $data = $this->requestData();
        $this->verifyToken($data['csrf_token'] ?? null);

        $typeId = (int)($data['id'] ?? 0);
        if ($typeId <= 0) {
            http_response_code(404);
            echo json_encode(['message' => 'Tipo de evento no encontrado.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $calendarModel = new CalendarModel($this->db);
        $existing = $calendarModel->findEventType((int)$companyId, $typeId);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['message' => 'Tipo de evento no encontrado.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $linked = $this->db->fetch(
            'SELECT COUNT(*) as total FROM calendar_events WHERE company_id = :company_id AND event_type_id = :event_type_id',
            ['company_id' => (int)$companyId, 'event_type_id' => $typeId]
        );
        if (!empty($linked['total'])) {
            http_response_code(409);
            echo json_encode(['message' => 'No se puede eliminar el tipo porque tiene eventos asociados.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $calendarModel->deleteEventType((int)$companyId, $typeId);
        echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
    }

    private function requestData(): array
    {
        if (!empty($_POST)) {
            return $_POST;
        }
        $raw = file_get_contents('php://input');
        if (!$raw) {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function verifyToken(?string $token): void
    {
        if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            echo json_encode(['message' => 'CSRF token inválido.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    private function parseDateTime(?string $value): ?DateTime
    {
        if (!$value) {
            return null;
        }
        try {
            return new DateTime($value);
        } catch (Throwable $e) {
            return null;
        }
    }

    private function sanitizeDocumentIds(array|string $documentIds): array
    {
        $ids = is_array($documentIds) ? $documentIds : [$documentIds];
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, static fn(int $id) => $id > 0);
        $ids = array_values(array_unique($ids));
        return $ids;
    }

    private function syncEventDocuments(int $eventId, int $companyId, array $documentIds): void
    {
        $this->db->execute('DELETE FROM calendar_event_documents WHERE event_id = :event_id', [
            'event_id' => $eventId,
        ]);
        if (empty($documentIds)) {
            return;
        }
        $placeholders = [];
        $params = ['company_id' => $companyId];
        foreach ($documentIds as $index => $documentId) {
            $key = 'doc' . $index;
            $placeholders[] = ':' . $key;
            $params[$key] = $documentId;
        }
        $rows = $this->db->fetchAll(
            'SELECT id FROM documents WHERE company_id = :company_id AND deleted_at IS NULL AND id IN (' . implode(',', $placeholders) . ')',
            $params
        );
        foreach ($rows as $row) {
            $this->db->execute(
                'INSERT INTO calendar_event_documents (event_id, document_id, created_at)
                 VALUES (:event_id, :document_id, NOW())',
                [
                    'event_id' => $eventId,
                    'document_id' => (int)$row['id'],
                ]
            );
        }
    }

    private function syncEventAttendees(int $eventId, int $companyId, array $attendeeIds): void
    {
        $this->db->execute('DELETE FROM calendar_event_attendees WHERE event_id = :event_id', [
            'event_id' => $eventId,
        ]);
        if (empty($attendeeIds)) {
            return;
        }
        $placeholders = [];
        $params = ['company_id' => $companyId];
        foreach ($attendeeIds as $index => $attendeeId) {
            $key = 'user' . $index;
            $placeholders[] = ':' . $key;
            $params[$key] = $attendeeId;
        }
        $rows = $this->db->fetchAll(
            'SELECT id FROM users WHERE company_id = :company_id AND deleted_at IS NULL AND id IN (' . implode(',', $placeholders) . ')',
            $params
        );
        foreach ($rows as $row) {
            $this->db->execute(
                'INSERT INTO calendar_event_attendees (event_id, user_id, created_at)
                 VALUES (:event_id, :user_id, NOW())',
                [
                    'event_id' => $eventId,
                    'user_id' => (int)$row['id'],
                ]
            );
        }
    }

    private function ensureCalendarTables(): void
    {
        $this->db->execute(
            'CREATE TABLE IF NOT EXISTS calendar_event_types (
                id INT AUTO_INCREMENT PRIMARY KEY,
                company_id INT NOT NULL,
                created_by_user_id INT NOT NULL,
                name VARCHAR(100) NOT NULL,
                class_name VARCHAR(100) NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX idx_calendar_event_types_company (company_id),
                UNIQUE KEY idx_calendar_event_types_name (company_id, name),
                CONSTRAINT fk_calendar_event_types_company
                    FOREIGN KEY (company_id) REFERENCES companies(id)
                    ON DELETE CASCADE,
                CONSTRAINT fk_calendar_event_types_user
                    FOREIGN KEY (created_by_user_id) REFERENCES users(id)
                    ON DELETE CASCADE
            )'
        );
        $this->db->execute(
            'CREATE TABLE IF NOT EXISTS calendar_events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                company_id INT NOT NULL,
                created_by_user_id INT NOT NULL,
                title VARCHAR(150) NOT NULL,
                description TEXT NULL,
                event_type VARCHAR(20) NOT NULL DEFAULT \'meeting\',
                event_type_id INT NULL,
                location VARCHAR(150) NULL,
                start_at DATETIME NOT NULL,
                end_at DATETIME NULL,
                all_day TINYINT(1) NOT NULL DEFAULT 0,
                reminder_minutes INT NULL,
                class_name VARCHAR(100) NOT NULL DEFAULT \'bg-primary-subtle text-primary\',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX idx_calendar_events_company (company_id),
                INDEX idx_calendar_events_start (start_at),
                INDEX idx_calendar_events_type (event_type_id),
                CONSTRAINT fk_calendar_events_company
                    FOREIGN KEY (company_id) REFERENCES companies(id)
                    ON DELETE CASCADE,
                CONSTRAINT fk_calendar_events_user
                    FOREIGN KEY (created_by_user_id) REFERENCES users(id)
                    ON DELETE CASCADE,
                CONSTRAINT fk_calendar_events_type
                    FOREIGN KEY (event_type_id) REFERENCES calendar_event_types(id)
                    ON DELETE SET NULL
            )'
        );
        $typeColumn = $this->db->fetch("SHOW COLUMNS FROM calendar_events LIKE 'event_type_id'");
        if (!$typeColumn) {
            $this->db->execute(
                'ALTER TABLE calendar_events
                 ADD COLUMN event_type_id INT NULL AFTER event_type,
                 ADD INDEX idx_calendar_events_type (event_type_id),
                 ADD CONSTRAINT fk_calendar_events_type
                    FOREIGN KEY (event_type_id) REFERENCES calendar_event_types(id)
                    ON DELETE SET NULL'
            );
        }
        $this->db->execute(
            'CREATE TABLE IF NOT EXISTS calendar_event_documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                document_id INT NOT NULL,
                created_at DATETIME NOT NULL,
                UNIQUE KEY idx_calendar_event_document_unique (event_id, document_id),
                INDEX idx_calendar_event_documents_event (event_id),
                INDEX idx_calendar_event_documents_document (document_id),
                CONSTRAINT fk_calendar_event_documents_event
                    FOREIGN KEY (event_id) REFERENCES calendar_events(id)
                    ON DELETE CASCADE,
                CONSTRAINT fk_calendar_event_documents_document
                    FOREIGN KEY (document_id) REFERENCES documents(id)
                    ON DELETE CASCADE
            )'
        );
        $this->db->execute(
            'CREATE TABLE IF NOT EXISTS calendar_event_attendees (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                user_id INT NOT NULL,
                created_at DATETIME NOT NULL,
                UNIQUE KEY idx_calendar_event_attendee_unique (event_id, user_id),
                INDEX idx_calendar_event_attendees_event (event_id),
                INDEX idx_calendar_event_attendees_user (user_id),
                CONSTRAINT fk_calendar_event_attendees_event
                    FOREIGN KEY (event_id) REFERENCES calendar_events(id)
                    ON DELETE CASCADE,
                CONSTRAINT fk_calendar_event_attendees_user
                    FOREIGN KEY (user_id) REFERENCES users(id)
                    ON DELETE CASCADE
            )'
        );
    }
}
