-- ==========================================================
-- ACTUALIZACIÓN: ELIMINAR EMPRESA 'ACQUAPERLA SPA' Y DATOS ASOCIADOS
-- FECHA: 2026-09-04
-- ==========================================================

SET @target_company_id := (SELECT id FROM companies WHERE name LIKE '%Acquaperla%' LIMIT 1);

-- Procedimiento anónimo / Bloque de eliminación condicional
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Desasociar usuarios que tengan asignada esta empresa
UPDATE users SET company_id = NULL WHERE company_id = @target_company_id;
DELETE FROM user_companies WHERE company_id = @target_company_id;

-- 2. Ventas, POS, Pagos y Facturación
DELETE FROM sale_payments WHERE sale_id IN (SELECT id FROM sales WHERE company_id = @target_company_id);
DELETE FROM sale_items WHERE sale_id IN (SELECT id FROM sales WHERE company_id = @target_company_id);
DELETE FROM pos_session_withdrawals WHERE company_id = @target_company_id;
DELETE FROM pos_sessions WHERE company_id = @target_company_id;
DELETE FROM sales WHERE company_id = @target_company_id;

DELETE FROM invoice_items WHERE invoice_id IN (SELECT id FROM invoices WHERE company_id = @target_company_id);
DELETE FROM invoice_comments WHERE invoice_id IN (SELECT id FROM invoices WHERE company_id = @target_company_id);
DELETE FROM invoice_sii_logs WHERE invoice_id IN (SELECT id FROM invoices WHERE company_id = @target_company_id);
DELETE FROM payments WHERE invoice_id IN (SELECT id FROM invoices WHERE company_id = @target_company_id);
DELETE FROM invoices WHERE company_id = @target_company_id;

-- 3. Cotizaciones
DELETE FROM quote_items WHERE quote_id IN (SELECT id FROM quotes WHERE company_id = @target_company_id);
DELETE FROM quote_approvals WHERE quote_id IN (SELECT id FROM quotes WHERE company_id = @target_company_id);
DELETE FROM quotes WHERE company_id = @target_company_id;

-- 4. Productos, Familias, Subfamilias y Catálogos
DELETE FROM produced_product_materials WHERE produced_product_id IN (SELECT id FROM produced_products WHERE company_id = @target_company_id);
DELETE FROM produced_products WHERE company_id = @target_company_id;
DELETE FROM products WHERE company_id = @target_company_id;
DELETE FROM product_subfamilies WHERE company_id = @target_company_id;
DELETE FROM product_families WHERE company_id = @target_company_id;

-- 5. Clientes y Contactos
DELETE FROM client_contacts WHERE client_id IN (SELECT id FROM clients WHERE company_id = @target_company_id);
DELETE FROM clients WHERE company_id = @target_company_id;

-- 6. Proveedores y Compras
DELETE FROM purchase_items WHERE purchase_id IN (SELECT id FROM purchases WHERE company_id = @target_company_id);
DELETE FROM purchases WHERE company_id = @target_company_id;
DELETE FROM suppliers WHERE company_id = @target_company_id;

-- 7. Tesorería y Finanzas
DELETE FROM cash_movements WHERE cash_box_id IN (SELECT id FROM cash_boxes WHERE company_id = @target_company_id);
DELETE FROM cash_boxes WHERE company_id = @target_company_id;
DELETE FROM bank_movements WHERE bank_account_id IN (SELECT id FROM bank_accounts WHERE company_id = @target_company_id);
DELETE FROM bank_accounts WHERE company_id = @target_company_id;
DELETE FROM fixed_assets WHERE company_id = @target_company_id;

-- 8. Impuestos
DELETE FROM tax_withholdings WHERE company_id = @target_company_id;
DELETE FROM tax_periods WHERE company_id = @target_company_id;

-- 9. Recursos Humanos (RRHH)
DELETE FROM hr_payrolls WHERE company_id = @target_company_id;
DELETE FROM hr_attendance WHERE company_id = @target_company_id;
DELETE FROM hr_contracts WHERE company_id = @target_company_id;
DELETE FROM hr_employees WHERE company_id = @target_company_id;
DELETE FROM hr_departments WHERE company_id = @target_company_id;
DELETE FROM hr_positions WHERE company_id = @target_company_id;
DELETE FROM hr_work_schedules WHERE company_id = @target_company_id;
DELETE FROM hr_contract_types WHERE company_id = @target_company_id;
DELETE FROM hr_pension_funds WHERE company_id = @target_company_id;
DELETE FROM hr_health_providers WHERE company_id = @target_company_id;
DELETE FROM hr_payroll_items WHERE company_id = @target_company_id;

-- 10. Tickets, Documentos, Calendario y Notificaciones
DELETE FROM ticket_attachments WHERE message_id IN (SELECT id FROM ticket_messages WHERE ticket_id IN (SELECT id FROM support_tickets WHERE company_id = @target_company_id));
DELETE FROM ticket_messages WHERE ticket_id IN (SELECT id FROM support_tickets WHERE company_id = @target_company_id);
DELETE FROM support_tickets WHERE company_id = @target_company_id;
DELETE FROM documents WHERE company_id = @target_company_id;
DELETE FROM document_categories WHERE company_id = @target_company_id;
DELETE FROM calendar_events WHERE company_id = @target_company_id;
DELETE FROM notifications WHERE company_id = @target_company_id;
DELETE FROM email_logs WHERE company_id = @target_company_id;
DELETE FROM email_queue WHERE company_id = @target_company_id;

-- 11. CRM, Servicios y Configuración
DELETE FROM commercial_briefs WHERE company_id = @target_company_id;
DELETE FROM sales_orders WHERE company_id = @target_company_id;
DELETE FROM service_renewals WHERE company_id = @target_company_id;
DELETE FROM settings WHERE company_id = @target_company_id;

-- 12. Eliminar la Empresa
DELETE FROM companies WHERE id = @target_company_id;

SET FOREIGN_KEY_CHECKS = 1;
