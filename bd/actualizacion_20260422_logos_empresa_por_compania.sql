START TRANSACTION;

SET @companies_logo_color := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'companies'
      AND COLUMN_NAME = 'logo_color'
);
SET @sql := IF(
    @companies_logo_color = 0,
    'ALTER TABLE companies ADD COLUMN logo_color VARCHAR(255) NULL AFTER commune;',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @companies_logo_black := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'companies'
      AND COLUMN_NAME = 'logo_black'
);
SET @sql := IF(
    @companies_logo_black = 0,
    'ALTER TABLE companies ADD COLUMN logo_black VARCHAR(255) NULL AFTER logo_color;',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO settings (company_id, `key`, value, created_at, updated_at)
SELECT
    c.id,
    'company',
    JSON_OBJECT(
        'name', COALESCE(c.name, ''),
        'rut', COALESCE(c.rut, ''),
        'email', COALESCE(c.email, ''),
        'phone', COALESCE(c.phone, ''),
        'address', COALESCE(c.address, ''),
        'giro', COALESCE(c.giro, ''),
        'commune', COALESCE(c.commune, ''),
        'logo_color', COALESCE(c.logo_color, ''),
        'logo_black', COALESCE(c.logo_black, '')
    ),
    NOW(),
    NOW()
FROM companies c
WHERE NOT EXISTS (
    SELECT 1
    FROM settings s
    WHERE s.company_id = c.id
      AND s.`key` = 'company'
);

COMMIT;
