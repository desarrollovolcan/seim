START TRANSACTION;

CREATE TABLE IF NOT EXISTS petty_cash_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(180) NOT NULL,
    category VARCHAR(120) NULL,
    suggested_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_petty_cash_products_company FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS petty_cash_receipts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    receipt_number VARCHAR(100) NOT NULL,
    receipt_date DATE NOT NULL,
    supplier_name VARCHAR(180) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_by INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_petty_cash_receipts_company FOREIGN KEY (company_id) REFERENCES companies(id),
    CONSTRAINT fk_petty_cash_receipts_user FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS petty_cash_receipt_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_id INT NOT NULL,
    product_id INT NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(12,2) NOT NULL DEFAULT 0,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    observation VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_petty_cash_items_receipt FOREIGN KEY (receipt_id) REFERENCES petty_cash_receipts(id) ON DELETE CASCADE,
    CONSTRAINT fk_petty_cash_items_product FOREIGN KEY (product_id) REFERENCES petty_cash_products(id)
);

SET @idx_pc_products_company := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'petty_cash_products' AND INDEX_NAME = 'idx_petty_cash_products_company'
);
SET @sql := IF(@idx_pc_products_company = 0, 'CREATE INDEX idx_petty_cash_products_company ON petty_cash_products(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_pc_receipts_company_date := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'petty_cash_receipts' AND INDEX_NAME = 'idx_petty_cash_receipts_company_date'
);
SET @sql := IF(@idx_pc_receipts_company_date = 0, 'CREATE INDEX idx_petty_cash_receipts_company_date ON petty_cash_receipts(company_id, receipt_date);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_pc_receipts_supplier := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'petty_cash_receipts' AND INDEX_NAME = 'idx_petty_cash_receipts_supplier'
);
SET @sql := IF(@idx_pc_receipts_supplier = 0, 'CREATE INDEX idx_petty_cash_receipts_supplier ON petty_cash_receipts(supplier_name);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_pc_items_receipt := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'petty_cash_receipt_items' AND INDEX_NAME = 'idx_petty_cash_items_receipt'
);
SET @sql := IF(@idx_pc_items_receipt = 0, 'CREATE INDEX idx_petty_cash_items_receipt ON petty_cash_receipt_items(receipt_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

INSERT INTO role_permissions (role_id, permission_key, created_at)
SELECT rp.role_id, 'petty_cash_view', NOW()
FROM (
    SELECT DISTINCT role_id FROM role_permissions WHERE permission_key IN ('costs_view', 'purchases_view', 'purchase_orders_view', 'treasury_view')
) rp
LEFT JOIN role_permissions existing ON existing.role_id = rp.role_id AND existing.permission_key = 'petty_cash_view'
WHERE existing.id IS NULL;

INSERT INTO role_permissions (role_id, permission_key, created_at)
SELECT rp.role_id, 'petty_cash_edit', NOW()
FROM (
    SELECT DISTINCT role_id FROM role_permissions WHERE permission_key IN ('costs_edit', 'purchases_edit', 'purchase_orders_edit', 'treasury_edit')
) rp
LEFT JOIN role_permissions existing ON existing.role_id = rp.role_id AND existing.permission_key = 'petty_cash_edit'
WHERE existing.id IS NULL;

COMMIT;
