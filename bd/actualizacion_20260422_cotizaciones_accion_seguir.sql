START TRANSACTION;

SET @quotes_next_action_note := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'quotes'
      AND COLUMN_NAME = 'next_action_note'
);
SET @sql := IF(
    @quotes_next_action_note = 0,
    'ALTER TABLE quotes ADD COLUMN next_action_note TEXT NULL AFTER next_action_date;',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
