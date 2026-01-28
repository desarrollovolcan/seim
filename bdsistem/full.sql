CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED DEFAULT NULL,
  `rut` VARCHAR(20) NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NOT NULL,
  `cargo` VARCHAR(100) DEFAULT NULL,
  `fecha_nacimiento` DATE DEFAULT NULL,
  `correo` VARCHAR(150) NOT NULL,
  `telefono` VARCHAR(30) NOT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `username` VARCHAR(60) NOT NULL,
  `rol` VARCHAR(60) DEFAULT NULL,
  `unidad_id` INT UNSIGNED DEFAULT NULL,
  `avatar_path` VARCHAR(255) DEFAULT NULL,
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
  `validation_token` VARCHAR(64) DEFAULT NULL,
  `unidad_id` INT UNSIGNED DEFAULT NULL,
  `creado_por` INT UNSIGNED NOT NULL,
  `encargado_id` INT UNSIGNED DEFAULT NULL,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_validation_token_unique` (`validation_token`),
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
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `authorities_empresa_id_idx` (`empresa_id`),
  CONSTRAINT `authorities_empresa_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `event_authorities` (
  `event_id` INT UNSIGNED NOT NULL,
  `authority_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`, `authority_id`),
  CONSTRAINT `event_authorities_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_authorities_authority_id_fk` FOREIGN KEY (`authority_id`) REFERENCES `authorities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `event_authority_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` INT UNSIGNED NOT NULL,
  `destinatario_nombre` VARCHAR(150) DEFAULT NULL,
  `destinatario_correo` VARCHAR(150) NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `correo_enviado` TINYINT(1) NOT NULL DEFAULT 0,
  `estado` ENUM('pendiente', 'respondido') NOT NULL DEFAULT 'pendiente',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_authority_requests_token_unique` (`token`),
  KEY `event_authority_requests_event_id_idx` (`event_id`),
  CONSTRAINT `event_authority_requests_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `event_authority_confirmations` (
  `request_id` INT UNSIGNED NOT NULL,
  `authority_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`, `authority_id`),
  CONSTRAINT `event_authority_confirmations_request_id_fk` FOREIGN KEY (`request_id`) REFERENCES `event_authority_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_authority_confirmations_authority_id_fk` FOREIGN KEY (`authority_id`) REFERENCES `authorities` (`id`) ON DELETE CASCADE
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
  `logo_topbar_height` INT UNSIGNED DEFAULT NULL,
  `logo_sidenav_height` INT UNSIGNED DEFAULT NULL,
  `logo_sidenav_height_sm` INT UNSIGNED DEFAULT NULL,
  `logo_auth_height` INT UNSIGNED DEFAULT NULL,
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

CREATE TABLE `event_authority_attendance` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` INT UNSIGNED NOT NULL,
  `authority_id` INT UNSIGNED NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `status` ENUM('pendiente', 'confirmado', 'rechazado') NOT NULL DEFAULT 'pendiente',
  `responded_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_authority_attendance_unique` (`event_id`, `authority_id`),
  UNIQUE KEY `event_authority_attendance_token_unique` (`token`),
  CONSTRAINT `event_authority_attendance_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_authority_attendance_authority_id_fk` FOREIGN KEY (`authority_id`) REFERENCES `authorities` (`id`) ON DELETE CASCADE
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

CREATE TABLE `document_shares` (
  `document_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`, `user_id`),
  CONSTRAINT `document_shares_document_id_fk` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_shares_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
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
  '$2y$12$nNyFQLLuFHy7yjLILUTlIO3NQ96Vw5rS90YCDml1ZKINCPv7Lvshe',
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

-- Datos QA para pruebas de flujo -- Sección: unidades
INSERT INTO `unidades` (`empresa_id`, `nombre`, `descripcion`)
VALUES
  (1, 'Administración', 'Gestión administrativa municipal'),
  (1, 'Finanzas', 'Gestión presupuestaria y contable'),
  (1, 'Recursos Humanos', 'Gestión de personal y bienestar'),
  (1, 'DIDECO', 'Desarrollo comunitario'),
  (1, 'SECPLAN', 'Planificación comunal'),
  (1, 'Tránsito', 'Permisos y gestión vial'),
  (1, 'Obras Municipales', 'Permisos y fiscalización de obras'),
  (1, 'Salud', 'Coordinación de atención primaria'),
  (1, 'Educación', 'Gestión educativa comunal'),
  (1, 'Medio Ambiente', 'Programas y fiscalización ambiental'),
  (1, 'Cultura', 'Actividades culturales'),
  (1, 'Deportes', 'Programas deportivos'),
  (1, 'Seguridad', 'Prevención y seguridad pública'),
  (1, 'Turismo', 'Promoción turística'),
  (1, 'Vivienda', 'Programas habitacionales'),
  (1, 'Fomento Productivo', 'Apoyo a emprendedores'),
  (1, 'Adulto Mayor', 'Programas para personas mayores'),
  (1, 'Infancia', 'Programas de infancia'),
  (1, 'Juventud', 'Programas juveniles'),
  (1, 'Participación Ciudadana', 'Vinculación con la comunidad');

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

CREATE TABLE `module_records` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_key` VARCHAR(120) NOT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `module_records_module_idx` (`module_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Datos QA para pruebas de flujo
-- Sección: empresas

INSERT INTO empresas (nombre, razon_social, ruc, telefono, correo, direccion)
VALUES ('Empresa Demo', 'Empresa Demo S.A.', '99999999-9', '+56 9 6000 0000', 'contacto@empresa-demo.cl', 'Av. Principal 123');

-- Sección: unidades

INSERT INTO unidades (empresa_id, nombre, descripcion) VALUES
    (1, 'Administración', 'Gestión administrativa municipal'),
    (1, 'Finanzas', 'Gestión presupuestaria y contable'),
    (1, 'Recursos Humanos', 'Gestión de personal y bienestar'),
    (1, 'DIDECO', 'Desarrollo comunitario'),
    (1, 'SECPLAN', 'Planificación comunal'),
    (1, 'Tránsito', 'Permisos y gestión vial'),
    (1, 'Obras Municipales', 'Permisos y fiscalización de obras'),
    (1, 'Salud', 'Coordinación de atención primaria'),
    (1, 'Educación', 'Gestión educativa comunal'),
    (1, 'Medio Ambiente', 'Programas y fiscalización ambiental'),
    (1, 'Cultura', 'Actividades culturales'),
    (1, 'Deportes', 'Programas deportivos'),
    (1, 'Seguridad', 'Prevención y seguridad pública'),
    (1, 'Turismo', 'Promoción turística'),
    (1, 'Vivienda', 'Programas habitacionales'),
    (1, 'Fomento Productivo', 'Apoyo a emprendedores'),
    (1, 'Adulto Mayor', 'Programas para personas mayores'),
    (1, 'Infancia', 'Programas de infancia'),
    (1, 'Juventud', 'Programas juveniles'),
    (1, 'Participación Ciudadana', 'Vinculación con la comunidad');

INSERT INTO roles (empresa_id, nombre, descripcion, estado) VALUES
    (1, 'Administrador General', 'Rol 1 para QA', 1),
    (1, 'Encargado de Finanzas', 'Rol 2 para QA', 1),
    (1, 'Jefe de Recursos Humanos', 'Rol 3 para QA', 1),
    (1, 'Encargado DIDECO', 'Rol 4 para QA', 1),
    (1, 'Planificador SECPLAN', 'Rol 5 para QA', 1),
    (1, 'Coordinador de Tránsito', 'Rol 6 para QA', 1),
    (1, 'Inspector de Obras', 'Rol 7 para QA', 1),
    (1, 'Coordinador de Salud', 'Rol 8 para QA', 1),
    (1, 'Director de Educación', 'Rol 9 para QA', 1),
    (1, 'Gestor Ambiental', 'Rol 10 para QA', 1),
    (1, 'Encargado de Cultura', 'Rol 11 para QA', 1),
    (1, 'Encargado de Deportes', 'Rol 12 para QA', 1),
    (1, 'Encargado de Seguridad', 'Rol 13 para QA', 1),
    (1, 'Encargado de Turismo', 'Rol 14 para QA', 1),
    (1, 'Encargado de Vivienda', 'Rol 15 para QA', 1),
    (1, 'Encargado Fomento', 'Rol 16 para QA', 1),
    (1, 'Encargado Adulto Mayor', 'Rol 17 para QA', 1),
    (1, 'Encargado de Infancia', 'Rol 18 para QA', 1),
    (1, 'Encargado de Juventud', 'Rol 19 para QA', 1),
    (1, 'Encargado Participación', 'Rol 20 para QA', 1);

INSERT INTO users (empresa_id, rut, nombre, apellido, correo, telefono, direccion, username, rol, password_hash, password_locked, is_superadmin, estado) VALUES
    (1, '100.000.000-0', 'Super', 'Administrador', 'superadmin@empresa-demo.cl', '+56 9 6000 0001', 'Av. Principal 123', 'superadmin', 'Super Administrador', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 1, 1, 1),
    (1, '101.000.001-1', 'Camila', 'González', 'camila.gonzalez@municipalidad.cl', '+56 9 5000 0001', 'Av. Municipal 1', 'usuario1', 'Administrador General', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '102.000.002-2', 'Diego', 'Muñoz', 'diego.munoz@municipalidad.cl', '+56 9 5000 0002', 'Av. Municipal 2', 'usuario2', 'Encargado de Finanzas', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '103.000.003-3', 'Valentina', 'Rojas', 'valentina.rojas@municipalidad.cl', '+56 9 5000 0003', 'Av. Municipal 3', 'usuario3', 'Jefe de Recursos Humanos', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '104.000.004-4', 'Matías', 'Díaz', 'matias.diaz@municipalidad.cl', '+56 9 5000 0004', 'Av. Municipal 4', 'usuario4', 'Encargado DIDECO', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '105.000.005-5', 'Fernanda', 'Pérez', 'fernanda.perez@municipalidad.cl', '+56 9 5000 0005', 'Av. Municipal 5', 'usuario5', 'Planificador SECPLAN', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '106.000.006-6', 'Sebastián', 'Soto', 'sebastian.soto@municipalidad.cl', '+56 9 5000 0006', 'Av. Municipal 6', 'usuario6', 'Coordinador de Tránsito', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '107.000.007-7', 'Constanza', 'Contreras', 'constanza.contreras@municipalidad.cl', '+56 9 5000 0007', 'Av. Municipal 7', 'usuario7', 'Inspector de Obras', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '108.000.008-8', 'Javiera', 'Silva', 'javiera.silva@municipalidad.cl', '+56 9 5000 0008', 'Av. Municipal 8', 'usuario8', 'Coordinador de Salud', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '109.000.009-0', 'Rodrigo', 'Martínez', 'rodrigo.martinez@municipalidad.cl', '+56 9 5000 0009', 'Av. Municipal 9', 'usuario9', 'Director de Educación', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '110.000.010-1', 'Francisca', 'Sepúlveda', 'francisca.sepulveda@municipalidad.cl', '+56 9 5000 0010', 'Av. Municipal 10', 'usuario10', 'Gestor Ambiental', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '111.000.011-2', 'Cristóbal', 'Morales', 'cristobal.morales@municipalidad.cl', '+56 9 5000 0011', 'Av. Municipal 11', 'usuario11', 'Encargado de Cultura', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '112.000.012-3', 'Daniela', 'Rodríguez', 'daniela.rodriguez@municipalidad.cl', '+56 9 5000 0012', 'Av. Municipal 12', 'usuario12', 'Encargado de Deportes', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '113.000.013-4', 'Tomás', 'López', 'tomas.lopez@municipalidad.cl', '+56 9 5000 0013', 'Av. Municipal 13', 'usuario13', 'Encargado de Seguridad', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '114.000.014-5', 'Paula', 'Fuentes', 'paula.fuentes@municipalidad.cl', '+56 9 5000 0014', 'Av. Municipal 14', 'usuario14', 'Encargado de Turismo', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '115.000.015-6', 'Ignacio', 'Hernández', 'ignacio.hernandez@municipalidad.cl', '+56 9 5000 0015', 'Av. Municipal 15', 'usuario15', 'Encargado de Vivienda', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '116.000.016-7', 'Macarena', 'Torres', 'macarena.torres@municipalidad.cl', '+56 9 5000 0016', 'Av. Municipal 16', 'usuario16', 'Encargado Fomento', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '117.000.017-8', 'Felipe', 'Araya', 'felipe.araya@municipalidad.cl', '+56 9 5000 0017', 'Av. Municipal 17', 'usuario17', 'Encargado Adulto Mayor', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '118.000.018-0', 'Daniel', 'Flores', 'daniel.flores@municipalidad.cl', '+56 9 5000 0018', 'Av. Municipal 18', 'usuario18', 'Encargado de Infancia', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '119.000.019-1', 'María', 'Castillo', 'maria.castillo@municipalidad.cl', '+56 9 5000 0019', 'Av. Municipal 19', 'usuario19', 'Encargado de Juventud', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1),
    (1, '120.000.020-2', 'José', 'Vargas', 'jose.vargas@municipalidad.cl', '+56 9 5000 0020', 'Av. Municipal 20', 'usuario20', 'Encargado Participación', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 0, 0, 1);

INSERT INTO permissions (modulo, accion, descripcion) VALUES
    ('modulo1', 'accion1', 'Permiso QA 1'),
    ('modulo2', 'accion2', 'Permiso QA 2'),
    ('modulo3', 'accion3', 'Permiso QA 3'),
    ('modulo4', 'accion4', 'Permiso QA 4'),
    ('modulo5', 'accion5', 'Permiso QA 5'),
    ('modulo6', 'accion6', 'Permiso QA 6'),
    ('modulo7', 'accion7', 'Permiso QA 7'),
    ('modulo8', 'accion8', 'Permiso QA 8'),
    ('modulo9', 'accion9', 'Permiso QA 9'),
    ('modulo10', 'accion10', 'Permiso QA 10'),
    ('modulo11', 'accion11', 'Permiso QA 11'),
    ('modulo12', 'accion12', 'Permiso QA 12'),
    ('modulo13', 'accion13', 'Permiso QA 13'),
    ('modulo14', 'accion14', 'Permiso QA 14'),
    ('modulo15', 'accion15', 'Permiso QA 15'),
    ('modulo16', 'accion16', 'Permiso QA 16'),
    ('modulo17', 'accion17', 'Permiso QA 17'),
    ('modulo18', 'accion18', 'Permiso QA 18'),
    ('modulo19', 'accion19', 'Permiso QA 19'),
    ('modulo20', 'accion20', 'Permiso QA 20');

INSERT INTO event_types (nombre, color_class) VALUES
    ('Tipo QA 1', 'bg-primary-subtle text-primary'),
    ('Tipo QA 2', 'bg-secondary-subtle text-secondary'),
    ('Tipo QA 3', 'bg-success-subtle text-success'),
    ('Tipo QA 4', 'bg-warning-subtle text-warning'),
    ('Tipo QA 5', 'bg-danger-subtle text-danger'),
    ('Tipo QA 6', 'bg-info-subtle text-info'),
    ('Tipo QA 7', 'bg-dark-subtle text-dark'),
    ('Tipo QA 8', 'bg-primary-subtle text-primary'),
    ('Tipo QA 9', 'bg-secondary-subtle text-secondary'),
    ('Tipo QA 10', 'bg-success-subtle text-success'),
    ('Tipo QA 11', 'bg-warning-subtle text-warning'),
    ('Tipo QA 12', 'bg-danger-subtle text-danger'),
    ('Tipo QA 13', 'bg-info-subtle text-info'),
    ('Tipo QA 14', 'bg-dark-subtle text-dark'),
    ('Tipo QA 15', 'bg-primary-subtle text-primary'),
    ('Tipo QA 16', 'bg-secondary-subtle text-secondary'),
    ('Tipo QA 17', 'bg-success-subtle text-success'),
    ('Tipo QA 18', 'bg-warning-subtle text-warning'),
    ('Tipo QA 19', 'bg-danger-subtle text-danger'),
    ('Tipo QA 20', 'bg-info-subtle text-info');

INSERT INTO events (titulo, descripcion, ubicacion, fecha_inicio, fecha_fin, tipo, cupos, publico_objetivo, estado, aprobacion_estado, habilitado, unidad_id, creado_por, encargado_id) VALUES
    ('Operativo de Salud', 'Actividad planificada 1 para la comunidad.', 'Sala 1', '2025-02-01 09:00:00', '2025-02-01 11:00:00', 'Tipo QA 1', 21, 'Comunidad', 'publicado', 'publicado', 1, 1, 1, 1),
    ('Feria de Emprendimiento', 'Actividad planificada 2 para la comunidad.', 'Sala 2', '2025-02-02 09:00:00', '2025-02-02 11:00:00', 'Tipo QA 2', 22, 'Comunidad', 'publicado', 'publicado', 1, 2, 2, 2),
    ('Cabildo Vecinal', 'Actividad planificada 3 para la comunidad.', 'Sala 3', '2025-02-03 09:00:00', '2025-02-03 11:00:00', 'Tipo QA 3', 23, 'Comunidad', 'publicado', 'publicado', 1, 3, 3, 3),
    ('Taller de Prevención', 'Actividad planificada 4 para la comunidad.', 'Sala 4', '2025-02-04 09:00:00', '2025-02-04 11:00:00', 'Tipo QA 4', 24, 'Comunidad', 'publicado', 'publicado', 1, 4, 4, 4),
    ('Feria Costumbrista', 'Actividad planificada 5 para la comunidad.', 'Sala 5', '2025-02-05 09:00:00', '2025-02-05 11:00:00', 'Tipo QA 5', 25, 'Comunidad', 'publicado', 'publicado', 1, 5, 5, 5),
    ('Campaña de Reciclaje', 'Actividad planificada 6 para la comunidad.', 'Sala 6', '2025-02-06 09:00:00', '2025-02-06 11:00:00', 'Tipo QA 6', 26, 'Comunidad', 'publicado', 'publicado', 1, 6, 6, 6),
    ('Encuentro Deportivo', 'Actividad planificada 7 para la comunidad.', 'Sala 7', '2025-02-07 09:00:00', '2025-02-07 11:00:00', 'Tipo QA 7', 27, 'Comunidad', 'publicado', 'publicado', 1, 7, 7, 7),
    ('Charla Comunitaria', 'Actividad planificada 8 para la comunidad.', 'Sala 8', '2025-02-08 09:00:00', '2025-02-08 11:00:00', 'Tipo QA 8', 28, 'Comunidad', 'publicado', 'publicado', 1, 8, 8, 8),
    ('Festival Cultural', 'Actividad planificada 9 para la comunidad.', 'Sala 9', '2025-02-09 09:00:00', '2025-02-09 11:00:00', 'Tipo QA 9', 29, 'Comunidad', 'publicado', 'publicado', 1, 9, 9, 9),
    ('Operativo Veterinario', 'Actividad planificada 10 para la comunidad.', 'Sala 10', '2025-02-10 09:00:00', '2025-02-10 11:00:00', 'Tipo QA 10', 30, 'Comunidad', 'publicado', 'publicado', 1, 10, 10, 10),
    ('Expo Educativa', 'Actividad planificada 11 para la comunidad.', 'Sala 11', '2025-02-11 09:00:00', '2025-02-11 11:00:00', 'Tipo QA 11', 31, 'Comunidad', 'publicado', 'publicado', 1, 11, 11, 11),
    ('Reunión de Seguridad', 'Actividad planificada 12 para la comunidad.', 'Sala 12', '2025-02-12 09:00:00', '2025-02-12 11:00:00', 'Tipo QA 12', 32, 'Comunidad', 'publicado', 'publicado', 1, 12, 12, 12),
    ('Jornada de Participación', 'Actividad planificada 13 para la comunidad.', 'Sala 13', '2025-02-13 09:00:00', '2025-02-13 11:00:00', 'Tipo QA 13', 33, 'Comunidad', 'publicado', 'publicado', 1, 13, 13, 13),
    ('Operativo Social', 'Actividad planificada 14 para la comunidad.', 'Sala 14', '2025-02-14 09:00:00', '2025-02-14 11:00:00', 'Tipo QA 14', 34, 'Comunidad', 'publicado', 'publicado', 1, 14, 14, 14),
    ('Capacitación Municipal', 'Actividad planificada 15 para la comunidad.', 'Sala 15', '2025-02-15 09:00:00', '2025-02-15 11:00:00', 'Tipo QA 15', 35, 'Comunidad', 'publicado', 'publicado', 1, 15, 15, 15),
    ('Feria Laboral', 'Actividad planificada 16 para la comunidad.', 'Sala 16', '2025-02-16 09:00:00', '2025-02-16 11:00:00', 'Tipo QA 16', 36, 'Comunidad', 'publicado', 'publicado', 1, 16, 16, 16),
    ('Reunión de Coordinación', 'Actividad planificada 17 para la comunidad.', 'Sala 17', '2025-02-17 09:00:00', '2025-02-17 11:00:00', 'Tipo QA 17', 37, 'Comunidad', 'publicado', 'publicado', 1, 17, 17, 17),
    ('Encuentro de Adulto Mayor', 'Actividad planificada 18 para la comunidad.', 'Sala 18', '2025-02-18 09:00:00', '2025-02-18 11:00:00', 'Tipo QA 18', 38, 'Comunidad', 'publicado', 'publicado', 1, 18, 18, 18),
    ('Actividad Infantil', 'Actividad planificada 19 para la comunidad.', 'Sala 19', '2025-02-19 09:00:00', '2025-02-19 11:00:00', 'Tipo QA 19', 39, 'Comunidad', 'publicado', 'publicado', 1, 19, 19, 19),
    ('Mesa Territorial', 'Actividad planificada 20 para la comunidad.', 'Sala 20', '2025-02-20 09:00:00', '2025-02-20 11:00:00', 'Tipo QA 20', 40, 'Comunidad', 'publicado', 'publicado', 1, 20, 20, 20);

INSERT INTO authorities (nombre, tipo, correo, telefono, fecha_inicio, fecha_fin, estado, aprobacion_estado, unidad_id) VALUES
    ('Camila González', 'Alcalde', 'camila.gonzalez@municipalidad.cl', '+56 9 4000 0001', '2024-01-02', NULL, 1, 'vigente', 1),
    ('Diego Muñoz', 'Concejal', 'diego.munoz@municipalidad.cl', '+56 9 4000 0002', '2024-01-03', NULL, 1, 'vigente', 2),
    ('Valentina Rojas', 'Director', 'valentina.rojas@municipalidad.cl', '+56 9 4000 0003', '2024-01-04', NULL, 1, 'vigente', 3),
    ('Matías Díaz', 'Coordinador', 'matias.diaz@municipalidad.cl', '+56 9 4000 0004', '2024-01-05', NULL, 1, 'vigente', 4),
    ('Fernanda Pérez', 'Encargado', 'fernanda.perez@municipalidad.cl', '+56 9 4000 0005', '2024-01-06', NULL, 1, 'vigente', 5),
    ('Sebastián Soto', 'Jefe de Unidad', 'sebastian.soto@municipalidad.cl', '+56 9 4000 0006', '2024-01-07', NULL, 1, 'vigente', 6),
    ('Constanza Contreras', 'Secretario Municipal', 'constanza.contreras@municipalidad.cl', '+56 9 4000 0007', '2024-01-08', NULL, 1, 'vigente', 7),
    ('Javiera Silva', 'Administrador', 'javiera.silva@municipalidad.cl', '+56 9 4000 0008', '2024-01-09', NULL, 1, 'vigente', 8),
    ('Rodrigo Martínez', 'Inspector', 'rodrigo.martinez@municipalidad.cl', '+56 9 4000 0009', '2024-01-10', NULL, 1, 'vigente', 9),
    ('Francisca Sepúlveda', 'Asesor', 'francisca.sepulveda@municipalidad.cl', '+56 9 4000 0010', '2024-01-11', NULL, 1, 'vigente', 10),
    ('Cristóbal Morales', 'Alcalde', 'cristobal.morales@municipalidad.cl', '+56 9 4000 0011', '2024-01-12', NULL, 1, 'vigente', 11),
    ('Daniela Rodríguez', 'Concejal', 'daniela.rodriguez@municipalidad.cl', '+56 9 4000 0012', '2024-01-13', NULL, 1, 'vigente', 12),
    ('Tomás López', 'Director', 'tomas.lopez@municipalidad.cl', '+56 9 4000 0013', '2024-01-14', NULL, 1, 'vigente', 13),
    ('Paula Fuentes', 'Coordinador', 'paula.fuentes@municipalidad.cl', '+56 9 4000 0014', '2024-01-15', NULL, 1, 'vigente', 14),
    ('Ignacio Hernández', 'Encargado', 'ignacio.hernandez@municipalidad.cl', '+56 9 4000 0015', '2024-01-16', NULL, 1, 'vigente', 15),
    ('Macarena Torres', 'Jefe de Unidad', 'macarena.torres@municipalidad.cl', '+56 9 4000 0016', '2024-01-17', NULL, 1, 'vigente', 16),
    ('Felipe Araya', 'Secretario Municipal', 'felipe.araya@municipalidad.cl', '+56 9 4000 0017', '2024-01-18', NULL, 1, 'vigente', 17),
    ('Daniel Flores', 'Administrador', 'daniel.flores@municipalidad.cl', '+56 9 4000 0018', '2024-01-19', NULL, 1, 'vigente', 18),
    ('María Castillo', 'Inspector', 'maria.castillo@municipalidad.cl', '+56 9 4000 0019', '2024-01-20', NULL, 1, 'vigente', 19),
    ('José Vargas', 'Asesor', 'jose.vargas@municipalidad.cl', '+56 9 4000 0020', '2024-01-21', NULL, 1, 'vigente', 20);

INSERT INTO event_authorities (event_id, authority_id) VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (5, 5),
    (6, 6),
    (7, 7),
    (8, 8),
    (9, 9),
    (10, 10),
    (11, 11),
    (12, 12),
    (13, 13),
    (14, 14),
    (15, 15),
    (16, 16),
    (17, 17),
    (18, 18),
    (19, 19),
    (20, 20);

INSERT INTO event_authority_requests (event_id, destinatario_nombre, destinatario_correo, token, correo_enviado, estado, responded_at) VALUES
    (1, 'Camila González', 'camila.gonzalez@municipalidad.cl', 'reqtoken01', 1, 'respondido', NOW()),
    (2, 'Diego Muñoz', 'diego.munoz@municipalidad.cl', 'reqtoken02', 1, 'respondido', NOW()),
    (3, 'Valentina Rojas', 'valentina.rojas@municipalidad.cl', 'reqtoken03', 1, 'respondido', NOW()),
    (4, 'Matías Díaz', 'matias.diaz@municipalidad.cl', 'reqtoken04', 1, 'respondido', NOW()),
    (5, 'Fernanda Pérez', 'fernanda.perez@municipalidad.cl', 'reqtoken05', 1, 'respondido', NOW()),
    (6, 'Sebastián Soto', 'sebastian.soto@municipalidad.cl', 'reqtoken06', 1, 'respondido', NOW()),
    (7, 'Constanza Contreras', 'constanza.contreras@municipalidad.cl', 'reqtoken07', 1, 'pendiente', NULL),
    (8, 'Javiera Silva', 'javiera.silva@municipalidad.cl', 'reqtoken08', 1, 'pendiente', NULL),
    (9, 'Rodrigo Martínez', 'rodrigo.martinez@municipalidad.cl', 'reqtoken09', 1, 'pendiente', NULL),
    (10, 'Francisca Sepúlveda', 'francisca.sepulveda@municipalidad.cl', 'reqtoken10', 1, 'pendiente', NULL);

INSERT INTO event_authority_confirmations (request_id, authority_id) VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (5, 5),
    (6, 6),
    (7, 7),
    (8, 8),
    (9, 9),
    (10, 10);

INSERT INTO event_attachments (event_id, archivo_nombre, archivo_ruta, archivo_tipo, subido_por) VALUES
    (1, 'adjunto_evento_1.pdf', 'uploads/eventos/adjunto_evento_1.pdf', 'application/pdf', 1),
    (2, 'adjunto_evento_2.pdf', 'uploads/eventos/adjunto_evento_2.pdf', 'application/pdf', 2),
    (3, 'adjunto_evento_3.pdf', 'uploads/eventos/adjunto_evento_3.pdf', 'application/pdf', 3),
    (4, 'adjunto_evento_4.pdf', 'uploads/eventos/adjunto_evento_4.pdf', 'application/pdf', 4),
    (5, 'adjunto_evento_5.pdf', 'uploads/eventos/adjunto_evento_5.pdf', 'application/pdf', 5),
    (6, 'adjunto_evento_6.pdf', 'uploads/eventos/adjunto_evento_6.pdf', 'application/pdf', 6),
    (7, 'adjunto_evento_7.pdf', 'uploads/eventos/adjunto_evento_7.pdf', 'application/pdf', 7),
    (8, 'adjunto_evento_8.pdf', 'uploads/eventos/adjunto_evento_8.pdf', 'application/pdf', 8),
    (9, 'adjunto_evento_9.pdf', 'uploads/eventos/adjunto_evento_9.pdf', 'application/pdf', 9),
    (10, 'adjunto_evento_10.pdf', 'uploads/eventos/adjunto_evento_10.pdf', 'application/pdf', 10),
    (11, 'adjunto_evento_11.pdf', 'uploads/eventos/adjunto_evento_11.pdf', 'application/pdf', 11),
    (12, 'adjunto_evento_12.pdf', 'uploads/eventos/adjunto_evento_12.pdf', 'application/pdf', 12),
    (13, 'adjunto_evento_13.pdf', 'uploads/eventos/adjunto_evento_13.pdf', 'application/pdf', 13),
    (14, 'adjunto_evento_14.pdf', 'uploads/eventos/adjunto_evento_14.pdf', 'application/pdf', 14),
    (15, 'adjunto_evento_15.pdf', 'uploads/eventos/adjunto_evento_15.pdf', 'application/pdf', 15),
    (16, 'adjunto_evento_16.pdf', 'uploads/eventos/adjunto_evento_16.pdf', 'application/pdf', 16),
    (17, 'adjunto_evento_17.pdf', 'uploads/eventos/adjunto_evento_17.pdf', 'application/pdf', 17),
    (18, 'adjunto_evento_18.pdf', 'uploads/eventos/adjunto_evento_18.pdf', 'application/pdf', 18),
    (19, 'adjunto_evento_19.pdf', 'uploads/eventos/adjunto_evento_19.pdf', 'application/pdf', 19),
    (20, 'adjunto_evento_20.pdf', 'uploads/eventos/adjunto_evento_20.pdf', 'application/pdf', 20);

INSERT INTO authority_attachments (authority_id, archivo_nombre, archivo_ruta, archivo_tipo, subido_por) VALUES
    (1, 'adjunto_autoridad_1.pdf', 'uploads/autoridades/adjunto_autoridad_1.pdf', 'application/pdf', 1),
    (2, 'adjunto_autoridad_2.pdf', 'uploads/autoridades/adjunto_autoridad_2.pdf', 'application/pdf', 2),
    (3, 'adjunto_autoridad_3.pdf', 'uploads/autoridades/adjunto_autoridad_3.pdf', 'application/pdf', 3),
    (4, 'adjunto_autoridad_4.pdf', 'uploads/autoridades/adjunto_autoridad_4.pdf', 'application/pdf', 4),
    (5, 'adjunto_autoridad_5.pdf', 'uploads/autoridades/adjunto_autoridad_5.pdf', 'application/pdf', 5),
    (6, 'adjunto_autoridad_6.pdf', 'uploads/autoridades/adjunto_autoridad_6.pdf', 'application/pdf', 6),
    (7, 'adjunto_autoridad_7.pdf', 'uploads/autoridades/adjunto_autoridad_7.pdf', 'application/pdf', 7),
    (8, 'adjunto_autoridad_8.pdf', 'uploads/autoridades/adjunto_autoridad_8.pdf', 'application/pdf', 8),
    (9, 'adjunto_autoridad_9.pdf', 'uploads/autoridades/adjunto_autoridad_9.pdf', 'application/pdf', 9),
    (10, 'adjunto_autoridad_10.pdf', 'uploads/autoridades/adjunto_autoridad_10.pdf', 'application/pdf', 10),
    (11, 'adjunto_autoridad_11.pdf', 'uploads/autoridades/adjunto_autoridad_11.pdf', 'application/pdf', 11),
    (12, 'adjunto_autoridad_12.pdf', 'uploads/autoridades/adjunto_autoridad_12.pdf', 'application/pdf', 12),
    (13, 'adjunto_autoridad_13.pdf', 'uploads/autoridades/adjunto_autoridad_13.pdf', 'application/pdf', 13),
    (14, 'adjunto_autoridad_14.pdf', 'uploads/autoridades/adjunto_autoridad_14.pdf', 'application/pdf', 14),
    (15, 'adjunto_autoridad_15.pdf', 'uploads/autoridades/adjunto_autoridad_15.pdf', 'application/pdf', 15),
    (16, 'adjunto_autoridad_16.pdf', 'uploads/autoridades/adjunto_autoridad_16.pdf', 'application/pdf', 16),
    (17, 'adjunto_autoridad_17.pdf', 'uploads/autoridades/adjunto_autoridad_17.pdf', 'application/pdf', 17),
    (18, 'adjunto_autoridad_18.pdf', 'uploads/autoridades/adjunto_autoridad_18.pdf', 'application/pdf', 18),
    (19, 'adjunto_autoridad_19.pdf', 'uploads/autoridades/adjunto_autoridad_19.pdf', 'application/pdf', 19),
    (20, 'adjunto_autoridad_20.pdf', 'uploads/autoridades/adjunto_autoridad_20.pdf', 'application/pdf', 20);

INSERT INTO document_categories (nombre, descripcion) VALUES
    ('Categoría 1', 'Documentación municipal 1'),
    ('Categoría 2', 'Documentación municipal 2'),
    ('Categoría 3', 'Documentación municipal 3'),
    ('Categoría 4', 'Documentación municipal 4'),
    ('Categoría 5', 'Documentación municipal 5'),
    ('Categoría 6', 'Documentación municipal 6'),
    ('Categoría 7', 'Documentación municipal 7'),
    ('Categoría 8', 'Documentación municipal 8'),
    ('Categoría 9', 'Documentación municipal 9'),
    ('Categoría 10', 'Documentación municipal 10'),
    ('Categoría 11', 'Documentación municipal 11'),
    ('Categoría 12', 'Documentación municipal 12'),
    ('Categoría 13', 'Documentación municipal 13'),
    ('Categoría 14', 'Documentación municipal 14'),
    ('Categoría 15', 'Documentación municipal 15'),
    ('Categoría 16', 'Documentación municipal 16'),
    ('Categoría 17', 'Documentación municipal 17'),
    ('Categoría 18', 'Documentación municipal 18'),
    ('Categoría 19', 'Documentación municipal 19'),
    ('Categoría 20', 'Documentación municipal 20');

INSERT INTO document_tags (nombre) VALUES
    ('Etiqueta 1'),
    ('Etiqueta 2'),
    ('Etiqueta 3'),
    ('Etiqueta 4'),
    ('Etiqueta 5'),
    ('Etiqueta 6'),
    ('Etiqueta 7'),
    ('Etiqueta 8'),
    ('Etiqueta 9'),
    ('Etiqueta 10'),
    ('Etiqueta 11'),
    ('Etiqueta 12'),
    ('Etiqueta 13'),
    ('Etiqueta 14'),
    ('Etiqueta 15'),
    ('Etiqueta 16'),
    ('Etiqueta 17'),
    ('Etiqueta 18'),
    ('Etiqueta 19'),
    ('Etiqueta 20');

INSERT INTO documents (titulo, descripcion, categoria_id, unidad_id, estado, created_by) VALUES
    ('Documento QA 1', 'Documento municipal 1 para QA', 1, 1, 'vigente', 1),
    ('Documento QA 2', 'Documento municipal 2 para QA', 2, 2, 'vigente', 2),
    ('Documento QA 3', 'Documento municipal 3 para QA', 3, 3, 'vigente', 3),
    ('Documento QA 4', 'Documento municipal 4 para QA', 4, 4, 'vigente', 4),
    ('Documento QA 5', 'Documento municipal 5 para QA', 5, 5, 'vigente', 5),
    ('Documento QA 6', 'Documento municipal 6 para QA', 6, 6, 'vigente', 6),
    ('Documento QA 7', 'Documento municipal 7 para QA', 7, 7, 'vigente', 7),
    ('Documento QA 8', 'Documento municipal 8 para QA', 8, 8, 'vigente', 8),
    ('Documento QA 9', 'Documento municipal 9 para QA', 9, 9, 'vigente', 9),
    ('Documento QA 10', 'Documento municipal 10 para QA', 10, 10, 'vigente', 10),
    ('Documento QA 11', 'Documento municipal 11 para QA', 11, 11, 'vigente', 11),
    ('Documento QA 12', 'Documento municipal 12 para QA', 12, 12, 'vigente', 12),
    ('Documento QA 13', 'Documento municipal 13 para QA', 13, 13, 'vigente', 13),
    ('Documento QA 14', 'Documento municipal 14 para QA', 14, 14, 'vigente', 14),
    ('Documento QA 15', 'Documento municipal 15 para QA', 15, 15, 'vigente', 15),
    ('Documento QA 16', 'Documento municipal 16 para QA', 16, 16, 'vigente', 16),
    ('Documento QA 17', 'Documento municipal 17 para QA', 17, 17, 'vigente', 17),
    ('Documento QA 18', 'Documento municipal 18 para QA', 18, 18, 'vigente', 18),
    ('Documento QA 19', 'Documento municipal 19 para QA', 19, 19, 'vigente', 19),
    ('Documento QA 20', 'Documento municipal 20 para QA', 20, 20, 'vigente', 20);

INSERT INTO document_versions (document_id, version, archivo_ruta, archivo_tipo, vencimiento, created_by) VALUES
    (1, 'v1.1', 'uploads/documentos/doc_1.pdf', 'application/pdf', '2026-12-31', 1),
    (2, 'v1.2', 'uploads/documentos/doc_2.pdf', 'application/pdf', '2026-12-31', 2),
    (3, 'v1.3', 'uploads/documentos/doc_3.pdf', 'application/pdf', '2026-12-31', 3),
    (4, 'v1.4', 'uploads/documentos/doc_4.pdf', 'application/pdf', '2026-12-31', 4),
    (5, 'v1.5', 'uploads/documentos/doc_5.pdf', 'application/pdf', '2026-12-31', 5),
    (6, 'v1.6', 'uploads/documentos/doc_6.pdf', 'application/pdf', '2026-12-31', 6),
    (7, 'v1.7', 'uploads/documentos/doc_7.pdf', 'application/pdf', '2026-12-31', 7),
    (8, 'v1.8', 'uploads/documentos/doc_8.pdf', 'application/pdf', '2026-12-31', 8),
    (9, 'v1.9', 'uploads/documentos/doc_9.pdf', 'application/pdf', '2026-12-31', 9),
    (10, 'v1.10', 'uploads/documentos/doc_10.pdf', 'application/pdf', '2026-12-31', 10),
    (11, 'v1.11', 'uploads/documentos/doc_11.pdf', 'application/pdf', '2026-12-31', 11),
    (12, 'v1.12', 'uploads/documentos/doc_12.pdf', 'application/pdf', '2026-12-31', 12),
    (13, 'v1.13', 'uploads/documentos/doc_13.pdf', 'application/pdf', '2026-12-31', 13),
    (14, 'v1.14', 'uploads/documentos/doc_14.pdf', 'application/pdf', '2026-12-31', 14),
    (15, 'v1.15', 'uploads/documentos/doc_15.pdf', 'application/pdf', '2026-12-31', 15),
    (16, 'v1.16', 'uploads/documentos/doc_16.pdf', 'application/pdf', '2026-12-31', 16),
    (17, 'v1.17', 'uploads/documentos/doc_17.pdf', 'application/pdf', '2026-12-31', 17),
    (18, 'v1.18', 'uploads/documentos/doc_18.pdf', 'application/pdf', '2026-12-31', 18),
    (19, 'v1.19', 'uploads/documentos/doc_19.pdf', 'application/pdf', '2026-12-31', 19),
    (20, 'v1.20', 'uploads/documentos/doc_20.pdf', 'application/pdf', '2026-12-31', 20);

INSERT INTO document_tag_links (document_id, tag_id) VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (5, 5),
    (6, 6),
    (7, 7),
    (8, 8),
    (9, 9),
    (10, 10),
    (11, 11),
    (12, 12),
    (13, 13),
    (14, 14),
    (15, 15),
    (16, 16),
    (17, 17),
    (18, 18),
    (19, 19),
    (20, 20);

INSERT INTO document_access (document_id, role_id) VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (5, 5),
    (6, 6),
    (7, 7),
    (8, 8),
    (9, 9),
    (10, 10),
    (11, 11),
    (12, 12),
    (13, 13),
    (14, 14),
    (15, 15),
    (16, 16),
    (17, 17),
    (18, 18),
    (19, 19),
    (20, 20);

INSERT INTO document_shares (document_id, user_id) VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (5, 5),
    (6, 6),
    (7, 7),
    (8, 8),
    (9, 9),
    (10, 10);

INSERT INTO approval_flows (nombre, entidad, unidad_id, sla_horas, estado) VALUES
    ('Flujo QA 1', 'Eventos', 1, 48, 'activo'),
    ('Flujo QA 2', 'Eventos', 2, 48, 'activo'),
    ('Flujo QA 3', 'Eventos', 3, 48, 'activo'),
    ('Flujo QA 4', 'Eventos', 4, 48, 'activo'),
    ('Flujo QA 5', 'Eventos', 5, 48, 'activo'),
    ('Flujo QA 6', 'Eventos', 6, 48, 'activo'),
    ('Flujo QA 7', 'Eventos', 7, 48, 'activo'),
    ('Flujo QA 8', 'Eventos', 8, 48, 'activo'),
    ('Flujo QA 9', 'Eventos', 9, 48, 'activo'),
    ('Flujo QA 10', 'Eventos', 10, 48, 'activo'),
    ('Flujo QA 11', 'Eventos', 11, 48, 'activo'),
    ('Flujo QA 12', 'Eventos', 12, 48, 'activo'),
    ('Flujo QA 13', 'Eventos', 13, 48, 'activo'),
    ('Flujo QA 14', 'Eventos', 14, 48, 'activo'),
    ('Flujo QA 15', 'Eventos', 15, 48, 'activo'),
    ('Flujo QA 16', 'Eventos', 16, 48, 'activo'),
    ('Flujo QA 17', 'Eventos', 17, 48, 'activo'),
    ('Flujo QA 18', 'Eventos', 18, 48, 'activo'),
    ('Flujo QA 19', 'Eventos', 19, 48, 'activo'),
    ('Flujo QA 20', 'Eventos', 20, 48, 'activo');

INSERT INTO approval_steps (flow_id, orden, responsable) VALUES
    (1, 1, 'Responsable QA 1'),
    (2, 1, 'Responsable QA 2'),
    (3, 1, 'Responsable QA 3'),
    (4, 1, 'Responsable QA 4'),
    (5, 1, 'Responsable QA 5'),
    (6, 1, 'Responsable QA 6'),
    (7, 1, 'Responsable QA 7'),
    (8, 1, 'Responsable QA 8'),
    (9, 1, 'Responsable QA 9'),
    (10, 1, 'Responsable QA 10'),
    (11, 1, 'Responsable QA 11'),
    (12, 1, 'Responsable QA 12'),
    (13, 1, 'Responsable QA 13'),
    (14, 1, 'Responsable QA 14'),
    (15, 1, 'Responsable QA 15'),
    (16, 1, 'Responsable QA 16'),
    (17, 1, 'Responsable QA 17'),
    (18, 1, 'Responsable QA 18'),
    (19, 1, 'Responsable QA 19'),
    (20, 1, 'Responsable QA 20');

INSERT INTO notification_rules (evento, destino, canal, estado) VALUES
    ('Operativo de Salud', 'destino1@qa.cl', 'email', 'activa'),
    ('Feria de Emprendimiento', 'destino2@qa.cl', 'email', 'activa'),
    ('Cabildo Vecinal', 'destino3@qa.cl', 'email', 'activa'),
    ('Taller de Prevención', 'destino4@qa.cl', 'email', 'activa'),
    ('Feria Costumbrista', 'destino5@qa.cl', 'email', 'activa'),
    ('Campaña de Reciclaje', 'destino6@qa.cl', 'email', 'activa'),
    ('Encuentro Deportivo', 'destino7@qa.cl', 'email', 'activa'),
    ('Charla Comunitaria', 'destino8@qa.cl', 'email', 'activa'),
    ('Festival Cultural', 'destino9@qa.cl', 'email', 'activa'),
    ('Operativo Veterinario', 'destino10@qa.cl', 'email', 'activa'),
    ('Expo Educativa', 'destino11@qa.cl', 'email', 'activa'),
    ('Reunión de Seguridad', 'destino12@qa.cl', 'email', 'activa'),
    ('Jornada de Participación', 'destino13@qa.cl', 'email', 'activa'),
    ('Operativo Social', 'destino14@qa.cl', 'email', 'activa'),
    ('Capacitación Municipal', 'destino15@qa.cl', 'email', 'activa'),
    ('Feria Laboral', 'destino16@qa.cl', 'email', 'activa'),
    ('Reunión de Coordinación', 'destino17@qa.cl', 'email', 'activa'),
    ('Encuentro de Adulto Mayor', 'destino18@qa.cl', 'email', 'activa'),
    ('Actividad Infantil', 'destino19@qa.cl', 'email', 'activa'),
    ('Mesa Territorial', 'destino20@qa.cl', 'email', 'activa');

INSERT INTO user_roles (user_id, role_id) VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (5, 5),
    (6, 6),
    (7, 7),
    (8, 8),
    (9, 9),
    (10, 10),
    (11, 11),
    (12, 12),
    (13, 13),
    (14, 14),
    (15, 15),
    (16, 16),
    (17, 17),
    (18, 18),
    (19, 19),
    (20, 20);

INSERT INTO role_permissions (role_id, permission_id) VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (5, 5),
    (6, 6),
    (7, 7),
    (8, 8),
    (9, 9),
    (10, 10),
    (11, 11),
    (12, 12),
    (13, 13),
    (14, 14),
    (15, 15),
    (16, 16),
    (17, 17),
    (18, 18),
    (19, 19),
    (20, 20);

INSERT INTO role_unit_permissions (role_id, unidad_id, permission_id) VALUES
    (1, 1, 1),
    (2, 2, 2),
    (3, 3, 3),
    (4, 4, 4),
    (5, 5, 5),
    (6, 6, 6),
    (7, 7, 7),
    (8, 8, 8),
    (9, 9, 9),
    (10, 10, 10),
    (11, 11, 11),
    (12, 12, 12),
    (13, 13, 13),
    (14, 14, 14),
    (15, 15, 15),
    (16, 16, 16),
    (17, 17, 17),
    (18, 18, 18),
    (19, 19, 19),
    (20, 20, 20);

INSERT INTO municipalidad (nombre, rut, direccion, telefono, correo, logo_path, logo_topbar_height, logo_sidenav_height, logo_sidenav_height_sm, color_primary, color_secondary) VALUES
    ('Municipalidad de Go Muni', '76.123.456-7', 'Av. Principal 123', '+56 2 2345 6789', 'contacto@gomuni.cl', 'assets/images/logo.png', 56, 48, 36, '#1f6feb', '#0ea5e9');

INSERT INTO notificacion_correos (correo_imap, password_imap, host_imap, puerto_imap, seguridad_imap, from_nombre, from_correo) VALUES
    ('notificaciones@gomuni.cl', 'DemoPass123', 'imap.gomuni.cl', 993, 'ssl', 'Go Muni', 'notificaciones@gomuni.cl');

INSERT INTO notification_settings (canal_email, canal_sms, canal_app, frecuencia) VALUES
    (1, 0, 1, 'diario');

INSERT INTO audit_logs (user_id, tabla, accion, registro_id, descripcion) VALUES
    (1, 'events', 'crear', 1, 'Evento creado desde QA'),
    (2, 'authorities', 'crear', 2, 'Autoridad creada desde QA'),
    (3, 'documents', 'crear', 3, 'Documento creado desde QA'),
    (4, 'users', 'actualizar', 4, 'Usuario actualizado desde QA'),
    (5, 'roles', 'crear', 5, 'Rol creado desde QA'),
    (6, 'document_categories', 'crear', 6, 'Categoría creada desde QA'),
    (7, 'document_tags', 'crear', 7, 'Etiqueta creada desde QA'),
    (8, 'event_authorities', 'actualizar', 8, 'Asignación de autoridades actualizada'),
    (9, 'document_shares', 'crear', 9, 'Documento compartido'),
    (10, 'notification_settings', 'actualizar', 1, 'Configuración de notificaciones actualizada');

INSERT INTO user_sessions (user_id, session_id, ip, user_agent, last_activity) VALUES
    (1, 'sess-1', '192.168.1.1', 'QA Agent 1', NOW()),
    (2, 'sess-2', '192.168.1.2', 'QA Agent 2', NOW()),
    (3, 'sess-3', '192.168.1.3', 'QA Agent 3', NOW()),
    (4, 'sess-4', '192.168.1.4', 'QA Agent 4', NOW()),
    (5, 'sess-5', '192.168.1.5', 'QA Agent 5', NOW()),
    (6, 'sess-6', '192.168.1.6', 'QA Agent 6', NOW()),
    (7, 'sess-7', '192.168.1.7', 'QA Agent 7', NOW()),
    (8, 'sess-8', '192.168.1.8', 'QA Agent 8', NOW()),
    (9, 'sess-9', '192.168.1.9', 'QA Agent 9', NOW()),
    (10, 'sess-10', '192.168.1.10', 'QA Agent 10', NOW()),
    (11, 'sess-11', '192.168.1.11', 'QA Agent 11', NOW()),
    (12, 'sess-12', '192.168.1.12', 'QA Agent 12', NOW()),
    (13, 'sess-13', '192.168.1.13', 'QA Agent 13', NOW()),
    (14, 'sess-14', '192.168.1.14', 'QA Agent 14', NOW()),
    (15, 'sess-15', '192.168.1.15', 'QA Agent 15', NOW()),
    (16, 'sess-16', '192.168.1.16', 'QA Agent 16', NOW()),
    (17, 'sess-17', '192.168.1.17', 'QA Agent 17', NOW()),
    (18, 'sess-18', '192.168.1.18', 'QA Agent 18', NOW()),
    (19, 'sess-19', '192.168.1.19', 'QA Agent 19', NOW()),
    (20, 'sess-20', '192.168.1.20', 'QA Agent 20', NOW());