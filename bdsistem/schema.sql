CREATE TABLE `empresas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `razon_social` VARCHAR(180) DEFAULT NULL,
  `ruc` VARCHAR(30) DEFAULT NULL,
  `telefono` VARCHAR(30) DEFAULT NULL,
  `correo` VARCHAR(150) DEFAULT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `rut` VARCHAR(20) NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NOT NULL,
  `correo` VARCHAR(150) NOT NULL,
  `telefono` VARCHAR(30) NOT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `username` VARCHAR(60) NOT NULL,
  `rol` VARCHAR(60) DEFAULT NULL,
  `avatar_path` VARCHAR(255) DEFAULT NULL,
  `unidad_id` INT UNSIGNED DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `password_locked` TINYINT(1) NOT NULL DEFAULT 0,
  `is_superadmin` TINYINT(1) NOT NULL DEFAULT 0,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_rut_unique` (`rut`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_correo_unique` (`correo`),
  KEY `users_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `users_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `unidades` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unidades_nombre_unique` (`nombre`),
  KEY `unidades_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `unidades_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(60) NOT NULL,
  `descripcion` VARCHAR(200) DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nombre_unique` (`nombre`),
  KEY `roles_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `roles_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user_roles` (
  `user_id` INT UNSIGNED NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `role_id`),
  CONSTRAINT `user_roles_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `modulo` VARCHAR(60) NOT NULL,
  `accion` VARCHAR(30) NOT NULL,
  `descripcion` VARCHAR(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_modulo_accion_unique` (`modulo`, `accion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `role_permissions` (
  `role_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`, `permission_id`),
  CONSTRAINT `role_permissions_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_permission_id_fk` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `role_unit_permissions` (
  `role_id` INT UNSIGNED NOT NULL,
  `unidad_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`, `unidad_id`, `permission_id`),
  CONSTRAINT `role_unit_permissions_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_unit_permissions_unidad_id_fk` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_unit_permissions_permission_id_fk` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user_sessions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `session_id` VARCHAR(128) NOT NULL,
  `ip` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity` TIMESTAMP NULL DEFAULT NULL,
  `ended_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_sessions_session_unique` (`session_id`),
  CONSTRAINT `user_sessions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `audit_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `tabla` VARCHAR(60) NOT NULL,
  `accion` VARCHAR(20) NOT NULL,
  `registro_id` INT UNSIGNED DEFAULT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_idx` (`user_id`),
  CONSTRAINT `audit_logs_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `events` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `titulo` VARCHAR(150) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `ubicacion` VARCHAR(200) NOT NULL,
  `fecha_inicio` DATETIME NOT NULL,
  `fecha_fin` DATETIME NOT NULL,
  `tipo` VARCHAR(80) NOT NULL,
  `cupos` INT UNSIGNED DEFAULT NULL,
  `publico_objetivo` VARCHAR(150) DEFAULT NULL,
  `estado` ENUM('borrador', 'revision', 'publicado', 'finalizado', 'cancelado') NOT NULL DEFAULT 'borrador',
  `aprobacion_estado` ENUM('borrador', 'revision', 'publicado') NOT NULL DEFAULT 'borrador',
  `habilitado` TINYINT(1) NOT NULL DEFAULT 1,
  `unidad_id` INT UNSIGNED DEFAULT NULL,
  `creado_por` INT UNSIGNED NOT NULL,
  `encargado_id` INT UNSIGNED DEFAULT NULL,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `events_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `events_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `events_creado_por_fk` FOREIGN KEY (`creado_por`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `events_encargado_fk` FOREIGN KEY (`encargado_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `event_attachments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` INT UNSIGNED NOT NULL,
  `archivo_nombre` VARCHAR(200) NOT NULL,
  `archivo_ruta` VARCHAR(255) NOT NULL,
  `archivo_tipo` VARCHAR(50) NOT NULL,
  `subido_por` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `event_attachments_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_attachments_subido_por_fk` FOREIGN KEY (`subido_por`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `authorities` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `tipo` VARCHAR(80) NOT NULL,
  `correo` VARCHAR(150) DEFAULT NULL,
  `telefono` VARCHAR(30) DEFAULT NULL,
  `fecha_inicio` DATE NOT NULL,
  `fecha_fin` DATE DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `aprobacion_estado` ENUM('propuesta', 'validacion', 'vigente') NOT NULL DEFAULT 'propuesta',
  `unidad_id` INT UNSIGNED DEFAULT NULL,
  `group_id` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `authorities_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `authorities_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `authority_groups` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(120) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `authority_groups_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `event_authorities` (
  `event_id` INT UNSIGNED NOT NULL,
  `authority_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`, `authority_id`),
  CONSTRAINT `event_authorities_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_authorities_authority_id_fk` FOREIGN KEY (`authority_id`) REFERENCES `authorities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `authority_attachments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `authority_id` INT UNSIGNED NOT NULL,
  `archivo_nombre` VARCHAR(200) NOT NULL,
  `archivo_ruta` VARCHAR(255) NOT NULL,
  `archivo_tipo` VARCHAR(50) NOT NULL,
  `subido_por` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `authority_attachments_authority_id_fk` FOREIGN KEY (`authority_id`) REFERENCES `authorities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `authority_attachments_subido_por_fk` FOREIGN KEY (`subido_por`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `municipalidad` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `rut` VARCHAR(20) DEFAULT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `telefono` VARCHAR(30) DEFAULT NULL,
  `correo` VARCHAR(150) DEFAULT NULL,
  `logo_path` VARCHAR(255) DEFAULT NULL,
  `color_primary` VARCHAR(20) DEFAULT NULL,
  `color_secondary` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `municipalidad_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `municipalidad_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `notificacion_correos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `correo_imap` VARCHAR(150) NOT NULL,
  `password_imap` VARCHAR(255) NOT NULL,
  `host_imap` VARCHAR(150) NOT NULL,
  `puerto_imap` INT UNSIGNED NOT NULL DEFAULT 993,
  `seguridad_imap` VARCHAR(30) NOT NULL DEFAULT 'ssl',
  `from_nombre` VARCHAR(150) DEFAULT NULL,
  `from_correo` VARCHAR(150) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `notificacion_correos_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `notificacion_correos_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `email_templates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `template_key` VARCHAR(120) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `body_html` MEDIUMTEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_templates_key_unique` (`template_key`),
  KEY `email_templates_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `email_templates_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `event_authority_invitations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` INT UNSIGNED NOT NULL,
  `authority_id` INT UNSIGNED NOT NULL,
  `destinatario_correo` VARCHAR(150) DEFAULT NULL,
  `correo_enviado` TINYINT(1) NOT NULL DEFAULT 0,
  `sent_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_authority_invitations_unique` (`event_id`, `authority_id`),
  CONSTRAINT `event_authority_invitations_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_authority_invitations_authority_id_fk` FOREIGN KEY (`authority_id`) REFERENCES `authorities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `notification_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `canal_email` TINYINT(1) NOT NULL DEFAULT 1,
  `canal_sms` TINYINT(1) NOT NULL DEFAULT 0,
  `canal_app` TINYINT(1) NOT NULL DEFAULT 1,
  `frecuencia` VARCHAR(30) NOT NULL DEFAULT 'diario',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `notification_rules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `evento` VARCHAR(120) NOT NULL,
  `destino` VARCHAR(150) NOT NULL,
  `canal` VARCHAR(50) NOT NULL,
  `estado` VARCHAR(30) NOT NULL DEFAULT 'activa',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `document_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `document_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `documents` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(200) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `categoria_id` INT UNSIGNED DEFAULT NULL,
  `unidad_id` INT UNSIGNED DEFAULT NULL,
  `estado` VARCHAR(30) NOT NULL DEFAULT 'vigente',
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `documents_categoria_id_fk` FOREIGN KEY (`categoria_id`) REFERENCES `document_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_unidad_id_fk` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `document_versions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `document_id` INT UNSIGNED NOT NULL,
  `version` VARCHAR(20) NOT NULL,
  `archivo_ruta` VARCHAR(255) NOT NULL,
  `archivo_tipo` VARCHAR(50) NOT NULL,
  `vencimiento` DATE DEFAULT NULL,
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `document_versions_document_id_fk` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_versions_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `document_tag_links` (
  `document_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`document_id`, `tag_id`),
  CONSTRAINT `document_tag_links_document_id_fk` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_tag_links_tag_id_fk` FOREIGN KEY (`tag_id`) REFERENCES `document_tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `document_access` (
  `document_id` INT UNSIGNED NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`document_id`, `role_id`),
  CONSTRAINT `document_access_document_id_fk` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_access_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `approval_flows` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `entidad` VARCHAR(80) NOT NULL,
  `unidad_id` INT UNSIGNED DEFAULT NULL,
  `sla_horas` INT UNSIGNED NOT NULL DEFAULT 48,
  `estado` VARCHAR(30) NOT NULL DEFAULT 'activo',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `approval_flows_unidad_id_fk` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `approval_steps` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `flow_id` INT UNSIGNED NOT NULL,
  `orden` INT UNSIGNED NOT NULL,
  `responsable` VARCHAR(150) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `approval_steps_flow_id_fk` FOREIGN KEY (`flow_id`) REFERENCES `approval_flows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `empresas` (`nombre`, `razon_social`, `ruc`, `telefono`, `correo`, `direccion`)
VALUES ('Empresa Demo', 'Empresa Demo S.A.', '99999999-9', '+56 9 6000 0000', 'contacto@empresa-demo.cl', 'Av. Principal 123');

INSERT INTO `users` (
  `empresa_id`,
  `rut`,
  `nombre`,
  `apellido`,
  `correo`,
  `telefono`,
  `direccion`,
  `username`,
  `rol`,
  `password_hash`,
  `password_locked`,
  `is_superadmin`,
  `estado`
) VALUES (
  1,
  '9.999.999-9',
  'Super',
  'User',
  'admin@muni.cl',
  '+56 9 1234 5678',
  'Municipalidad Central',
  'superuser',
  'SuperAdmin',
  '$2y$12$qxdc5O8qjG7F80sMd59hHe69.5H6jzRg73BVwO7ZezIGLFNF3LB2.',
  1,
  1,
  1
);

INSERT INTO `roles` (`empresa_id`, `nombre`, `descripcion`, `estado`)
VALUES
  (1, 'SuperAdmin', 'Control total del sistema', 1),
  (1, 'Admin', 'Administración general', 1),
  (1, 'EncargadoEventos', 'Gestión de eventos', 1),
  (1, 'Auditor', 'Revisión y auditoría', 1),
  (1, 'Consulta', 'Acceso de solo lectura', 1);

INSERT INTO `unidades` (`empresa_id`, `nombre`, `descripcion`)
VALUES
  (1, 'Administración', 'Unidad Administrativa'),
  (1, 'DIDECO', 'Desarrollo Comunitario'),
  (1, 'SECPLAN', 'Secretaría Comunal de Planificación');

INSERT INTO `municipalidad` (`empresa_id`, `nombre`, `rut`, `direccion`, `telefono`, `correo`, `logo_path`, `color_primary`, `color_secondary`)
VALUES (1, 'Go Muni', NULL, NULL, NULL, NULL, 'assets/images/logo.png', '#6658dd', '#4a81d4');

INSERT INTO `notificacion_correos` (`empresa_id`, `correo_imap`, `password_imap`, `host_imap`, `puerto_imap`, `seguridad_imap`, `from_nombre`, `from_correo`)
VALUES (1, 'notificaciones@municipalidad.cl', 'cambiar_password', 'imap.municipalidad.cl', 993, 'ssl', 'Sistema Municipal', 'notificaciones@municipalidad.cl');

INSERT INTO `notification_settings` (`canal_email`, `canal_sms`, `canal_app`, `frecuencia`)
VALUES (1, 0, 1, 'diario');

INSERT INTO `permissions` (`modulo`, `accion`, `descripcion`)
VALUES
  ('usuarios', 'ver', 'Ver usuarios'),
  ('usuarios', 'crear', 'Crear usuarios'),
  ('usuarios', 'editar', 'Editar usuarios'),
  ('usuarios', 'eliminar', 'Deshabilitar usuarios'),
  ('roles', 'ver', 'Ver roles'),
  ('roles', 'crear', 'Crear roles'),
  ('roles', 'editar', 'Editar roles'),
  ('roles', 'eliminar', 'Deshabilitar roles'),
  ('eventos', 'ver', 'Ver eventos'),
  ('eventos', 'crear', 'Crear eventos'),
  ('eventos', 'editar', 'Editar eventos'),
  ('eventos', 'eliminar', 'Deshabilitar eventos'),
  ('eventos', 'publicar', 'Publicar eventos'),
  ('autoridades', 'ver', 'Ver autoridades'),
  ('autoridades', 'crear', 'Crear autoridades'),
  ('autoridades', 'editar', 'Editar autoridades'),
  ('autoridades', 'eliminar', 'Deshabilitar autoridades'),
  ('adjuntos', 'subir', 'Subir adjuntos'),
  ('adjuntos', 'eliminar', 'Eliminar adjuntos'),
  ('adjuntos', 'descargar', 'Descargar adjuntos');

-- === Inventario y ventas ===
CREATE TABLE `bodegas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `ciudad` VARCHAR(100) DEFAULT NULL,
  `telefono` VARCHAR(30) DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bodegas_nombre_unique` (`nombre`),
  KEY `bodegas_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `bodegas_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `formas_pago` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(120) NOT NULL,
  `descripcion` VARCHAR(200) DEFAULT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `formas_pago_nombre_unique` (`nombre`),
  KEY `formas_pago_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `formas_pago_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `impuestos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(120) NOT NULL,
  `porcentaje` DECIMAL(6, 2) NOT NULL DEFAULT 0,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `impuestos_nombre_unique` (`nombre`),
  KEY `impuestos_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `impuestos_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `parametros_inventario` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `stock_minimo_default` INT UNSIGNED NOT NULL DEFAULT 0,
  `stock_maximo_default` INT UNSIGNED NOT NULL DEFAULT 0,
  `metodo_costeo` ENUM('promedio', 'fifo', 'lifo') NOT NULL DEFAULT 'promedio',
  `permite_stock_negativo` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parametros_inventario_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `parametros_inventario_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categorias_productos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` VARCHAR(200) DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorias_productos_nombre_unique` (`nombre`),
  KEY `categorias_productos_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `categorias_productos_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `unidades_medida` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `abreviatura` VARCHAR(20) NOT NULL,
  `descripcion` VARCHAR(200) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unidades_medida_nombre_unique` (`nombre`),
  KEY `unidades_medida_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `unidades_medida_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `estados_producto` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `descripcion` VARCHAR(200) DEFAULT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estados_producto_nombre_unique` (`nombre`),
  KEY `estados_producto_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `estados_producto_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `productos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `sku` VARCHAR(60) NOT NULL,
  `nombre` VARCHAR(180) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `categoria_id` INT UNSIGNED DEFAULT NULL,
  `unidad_medida_id` INT UNSIGNED DEFAULT NULL,
  `estado_id` INT UNSIGNED DEFAULT NULL,
  `impuesto_id` INT UNSIGNED DEFAULT NULL,
  `costo_promedio` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `precio_base` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `stock_minimo` INT UNSIGNED NOT NULL DEFAULT 0,
  `stock_maximo` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_sku_unique` (`sku`),
  KEY `productos_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `productos_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `productos_categoria_fk` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_productos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `productos_unidad_fk` FOREIGN KEY (`unidad_medida_id`) REFERENCES `unidades_medida` (`id`) ON DELETE SET NULL,
  CONSTRAINT `productos_estado_fk` FOREIGN KEY (`estado_id`) REFERENCES `estados_producto` (`id`) ON DELETE SET NULL,
  CONSTRAINT `productos_impuesto_fk` FOREIGN KEY (`impuesto_id`) REFERENCES `impuestos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `producto_precios_costos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `producto_id` INT UNSIGNED NOT NULL,
  `costo_unitario` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `precio_venta` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `vigente_desde` DATE NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `producto_precios_costos_producto_idx` (`producto_id`),
  KEY `producto_precios_costos_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `producto_precios_costos_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `producto_precios_costos_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `stock_actual` (
  `producto_id` INT UNSIGNED NOT NULL,
  `bodega_id` INT UNSIGNED NOT NULL,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `cantidad` INT NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`producto_id`, `bodega_id`),
  KEY `stock_actual_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `stock_actual_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_actual_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_actual_bodega_fk` FOREIGN KEY (`bodega_id`) REFERENCES `bodegas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario_entradas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `referencia` VARCHAR(120) DEFAULT NULL,
  `proveedor` VARCHAR(150) DEFAULT NULL,
  `bodega_id` INT UNSIGNED NOT NULL,
  `fecha` DATE NOT NULL,
  `observacion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inventario_entradas_bodega_idx` (`bodega_id`),
  KEY `inventario_entradas_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `inventario_entradas_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventario_entradas_bodega_fk` FOREIGN KEY (`bodega_id`) REFERENCES `bodegas` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario_entrada_detalles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `entrada_id` INT UNSIGNED NOT NULL,
  `producto_id` INT UNSIGNED NOT NULL,
  `cantidad` INT NOT NULL,
  `costo_unitario` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `inventario_entrada_detalles_entrada_idx` (`entrada_id`),
  KEY `inventario_entrada_detalles_producto_idx` (`producto_id`),
  CONSTRAINT `inventario_entrada_detalles_entrada_fk` FOREIGN KEY (`entrada_id`) REFERENCES `inventario_entradas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventario_entrada_detalles_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario_ajustes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `bodega_id` INT UNSIGNED NOT NULL,
  `motivo` VARCHAR(150) NOT NULL,
  `fecha` DATE NOT NULL,
  `observacion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inventario_ajustes_bodega_idx` (`bodega_id`),
  KEY `inventario_ajustes_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `inventario_ajustes_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventario_ajustes_bodega_fk` FOREIGN KEY (`bodega_id`) REFERENCES `bodegas` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario_ajuste_detalles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ajuste_id` INT UNSIGNED NOT NULL,
  `producto_id` INT UNSIGNED NOT NULL,
  `cantidad` INT NOT NULL,
  `tipo` ENUM('incremento', 'decremento') NOT NULL,
  `costo_unitario` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `inventario_ajuste_detalles_ajuste_idx` (`ajuste_id`),
  KEY `inventario_ajuste_detalles_producto_idx` (`producto_id`),
  CONSTRAINT `inventario_ajuste_detalles_ajuste_fk` FOREIGN KEY (`ajuste_id`) REFERENCES `inventario_ajustes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventario_ajuste_detalles_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario_movimientos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `producto_id` INT UNSIGNED NOT NULL,
  `bodega_id` INT UNSIGNED NOT NULL,
  `tipo` ENUM('entrada', 'salida', 'ajuste', 'traslado') NOT NULL,
  `referencia` VARCHAR(150) DEFAULT NULL,
  `cantidad` INT NOT NULL,
  `costo_unitario` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `saldo_cantidad` INT NOT NULL DEFAULT 0,
  `saldo_costo` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inventario_movimientos_producto_idx` (`producto_id`),
  KEY `inventario_movimientos_bodega_idx` (`bodega_id`),
  KEY `inventario_movimientos_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `inventario_movimientos_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventario_movimientos_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `inventario_movimientos_bodega_fk` FOREIGN KEY (`bodega_id`) REFERENCES `bodegas` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `traslados` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `origen_bodega_id` INT UNSIGNED NOT NULL,
  `destino_bodega_id` INT UNSIGNED NOT NULL,
  `estado` ENUM('pendiente', 'enviado', 'recibido', 'cancelado') NOT NULL DEFAULT 'pendiente',
  `fecha_solicitud` DATE NOT NULL,
  `fecha_envio` DATE DEFAULT NULL,
  `fecha_recepcion` DATE DEFAULT NULL,
  `observacion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `traslados_origen_idx` (`origen_bodega_id`),
  KEY `traslados_destino_idx` (`destino_bodega_id`),
  KEY `traslados_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `traslados_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `traslados_origen_fk` FOREIGN KEY (`origen_bodega_id`) REFERENCES `bodegas` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `traslados_destino_fk` FOREIGN KEY (`destino_bodega_id`) REFERENCES `bodegas` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `traslado_detalles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `traslado_id` INT UNSIGNED NOT NULL,
  `producto_id` INT UNSIGNED NOT NULL,
  `cantidad` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `traslado_detalles_traslado_idx` (`traslado_id`),
  KEY `traslado_detalles_producto_idx` (`producto_id`),
  CONSTRAINT `traslado_detalles_traslado_fk` FOREIGN KEY (`traslado_id`) REFERENCES `traslados` (`id`) ON DELETE CASCADE,
  CONSTRAINT `traslado_detalles_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `clientes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `documento` VARCHAR(60) DEFAULT NULL,
  `telefono` VARCHAR(30) DEFAULT NULL,
  `correo` VARCHAR(150) DEFAULT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `clientes_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `clientes_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ventas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `cliente_id` INT UNSIGNED DEFAULT NULL,
  `bodega_id` INT UNSIGNED NOT NULL,
  `fecha` DATE NOT NULL,
  `estado` ENUM('registrada', 'anulada') NOT NULL DEFAULT 'registrada',
  `subtotal` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `impuesto_total` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `descuento_total` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `total` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `forma_pago_id` INT UNSIGNED DEFAULT NULL,
  `observacion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ventas_cliente_idx` (`cliente_id`),
  KEY `ventas_bodega_idx` (`bodega_id`),
  KEY `ventas_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `ventas_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ventas_cliente_fk` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ventas_bodega_fk` FOREIGN KEY (`bodega_id`) REFERENCES `bodegas` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ventas_forma_pago_fk` FOREIGN KEY (`forma_pago_id`) REFERENCES `formas_pago` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `venta_detalles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `venta_id` INT UNSIGNED NOT NULL,
  `producto_id` INT UNSIGNED NOT NULL,
  `cantidad` INT NOT NULL,
  `precio_unitario` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `costo_unitario` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `impuesto_id` INT UNSIGNED DEFAULT NULL,
  `subtotal` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `venta_detalles_venta_idx` (`venta_id`),
  KEY `venta_detalles_producto_idx` (`producto_id`),
  CONSTRAINT `venta_detalles_venta_fk` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `venta_detalles_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `venta_detalles_impuesto_fk` FOREIGN KEY (`impuesto_id`) REFERENCES `impuestos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `devoluciones` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `venta_id` INT UNSIGNED NOT NULL,
  `fecha` DATE NOT NULL,
  `motivo` VARCHAR(200) DEFAULT NULL,
  `total_devuelto` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `devoluciones_venta_idx` (`venta_id`),
  KEY `devoluciones_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `devoluciones_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `devoluciones_venta_fk` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `devolucion_detalles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `devolucion_id` INT UNSIGNED NOT NULL,
  `producto_id` INT UNSIGNED NOT NULL,
  `cantidad` INT NOT NULL,
  `monto` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `devolucion_detalles_devolucion_idx` (`devolucion_id`),
  KEY `devolucion_detalles_producto_idx` (`producto_id`),
  CONSTRAINT `devolucion_detalles_devolucion_fk` FOREIGN KEY (`devolucion_id`) REFERENCES `devoluciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `devolucion_detalles_producto_fk` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cuentas_por_cobrar` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `cliente_id` INT UNSIGNED NOT NULL,
  `venta_id` INT UNSIGNED DEFAULT NULL,
  `monto` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `saldo` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `fecha_vencimiento` DATE DEFAULT NULL,
  `estado` ENUM('pendiente', 'pagado', 'vencido') NOT NULL DEFAULT 'pendiente',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cuentas_por_cobrar_cliente_idx` (`cliente_id`),
  KEY `cuentas_por_cobrar_venta_idx` (`venta_id`),
  KEY `cuentas_por_cobrar_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `cuentas_por_cobrar_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cuentas_por_cobrar_cliente_fk` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cuentas_por_cobrar_venta_fk` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `respaldos_sistema` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `archivo` VARCHAR(200) NOT NULL,
  `tamano` BIGINT UNSIGNED DEFAULT NULL,
  `generado_por` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `respaldos_sistema_usuario_idx` (`generado_por`),
  KEY `respaldos_sistema_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `respaldos_sistema_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `respaldos_sistema_usuario_fk` FOREIGN KEY (`generado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
