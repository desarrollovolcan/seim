USE gocreative_ges;

SET @users_signature_image_path := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'signature_image_path'
);
SET @sql := IF(
    @users_signature_image_path = 0,
    'ALTER TABLE users ADD COLUMN signature_image_path VARCHAR(255) NULL AFTER signature;',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
