ALTER TABLE purchase_items
    ADD COLUMN petty_cash_product_id INT NULL AFTER product_id,
    ADD CONSTRAINT fk_purchase_items_petty_cash_product
        FOREIGN KEY (petty_cash_product_id) REFERENCES petty_cash_products(id);
