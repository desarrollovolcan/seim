ALTER TABLE purchase_items
    ADD COLUMN item_type ENUM('producto', 'servicio') NOT NULL DEFAULT 'producto' AFTER purchase_id,
    ADD COLUMN description VARCHAR(255) NULL AFTER item_type,
    MODIFY COLUMN product_id INT NULL;

UPDATE purchase_items pi
LEFT JOIN products p ON p.id = pi.product_id
SET pi.description = COALESCE(NULLIF(TRIM(p.name), ''), CONCAT('Producto #', pi.product_id))
WHERE pi.description IS NULL OR TRIM(pi.description) = '';

ALTER TABLE purchase_items
    MODIFY COLUMN description VARCHAR(255) NOT NULL;
