-- Asegura columnas SII en compras para evitar errores al guardar
SET @db_name := DATABASE();

SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_document_type'
        ),
        'SELECT 1;',
        'ALTER TABLE purchases ADD COLUMN sii_document_type VARCHAR(50) NULL AFTER total;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_document_number'
        ),
        'SELECT 1;',
        'ALTER TABLE purchases ADD COLUMN sii_document_number VARCHAR(50) NULL AFTER sii_document_type;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_rut'
        ),
        'SELECT 1;',
        'ALTER TABLE purchases ADD COLUMN sii_receiver_rut VARCHAR(50) NULL AFTER sii_document_number;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_name'
        ),
        'SELECT 1;',
        'ALTER TABLE purchases ADD COLUMN sii_receiver_name VARCHAR(150) NULL AFTER sii_receiver_rut;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_giro'
        ),
        'SELECT 1;',
        'ALTER TABLE purchases ADD COLUMN sii_receiver_giro VARCHAR(150) NULL AFTER sii_receiver_name;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_address'
        ),
        'SELECT 1;',
        'ALTER TABLE purchases ADD COLUMN sii_receiver_address VARCHAR(255) NULL AFTER sii_receiver_giro;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_commune'
        ),
        'SELECT 1;',
        'ALTER TABLE purchases ADD COLUMN sii_receiver_commune VARCHAR(100) NULL AFTER sii_receiver_address;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_tax_rate'
        ),
        'SELECT 1;',
        'ALTER TABLE purchases ADD COLUMN sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19 AFTER sii_receiver_commune;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_exempt_amount'
        ),
        'SELECT 1;',
        'ALTER TABLE purchases ADD COLUMN sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER sii_tax_rate;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
