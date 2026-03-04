ALTER TABLE petty_cash_products
    ADD COLUMN unit_measure VARCHAR(60) NOT NULL DEFAULT 'Unidad' AFTER category;

ALTER TABLE purchase_items
    ADD COLUMN unit_measure VARCHAR(60) NOT NULL DEFAULT 'Unidad' AFTER quantity;
