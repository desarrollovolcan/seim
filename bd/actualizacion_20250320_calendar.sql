START TRANSACTION;

CREATE TABLE IF NOT EXISTS calendar_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    created_by_user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    event_type VARCHAR(20) NOT NULL DEFAULT 'meeting',
    location VARCHAR(150) NULL,
    start_at DATETIME NOT NULL,
    end_at DATETIME NULL,
    all_day TINYINT(1) NOT NULL DEFAULT 0,
    reminder_minutes INT NULL,
    class_name VARCHAR(100) NOT NULL DEFAULT 'bg-primary-subtle text-primary',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_calendar_events_company (company_id),
    INDEX idx_calendar_events_start (start_at),
    CONSTRAINT fk_calendar_events_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_calendar_events_user
        FOREIGN KEY (created_by_user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS calendar_event_documents (
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
);

CREATE TABLE IF NOT EXISTS calendar_event_attendees (
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
);

COMMIT;
