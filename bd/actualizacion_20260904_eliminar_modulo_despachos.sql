-- ====================================================================
-- Migración: Eliminar Módulo Despacho de Camiones (sales/dispatches)
-- Fecha: 2026-09-04
-- ====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Eliminar tablas relacionadas
DROP TABLE IF EXISTS `sales_dispatch_items`;
DROP TABLE IF EXISTS `sales_dispatches`;

-- 2. Eliminar permisos de despacho de ventas
DELETE FROM `role_permissions` WHERE `permission_id` IN (
    SELECT `id` FROM `permissions` WHERE `name` LIKE 'sales_dispatches%'
);
DELETE FROM `permissions` WHERE `name` LIKE 'sales_dispatches%';

SET FOREIGN_KEY_CHECKS = 1;
