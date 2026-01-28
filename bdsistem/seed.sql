-- Datos base mínimos
-- Sección: empresa
INSERT INTO empresas (nombre, razon_social, ruc, telefono, correo, direccion)
VALUES ('Empresa Demo', 'Empresa Demo S.A.', '99999999-9', '+56 9 6000 0000', 'contacto@empresa-demo.cl', 'Av. Principal 123');

-- Sección: super usuario
INSERT INTO users (empresa_id, rut, nombre, apellido, correo, telefono, direccion, username, rol, password_hash, password_locked, is_superadmin, estado)
VALUES (1, '100.000.000-0', 'Super', 'Administrador', 'superadmin@empresa-demo.cl', '+56 9 6000 0001', 'Av. Principal 123', 'superadmin', 'Super Administrador', '$2y$12$JAhfqQwEtM4I4tccGNnOouQ7DKgwiG1hKo6UIvoS0dbYFJ.BXGuW6', 1, 1, 1);
