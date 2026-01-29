-- Datos base mínimos
-- Sección: empresa
INSERT INTO empresas (nombre, razon_social, ruc, telefono, correo, direccion, logo_default)
VALUES ('Acquaperla', 'Acquaperla SpA', '99999999-9', '+56 9 6000 0000', 'contacto@acquaperla.cl', 'Av. Principal 123', 1);

-- Sección: roles
INSERT INTO roles (nombre, descripcion)
VALUES
  ('Super Administrador', 'Acceso total al sistema'),
  ('Administrador', 'Acceso total al sistema'),
  ('Operador', 'Gestión operativa'),
  ('Consulta', 'Solo lectura');

-- Sección: super usuario
INSERT INTO users (empresa_id, rut, nombre, apellido, correo, telefono, direccion, username, rol, password_hash, password_locked, is_superadmin, estado)
VALUES (1, '100.000.000-0', 'Super', 'Administrador', 'superadmin@acquaperla.cl', '+56 9 6000 0001', 'Av. Principal 123', 'superadmin', 'Super Administrador', '$2y$12$V5dGzFBh96YkYUt73d3tS.tsoUu1qF7pBwWc0vpcbr4yQjDHRJu.O', 1, 1, 1);

-- Sección: usuario-empresa
INSERT INTO user_empresas (user_id, empresa_id)
VALUES (1, 1);
