-- Datos base mínimos
-- Sección: empresa
INSERT INTO empresas (nombre, razon_social, ruc, telefono, correo, direccion)
VALUES ('Acquaperla', 'Acquaperla SpA', '99999999-9', '+56 9 6000 0000', 'contacto@acquaperla.cl', 'Av. Principal 123');

-- Sección: super usuario
INSERT INTO users (empresa_id, rut, nombre, apellido, correo, telefono, direccion, username, rol, password_hash, password_locked, is_superadmin, estado)
VALUES (1, '100.000.000-0', 'Super', 'Administrador', 'superadmin@acquaperla.cl', '+56 9 6000 0001', 'Av. Principal 123', 'superadmin', 'Super Administrador', '$2y$12$3ZBzq8bVxi/JfrIjwLt14OzSksyeb0PX95kdyA.jjLSj5PgaancpS', 1, 1, 1);
