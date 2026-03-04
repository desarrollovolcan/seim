ALTER TABLE petty_cash_products
    ADD COLUMN classification ENUM('producto', 'servicio') NOT NULL DEFAULT 'servicio' AFTER name;
