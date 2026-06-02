START TRANSACTION;

SET @col_pc_receipts_document_path := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'petty_cash_receipts' AND COLUMN_NAME = 'document_path'
);
SET @sql := IF(@col_pc_receipts_document_path = 0, "ALTER TABLE petty_cash_receipts ADD COLUMN document_path VARCHAR(255) NULL AFTER total_amount;", 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_pc_receipts_document_original_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'petty_cash_receipts' AND COLUMN_NAME = 'document_original_name'
);
SET @sql := IF(@col_pc_receipts_document_original_name = 0, "ALTER TABLE petty_cash_receipts ADD COLUMN document_original_name VARCHAR(255) NULL AFTER document_path;", 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_pc_receipts_document_mime_type := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'petty_cash_receipts' AND COLUMN_NAME = 'document_mime_type'
);
SET @sql := IF(@col_pc_receipts_document_mime_type = 0, "ALTER TABLE petty_cash_receipts ADD COLUMN document_mime_type VARCHAR(100) NULL AFTER document_original_name;", 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_pc_receipts_document_size := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'petty_cash_receipts' AND COLUMN_NAME = 'document_size'
);
SET @sql := IF(@col_pc_receipts_document_size = 0, "ALTER TABLE petty_cash_receipts ADD COLUMN document_size INT NULL AFTER document_mime_type;", 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

COMMIT;
