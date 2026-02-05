USE gocreative_ges;

CREATE TABLE IF NOT EXISTS sales_dispatches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    truck_code VARCHAR(80) NOT NULL,
    seller_name VARCHAR(120) NOT NULL,
    dispatch_date DATE NOT NULL,
    pos_session_id INT NULL,
    status ENUM('abierto','cerrado') NOT NULL DEFAULT 'abierto',
    notes TEXT NULL,
    cash_delivered DECIMAL(14,2) NOT NULL DEFAULT 0,
    closed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_sales_dispatches_company_date (company_id, dispatch_date),
    INDEX idx_sales_dispatches_session (pos_session_id),
    CONSTRAINT fk_sales_dispatches_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    CONSTRAINT fk_sales_dispatches_pos_session FOREIGN KEY (pos_session_id) REFERENCES pos_sessions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS sales_dispatch_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dispatch_id INT NOT NULL,
    produced_product_id INT NOT NULL,
    quantity_dispatched INT NOT NULL DEFAULT 0,
    empty_returned_total INT NOT NULL DEFAULT 0,
    empty_muy_bueno INT NOT NULL DEFAULT 0,
    empty_bueno INT NOT NULL DEFAULT 0,
    empty_aceptable INT NOT NULL DEFAULT 0,
    empty_malo INT NOT NULL DEFAULT 0,
    empty_merma INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_sales_dispatch_items_dispatch (dispatch_id),
    INDEX idx_sales_dispatch_items_product (produced_product_id),
    CONSTRAINT fk_sales_dispatch_items_dispatch FOREIGN KEY (dispatch_id) REFERENCES sales_dispatches(id) ON DELETE CASCADE,
    CONSTRAINT fk_sales_dispatch_items_product FOREIGN KEY (produced_product_id) REFERENCES produced_products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
