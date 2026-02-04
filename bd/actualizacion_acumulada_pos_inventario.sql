START TRANSACTION;

-- Familias y subfamilias
CREATE TABLE IF NOT EXISTS product_families (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(3) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS product_subfamilies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    family_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(3) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (family_id) REFERENCES product_families(id)
);

CREATE TABLE IF NOT EXISTS competitor_companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NOT NULL,
    rut VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    address VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

SET @competitor_companies_rut := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'competitor_companies' AND COLUMN_NAME = 'rut'
);
SET @sql := IF(@competitor_companies_rut = 0, 'ALTER TABLE competitor_companies ADD COLUMN rut VARCHAR(50) NULL AFTER code;', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @competitor_companies_email := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'competitor_companies' AND COLUMN_NAME = 'email'
);
SET @sql := IF(@competitor_companies_email = 0, 'ALTER TABLE competitor_companies ADD COLUMN email VARCHAR(150) NULL AFTER rut;', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @competitor_companies_address := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'competitor_companies' AND COLUMN_NAME = 'address'
);
SET @sql := IF(@competitor_companies_address = 0, 'ALTER TABLE competitor_companies ADD COLUMN address VARCHAR(255) NULL AFTER email;', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @product_families_code := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_families' AND COLUMN_NAME = 'code'
);
SET @sql := IF(@product_families_code = 0, 'ALTER TABLE product_families ADD COLUMN code VARCHAR(3) NOT NULL DEFAULT '' AFTER name;', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @product_subfamilies_code := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_subfamilies' AND COLUMN_NAME = 'code'
);
SET @sql := IF(@product_subfamilies_code = 0, 'ALTER TABLE product_subfamilies ADD COLUMN code VARCHAR(3) NOT NULL DEFAULT '' AFTER name;', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Productos con vínculos a familias/subfamilias
SET @family_id_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'family_id'
);
SET @sql := IF(@family_id_exists = 0, 'ALTER TABLE products ADD COLUMN family_id INT NULL AFTER supplier_id;', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @subfamily_id_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'subfamily_id'
);
SET @sql := IF(@subfamily_id_exists = 0, 'ALTER TABLE products ADD COLUMN subfamily_id INT NULL AFTER family_id;', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @competitor_company_id_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'competitor_company_id'
);
SET @sql := IF(@competitor_company_id_exists = 0, 'ALTER TABLE products ADD COLUMN competitor_company_id INT NULL AFTER supplier_id;', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @competition_code_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'competition_code'
);
SET @sql := IF(@competition_code_exists = 0, 'ALTER TABLE products ADD COLUMN competition_code VARCHAR(30) NULL AFTER subfamily_id;', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @supplier_code_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'supplier_code'
);
SET @sql := IF(@supplier_code_exists = 0, 'ALTER TABLE products ADD COLUMN supplier_code VARCHAR(30) NULL AFTER competition_code;', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- POS: sesiones, pagos y referencia en ventas
CREATE TABLE IF NOT EXISTS pos_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    user_id INT NOT NULL,
    opening_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    closing_amount DECIMAL(12,2) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'abierto',
    opened_at DATETIME NOT NULL,
    closed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS pos_session_withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pos_session_id INT NOT NULL,
    company_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    reason VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (pos_session_id) REFERENCES pos_sessions(id),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

SET @pos_col := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'pos_session_id'
);
SET @sql := IF(@pos_col = 0, 'ALTER TABLE sales ADD COLUMN pos_session_id INT NULL AFTER client_id, ADD CONSTRAINT fk_sales_pos_session FOREIGN KEY (pos_session_id) REFERENCES pos_sessions(id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS sale_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    method VARCHAR(50) NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id)
);

-- Ítems de venta para productos o servicios
SET @service_col := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sale_items' AND COLUMN_NAME = 'service_id'
);
SET @sql := IF(@service_col = 0, 'ALTER TABLE sale_items ADD COLUMN service_id INT NULL AFTER product_id, MODIFY product_id INT NULL, ADD CONSTRAINT fk_sale_items_service FOREIGN KEY (service_id) REFERENCES services(id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Índices
SET @idx_pf := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_families' AND INDEX_NAME = 'idx_product_families_company'
);
SET @sql := IF(@idx_pf = 0, 'CREATE INDEX idx_product_families_company ON product_families(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_psf := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_subfamilies' AND INDEX_NAME = 'idx_product_subfamilies_company'
);
SET @sql := IF(@idx_psf = 0, 'CREATE INDEX idx_product_subfamilies_company ON product_subfamilies(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_cc := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'competitor_companies' AND INDEX_NAME = 'idx_competitor_companies_company'
);
SET @sql := IF(@idx_cc = 0, 'CREATE INDEX idx_competitor_companies_company ON competitor_companies(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_pos := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pos_sessions' AND INDEX_NAME = 'idx_pos_sessions_company_user'
);
SET @sql := IF(@idx_pos = 0, 'CREATE INDEX idx_pos_sessions_company_user ON pos_sessions(company_id, user_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

COMMIT;
