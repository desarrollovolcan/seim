-- Poblar regiones y comunas desde la tabla chile_communes (sin ciudades).
INSERT IGNORE INTO regions (name)
SELECT DISTINCT region
FROM chile_communes
WHERE region <> ''
ORDER BY region;

INSERT IGNORE INTO communes (name, region_id)
SELECT chile_communes.commune, regions.id
FROM chile_communes
JOIN regions ON regions.name = chile_communes.region
WHERE chile_communes.commune <> ''
ORDER BY chile_communes.commune;
