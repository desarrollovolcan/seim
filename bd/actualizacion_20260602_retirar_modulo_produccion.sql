START TRANSACTION;

DELETE FROM role_permissions
WHERE permission_key IN (
    'production_view',
    'production_edit',
    'produced_products_view',
    'produced_products_edit'
);


DROP TABLE IF EXISTS production_expenses;
DROP TABLE IF EXISTS production_outputs;
DROP TABLE IF EXISTS production_inputs;
DROP TABLE IF EXISTS production_orders;
DROP TABLE IF EXISTS produced_product_materials;

COMMIT;
