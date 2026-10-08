-- ==========================================================
-- ACTUALIZACIÓN: ELIMINACIÓN DEL MÓDULO DE SERVICIOS
-- FECHA: 2026-09-04
-- ==========================================================

-- 1. Eliminar permisos asignados al módulo de servicios
DELETE FROM role_permissions 
WHERE permission_key IN ('services', 'services_view', 'services_edit', 'services_delete');

-- 2. Limpiar referencias en cotizaciones y facturas si existen
UPDATE quotes SET service_id = NULL WHERE service_id IS NOT NULL;
UPDATE invoices SET service_id = NULL WHERE service_id IS NOT NULL;

-- 3. Si existe tabla service_renewals que apunte a services
UPDATE service_renewals SET service_id = NULL WHERE service_id IS NOT NULL;

-- 4. Eliminar tabla de servicios si no se requiere
DROP TABLE IF EXISTS services;
