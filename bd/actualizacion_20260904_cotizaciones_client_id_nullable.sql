START TRANSACTION;

SET @quotes_client_id_nullable := (
    SELECT IS_NULLABLE
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'quotes'
      AND COLUMN_NAME = 'client_id'
);
SET @sql := IF(
    @quotes_client_id_nullable = 'NO',
    'ALTER TABLE quotes MODIFY COLUMN client_id INT NULL DEFAULT NULL;',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
