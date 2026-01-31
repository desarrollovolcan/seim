START TRANSACTION;

SET @companies_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'companies' AND COLUMN_NAME = 'giro'
);
SET @sql := IF(@companies_giro = 0, 'ALTER TABLE companies ADD COLUMN giro VARCHAR(150) NULL AFTER address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


SET @companies_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'companies' AND COLUMN_NAME = 'commune'
);
SET @sql := IF(@companies_commune = 0, 'ALTER TABLE companies ADD COLUMN commune VARCHAR(120) NULL AFTER giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


SET @company_id_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'service_types'
      AND COLUMN_NAME = 'company_id'
);

SET @sql := IF(
    @company_id_exists = 0,
    'ALTER TABLE service_types ADD COLUMN company_id INT NULL AFTER id;',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE service_types
SET company_id = (SELECT id FROM companies ORDER BY id LIMIT 1)
WHERE company_id IS NULL;

SET @fk_service_types_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'service_types'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
      AND CONSTRAINT_NAME = 'fk_service_types_company'
);

SET @sql := IF(
    @fk_service_types_exists = 0,
    'ALTER TABLE service_types MODIFY company_id INT NOT NULL, ADD CONSTRAINT fk_service_types_company FOREIGN KEY (company_id) REFERENCES companies(id);',
    'ALTER TABLE service_types MODIFY company_id INT NOT NULL;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @company_id_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'system_services'
      AND COLUMN_NAME = 'company_id'
);

SET @sql := IF(
    @company_id_exists = 0,
    'ALTER TABLE system_services ADD COLUMN company_id INT NULL AFTER id;',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE system_services
SET company_id = (SELECT id FROM companies ORDER BY id LIMIT 1)
WHERE company_id IS NULL;

SET @fk_system_services_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'system_services'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
      AND CONSTRAINT_NAME = 'fk_system_services_company'
);

SET @sql := IF(
    @fk_system_services_exists = 0,
    'ALTER TABLE system_services MODIFY company_id INT NOT NULL, ADD CONSTRAINT fk_system_services_company FOREIGN KEY (company_id) REFERENCES companies(id);',
    'ALTER TABLE system_services MODIFY company_id INT NOT NULL;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS commercial_briefs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    contact_name VARCHAR(150) NULL,
    contact_email VARCHAR(150) NULL,
    contact_phone VARCHAR(50) NULL,
    service_summary VARCHAR(150) NULL,
    expected_budget DECIMAL(12,2) NULL,
    desired_start_date DATE NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'nuevo',
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

CREATE TABLE IF NOT EXISTS sales_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    brief_id INT NULL,
    order_number VARCHAR(50) NOT NULL,
    order_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    total DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (brief_id) REFERENCES commercial_briefs(id)
);

CREATE TABLE IF NOT EXISTS service_renewals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    service_id INT NULL,
    renewal_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    reminder_days INT NOT NULL DEFAULT 15,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    contact_name VARCHAR(150) NULL,
    tax_id VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    address VARCHAR(255) NULL,
    giro VARCHAR(150) NULL,
    commune VARCHAR(120) NULL,
    website VARCHAR(150) NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

SET @suppliers_contact_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'contact_name'
);
SET @sql := IF(@suppliers_contact_name = 0, 'ALTER TABLE suppliers ADD COLUMN contact_name VARCHAR(150) NULL AFTER name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @suppliers_tax_id := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'tax_id'
);
SET @sql := IF(@suppliers_tax_id = 0, 'ALTER TABLE suppliers ADD COLUMN tax_id VARCHAR(50) NULL AFTER contact_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @suppliers_website := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'website'
);
SET @sql := IF(@suppliers_website = 0, 'ALTER TABLE suppliers ADD COLUMN website VARCHAR(150) NULL AFTER address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @suppliers_notes := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'notes'
);
SET @sql := IF(@suppliers_notes = 0, 'ALTER TABLE suppliers ADD COLUMN notes TEXT NULL AFTER website;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @suppliers_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'giro'
);
SET @sql := IF(@suppliers_giro = 0, 'ALTER TABLE suppliers ADD COLUMN giro VARCHAR(150) NULL AFTER address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


SET @suppliers_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'commune'
);
SET @sql := IF(@suppliers_commune = 0, 'ALTER TABLE suppliers ADD COLUMN commune VARCHAR(120) NULL AFTER giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


CREATE TABLE IF NOT EXISTS product_families (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS product_subfamilies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    family_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (family_id) REFERENCES product_families(id)
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    supplier_id INT NULL,
    family_id INT NULL,
    subfamily_id INT NULL,
    name VARCHAR(150) NOT NULL,
    sku VARCHAR(100) NULL,
    description TEXT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    stock_min INT NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

CREATE TABLE IF NOT EXISTS produced_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    sku VARCHAR(100) NULL,
    description TEXT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    stock_min INT NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS produced_product_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produced_product_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(12,2) NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (produced_product_id) REFERENCES produced_products(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    supplier_id INT NOT NULL,
    reference VARCHAR(100) NULL,
    purchase_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    tax DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

CREATE TABLE IF NOT EXISTS purchase_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

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

CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NULL,
    pos_session_id INT NULL,
    channel VARCHAR(20) NOT NULL DEFAULT 'venta',
    numero VARCHAR(50) NOT NULL,
    sale_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pagado',
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    tax DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (pos_session_id) REFERENCES pos_sessions(id)
);

CREATE TABLE IF NOT EXISTS sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NULL,
    produced_product_id INT NULL,
    service_id INT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (produced_product_id) REFERENCES produced_products(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE IF NOT EXISTS sale_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    method VARCHAR(50) NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id)
);

SET @idx_products_company := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND INDEX_NAME = 'idx_products_company'
);
SET @sql := IF(@idx_products_company = 0, 'CREATE INDEX idx_products_company ON products(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_products_supplier := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND INDEX_NAME = 'idx_products_supplier'
);
SET @sql := IF(@idx_products_supplier = 0, 'CREATE INDEX idx_products_supplier ON products(supplier_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @family_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'products'
      AND COLUMN_NAME = 'family_id'
);
SET @sql := IF(@family_exists = 0, 'ALTER TABLE products ADD COLUMN family_id INT NULL AFTER supplier_id;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @subfamily_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'products'
      AND COLUMN_NAME = 'subfamily_id'
);
SET @sql := IF(@subfamily_exists = 0, 'ALTER TABLE products ADD COLUMN subfamily_id INT NULL AFTER family_id;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_purchases_company := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND INDEX_NAME = 'idx_purchases_company'
);
SET @sql := IF(@idx_purchases_company = 0, 'CREATE INDEX idx_purchases_company ON purchases(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_sales_company := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND INDEX_NAME = 'idx_sales_company'
);
SET @sql := IF(@idx_sales_company = 0, 'CREATE INDEX idx_sales_company ON sales(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_product_families_company := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_families' AND INDEX_NAME = 'idx_product_families_company'
);
SET @sql := IF(@idx_product_families_company = 0, 'CREATE INDEX idx_product_families_company ON product_families(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_product_subfamilies_company := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_subfamilies' AND INDEX_NAME = 'idx_product_subfamilies_company'
);
SET @sql := IF(@idx_product_subfamilies_company = 0, 'CREATE INDEX idx_product_subfamilies_company ON product_subfamilies(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_pos_sessions_company_user := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pos_sessions' AND INDEX_NAME = 'idx_pos_sessions_company_user'
);
SET @sql := IF(@idx_pos_sessions_company_user = 0, 'CREATE INDEX idx_pos_sessions_company_user ON pos_sessions(company_id, user_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sale_pos_col := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'pos_session_id'
);
SET @sql := IF(@sale_pos_col = 0, 'ALTER TABLE sales ADD COLUMN pos_session_id INT NULL AFTER client_id, ADD CONSTRAINT fk_sales_pos_session FOREIGN KEY (pos_session_id) REFERENCES pos_sessions(id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sale_items_service_col := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sale_items' AND COLUMN_NAME = 'service_id'
);
SET @sql := IF(@sale_items_service_col = 0, 'ALTER TABLE sale_items ADD COLUMN service_id INT NULL AFTER product_id, MODIFY product_id INT NULL, ADD CONSTRAINT fk_sale_items_service FOREIGN KEY (service_id) REFERENCES services(id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sale_items_produced_col := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sale_items' AND COLUMN_NAME = 'produced_product_id'
);
SET @sql := IF(@sale_items_produced_col = 0, 'ALTER TABLE sale_items ADD COLUMN produced_product_id INT NULL AFTER product_id, ADD CONSTRAINT fk_sale_items_produced FOREIGN KEY (produced_product_id) REFERENCES produced_products(id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sale_items_product_col := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sale_items' AND COLUMN_NAME = 'product_id'
);
SET @sql := IF(@sale_items_product_col = 1, 'ALTER TABLE sale_items MODIFY product_id INT NULL;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @production_outputs_produced_col := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_outputs' AND COLUMN_NAME = 'produced_product_id'
);
SET @sql := IF(@production_outputs_produced_col = 0, 'ALTER TABLE production_outputs ADD COLUMN produced_product_id INT NULL AFTER production_id, ADD CONSTRAINT fk_production_outputs_produced FOREIGN KEY (produced_product_id) REFERENCES produced_products(id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @production_outputs_product_col := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_outputs' AND COLUMN_NAME = 'product_id'
);
SET @sql := IF(@production_outputs_product_col = 1, 'ALTER TABLE production_outputs MODIFY product_id INT NULL;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS hr_departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_contract_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    max_duration_months INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_health_providers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    provider_type VARCHAR(20) NOT NULL DEFAULT 'fonasa',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_pension_funds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_work_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    weekly_hours INT NOT NULL DEFAULT 45,
    start_time TIME NULL,
    end_time TIME NULL,
    lunch_break_minutes INT NOT NULL DEFAULT 60,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_payroll_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    item_type VARCHAR(20) NOT NULL DEFAULT 'haber',
    taxable TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    department_id INT NULL,
    position_id INT NULL,
    health_provider_id INT NULL,
    pension_fund_id INT NULL,
    rut VARCHAR(50) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    nationality VARCHAR(100) NULL,
    birth_date DATE NULL,
    civil_status VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    address VARCHAR(255) NULL,
    hire_date DATE NOT NULL,
    termination_date DATE NULL,
    health_provider VARCHAR(100) NULL,
    health_plan VARCHAR(150) NULL,
    pension_fund VARCHAR(100) NULL,
    pension_rate DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    health_rate DECIMAL(5,2) NOT NULL DEFAULT 7.00,
    unemployment_rate DECIMAL(5,2) NOT NULL DEFAULT 0.60,
    dependents_count INT NOT NULL DEFAULT 0,
    payment_method VARCHAR(50) NULL,
    bank_name VARCHAR(100) NULL,
    bank_account_type VARCHAR(50) NULL,
    bank_account_number VARCHAR(50) NULL,
    qr_token VARCHAR(100) NULL,
    face_descriptor TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (department_id) REFERENCES hr_departments(id),
    FOREIGN KEY (position_id) REFERENCES hr_positions(id),
    FOREIGN KEY (health_provider_id) REFERENCES hr_health_providers(id),
    FOREIGN KEY (pension_fund_id) REFERENCES hr_pension_funds(id)
);

SET @hr_employees_health_provider_id := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'health_provider_id'
);
SET @sql := IF(@hr_employees_health_provider_id = 0, 'ALTER TABLE hr_employees ADD COLUMN health_provider_id INT NULL AFTER position_id;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_pension_fund_id := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'pension_fund_id'
);
SET @sql := IF(@hr_employees_pension_fund_id = 0, 'ALTER TABLE hr_employees ADD COLUMN pension_fund_id INT NULL AFTER health_provider_id;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_qr_token := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'qr_token'
);
SET @sql := IF(@hr_employees_qr_token = 0, 'ALTER TABLE hr_employees ADD COLUMN qr_token VARCHAR(100) NULL AFTER bank_account_number;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_face_descriptor := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'face_descriptor'
);
SET @sql := IF(@hr_employees_face_descriptor = 0, 'ALTER TABLE hr_employees ADD COLUMN face_descriptor TEXT NULL AFTER qr_token;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_nationality := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'nationality'
);
SET @sql := IF(@hr_employees_nationality = 0, 'ALTER TABLE hr_employees ADD COLUMN nationality VARCHAR(100) NULL AFTER last_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_birth_date := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'birth_date'
);
SET @sql := IF(@hr_employees_birth_date = 0, 'ALTER TABLE hr_employees ADD COLUMN birth_date DATE NULL AFTER nationality;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_civil_status := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'civil_status'
);
SET @sql := IF(@hr_employees_civil_status = 0, 'ALTER TABLE hr_employees ADD COLUMN civil_status VARCHAR(50) NULL AFTER birth_date;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_health_provider := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'health_provider'
);
SET @sql := IF(@hr_employees_health_provider = 0, 'ALTER TABLE hr_employees ADD COLUMN health_provider VARCHAR(100) NULL AFTER termination_date;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_health_plan := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'health_plan'
);
SET @sql := IF(@hr_employees_health_plan = 0, 'ALTER TABLE hr_employees ADD COLUMN health_plan VARCHAR(150) NULL AFTER health_provider;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_pension_fund := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'pension_fund'
);
SET @sql := IF(@hr_employees_pension_fund = 0, 'ALTER TABLE hr_employees ADD COLUMN pension_fund VARCHAR(100) NULL AFTER health_plan;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_pension_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'pension_rate'
);
SET @sql := IF(@hr_employees_pension_rate = 0, 'ALTER TABLE hr_employees ADD COLUMN pension_rate DECIMAL(5,2) NOT NULL DEFAULT 10.00 AFTER pension_fund;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_health_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'health_rate'
);
SET @sql := IF(@hr_employees_health_rate = 0, 'ALTER TABLE hr_employees ADD COLUMN health_rate DECIMAL(5,2) NOT NULL DEFAULT 7.00 AFTER pension_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_unemployment_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'unemployment_rate'
);
SET @sql := IF(@hr_employees_unemployment_rate = 0, 'ALTER TABLE hr_employees ADD COLUMN unemployment_rate DECIMAL(5,2) NOT NULL DEFAULT 0.60 AFTER health_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_dependents := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'dependents_count'
);
SET @sql := IF(@hr_employees_dependents = 0, 'ALTER TABLE hr_employees ADD COLUMN dependents_count INT NOT NULL DEFAULT 0 AFTER unemployment_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_payment_method := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'payment_method'
);
SET @sql := IF(@hr_employees_payment_method = 0, 'ALTER TABLE hr_employees ADD COLUMN payment_method VARCHAR(50) NULL AFTER dependents_count;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_bank_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'bank_name'
);
SET @sql := IF(@hr_employees_bank_name = 0, 'ALTER TABLE hr_employees ADD COLUMN bank_name VARCHAR(100) NULL AFTER payment_method;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_bank_account_type := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'bank_account_type'
);
SET @sql := IF(@hr_employees_bank_account_type = 0, 'ALTER TABLE hr_employees ADD COLUMN bank_account_type VARCHAR(50) NULL AFTER bank_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_bank_account_number := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'bank_account_number'
);
SET @sql := IF(@hr_employees_bank_account_number = 0, 'ALTER TABLE hr_employees ADD COLUMN bank_account_number VARCHAR(50) NULL AFTER bank_account_type;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS hr_contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    employee_id INT NOT NULL,
    contract_type_id INT NULL,
    department_id INT NULL,
    position_id INT NULL,
    schedule_id INT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    salary DECIMAL(12,2) NOT NULL,
    weekly_hours INT NOT NULL DEFAULT 45,
    status VARCHAR(20) NOT NULL DEFAULT 'vigente',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id),
    FOREIGN KEY (contract_type_id) REFERENCES hr_contract_types(id),
    FOREIGN KEY (department_id) REFERENCES hr_departments(id),
    FOREIGN KEY (position_id) REFERENCES hr_positions(id),
    FOREIGN KEY (schedule_id) REFERENCES hr_work_schedules(id)
);

CREATE TABLE IF NOT EXISTS hr_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    employee_id INT NOT NULL,
    date DATE NOT NULL,
    check_in TIME NULL,
    check_out TIME NULL,
    worked_hours DECIMAL(5,2) NULL,
    overtime_hours DECIMAL(5,2) NOT NULL DEFAULT 0,
    absence_type VARCHAR(100) NULL,
    notes VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id)
);

CREATE TABLE IF NOT EXISTS hr_payrolls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    employee_id INT NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    base_salary DECIMAL(12,2) NOT NULL,
    bonuses DECIMAL(12,2) NOT NULL DEFAULT 0,
    other_earnings DECIMAL(12,2) NOT NULL DEFAULT 0,
    other_deductions DECIMAL(12,2) NOT NULL DEFAULT 0,
    taxable_income DECIMAL(12,2) NOT NULL DEFAULT 0,
    pension_deduction DECIMAL(12,2) NOT NULL DEFAULT 0,
    health_deduction DECIMAL(12,2) NOT NULL DEFAULT 0,
    unemployment_deduction DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_deductions DECIMAL(12,2) NOT NULL DEFAULT 0,
    net_pay DECIMAL(12,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'borrador',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id)
);

SET @hr_payrolls_other_earnings := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'other_earnings'
);
SET @sql := IF(@hr_payrolls_other_earnings = 0, 'ALTER TABLE hr_payrolls ADD COLUMN other_earnings DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER bonuses;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_payrolls_other_deductions := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'other_deductions'
);
SET @sql := IF(@hr_payrolls_other_deductions = 0, 'ALTER TABLE hr_payrolls ADD COLUMN other_deductions DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER other_earnings;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_payrolls_taxable_income := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'taxable_income'
);
SET @sql := IF(@hr_payrolls_taxable_income = 0, 'ALTER TABLE hr_payrolls ADD COLUMN taxable_income DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER other_deductions;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_payrolls_pension_deduction := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'pension_deduction'
);
SET @sql := IF(@hr_payrolls_pension_deduction = 0, 'ALTER TABLE hr_payrolls ADD COLUMN pension_deduction DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER taxable_income;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_payrolls_health_deduction := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'health_deduction'
);
SET @sql := IF(@hr_payrolls_health_deduction = 0, 'ALTER TABLE hr_payrolls ADD COLUMN health_deduction DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER pension_deduction;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_payrolls_unemployment_deduction := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'unemployment_deduction'
);
SET @sql := IF(@hr_payrolls_unemployment_deduction = 0, 'ALTER TABLE hr_payrolls ADD COLUMN unemployment_deduction DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER health_deduction;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_payrolls_total_deductions := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'total_deductions'
);
SET @sql := IF(@hr_payrolls_total_deductions = 0, 'ALTER TABLE hr_payrolls ADD COLUMN total_deductions DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER unemployment_deduction;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS hr_payroll_lines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payroll_id INT NOT NULL,
    payroll_item_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (payroll_id) REFERENCES hr_payrolls(id),
    FOREIGN KEY (payroll_item_id) REFERENCES hr_payroll_items(id)
);

CREATE TABLE IF NOT EXISTS accounting_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(150) NOT NULL,
    type VARCHAR(30) NOT NULL,
    level INT NOT NULL DEFAULT 1,
    parent_id INT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (parent_id) REFERENCES accounting_accounts(id)
);

CREATE TABLE IF NOT EXISTS accounting_periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    period VARCHAR(20) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'abierto',
    closed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS accounting_journals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    entry_number VARCHAR(50) NOT NULL,
    entry_date DATE NOT NULL,
    description VARCHAR(255) NULL,
    source VARCHAR(20) NOT NULL DEFAULT 'manual',
    status VARCHAR(20) NOT NULL DEFAULT 'borrador',
    created_by INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS accounting_journal_lines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journal_id INT NOT NULL,
    account_id INT NOT NULL,
    line_description VARCHAR(255) NULL,
    debit DECIMAL(12,2) NOT NULL DEFAULT 0,
    credit DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (journal_id) REFERENCES accounting_journals(id),
    FOREIGN KEY (account_id) REFERENCES accounting_accounts(id)
);

CREATE TABLE IF NOT EXISTS tax_periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    period VARCHAR(20) NOT NULL,
    iva_debito DECIMAL(12,2) NOT NULL DEFAULT 0,
    iva_credito DECIMAL(12,2) NOT NULL DEFAULT 0,
    remanente DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_retenciones DECIMAL(12,2) NOT NULL DEFAULT 0,
    impuesto_unico DECIMAL(12,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS tax_withholdings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    period_id INT NULL,
    type VARCHAR(50) NOT NULL,
    base_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    rate DECIMAL(5,2) NOT NULL DEFAULT 0,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (period_id) REFERENCES tax_periods(id)
);

CREATE TABLE IF NOT EXISTS honorarios_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    provider_name VARCHAR(150) NOT NULL,
    provider_rut VARCHAR(50) NULL,
    document_number VARCHAR(50) NOT NULL,
    issue_date DATE NOT NULL,
    gross_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    retention_rate DECIMAL(5,2) NOT NULL DEFAULT 13,
    retention_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    net_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    paid_at DATE NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS fixed_assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NULL,
    acquisition_date DATE NOT NULL,
    acquisition_value DECIMAL(12,2) NOT NULL DEFAULT 0,
    depreciation_method VARCHAR(30) NOT NULL DEFAULT 'linea_recta',
    useful_life_months INT NOT NULL DEFAULT 0,
    accumulated_depreciation DECIMAL(12,2) NOT NULL DEFAULT 0,
    book_value DECIMAL(12,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS bank_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    bank_name VARCHAR(150) NULL,
    account_number VARCHAR(80) NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    current_balance DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS bank_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    bank_account_id INT NOT NULL,
    transaction_date DATE NOT NULL,
    description VARCHAR(255) NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'deposito',
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    balance DECIMAL(12,2) NOT NULL DEFAULT 0,
    reference VARCHAR(150) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id)
);

CREATE TABLE IF NOT EXISTS inventory_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    product_id INT NULL,
    produced_product_id INT NULL,
    movement_date DATE NOT NULL,
    movement_type VARCHAR(20) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    reference_type VARCHAR(50) NULL,
    reference_id INT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (produced_product_id) REFERENCES produced_products(id)
);

SET @invoices_sii_document_type := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_document_type'
);
SET @sql := IF(@invoices_sii_document_type = 0, 'ALTER TABLE invoices ADD COLUMN sii_document_type VARCHAR(50) NULL AFTER total;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_document_number := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_document_number'
);
SET @sql := IF(@invoices_sii_document_number = 0, 'ALTER TABLE invoices ADD COLUMN sii_document_number VARCHAR(50) NULL AFTER sii_document_type;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_receiver_rut := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_receiver_rut'
);
SET @sql := IF(@invoices_sii_receiver_rut = 0, 'ALTER TABLE invoices ADD COLUMN sii_receiver_rut VARCHAR(50) NULL AFTER sii_document_number;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_receiver_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_receiver_name'
);
SET @sql := IF(@invoices_sii_receiver_name = 0, 'ALTER TABLE invoices ADD COLUMN sii_receiver_name VARCHAR(150) NULL AFTER sii_receiver_rut;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_receiver_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_receiver_giro'
);
SET @sql := IF(@invoices_sii_receiver_giro = 0, 'ALTER TABLE invoices ADD COLUMN sii_receiver_giro VARCHAR(150) NULL AFTER sii_receiver_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


SET @invoices_sii_receiver_address := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_receiver_address'
);
SET @sql := IF(@invoices_sii_receiver_address = 0, 'ALTER TABLE invoices ADD COLUMN sii_receiver_address VARCHAR(255) NULL AFTER sii_receiver_giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_receiver_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_receiver_commune'
);
SET @sql := IF(@invoices_sii_receiver_commune = 0, 'ALTER TABLE invoices ADD COLUMN sii_receiver_commune VARCHAR(100) NULL AFTER sii_receiver_address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


SET @invoices_sii_tax_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_tax_rate'
);
SET @sql := IF(@invoices_sii_tax_rate = 0, 'ALTER TABLE invoices ADD COLUMN sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19 AFTER sii_receiver_commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_exempt_amount := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_exempt_amount'
);
SET @sql := IF(@invoices_sii_exempt_amount = 0, 'ALTER TABLE invoices ADD COLUMN sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER sii_tax_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_document_type := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_document_type'
);
SET @sql := IF(@quotes_sii_document_type = 0, 'ALTER TABLE quotes ADD COLUMN sii_document_type VARCHAR(50) NULL AFTER total;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_document_number := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_document_number'
);
SET @sql := IF(@quotes_sii_document_number = 0, 'ALTER TABLE quotes ADD COLUMN sii_document_number VARCHAR(50) NULL AFTER sii_document_type;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_receiver_rut := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_receiver_rut'
);
SET @sql := IF(@quotes_sii_receiver_rut = 0, 'ALTER TABLE quotes ADD COLUMN sii_receiver_rut VARCHAR(50) NULL AFTER sii_document_number;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_receiver_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_receiver_name'
);
SET @sql := IF(@quotes_sii_receiver_name = 0, 'ALTER TABLE quotes ADD COLUMN sii_receiver_name VARCHAR(150) NULL AFTER sii_receiver_rut;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_receiver_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_receiver_giro'
);
SET @sql := IF(@quotes_sii_receiver_giro = 0, 'ALTER TABLE quotes ADD COLUMN sii_receiver_giro VARCHAR(150) NULL AFTER sii_receiver_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


SET @quotes_sii_receiver_address := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_receiver_address'
);
SET @sql := IF(@quotes_sii_receiver_address = 0, 'ALTER TABLE quotes ADD COLUMN sii_receiver_address VARCHAR(255) NULL AFTER sii_receiver_giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_receiver_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_receiver_commune'
);
SET @sql := IF(@quotes_sii_receiver_commune = 0, 'ALTER TABLE quotes ADD COLUMN sii_receiver_commune VARCHAR(100) NULL AFTER sii_receiver_address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


SET @quotes_sii_tax_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_tax_rate'
);
SET @sql := IF(@quotes_sii_tax_rate = 0, 'ALTER TABLE quotes ADD COLUMN sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19 AFTER sii_receiver_commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_exempt_amount := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_exempt_amount'
);
SET @sql := IF(@quotes_sii_exempt_amount = 0, 'ALTER TABLE quotes ADD COLUMN sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER sii_tax_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_document_type := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_document_type'
);
SET @sql := IF(@purchases_sii_document_type = 0, 'ALTER TABLE purchases ADD COLUMN sii_document_type VARCHAR(50) NULL AFTER total;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_document_number := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_document_number'
);
SET @sql := IF(@purchases_sii_document_number = 0, 'ALTER TABLE purchases ADD COLUMN sii_document_number VARCHAR(50) NULL AFTER sii_document_type;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_receiver_rut := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_rut'
);
SET @sql := IF(@purchases_sii_receiver_rut = 0, 'ALTER TABLE purchases ADD COLUMN sii_receiver_rut VARCHAR(50) NULL AFTER sii_document_number;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_receiver_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_name'
);
SET @sql := IF(@purchases_sii_receiver_name = 0, 'ALTER TABLE purchases ADD COLUMN sii_receiver_name VARCHAR(150) NULL AFTER sii_receiver_rut;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_receiver_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_giro'
);
SET @sql := IF(@purchases_sii_receiver_giro = 0, 'ALTER TABLE purchases ADD COLUMN sii_receiver_giro VARCHAR(150) NULL AFTER sii_receiver_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


SET @purchases_sii_receiver_address := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_address'
);
SET @sql := IF(@purchases_sii_receiver_address = 0, 'ALTER TABLE purchases ADD COLUMN sii_receiver_address VARCHAR(255) NULL AFTER sii_receiver_giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_receiver_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_commune'
);
SET @sql := IF(@purchases_sii_receiver_commune = 0, 'ALTER TABLE purchases ADD COLUMN sii_receiver_commune VARCHAR(100) NULL AFTER sii_receiver_address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


SET @purchases_sii_tax_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_tax_rate'
);
SET @sql := IF(@purchases_sii_tax_rate = 0, 'ALTER TABLE purchases ADD COLUMN sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19 AFTER sii_receiver_commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_exempt_amount := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_exempt_amount'
);
SET @sql := IF(@purchases_sii_exempt_amount = 0, 'ALTER TABLE purchases ADD COLUMN sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER sii_tax_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_document_type := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_document_type'
);
SET @sql := IF(@sales_sii_document_type = 0, 'ALTER TABLE sales ADD COLUMN sii_document_type VARCHAR(50) NULL AFTER total;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_document_number := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_document_number'
);
SET @sql := IF(@sales_sii_document_number = 0, 'ALTER TABLE sales ADD COLUMN sii_document_number VARCHAR(50) NULL AFTER sii_document_type;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_receiver_rut := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_receiver_rut'
);
SET @sql := IF(@sales_sii_receiver_rut = 0, 'ALTER TABLE sales ADD COLUMN sii_receiver_rut VARCHAR(50) NULL AFTER sii_document_number;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_receiver_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_receiver_name'
);
SET @sql := IF(@sales_sii_receiver_name = 0, 'ALTER TABLE sales ADD COLUMN sii_receiver_name VARCHAR(150) NULL AFTER sii_receiver_rut;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_receiver_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_receiver_giro'
);
SET @sql := IF(@sales_sii_receiver_giro = 0, 'ALTER TABLE sales ADD COLUMN sii_receiver_giro VARCHAR(150) NULL AFTER sii_receiver_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


SET @sales_sii_receiver_address := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_receiver_address'
);
SET @sql := IF(@sales_sii_receiver_address = 0, 'ALTER TABLE sales ADD COLUMN sii_receiver_address VARCHAR(255) NULL AFTER sii_receiver_giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_receiver_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_receiver_commune'
);
SET @sql := IF(@sales_sii_receiver_commune = 0, 'ALTER TABLE sales ADD COLUMN sii_receiver_commune VARCHAR(100) NULL AFTER sii_receiver_address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


SET @sales_sii_tax_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_tax_rate'
);
SET @sql := IF(@sales_sii_tax_rate = 0, 'ALTER TABLE sales ADD COLUMN sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19 AFTER sii_receiver_commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_exempt_amount := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_exempt_amount'
);
SET @sql := IF(@sales_sii_exempt_amount = 0, 'ALTER TABLE sales ADD COLUMN sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER sii_tax_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
