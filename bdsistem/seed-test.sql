-- Datos de prueba fijos (idempotentes)
-- Este script asegura que la empresa y el usuario de prueba existan en cada actualización.

-- Empresa de prueba (ID 1)
INSERT INTO empresas (id, nombre, razon_social, ruc, telefono, correo, direccion, logo_default)
VALUES (1, 'Acquaperla', 'Acquaperla SpA', '99999999-9', '+56 9 6000 0000', 'contacto@acquaperla.cl', 'Av. Principal 123', 1)
ON DUPLICATE KEY UPDATE
  nombre = VALUES(nombre),
  razon_social = VALUES(razon_social),
  ruc = VALUES(ruc),
  telefono = VALUES(telefono),
  correo = VALUES(correo),
  direccion = VALUES(direccion),
  logo_default = VALUES(logo_default);

-- Usuario de prueba (ID 1)
INSERT INTO users (
  id,
  empresa_id,
  rut,
  nombre,
  apellido,
  correo,
  telefono,
  direccion,
  username,
  rol,
  password_hash,
  password_locked,
  is_superadmin,
  estado
)
VALUES (
  1,
  1,
  '100.000.000-0',
  'Super',
  'Administrador',
  'superadmin@acquaperla.cl',
  '+56 9 6000 0001',
  'Av. Principal 123',
  'superadmin',
  'Super Administrador',
  '$2y$12$V5dGzFBh96YkYUt73d3tS.tsoUu1qF7pBwWc0vpcbr4yQjDHRJu.O',
  1,
  1,
  1
)
ON DUPLICATE KEY UPDATE
  empresa_id = VALUES(empresa_id),
  rut = VALUES(rut),
  nombre = VALUES(nombre),
  apellido = VALUES(apellido),
  correo = VALUES(correo),
  telefono = VALUES(telefono),
  direccion = VALUES(direccion),
  username = VALUES(username),
  rol = VALUES(rol),
  password_hash = VALUES(password_hash),
  password_locked = VALUES(password_locked),
  is_superadmin = VALUES(is_superadmin),
  estado = VALUES(estado);

-- Asociación usuario-empresa (idempotente)
INSERT INTO user_empresas (user_id, empresa_id)
VALUES (1, 1)
ON DUPLICATE KEY UPDATE
  user_id = VALUES(user_id),
  empresa_id = VALUES(empresa_id);
