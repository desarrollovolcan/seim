START TRANSACTION;

SET @quotes_estado_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'quotes'
      AND COLUMN_NAME = 'estado'
);
SET @sql := IF(
    @quotes_estado_exists = 1,
    'ALTER TABLE quotes MODIFY COLUMN estado VARCHAR(20) NOT NULL DEFAULT ''creada'';',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE quotes
SET estado = CASE LOWER(TRIM(estado))
    WHEN 'pendiente' THEN 'creada'
    WHEN 'aceptada' THEN 'aprobada'
    WHEN 'en curzo' THEN 'en_curso'
    WHEN 'en curso' THEN 'en_curso'
    WHEN 'creada' THEN 'creada'
    WHEN 'enviada' THEN 'enviada'
    WHEN 'aprobada' THEN 'aprobada'
    WHEN 'rechazada' THEN 'rechazada'
    ELSE 'creada'
END;

SET @quotes_next_action_date := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'quotes'
      AND COLUMN_NAME = 'next_action_date'
);
SET @sql := IF(
    @quotes_next_action_date = 0,
    'ALTER TABLE quotes ADD COLUMN next_action_date DATE NULL AFTER estado;',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_is_closed := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'quotes'
      AND COLUMN_NAME = 'is_closed'
);
SET @sql := IF(
    @quotes_is_closed = 0,
    'ALTER TABLE quotes ADD COLUMN is_closed TINYINT(1) NOT NULL DEFAULT 0 AFTER next_action_date;',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_closed_at := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'quotes'
      AND COLUMN_NAME = 'closed_at'
);
SET @sql := IF(
    @quotes_closed_at = 0,
    'ALTER TABLE quotes ADD COLUMN closed_at DATETIME NULL AFTER is_closed;',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE quotes
SET closed_at = COALESCE(closed_at, updated_at, created_at, NOW())
WHERE is_closed = 1 AND closed_at IS NULL;

SET @idx_quotes_estado := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'quotes'
      AND INDEX_NAME = 'idx_quotes_estado'
);
SET @sql := IF(
    @idx_quotes_estado = 0,
    'CREATE INDEX idx_quotes_estado ON quotes(estado);',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_quotes_next_action_date := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'quotes'
      AND INDEX_NAME = 'idx_quotes_next_action_date'
);
SET @sql := IF(
    @idx_quotes_next_action_date = 0,
    'CREATE INDEX idx_quotes_next_action_date ON quotes(next_action_date);',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_quotes_is_closed := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'quotes'
      AND INDEX_NAME = 'idx_quotes_is_closed'
);
SET @sql := IF(
    @idx_quotes_is_closed = 0,
    'CREATE INDEX idx_quotes_is_closed ON quotes(is_closed);',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
