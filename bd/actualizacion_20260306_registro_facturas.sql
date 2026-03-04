CREATE TABLE IF NOT EXISTS purchase_invoice_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    document_type ENUM('factura', 'boleta', 'servicio') NOT NULL DEFAULT 'factura',
    invoice_number VARCHAR(120) NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NULL,
    supplier_name VARCHAR(180) NOT NULL,
    supplier_tax_id VARCHAR(30) NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    net_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_by INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS purchase_invoice_record_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    item_type ENUM('producto', 'servicio') NOT NULL DEFAULT 'producto',
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(12,2) NOT NULL DEFAULT 0,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    observation VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES purchase_invoice_records(id) ON DELETE CASCADE
);

CREATE INDEX idx_purchase_invoice_records_company_date ON purchase_invoice_records(company_id, invoice_date);
CREATE INDEX idx_purchase_invoice_records_supplier ON purchase_invoice_records(supplier_name);
