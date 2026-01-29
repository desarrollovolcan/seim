-- Limpia datos dejando solo el usuario superadmin actual y su empresa.
-- Ajusta el filtro si tu superadmin usa otra marca/rol.

SET FOREIGN_KEY_CHECKS = 0;

-- Conserva solo usuarios superadmin
DELETE FROM users
WHERE is_superadmin = 0;

-- Conserva solo empresas vinculadas a usuarios superadmin
DELETE FROM empresas
WHERE id NOT IN (
    SELECT DISTINCT empresa_id
    FROM users
    WHERE empresa_id IS NOT NULL
);

SET FOREIGN_KEY_CHECKS = 1;
