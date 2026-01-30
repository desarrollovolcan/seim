START TRANSACTION;

SET @clients_billing_email := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'billing_email'
);
SET @sql := IF(@clients_billing_email = 0, 'ALTER TABLE clients ADD COLUMN billing_email VARCHAR(150) NULL AFTER email;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_phone := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'phone'
);
SET @sql := IF(@clients_phone = 0, 'ALTER TABLE clients ADD COLUMN phone VARCHAR(50) NULL AFTER billing_email;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_address := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'address'
);
SET @sql := IF(@clients_address = 0, 'ALTER TABLE clients ADD COLUMN address VARCHAR(255) NULL AFTER phone;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'giro'
);
SET @sql := IF(@clients_giro = 0, 'ALTER TABLE clients ADD COLUMN giro VARCHAR(150) NULL AFTER address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_activity_code := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'activity_code'
);
SET @sql := IF(@clients_activity_code = 0, 'ALTER TABLE clients ADD COLUMN activity_code VARCHAR(50) NULL AFTER giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'commune'
);
SET @sql := IF(@clients_commune = 0, 'ALTER TABLE clients ADD COLUMN commune VARCHAR(120) NULL AFTER activity_code;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_city := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'city'
);
SET @sql := IF(@clients_city = 0, 'ALTER TABLE clients ADD COLUMN city VARCHAR(120) NULL AFTER commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_contact := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'contact'
);
SET @sql := IF(@clients_contact = 0, 'ALTER TABLE clients ADD COLUMN contact VARCHAR(150) NULL AFTER city;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_mandante_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'mandante_name'
);
SET @sql := IF(@clients_mandante_name = 0, 'ALTER TABLE clients ADD COLUMN mandante_name VARCHAR(150) NULL AFTER contact;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_mandante_rut := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'mandante_rut'
);
SET @sql := IF(@clients_mandante_rut = 0, 'ALTER TABLE clients ADD COLUMN mandante_rut VARCHAR(50) NULL AFTER mandante_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_mandante_phone := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'mandante_phone'
);
SET @sql := IF(@clients_mandante_phone = 0, 'ALTER TABLE clients ADD COLUMN mandante_phone VARCHAR(50) NULL AFTER mandante_rut;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_mandante_email := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'mandante_email'
);
SET @sql := IF(@clients_mandante_email = 0, 'ALTER TABLE clients ADD COLUMN mandante_email VARCHAR(150) NULL AFTER mandante_phone;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_avatar_path := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'avatar_path'
);
SET @sql := IF(@clients_avatar_path = 0, 'ALTER TABLE clients ADD COLUMN avatar_path VARCHAR(255) NULL AFTER mandante_email;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_portal_token := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'portal_token'
);
SET @sql := IF(@clients_portal_token = 0, 'ALTER TABLE clients ADD COLUMN portal_token VARCHAR(64) NULL AFTER avatar_path;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_portal_password := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'portal_password'
);
SET @sql := IF(@clients_portal_password = 0, 'ALTER TABLE clients ADD COLUMN portal_password VARCHAR(255) NULL AFTER portal_token;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_notes := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'notes'
);
SET @sql := IF(@clients_notes = 0, 'ALTER TABLE clients ADD COLUMN notes TEXT NULL AFTER portal_password;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_status := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'status'
);
SET @sql := IF(@clients_status = 0, 'ALTER TABLE clients ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT ''activo'' AFTER notes;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_clients_portal_token := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'clients'
      AND INDEX_NAME = 'idx_clients_portal_token'
);
SET @sql := IF(@idx_clients_portal_token = 0, 'CREATE UNIQUE INDEX idx_clients_portal_token ON clients(portal_token);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_clients_status := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'clients'
      AND INDEX_NAME = 'idx_clients_status'
);
SET @sql := IF(@idx_clients_status = 0, 'CREATE INDEX idx_clients_status ON clients(status);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
