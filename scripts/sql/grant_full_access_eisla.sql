-- Script: Otorga acceso total a eisla@gocreative.cl
-- Uso: ejecutar en la base de datos de producción de SEIM.

START TRANSACTION;

-- 1) Validar que el usuario exista y no esté eliminado.
SELECT id, email, role_id, company_id, deleted_at
FROM users
WHERE email = 'eisla@gocreative.cl'
FOR UPDATE;

-- 2) Asegurar rol admin y asignarlo al usuario.
INSERT INTO roles (name, created_at)
SELECT 'admin', NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM roles WHERE name = 'admin'
);

UPDATE users u
JOIN roles r ON r.name = 'admin'
SET u.role_id = r.id
WHERE u.email = 'eisla@gocreative.cl'
  AND u.deleted_at IS NULL;

-- 3) Asegurar pertenencia a empresas activas (si la tabla user_companies existe).
INSERT IGNORE INTO user_companies (user_id, company_id)
SELECT u.id, c.id
FROM users u
JOIN companies c ON c.deleted_at IS NULL
WHERE u.email = 'eisla@gocreative.cl'
  AND u.deleted_at IS NULL;

-- 4) Asignar TODOS los permisos definidos al rol admin.
SET @admin_role_id := (
    SELECT id FROM roles WHERE name = 'admin' LIMIT 1
);

INSERT IGNORE INTO role_permissions (role_id, permission_key, created_at)
SELECT @admin_role_id, p.permission_key, NOW()
FROM (
    SELECT 'dashboard_view' AS permission_key UNION ALL
    SELECT 'crm_view' UNION ALL
    SELECT 'clients_view' UNION ALL
    SELECT 'clients_edit' UNION ALL
    SELECT 'tickets_view' UNION ALL
    SELECT 'tickets_edit' UNION ALL
    SELECT 'projects_view' UNION ALL
    SELECT 'projects_edit' UNION ALL
    SELECT 'documents_view' UNION ALL
    SELECT 'documents_edit' UNION ALL
    SELECT 'products_view' UNION ALL
    SELECT 'products_edit' UNION ALL
    SELECT 'produced_products_view' UNION ALL
    SELECT 'produced_products_edit' UNION ALL
    SELECT 'suppliers_view' UNION ALL
    SELECT 'suppliers_edit' UNION ALL
    SELECT 'purchases_view' UNION ALL
    SELECT 'purchases_edit' UNION ALL
    SELECT 'purchase_orders_view' UNION ALL
    SELECT 'purchase_orders_edit' UNION ALL
    SELECT 'production_view' UNION ALL
    SELECT 'production_edit' UNION ALL
    SELECT 'sales_view' UNION ALL
    SELECT 'sales_edit' UNION ALL
    SELECT 'sales_dispatches_view' UNION ALL
    SELECT 'sales_dispatches_edit' UNION ALL
    SELECT 'services_view' UNION ALL
    SELECT 'services_edit' UNION ALL
    SELECT 'quotes_view' UNION ALL
    SELECT 'quotes_edit' UNION ALL
    SELECT 'invoices_view' UNION ALL
    SELECT 'invoices_edit' UNION ALL
    SELECT 'email_templates_view' UNION ALL
    SELECT 'email_templates_edit' UNION ALL
    SELECT 'email_queue_view' UNION ALL
    SELECT 'email_queue_edit' UNION ALL
    SELECT 'settings_view' UNION ALL
    SELECT 'settings_edit' UNION ALL
    SELECT 'email_config_view' UNION ALL
    SELECT 'email_config_edit' UNION ALL
    SELECT 'online_payments_config_view' UNION ALL
    SELECT 'online_payments_config_edit' UNION ALL
    SELECT 'accounting_view' UNION ALL
    SELECT 'accounting_edit' UNION ALL
    SELECT 'taxes_view' UNION ALL
    SELECT 'taxes_edit' UNION ALL
    SELECT 'honorarios_view' UNION ALL
    SELECT 'honorarios_edit' UNION ALL
    SELECT 'fixed_assets_view' UNION ALL
    SELECT 'fixed_assets_edit' UNION ALL
    SELECT 'treasury_view' UNION ALL
    SELECT 'treasury_edit' UNION ALL
    SELECT 'petty_cash_view' UNION ALL
    SELECT 'petty_cash_edit' UNION ALL
    SELECT 'invoice_register_view' UNION ALL
    SELECT 'invoice_register_edit' UNION ALL
    SELECT 'inventory_view' UNION ALL
    SELECT 'inventory_edit' UNION ALL
    SELECT 'companies_view' UNION ALL
    SELECT 'companies_edit' UNION ALL
    SELECT 'users_view' UNION ALL
    SELECT 'users_edit' UNION ALL
    SELECT 'roles_view' UNION ALL
    SELECT 'roles_edit' UNION ALL
    SELECT 'users_companies_view' UNION ALL
    SELECT 'users_companies_edit' UNION ALL
    SELECT 'users_permissions_view' UNION ALL
    SELECT 'users_permissions_edit' UNION ALL
    SELECT 'calendar_view' UNION ALL
    SELECT 'calendar_edit' UNION ALL
    SELECT 'company_switch_view' UNION ALL
    SELECT 'company_switch_edit'
) p;

COMMIT;

-- Verificaciones finales:
SELECT u.id, u.email, u.role_id, r.name AS role_name
FROM users u
LEFT JOIN roles r ON r.id = u.role_id
WHERE u.email = 'eisla@gocreative.cl'
  AND u.deleted_at IS NULL;

SELECT rp.permission_key
FROM role_permissions rp
JOIN roles r ON r.id = rp.role_id
WHERE r.name = 'admin'
ORDER BY rp.permission_key;
