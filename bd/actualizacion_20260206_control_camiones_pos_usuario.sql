USE gocreative_ges;

SET @has_seller_user_id := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_dispatches' AND COLUMN_NAME = 'seller_user_id'
);
SET @sql := IF(@has_seller_user_id = 0,
    'ALTER TABLE sales_dispatches ADD COLUMN seller_user_id INT NULL AFTER seller_name',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_seller_idx := (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_dispatches' AND INDEX_NAME = 'idx_sales_dispatches_seller_user'
);
SET @sql := IF(@has_seller_idx = 0,
    'ALTER TABLE sales_dispatches ADD INDEX idx_sales_dispatches_seller_user (seller_user_id)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_seller_fk := (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_dispatches' AND CONSTRAINT_NAME = 'fk_sales_dispatches_seller_user'
);
SET @sql := IF(@has_seller_fk = 0,
    'ALTER TABLE sales_dispatches ADD CONSTRAINT fk_sales_dispatches_seller_user FOREIGN KEY (seller_user_id) REFERENCES users(id) ON DELETE SET NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE sales_dispatches sd
JOIN users u ON u.company_id = sd.company_id AND u.name = sd.seller_name
SET sd.seller_user_id = u.id
WHERE sd.seller_user_id IS NULL;

SET @has_sale_context := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pos_sessions' AND COLUMN_NAME = 'sale_context'
);
SET @sql := IF(@has_sale_context = 0,
    'ALTER TABLE pos_sessions ADD COLUMN sale_context ENUM(\'local\',\'camion\') NOT NULL DEFAULT \'local\' AFTER opening_amount',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
