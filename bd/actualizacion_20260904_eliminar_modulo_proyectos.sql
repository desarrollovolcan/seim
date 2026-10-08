-- ====================================================================
-- Migración: Eliminar Módulo Proyectos completamente
-- Fecha: 2026-09-04
-- ====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Eliminar tablas relacionadas
DROP TABLE IF EXISTS `project_tasks`;
DROP TABLE IF EXISTS `projects`;

-- 2. Eliminar permisos de proyectos
DELETE FROM `role_permissions` WHERE `permission_id` IN (
    SELECT `id` FROM `permissions` WHERE `name` LIKE 'projects%'
);
DELETE FROM `permissions` WHERE `name` LIKE 'projects%';

SET FOREIGN_KEY_CHECKS = 1;
