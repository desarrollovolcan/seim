-- Base completa generada al concatenar database.sql + actualizaciones.
-- Orden: database.sql, actualizaciones 20250301, 20250320, 20251231, 20260415,
-- 20260420, 20260425, 20260428 (OC), 20260428 (detalle OV), acumulada POS/Inventario.

CREATE DATABASE IF NOT EXISTS gocreative_ges CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE gocreative_ges;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    rut VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    address VARCHAR(255) NULL,
    giro VARCHAR(150) NULL,
    activity_code VARCHAR(50) NULL,
    commune VARCHAR(120) NULL,
    city VARCHAR(120) NULL,
    logo_color VARCHAR(255) NULL,
    logo_black VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE chile_communes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commune VARCHAR(150) NOT NULL,
    city VARCHAR(150) NOT NULL,
    region VARCHAR(150) NOT NULL,
    UNIQUE KEY uniq_chile_communes_commune (commune),
    INDEX idx_chile_communes_city (city),
    INDEX idx_chile_communes_region (region)
);

CREATE TABLE regions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    UNIQUE KEY uniq_regions_name (name)
);

CREATE TABLE cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    UNIQUE KEY uniq_cities_region_name (region_id, name),
    INDEX idx_cities_name (name),
    FOREIGN KEY (region_id) REFERENCES regions(id)
);

CREATE TABLE communes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    UNIQUE KEY uniq_communes_city_name (city_id, name),
    INDEX idx_communes_name (name),
    FOREIGN KEY (city_id) REFERENCES cities(id)
);

CREATE TABLE IF NOT EXISTS sii_activity_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL,
    name VARCHAR(255) NOT NULL,
    UNIQUE KEY uniq_sii_activity_code (code),
    INDEX idx_sii_activity_name (name)
);

INSERT INTO chile_communes (commune, city, region) VALUES
('Arica', 'Arica', 'Arica y Parinacota'),
('Camarones', 'Camarones', 'Arica y Parinacota'),
('Putre', 'Putre', 'Arica y Parinacota'),
('General Lagos', 'General Lagos', 'Arica y Parinacota'),
('Iquique', 'Iquique', 'Tarapacá'),
('Alto Hospicio', 'Alto Hospicio', 'Tarapacá'),
('Pozo Almonte', 'Pozo Almonte', 'Tarapacá'),
('Camiña', 'Camiña', 'Tarapacá'),
('Colchane', 'Colchane', 'Tarapacá'),
('Huara', 'Huara', 'Tarapacá'),
('Pica', 'Pica', 'Tarapacá'),
('Antofagasta', 'Antofagasta', 'Antofagasta'),
('Mejillones', 'Mejillones', 'Antofagasta'),
('Sierra Gorda', 'Sierra Gorda', 'Antofagasta'),
('Taltal', 'Taltal', 'Antofagasta'),
('Calama', 'Calama', 'Antofagasta'),
('Ollagüe', 'Ollagüe', 'Antofagasta'),
('San Pedro de Atacama', 'San Pedro de Atacama', 'Antofagasta'),
('Tocopilla', 'Tocopilla', 'Antofagasta'),
('María Elena', 'María Elena', 'Antofagasta'),
('Copiapó', 'Copiapó', 'Atacama'),
('Caldera', 'Caldera', 'Atacama'),
('Tierra Amarilla', 'Tierra Amarilla', 'Atacama'),
('Chañaral', 'Chañaral', 'Atacama'),
('Diego de Almagro', 'Diego de Almagro', 'Atacama'),
('Vallenar', 'Vallenar', 'Atacama'),
('Alto del Carmen', 'Alto del Carmen', 'Atacama'),
('Freirina', 'Freirina', 'Atacama'),
('Huasco', 'Huasco', 'Atacama'),
('La Serena', 'La Serena', 'Coquimbo'),
('Coquimbo', 'Coquimbo', 'Coquimbo'),
('Andacollo', 'Andacollo', 'Coquimbo'),
('La Higuera', 'La Higuera', 'Coquimbo'),
('Paiguano', 'Paiguano', 'Coquimbo'),
('Vicuña', 'Vicuña', 'Coquimbo'),
('Illapel', 'Illapel', 'Coquimbo'),
('Canela', 'Canela', 'Coquimbo'),
('Los Vilos', 'Los Vilos', 'Coquimbo'),
('Salamanca', 'Salamanca', 'Coquimbo'),
('Ovalle', 'Ovalle', 'Coquimbo'),
('Combarbalá', 'Combarbalá', 'Coquimbo'),
('Monte Patria', 'Monte Patria', 'Coquimbo'),
('Punitaqui', 'Punitaqui', 'Coquimbo'),
('Río Hurtado', 'Río Hurtado', 'Coquimbo'),
('Valparaíso', 'Valparaíso', 'Valparaíso'),
('Casablanca', 'Casablanca', 'Valparaíso'),
('Concón', 'Concón', 'Valparaíso'),
('Juan Fernández', 'Juan Fernández', 'Valparaíso'),
('Puchuncaví', 'Puchuncaví', 'Valparaíso'),
('Quintero', 'Quintero', 'Valparaíso'),
('Viña del Mar', 'Viña del Mar', 'Valparaíso'),
('Isla de Pascua', 'Isla de Pascua', 'Valparaíso'),
('Los Andes', 'Los Andes', 'Valparaíso'),
('Calle Larga', 'Calle Larga', 'Valparaíso'),
('Rinconada', 'Rinconada', 'Valparaíso'),
('San Esteban', 'San Esteban', 'Valparaíso'),
('La Ligua', 'La Ligua', 'Valparaíso'),
('Cabildo', 'Cabildo', 'Valparaíso'),
('Papudo', 'Papudo', 'Valparaíso'),
('Petorca', 'Petorca', 'Valparaíso'),
('Zapallar', 'Zapallar', 'Valparaíso'),
('Quillota', 'Quillota', 'Valparaíso'),
('La Calera', 'La Calera', 'Valparaíso'),
('Hijuelas', 'Hijuelas', 'Valparaíso'),
('La Cruz', 'La Cruz', 'Valparaíso'),
('Nogales', 'Nogales', 'Valparaíso'),
('San Antonio', 'San Antonio', 'Valparaíso'),
('Algarrobo', 'Algarrobo', 'Valparaíso'),
('Cartagena', 'Cartagena', 'Valparaíso'),
('El Quisco', 'El Quisco', 'Valparaíso'),
('El Tabo', 'El Tabo', 'Valparaíso'),
('Santo Domingo', 'Santo Domingo', 'Valparaíso'),
('San Felipe', 'San Felipe', 'Valparaíso'),
('Catemu', 'Catemu', 'Valparaíso'),
('Llaillay', 'Llaillay', 'Valparaíso'),
('Panquehue', 'Panquehue', 'Valparaíso'),
('Putaendo', 'Putaendo', 'Valparaíso'),
('Santa María', 'Santa María', 'Valparaíso'),
('Limache', 'Limache', 'Valparaíso'),
('Olmué', 'Olmué', 'Valparaíso'),
('Quilpué', 'Quilpué', 'Valparaíso'),
('Villa Alemana', 'Villa Alemana', 'Valparaíso'),
('Santiago', 'Santiago', 'Metropolitana de Santiago'),
('Cerrillos', 'Cerrillos', 'Metropolitana de Santiago'),
('Cerro Navia', 'Cerro Navia', 'Metropolitana de Santiago'),
('Conchalí', 'Conchalí', 'Metropolitana de Santiago'),
('El Bosque', 'El Bosque', 'Metropolitana de Santiago'),
('Estación Central', 'Estación Central', 'Metropolitana de Santiago'),
('Huechuraba', 'Huechuraba', 'Metropolitana de Santiago'),
('Independencia', 'Independencia', 'Metropolitana de Santiago'),
('La Cisterna', 'La Cisterna', 'Metropolitana de Santiago'),
('La Florida', 'La Florida', 'Metropolitana de Santiago'),
('La Granja', 'La Granja', 'Metropolitana de Santiago'),
('La Pintana', 'La Pintana', 'Metropolitana de Santiago'),
('La Reina', 'La Reina', 'Metropolitana de Santiago'),
('Las Condes', 'Las Condes', 'Metropolitana de Santiago'),
('Lo Barnechea', 'Lo Barnechea', 'Metropolitana de Santiago'),
('Lo Espejo', 'Lo Espejo', 'Metropolitana de Santiago'),
('Lo Prado', 'Lo Prado', 'Metropolitana de Santiago'),
('Macul', 'Macul', 'Metropolitana de Santiago'),
('Maipú', 'Maipú', 'Metropolitana de Santiago'),
('Ñuñoa', 'Ñuñoa', 'Metropolitana de Santiago'),
('Pedro Aguirre Cerda', 'Pedro Aguirre Cerda', 'Metropolitana de Santiago'),
('Peñalolén', 'Peñalolén', 'Metropolitana de Santiago'),
('Providencia', 'Providencia', 'Metropolitana de Santiago'),
('Pudahuel', 'Pudahuel', 'Metropolitana de Santiago'),
('Quilicura', 'Quilicura', 'Metropolitana de Santiago'),
('Quinta Normal', 'Quinta Normal', 'Metropolitana de Santiago'),
('Recoleta', 'Recoleta', 'Metropolitana de Santiago'),
('Renca', 'Renca', 'Metropolitana de Santiago'),
('San Joaquín', 'San Joaquín', 'Metropolitana de Santiago'),
('San Miguel', 'San Miguel', 'Metropolitana de Santiago'),
('San Ramón', 'San Ramón', 'Metropolitana de Santiago'),
('Vitacura', 'Vitacura', 'Metropolitana de Santiago'),
('Puente Alto', 'Puente Alto', 'Metropolitana de Santiago'),
('Pirque', 'Pirque', 'Metropolitana de Santiago'),
('San José de Maipo', 'San José de Maipo', 'Metropolitana de Santiago'),
('Colina', 'Colina', 'Metropolitana de Santiago'),
('Lampa', 'Lampa', 'Metropolitana de Santiago'),
('Tiltil', 'Tiltil', 'Metropolitana de Santiago'),
('San Bernardo', 'San Bernardo', 'Metropolitana de Santiago'),
('Buin', 'Buin', 'Metropolitana de Santiago'),
('Calera de Tango', 'Calera de Tango', 'Metropolitana de Santiago'),
('Paine', 'Paine', 'Metropolitana de Santiago'),
('Melipilla', 'Melipilla', 'Metropolitana de Santiago'),
('Alhué', 'Alhué', 'Metropolitana de Santiago'),
('Curacaví', 'Curacaví', 'Metropolitana de Santiago'),
('María Pinto', 'María Pinto', 'Metropolitana de Santiago'),
('San Pedro', 'San Pedro', 'Metropolitana de Santiago'),
('Talagante', 'Talagante', 'Metropolitana de Santiago'),
('El Monte', 'El Monte', 'Metropolitana de Santiago'),
('Isla de Maipo', 'Isla de Maipo', 'Metropolitana de Santiago'),
('Padre Hurtado', 'Padre Hurtado', 'Metropolitana de Santiago'),
('Peñaflor', 'Peñaflor', 'Metropolitana de Santiago'),
('Rancagua', 'Rancagua', 'Libertador General Bernardo O\'Higgins'),
('Codegua', 'Codegua', 'Libertador General Bernardo O\'Higgins'),
('Coinco', 'Coinco', 'Libertador General Bernardo O\'Higgins'),
('Coltauco', 'Coltauco', 'Libertador General Bernardo O\'Higgins'),
('Doñihue', 'Doñihue', 'Libertador General Bernardo O\'Higgins'),
('Graneros', 'Graneros', 'Libertador General Bernardo O\'Higgins'),
('Las Cabras', 'Las Cabras', 'Libertador General Bernardo O\'Higgins'),
('Machalí', 'Machalí', 'Libertador General Bernardo O\'Higgins'),
('Malloa', 'Malloa', 'Libertador General Bernardo O\'Higgins'),
('Mostazal', 'Mostazal', 'Libertador General Bernardo O\'Higgins'),
('Olivar', 'Olivar', 'Libertador General Bernardo O\'Higgins'),
('Peumo', 'Peumo', 'Libertador General Bernardo O\'Higgins'),
('Pichidegua', 'Pichidegua', 'Libertador General Bernardo O\'Higgins'),
('Quinta de Tilcoco', 'Quinta de Tilcoco', 'Libertador General Bernardo O\'Higgins'),
('Rengo', 'Rengo', 'Libertador General Bernardo O\'Higgins'),
('Requínoa', 'Requínoa', 'Libertador General Bernardo O\'Higgins'),
('San Vicente', 'San Vicente', 'Libertador General Bernardo O\'Higgins'),
('San Fernando', 'San Fernando', 'Libertador General Bernardo O\'Higgins'),
('Chimbarongo', 'Chimbarongo', 'Libertador General Bernardo O\'Higgins'),
('Lolol', 'Lolol', 'Libertador General Bernardo O\'Higgins'),
('Nancagua', 'Nancagua', 'Libertador General Bernardo O\'Higgins'),
('Palmilla', 'Palmilla', 'Libertador General Bernardo O\'Higgins'),
('Peralillo', 'Peralillo', 'Libertador General Bernardo O\'Higgins'),
('Placilla', 'Placilla', 'Libertador General Bernardo O\'Higgins'),
('Pumanque', 'Pumanque', 'Libertador General Bernardo O\'Higgins'),
('Santa Cruz', 'Santa Cruz', 'Libertador General Bernardo O\'Higgins'),
('Pichilemu', 'Pichilemu', 'Libertador General Bernardo O\'Higgins'),
('La Estrella', 'La Estrella', 'Libertador General Bernardo O\'Higgins'),
('Litueche', 'Litueche', 'Libertador General Bernardo O\'Higgins'),
('Marchihue', 'Marchihue', 'Libertador General Bernardo O\'Higgins'),
('Navidad', 'Navidad', 'Libertador General Bernardo O\'Higgins'),
('Paredones', 'Paredones', 'Libertador General Bernardo O\'Higgins'),
('Talca', 'Talca', 'Maule'),
('Constitución', 'Constitución', 'Maule'),
('Curepto', 'Curepto', 'Maule'),
('Empedrado', 'Empedrado', 'Maule'),
('Maule', 'Maule', 'Maule'),
('Pelarco', 'Pelarco', 'Maule'),
('Pencahue', 'Pencahue', 'Maule'),
('Río Claro', 'Río Claro', 'Maule'),
('San Clemente', 'San Clemente', 'Maule'),
('San Rafael', 'San Rafael', 'Maule'),
('Cauquenes', 'Cauquenes', 'Maule'),
('Chanco', 'Chanco', 'Maule'),
('Pelluhue', 'Pelluhue', 'Maule'),
('Curicó', 'Curicó', 'Maule'),
('Hualañé', 'Hualañé', 'Maule'),
('Licantén', 'Licantén', 'Maule'),
('Molina', 'Molina', 'Maule'),
('Rauco', 'Rauco', 'Maule'),
('Romeral', 'Romeral', 'Maule'),
('Sagrada Familia', 'Sagrada Familia', 'Maule'),
('Teno', 'Teno', 'Maule'),
('Vichuquén', 'Vichuquén', 'Maule'),
('Linares', 'Linares', 'Maule'),
('Colbún', 'Colbún', 'Maule'),
('Longaví', 'Longaví', 'Maule'),
('Parral', 'Parral', 'Maule'),
('Retiro', 'Retiro', 'Maule'),
('San Javier', 'San Javier', 'Maule'),
('Villa Alegre', 'Villa Alegre', 'Maule'),
('Yerbas Buenas', 'Yerbas Buenas', 'Maule'),
('Chillán', 'Chillán', 'Ñuble'),
('Bulnes', 'Bulnes', 'Ñuble'),
('Chillán Viejo', 'Chillán Viejo', 'Ñuble'),
('Cobquecura', 'Cobquecura', 'Ñuble'),
('Coelemu', 'Coelemu', 'Ñuble'),
('Coihueco', 'Coihueco', 'Ñuble'),
('El Carmen', 'El Carmen', 'Ñuble'),
('Ninhue', 'Ninhue', 'Ñuble'),
('Ñiquén', 'Ñiquén', 'Ñuble'),
('Pemuco', 'Pemuco', 'Ñuble'),
('Pinto', 'Pinto', 'Ñuble'),
('Portezuelo', 'Portezuelo', 'Ñuble'),
('Quillón', 'Quillón', 'Ñuble'),
('Quirihue', 'Quirihue', 'Ñuble'),
('Ránquil', 'Ránquil', 'Ñuble'),
('San Carlos', 'San Carlos', 'Ñuble'),
('San Fabián', 'San Fabián', 'Ñuble'),
('San Ignacio', 'San Ignacio', 'Ñuble'),
('San Nicolás', 'San Nicolás', 'Ñuble'),
('Trehuaco', 'Trehuaco', 'Ñuble'),
('Yungay', 'Yungay', 'Ñuble'),
('Concepción', 'Concepción', 'Biobío'),
('Coronel', 'Coronel', 'Biobío'),
('Chiguayante', 'Chiguayante', 'Biobío'),
('Florida', 'Florida', 'Biobío'),
('Hualqui', 'Hualqui', 'Biobío'),
('Lota', 'Lota', 'Biobío'),
('Penco', 'Penco', 'Biobío'),
('San Pedro de la Paz', 'San Pedro de la Paz', 'Biobío'),
('Santa Juana', 'Santa Juana', 'Biobío'),
('Talcahuano', 'Talcahuano', 'Biobío'),
('Tomé', 'Tomé', 'Biobío'),
('Lebu', 'Lebu', 'Biobío'),
('Arauco', 'Arauco', 'Biobío'),
('Cañete', 'Cañete', 'Biobío'),
('Contulmo', 'Contulmo', 'Biobío'),
('Curanilahue', 'Curanilahue', 'Biobío'),
('Los Álamos', 'Los Álamos', 'Biobío'),
('Tirúa', 'Tirúa', 'Biobío'),
('Los Ángeles', 'Los Ángeles', 'Biobío'),
('Antuco', 'Antuco', 'Biobío'),
('Cabrero', 'Cabrero', 'Biobío'),
('Laja', 'Laja', 'Biobío'),
('Mulchén', 'Mulchén', 'Biobío'),
('Nacimiento', 'Nacimiento', 'Biobío'),
('Negrete', 'Negrete', 'Biobío'),
('Quilaco', 'Quilaco', 'Biobío'),
('Quilleco', 'Quilleco', 'Biobío'),
('San Rosendo', 'San Rosendo', 'Biobío'),
('Santa Bárbara', 'Santa Bárbara', 'Biobío'),
('Tucapel', 'Tucapel', 'Biobío'),
('Yumbel', 'Yumbel', 'Biobío'),
('Alto Biobío', 'Alto Biobío', 'Biobío'),
('Temuco', 'Temuco', 'Araucanía'),
('Carahue', 'Carahue', 'Araucanía'),
('Cunco', 'Cunco', 'Araucanía'),
('Curarrehue', 'Curarrehue', 'Araucanía'),
('Freire', 'Freire', 'Araucanía'),
('Galvarino', 'Galvarino', 'Araucanía'),
('Gorbea', 'Gorbea', 'Araucanía'),
('Lautaro', 'Lautaro', 'Araucanía'),
('Loncoche', 'Loncoche', 'Araucanía'),
('Melipeuco', 'Melipeuco', 'Araucanía'),
('Nueva Imperial', 'Nueva Imperial', 'Araucanía'),
('Padre Las Casas', 'Padre Las Casas', 'Araucanía'),
('Perquenco', 'Perquenco', 'Araucanía'),
('Pitrufquén', 'Pitrufquén', 'Araucanía'),
('Pucón', 'Pucón', 'Araucanía'),
('Saavedra', 'Saavedra', 'Araucanía'),
('Teodoro Schmidt', 'Teodoro Schmidt', 'Araucanía'),
('Toltén', 'Toltén', 'Araucanía'),
('Vilcún', 'Vilcún', 'Araucanía'),
('Villarrica', 'Villarrica', 'Araucanía'),
('Cholchol', 'Cholchol', 'Araucanía'),
('Angol', 'Angol', 'Araucanía'),
('Collipulli', 'Collipulli', 'Araucanía'),
('Curacautín', 'Curacautín', 'Araucanía'),
('Ercilla', 'Ercilla', 'Araucanía'),
('Lonquimay', 'Lonquimay', 'Araucanía'),
('Los Sauces', 'Los Sauces', 'Araucanía'),
('Lumaco', 'Lumaco', 'Araucanía'),
('Purén', 'Purén', 'Araucanía'),
('Renaico', 'Renaico', 'Araucanía'),
('Traiguén', 'Traiguén', 'Araucanía'),
('Victoria', 'Victoria', 'Araucanía'),
('Valdivia', 'Valdivia', 'Los Ríos'),
('Corral', 'Corral', 'Los Ríos'),
('Lanco', 'Lanco', 'Los Ríos'),
('Los Lagos', 'Los Lagos', 'Los Ríos'),
('Máfil', 'Máfil', 'Los Ríos'),
('Mariquina', 'Mariquina', 'Los Ríos'),
('Paillaco', 'Paillaco', 'Los Ríos'),
('Panguipulli', 'Panguipulli', 'Los Ríos'),
('La Unión', 'La Unión', 'Los Ríos'),
('Futrono', 'Futrono', 'Los Ríos'),
('Lago Ranco', 'Lago Ranco', 'Los Ríos'),
('Río Bueno', 'Río Bueno', 'Los Ríos'),
('Puerto Montt', 'Puerto Montt', 'Los Lagos'),
('Calbuco', 'Calbuco', 'Los Lagos'),
('Cochamó', 'Cochamó', 'Los Lagos'),
('Fresia', 'Fresia', 'Los Lagos'),
('Frutillar', 'Frutillar', 'Los Lagos'),
('Los Muermos', 'Los Muermos', 'Los Lagos'),
('Llanquihue', 'Llanquihue', 'Los Lagos'),
('Maullín', 'Maullín', 'Los Lagos'),
('Puerto Varas', 'Puerto Varas', 'Los Lagos'),
('Castro', 'Castro', 'Los Lagos'),
('Ancud', 'Ancud', 'Los Lagos'),
('Chonchi', 'Chonchi', 'Los Lagos'),
('Curaco de Vélez', 'Curaco de Vélez', 'Los Lagos'),
('Dalcahue', 'Dalcahue', 'Los Lagos'),
('Puqueldón', 'Puqueldón', 'Los Lagos'),
('Queilén', 'Queilén', 'Los Lagos'),
('Quellón', 'Quellón', 'Los Lagos'),
('Quemchi', 'Quemchi', 'Los Lagos'),
('Quinchao', 'Quinchao', 'Los Lagos'),
('Osorno', 'Osorno', 'Los Lagos'),
('Puerto Octay', 'Puerto Octay', 'Los Lagos'),
('Purranque', 'Purranque', 'Los Lagos'),
('Puyehue', 'Puyehue', 'Los Lagos'),
('Río Negro', 'Río Negro', 'Los Lagos'),
('San Juan de la Costa', 'San Juan de la Costa', 'Los Lagos'),
('San Pablo', 'San Pablo', 'Los Lagos'),
('Chaitén', 'Chaitén', 'Los Lagos'),
('Futaleufú', 'Futaleufú', 'Los Lagos'),
('Hualaihué', 'Hualaihué', 'Los Lagos'),
('Palena', 'Palena', 'Los Lagos'),
('Coyhaique', 'Coyhaique', 'Aysén del General Carlos Ibáñez del Campo'),
('Lago Verde', 'Lago Verde', 'Aysén del General Carlos Ibáñez del Campo'),
('Aysén', 'Aysén', 'Aysén del General Carlos Ibáñez del Campo'),
('Cisnes', 'Cisnes', 'Aysén del General Carlos Ibáñez del Campo'),
('Guaitecas', 'Guaitecas', 'Aysén del General Carlos Ibáñez del Campo'),
('Cochrane', 'Cochrane', 'Aysén del General Carlos Ibáñez del Campo'),
('O\'Higgins', 'O\'Higgins', 'Aysén del General Carlos Ibáñez del Campo'),
('Tortel', 'Tortel', 'Aysén del General Carlos Ibáñez del Campo'),
('Chile Chico', 'Chile Chico', 'Aysén del General Carlos Ibáñez del Campo'),
('Río Ibáñez', 'Río Ibáñez', 'Aysén del General Carlos Ibáñez del Campo'),
('Punta Arenas', 'Punta Arenas', 'Magallanes y de la Antártica Chilena'),
('Laguna Blanca', 'Laguna Blanca', 'Magallanes y de la Antártica Chilena'),
('Río Verde', 'Río Verde', 'Magallanes y de la Antártica Chilena'),
('San Gregorio', 'San Gregorio', 'Magallanes y de la Antártica Chilena'),
('Cabo de Hornos', 'Cabo de Hornos', 'Magallanes y de la Antártica Chilena'),
('Antártica', 'Antártica', 'Magallanes y de la Antártica Chilena'),
('Porvenir', 'Porvenir', 'Magallanes y de la Antártica Chilena'),
('Primavera', 'Primavera', 'Magallanes y de la Antártica Chilena'),
('Timaukel', 'Timaukel', 'Magallanes y de la Antártica Chilena'),
('Natales', 'Natales', 'Magallanes y de la Antártica Chilena'),
('Torres del Paine', 'Torres del Paine', 'Magallanes y de la Antártica Chilena');

INSERT IGNORE INTO regions (name)
SELECT DISTINCT region
FROM chile_communes
ORDER BY region;

INSERT IGNORE INTO cities (name, region_id)
SELECT DISTINCT chile_communes.city, regions.id
FROM chile_communes
JOIN regions ON regions.name = chile_communes.region
ORDER BY chile_communes.city;

INSERT IGNORE INTO communes (name, city_id)
SELECT chile_communes.commune, cities.id
FROM chile_communes
JOIN regions ON regions.name = chile_communes.region
JOIN cities ON cities.name = chile_communes.city AND cities.region_id = regions.id
ORDER BY chile_communes.commune;

INSERT IGNORE INTO sii_activity_codes (code, name) VALUES
('011111', 'Cultivo de trigo'),
('011112', 'Cultivo de maíz'),
('011113', 'Cultivo de arroz'),
('011119', 'Otros cultivos de cereales'),
('011120', 'Cultivo de legumbres y semillas oleaginosas'),
('011131', 'Cultivo de papa'),
('011139', 'Cultivo de raíces y tubérculos n.c.p.'),
('011200', 'Cultivo de hortalizas'),
('011300', 'Cultivo de frutas'),
('011400', 'Cultivo de uvas'),
('011500', 'Cultivo de frutos oleaginosos'),
('011600', 'Cultivo de plantas para preparar bebidas'),
('011900', 'Otros cultivos permanentes'),
('012110', 'Cultivo de flores'),
('012120', 'Cultivo de plantas vivas y productos de vivero'),
('012130', 'Producción de semillas'),
('012140', 'Producción de plántulas'),
('012200', 'Producción de hongos y trufas'),
('012900', 'Otros cultivos no permanentes n.c.p.'),
('013000', 'Cultivo de plantas de vivero'),
('014111', 'Cría de ganado bovino'),
('014112', 'Engorda de ganado bovino'),
('014120', 'Cría de ganado equino'),
('014130', 'Cría de ovinos y caprinos'),
('014141', 'Cría de porcinos'),
('014200', 'Cría de aves de corral'),
('014300', 'Cría de otros animales'),
('014400', 'Producción de leche'),
('014500', 'Producción de huevos'),
('014600', 'Apicultura'),
('014900', 'Otras actividades de apoyo a la ganadería'),
('016100', 'Actividades de apoyo a la agricultura'),
('016200', 'Actividades de apoyo a la ganadería'),
('016300', 'Actividades posteriores a la cosecha'),
('016400', 'Tratamiento de semillas para propagación'),
('021000', 'Silvicultura'),
('022000', 'Extracción de madera'),
('023000', 'Recolección de productos forestales'),
('024000', 'Servicios de apoyo a la silvicultura'),
('031100', 'Pesca marítima'),
('031200', 'Pesca de agua dulce'),
('032100', 'Acuicultura marítima'),
('032200', 'Acuicultura de agua dulce'),
('051000', 'Extracción de carbón de piedra'),
('052000', 'Extracción de lignito'),
('061000', 'Extracción de petróleo crudo'),
('062000', 'Extracción de gas natural'),
('071000', 'Extracción de minerales de hierro'),
('072100', 'Extracción de minerales de uranio y torio'),
('072910', 'Extracción de cobre'),
('072920', 'Extracción de otros minerales metálicos no ferrosos'),
('081000', 'Extracción de piedra, arena y arcilla'),
('089100', 'Extracción de minerales para productos químicos'),
('089200', 'Extracción de turba'),
('089300', 'Extracción de sal'),
('089900', 'Otras actividades de explotación de minas y canteras'),
('101010', 'Elaboración y conservación de carne'),
('101020', 'Elaboración y conservación de productos de la pesca'),
('101030', 'Elaboración y conservación de frutas, legumbres y hortalizas'),
('101040', 'Elaboración de aceites y grasas'),
('101050', 'Elaboración de productos lácteos'),
('101060', 'Elaboración de productos de molinería'),
('101070', 'Elaboración de almidones y productos derivados'),
('101080', 'Elaboración de productos de panadería'),
('101090', 'Elaboración de otros productos alimenticios'),
('102000', 'Elaboración de alimentos preparados para animales'),
('110100', 'Destilación de bebidas alcohólicas'),
('110200', 'Elaboración de vinos'),
('110300', 'Elaboración de cervezas'),
('110400', 'Elaboración de bebidas no alcohólicas'),
('120000', 'Elaboración de productos de tabaco'),
('131100', 'Preparación e hilatura de fibras textiles'),
('131200', 'Tejeduría de productos textiles'),
('131300', 'Acabado de productos textiles'),
('139100', 'Fabricación de tejidos de punto'),
('139200', 'Fabricación de otros productos textiles'),
('141000', 'Confección de prendas de vestir'),
('142000', 'Fabricación de artículos de piel'),
('143000', 'Fabricación de prendas de vestir de punto'),
('151100', 'Curtido y adobo de cuero'),
('151200', 'Fabricación de calzado'),
('152000', 'Fabricación de artículos de cuero'),
('161000', 'Aserrado y cepillado de madera'),
('162100', 'Fabricación de productos de madera'),
('170110', 'Fabricación de celulosa'),
('170120', 'Fabricación de papel y cartón'),
('170200', 'Fabricación de envases de papel'),
('181100', 'Impresión'),
('181200', 'Servicios relacionados con la impresión'),
('182000', 'Reproducción de soportes grabados'),
('191000', 'Fabricación de productos de hornos de coque'),
('192000', 'Fabricación de productos de la refinación del petróleo'),
('201100', 'Fabricación de sustancias químicas básicas'),
('201200', 'Fabricación de fertilizantes'),
('201300', 'Fabricación de plásticos y caucho sintético'),
('202100', 'Fabricación de plaguicidas'),
('202200', 'Fabricación de pinturas'),
('202300', 'Fabricación de jabones y detergentes'),
('202900', 'Fabricación de otros productos químicos'),
('210000', 'Fabricación de productos farmacéuticos'),
('221100', 'Fabricación de neumáticos'),
('221900', 'Fabricación de otros productos de caucho'),
('222000', 'Fabricación de productos de plástico'),
('231000', 'Fabricación de vidrio'),
('239100', 'Fabricación de productos de cerámica'),
('239200', 'Fabricación de productos refractarios'),
('239300', 'Fabricación de productos de arcilla'),
('239400', 'Fabricación de cemento'),
('239500', 'Fabricación de yeso'),
('239600', 'Fabricación de productos de hormigón'),
('239900', 'Fabricación de otros productos minerales no metálicos'),
('241000', 'Industrias básicas de hierro y acero'),
('242000', 'Industrias básicas de metales preciosos y no ferrosos'),
('243100', 'Fundición de hierro y acero'),
('243200', 'Fundición de metales no ferrosos'),
('251100', 'Fabricación de productos metálicos'),
('251200', 'Fabricación de tanques y depósitos'),
('252000', 'Fabricación de armas y municiones'),
('259100', 'Forja y estampado de metales'),
('259200', 'Tratamiento y revestimiento de metales'),
('259300', 'Fabricación de artículos de ferretería'),
('259900', 'Fabricación de otros productos elaborados de metal'),
('261000', 'Fabricación de componentes electrónicos'),
('262000', 'Fabricación de computadores y periféricos'),
('263000', 'Fabricación de equipos de comunicación'),
('264000', 'Fabricación de aparatos electrónicos de consumo'),
('265100', 'Fabricación de instrumentos de medición'),
('265200', 'Fabricación de relojes'),
('266000', 'Fabricación de equipos de radiación'),
('267000', 'Fabricación de instrumentos ópticos'),
('268000', 'Fabricación de soportes magnéticos y ópticos'),
('271000', 'Fabricación de motores eléctricos'),
('272000', 'Fabricación de pilas y baterías'),
('273100', 'Fabricación de cables de fibra óptica'),
('273200', 'Fabricación de cables'),
('273300', 'Fabricación de dispositivos de cableado'),
('274000', 'Fabricación de equipos de iluminación'),
('275000', 'Fabricación de aparatos de uso doméstico'),
('279000', 'Fabricación de otros equipos eléctricos'),
('281100', 'Fabricación de motores y turbinas'),
('281200', 'Fabricación de bombas y compresores'),
('281300', 'Fabricación de grifos y válvulas'),
('281400', 'Fabricación de cojinetes y engranajes'),
('281500', 'Fabricación de hornos y quemadores'),
('281600', 'Fabricación de equipos de elevación'),
('281700', 'Fabricación de maquinaria para la construcción'),
('281800', 'Fabricación de maquinaria para la agricultura'),
('281900', 'Fabricación de maquinaria de uso general'),
('282100', 'Fabricación de maquinaria para metalurgia'),
('282200', 'Fabricación de maquinaria para minería'),
('282300', 'Fabricación de maquinaria para industria alimentaria'),
('282400', 'Fabricación de maquinaria para la industria textil'),
('282500', 'Fabricación de maquinaria para la industria del papel'),
('282900', 'Fabricación de otras maquinarias especiales'),
('291000', 'Fabricación de vehículos automotores'),
('292000', 'Fabricación de carrocerías y remolques'),
('293000', 'Fabricación de partes y piezas de vehículos'),
('301100', 'Construcción de buques'),
('301200', 'Construcción de embarcaciones de recreo'),
('302000', 'Fabricación de locomotoras y material rodante'),
('303000', 'Fabricación de aeronaves'),
('304000', 'Fabricación de vehículos militares'),
('309100', 'Fabricación de motocicletas'),
('309200', 'Fabricación de bicicletas'),
('309900', 'Fabricación de otros equipos de transporte'),
('310000', 'Fabricación de muebles'),
('321100', 'Fabricación de joyas'),
('321200', 'Fabricación de bisutería'),
('322000', 'Fabricación de instrumentos musicales'),
('323000', 'Fabricación de artículos deportivos'),
('324000', 'Fabricación de juegos y juguetes'),
('325000', 'Fabricación de instrumentos médicos'),
('329000', 'Otras industrias manufactureras n.c.p.'),
('331100', 'Reparación de productos metálicos'),
('331200', 'Reparación de maquinaria'),
('331300', 'Reparación de equipos electrónicos'),
('331400', 'Reparación de equipos eléctricos'),
('331500', 'Reparación de equipos de transporte'),
('331900', 'Reparación de otros equipos'),
('332000', 'Instalación de maquinaria y equipos'),
('351000', 'Generación de energía eléctrica'),
('352000', 'Transmisión de energía eléctrica'),
('353000', 'Distribución de energía eléctrica'),
('360000', 'Captación y distribución de agua'),
('370000', 'Evacuación de aguas residuales'),
('381100', 'Recolección de desechos'),
('381200', 'Tratamiento y eliminación de desechos'),
('382100', 'Recuperación de materiales'),
('390000', 'Actividades de descontaminación'),
('410010', 'Construcción de edificios'),
('410020', 'Construcción de viviendas'),
('421000', 'Construcción de carreteras'),
('422000', 'Construcción de proyectos de servicio público'),
('429000', 'Construcción de otras obras de ingeniería'),
('431100', 'Demolición'),
('431200', 'Preparación del terreno'),
('432100', 'Instalación eléctrica'),
('432200', 'Instalaciones de gas y calefacción'),
('432900', 'Otras instalaciones para obras de construcción'),
('433000', 'Terminación y acabado de edificios'),
('439000', 'Otras actividades especializadas de construcción'),
('451001', 'Venta de vehículos automotores'),
('452001', 'Mantenimiento y reparación de vehículos automotores'),
('453000', 'Venta de partes y piezas para vehículos'),
('454000', 'Venta de motocicletas y accesorios'),
('461000', 'Venta al por mayor a cambio de una retribución'),
('462000', 'Venta al por mayor de materias primas agropecuarias'),
('463000', 'Venta al por mayor de alimentos'),
('464100', 'Venta al por mayor de textiles'),
('464200', 'Venta al por mayor de prendas de vestir'),
('464300', 'Venta al por mayor de calzado'),
('464901', 'Venta al por mayor de maquinaria'),
('464902', 'Venta al por mayor de artículos eléctricos'),
('464903', 'Venta al por mayor de productos farmacéuticos'),
('464904', 'Venta al por mayor de combustibles'),
('464905', 'Venta al por mayor de materiales de construcción'),
('464906', 'Venta al por mayor de metales'),
('464907', 'Venta al por mayor de madera'),
('464908', 'Venta al por mayor de equipos médicos'),
('464909', 'Venta al por mayor de otros productos'),
('465100', 'Venta al por mayor de computadores'),
('465200', 'Venta al por mayor de equipos de telecomunicaciones'),
('465300', 'Venta al por mayor de equipos electrónicos'),
('466100', 'Venta al por mayor de maquinaria agrícola'),
('466200', 'Venta al por mayor de maquinaria para la minería'),
('466300', 'Venta al por mayor de maquinaria para la construcción'),
('466901', 'Venta al por mayor de otros tipos de maquinaria'),
('466902', 'Venta al por mayor de vehículos'),
('466903', 'Venta al por mayor de equipos de transporte'),
('466904', 'Venta al por mayor de productos químicos'),
('466909', 'Venta al por mayor de otros productos'),
('469000', 'Venta al por mayor no especializada'),
('471100', 'Venta al por menor en comercios no especializados'),
('471900', 'Venta al por menor en otros comercios no especializados'),
('472100', 'Venta al por menor de alimentos'),
('472200', 'Venta al por menor de bebidas'),
('472300', 'Venta al por menor de tabaco'),
('472400', 'Venta al por menor de combustibles'),
('472500', 'Venta al por menor de textiles'),
('472600', 'Venta al por menor de prendas de vestir'),
('472700', 'Venta al por menor de calzado'),
('472900', 'Venta al por menor de otros productos'),
('473000', 'Venta al por menor de combustibles para vehículos'),
('474100', 'Venta al por menor de computadores'),
('474200', 'Venta al por menor de equipos de telecomunicaciones'),
('474300', 'Venta al por menor de equipos electrónicos'),
('475100', 'Venta al por menor de textiles'),
('475200', 'Venta al por menor de ferretería'),
('475900', 'Venta al por menor de otros productos en comercios especializados'),
('476100', 'Venta al por menor de libros'),
('476200', 'Venta al por menor de periódicos'),
('476300', 'Venta al por menor de productos culturales'),
('476400', 'Venta al por menor de aparatos electrónicos'),
('476500', 'Venta al por menor de música'),
('477100', 'Venta al por menor de prendas de vestir'),
('477200', 'Venta al por menor de calzado'),
('477300', 'Venta al por menor de productos farmacéuticos'),
('477400', 'Venta al por menor de productos de cosmética'),
('477500', 'Venta al por menor de productos médicos'),
('477600', 'Venta al por menor de artículos deportivos'),
('477700', 'Venta al por menor de artículos recreativos'),
('477800', 'Venta al por menor de otros productos en comercios especializados'),
('478100', 'Venta al por menor en puestos de alimentos'),
('478200', 'Venta al por menor en puestos de textiles'),
('478900', 'Venta al por menor en otros puestos'),
('479100', 'Venta al por menor por internet'),
('479900', 'Venta al por menor por otros medios'),
('491100', 'Transporte interurbano de pasajeros por ferrocarril'),
('491200', 'Transporte de carga por ferrocarril'),
('492100', 'Transporte urbano de pasajeros'),
('492200', 'Transporte interurbano de pasajeros'),
('492300', 'Transporte de carga por carretera'),
('493000', 'Transporte por oleoducto'),
('501100', 'Transporte marítimo de pasajeros'),
('501200', 'Transporte marítimo de carga'),
('502100', 'Transporte fluvial de pasajeros'),
('502200', 'Transporte fluvial de carga'),
('511000', 'Transporte aéreo de pasajeros'),
('512000', 'Transporte aéreo de carga'),
('521000', 'Depósito y almacenamiento'),
('522100', 'Servicios auxiliares de transporte'),
('522200', 'Servicios auxiliares de transporte marítimo'),
('522300', 'Servicios auxiliares de transporte aéreo'),
('522400', 'Manipulación de carga'),
('522900', 'Otras actividades de apoyo al transporte'),
('531000', 'Actividades de correo'),
('532000', 'Actividades de mensajería'),
('551000', 'Alojamiento'),
('552000', 'Actividades de campamento'),
('559000', 'Otros tipos de alojamiento'),
('561000', 'Restaurantes y servicios de comida'),
('562000', 'Servicios de catering'),
('563000', 'Actividades de bebidas'),
('581100', 'Edición de libros'),
('581200', 'Edición de periódicos'),
('581300', 'Edición de revistas'),
('581900', 'Otras actividades de edición'),
('582000', 'Edición de programas informáticos'),
('591100', 'Producción de películas'),
('591200', 'Postproducción'),
('591300', 'Distribución de películas'),
('591400', 'Proyección de películas'),
('592000', 'Actividades de grabación sonora'),
('601000', 'Transmisión radial'),
('602000', 'Programación televisiva'),
('611000', 'Telecomunicaciones'),
('612000', 'Telecomunicaciones inalámbricas'),
('613000', 'Telecomunicaciones por satélite'),
('619000', 'Otras telecomunicaciones'),
('620100', 'Actividades de programación informática'),
('620200', 'Consultoría de informática'),
('620300', 'Gestión de instalaciones informáticas'),
('620900', 'Otras actividades de tecnología de la información'),
('631100', 'Procesamiento de datos'),
('631200', 'Portales web'),
('639100', 'Actividades de agencias de noticias'),
('639900', 'Otras actividades de servicios de información'),
('641100', 'Banca central'),
('641900', 'Otros servicios de intermediación monetaria'),
('642000', 'Actividades de sociedades de cartera'),
('643000', 'Fondos y sociedades de inversión'),
('649100', 'Arrendamiento financiero'),
('649200', 'Otros servicios financieros'),
('649300', 'Financiamiento de consumo'),
('649900', 'Otros servicios financieros n.c.p.'),
('651100', 'Seguros'),
('651200', 'Reaseguros'),
('652000', 'Planes de pensiones'),
('653000', 'Servicios auxiliares de seguros'),
('661100', 'Administración de mercados financieros'),
('661200', 'Corretaje de valores'),
('661900', 'Otras actividades auxiliares de servicios financieros'),
('662100', 'Evaluación de riesgos'),
('662200', 'Corretaje de seguros'),
('662900', 'Otras actividades auxiliares de seguros'),
('663000', 'Administración de fondos'),
('681000', 'Actividades inmobiliarias'),
('682000', 'Alquiler de bienes inmuebles'),
('691000', 'Actividades jurídicas'),
('692000', 'Actividades de contabilidad'),
('701000', 'Actividades de oficinas principales'),
('702000', 'Actividades de consultoría de gestión'),
('711000', 'Actividades de arquitectura'),
('712000', 'Ensayos y análisis técnicos'),
('721000', 'Investigación y desarrollo experimental en ciencias naturales'),
('722000', 'Investigación y desarrollo experimental en ciencias sociales'),
('731000', 'Publicidad'),
('732000', 'Investigación de mercados'),
('741000', 'Actividades de diseño especializado'),
('742000', 'Actividades de fotografía'),
('749000', 'Otras actividades profesionales'),
('750000', 'Actividades veterinarias'),
('771000', 'Alquiler de vehículos'),
('772100', 'Alquiler de artículos recreativos'),
('772200', 'Alquiler de videos'),
('772900', 'Alquiler de otros bienes'),
('773000', 'Alquiler de maquinaria y equipo'),
('774000', 'Arrendamiento de propiedad intelectual'),
('781000', 'Actividades de empleo'),
('782000', 'Actividades de agencias de empleo'),
('783000', 'Otras actividades de suministro de recursos humanos'),
('791100', 'Actividades de agencias de viaje'),
('791200', 'Actividades de operadores turísticos'),
('799000', 'Otros servicios de reservas'),
('801000', 'Actividades de seguridad privada'),
('802000', 'Actividades de servicios de seguridad'),
('803000', 'Investigaciones'),
('811000', 'Actividades de limpieza'),
('812100', 'Limpieza general de edificios'),
('812900', 'Otras actividades de limpieza'),
('813000', 'Servicios de paisajismo'),
('821100', 'Actividades administrativas'),
('821900', 'Servicios de apoyo a oficinas'),
('822000', 'Actividades de call center'),
('823000', 'Organización de convenciones'),
('829100', 'Agencias de cobranza'),
('829900', 'Otros servicios de apoyo a empresas'),
('841100', 'Administración pública'),
('842100', 'Relaciones exteriores'),
('842200', 'Defensa'),
('842300', 'Orden público'),
('843000', 'Seguridad social'),
('851000', 'Enseñanza preescolar'),
('852100', 'Enseñanza primaria'),
('852200', 'Enseñanza secundaria'),
('853100', 'Educación superior'),
('853200', 'Educación técnica'),
('854100', 'Educación deportiva'),
('854200', 'Educación cultural'),
('854900', 'Otras actividades de enseñanza'),
('855000', 'Servicios de apoyo a la enseñanza'),
('861000', 'Actividades de hospitales'),
('862000', 'Actividades médicas y odontológicas'),
('869000', 'Otras actividades de atención de la salud'),
('871000', 'Instituciones de atención de salud'),
('872000', 'Atención a personas con discapacidad'),
('873000', 'Atención a personas mayores'),
('879000', 'Otras actividades de asistencia social'),
('881000', 'Actividades de servicios sociales'),
('889000', 'Otros servicios sociales'),
('900000', 'Actividades creativas y artísticas'),
('910100', 'Actividades de bibliotecas'),
('910200', 'Actividades de museos'),
('910300', 'Actividades de jardines botánicos'),
('920000', 'Actividades de juegos de azar'),
('931100', 'Actividades deportivas'),
('931200', 'Actividades de clubes deportivos'),
('931900', 'Otras actividades deportivas'),
('932100', 'Parques de atracciones'),
('932900', 'Otras actividades de entretenimiento'),
('941100', 'Actividades de organizaciones empresariales'),
('941200', 'Actividades de organizaciones profesionales'),
('942000', 'Actividades de sindicatos'),
('949100', 'Actividades de organizaciones religiosas'),
('949200', 'Actividades de organizaciones políticas'),
('949900', 'Otras actividades asociativas'),
('951100', 'Reparación de computadores'),
('951200', 'Reparación de equipos de comunicación'),
('952100', 'Reparación de aparatos electrónicos'),
('952200', 'Reparación de electrodomésticos'),
('952300', 'Reparación de calzado y artículos de cuero'),
('952400', 'Reparación de muebles'),
('952900', 'Reparación de otros bienes'),
('960100', 'Lavado y limpieza de vehículos'),
('960200', 'Peluquería y otros tratamientos de belleza'),
('960300', 'Pompas fúnebres'),
('960900', 'Otras actividades de servicios personales');

CREATE TABLE role_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    permission_key VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    avatar_path VARCHAR(255) NULL,
    signature TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE user_companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    rut VARCHAR(50) NULL,
    email VARCHAR(150) NOT NULL,
    billing_email VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    address VARCHAR(255) NULL,
    giro VARCHAR(150) NULL,
    activity_code VARCHAR(50) NULL,
    commune VARCHAR(120) NULL,
    city VARCHAR(120) NULL,
    contact VARCHAR(150) NULL,
    mandante_name VARCHAR(150) NULL,
    mandante_rut VARCHAR(50) NULL,
    mandante_phone VARCHAR(50) NULL,
    mandante_email VARCHAR(150) NULL,
    avatar_path VARCHAR(255) NULL,
    portal_token VARCHAR(64) NULL,
    portal_password VARCHAR(255) NULL,
    notes TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    contact_name VARCHAR(150) NULL,
    tax_id VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    address VARCHAR(255) NULL,
    giro VARCHAR(150) NULL,
    activity_code VARCHAR(50) NULL,
    commune VARCHAR(120) NULL,
    city VARCHAR(120) NULL,
    website VARCHAR(150) NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE product_families (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE product_subfamilies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    family_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (family_id) REFERENCES product_families(id)
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    supplier_id INT NULL,
    family_id INT NULL,
    subfamily_id INT NULL,
    name VARCHAR(150) NOT NULL,
    sku VARCHAR(100) NULL,
    description TEXT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    stock_min INT NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (family_id) REFERENCES product_families(id),
    FOREIGN KEY (subfamily_id) REFERENCES product_subfamilies(id)
);

CREATE TABLE produced_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    sku VARCHAR(100) NULL,
    description TEXT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    stock_min INT NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    status VARCHAR(50) NOT NULL,
    start_date DATE NULL,
    delivery_date DATE NULL,
    value DECIMAL(12,2) NULL,
    mandante_name VARCHAR(150) NULL,
    mandante_rut VARCHAR(50) NULL,
    mandante_phone VARCHAR(50) NULL,
    mandante_email VARCHAR(150) NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

CREATE TABLE project_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    progress_percent TINYINT UNSIGNED NOT NULL DEFAULT 0,
    completed TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id)
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    service_type VARCHAR(50) NOT NULL,
    name VARCHAR(150) NOT NULL,
    cost DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    billing_cycle VARCHAR(20) NOT NULL DEFAULT 'mensual',
    start_date DATE NULL,
    due_date DATE NULL,
    delete_date DATE NULL,
    notice_days_1 INT NOT NULL DEFAULT 15,
    notice_days_2 INT NOT NULL DEFAULT 5,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    auto_invoice TINYINT(1) NOT NULL DEFAULT 1,
    auto_email TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

CREATE TABLE service_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE system_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    service_type_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    cost DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (service_type_id) REFERENCES service_types(id),
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    service_id INT NULL,
    project_id INT NULL,
    numero VARCHAR(50) NOT NULL,
    fecha_emision DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    estado VARCHAR(20) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    impuestos DECIMAL(12,2) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    sii_document_type VARCHAR(50) NULL,
    sii_document_number VARCHAR(50) NULL,
    sii_receiver_rut VARCHAR(50) NULL,
    sii_receiver_name VARCHAR(150) NULL,
    sii_receiver_giro VARCHAR(150) NULL,
    sii_receiver_activity_code VARCHAR(50) NULL,
    sii_receiver_address VARCHAR(255) NULL,
    sii_receiver_commune VARCHAR(100) NULL,
    sii_receiver_city VARCHAR(100) NULL,
    sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19,
    sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    notas TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (project_id) REFERENCES projects(id)
);

CREATE TABLE quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    service_id INT NULL,
    system_service_id INT NULL,
    project_id INT NULL,
    numero VARCHAR(50) NOT NULL,
    fecha_emision DATE NOT NULL,
    estado VARCHAR(20) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    impuestos DECIMAL(12,2) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    sii_document_type VARCHAR(50) NULL,
    sii_document_number VARCHAR(50) NULL,
    sii_receiver_rut VARCHAR(50) NULL,
    sii_receiver_name VARCHAR(150) NULL,
    sii_receiver_giro VARCHAR(150) NULL,
    sii_receiver_activity_code VARCHAR(50) NULL,
    sii_receiver_address VARCHAR(255) NULL,
    sii_receiver_commune VARCHAR(100) NULL,
    sii_receiver_city VARCHAR(100) NULL,
    sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19,
    sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    notas TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (system_service_id) REFERENCES system_services(id),
    FOREIGN KEY (project_id) REFERENCES projects(id)
);

CREATE TABLE quote_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote_id INT NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (quote_id) REFERENCES quotes(id)
);

CREATE TABLE chat_threads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    subject VARCHAR(150) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'abierto',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    thread_id INT NOT NULL,
    sender_type VARCHAR(20) NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (thread_id) REFERENCES chat_threads(id)
);

CREATE TABLE support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    subject VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'abierto',
    priority VARCHAR(20) NOT NULL DEFAULT 'media',
    assigned_user_id INT NULL,
    created_by_type VARCHAR(20) NOT NULL DEFAULT 'client',
    created_by_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    closed_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (assigned_user_id) REFERENCES users(id)
);

CREATE TABLE support_ticket_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    sender_type VARCHAR(20) NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (ticket_id) REFERENCES support_tickets(id)
);


CREATE TABLE invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    fecha_pago DATE NOT NULL,
    metodo VARCHAR(50) NOT NULL,
    referencia VARCHAR(150) NULL,
    comprobante VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id)
);

CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    supplier_id INT NOT NULL,
    reference VARCHAR(100) NULL,
    purchase_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    tax DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    sii_document_type VARCHAR(50) NULL,
    sii_document_number VARCHAR(50) NULL,
    sii_receiver_rut VARCHAR(50) NULL,
    sii_receiver_name VARCHAR(150) NULL,
    sii_receiver_giro VARCHAR(150) NULL,
    sii_receiver_activity_code VARCHAR(50) NULL,
    sii_receiver_address VARCHAR(255) NULL,
    sii_receiver_commune VARCHAR(100) NULL,
    sii_receiver_city VARCHAR(100) NULL,
    sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19,
    sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

CREATE TABLE purchase_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    supplier_id INT NOT NULL,
    reference VARCHAR(100) NULL,
    order_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

CREATE TABLE purchase_order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE purchase_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE production_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    production_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'completada',
    total_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE production_inputs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    production_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (production_id) REFERENCES production_orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE production_outputs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    production_id INT NOT NULL,
    produced_product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (production_id) REFERENCES production_orders(id),
    FOREIGN KEY (produced_product_id) REFERENCES produced_products(id)
);

CREATE TABLE production_expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    production_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (production_id) REFERENCES production_orders(id)
);

CREATE TABLE pos_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    user_id INT NOT NULL,
    opening_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    closing_amount DECIMAL(12,2) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'abierto',
    opened_at DATETIME NOT NULL,
    closed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NULL,
    pos_session_id INT NULL,
    channel VARCHAR(20) NOT NULL DEFAULT 'venta',
    numero VARCHAR(50) NOT NULL,
    sale_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pagado',
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    tax DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    sii_document_type VARCHAR(50) NULL,
    sii_document_number VARCHAR(50) NULL,
    sii_receiver_rut VARCHAR(50) NULL,
    sii_receiver_name VARCHAR(150) NULL,
    sii_receiver_giro VARCHAR(150) NULL,
    sii_receiver_activity_code VARCHAR(50) NULL,
    sii_receiver_address VARCHAR(255) NULL,
    sii_receiver_commune VARCHAR(100) NULL,
    sii_receiver_city VARCHAR(100) NULL,
    sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19,
    sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (pos_session_id) REFERENCES pos_sessions(id)
);

CREATE TABLE sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NULL,
    produced_product_id INT NULL,
    service_id INT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (produced_product_id) REFERENCES produced_products(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE sale_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    method VARCHAR(50) NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id)
);

CREATE TABLE email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    body_html MEDIUMTEXT NOT NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'cobranza',
    created_by INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE email_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NULL,
    template_id INT NULL,
    subject VARCHAR(150) NOT NULL,
    body_html MEDIUMTEXT NOT NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'cobranza',
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    scheduled_at DATETIME NOT NULL,
    tries INT NOT NULL DEFAULT 0,
    last_error TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (template_id) REFERENCES email_templates(id)
);

CREATE TABLE email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NULL,
    type VARCHAR(20) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    body_html MEDIUMTEXT NOT NULL,
    status VARCHAR(20) NOT NULL,
    error TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NULL,
    `key` VARCHAR(100) NOT NULL,
    value MEDIUMTEXT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(20) NOT NULL,
    read_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE commercial_briefs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    contact_name VARCHAR(150) NULL,
    contact_email VARCHAR(150) NULL,
    contact_phone VARCHAR(50) NULL,
    service_summary VARCHAR(150) NULL,
    expected_budget DECIMAL(12,2) NULL,
    desired_start_date DATE NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'nuevo',
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

CREATE TABLE sales_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    brief_id INT NULL,
    order_number VARCHAR(50) NOT NULL,
    order_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    total DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (brief_id) REFERENCES commercial_briefs(id)
);

CREATE TABLE sales_order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sales_order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (sales_order_id) REFERENCES sales_orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE service_renewals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    service_id INT NULL,
    renewal_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    reminder_days INT NOT NULL DEFAULT 15,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NULL,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    entity VARCHAR(50) NOT NULL,
    entity_id INT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE INDEX idx_clients_status ON clients(status);
CREATE UNIQUE INDEX idx_clients_portal_token ON clients(portal_token);
CREATE INDEX idx_services_status ON services(status);
CREATE INDEX idx_services_due_date ON services(due_date);
CREATE INDEX idx_invoices_estado ON invoices(estado);
CREATE INDEX idx_invoices_numero ON invoices(numero);
CREATE INDEX idx_email_queue_status ON email_queue(status);
CREATE UNIQUE INDEX idx_settings_key_company ON settings(company_id, `key`);
CREATE UNIQUE INDEX idx_user_companies_unique ON user_companies(user_id, company_id);
CREATE INDEX idx_product_families_company ON product_families(company_id);
CREATE INDEX idx_product_subfamilies_company ON product_subfamilies(company_id);
CREATE INDEX idx_products_company ON products(company_id);
CREATE INDEX idx_products_supplier ON products(supplier_id);
CREATE INDEX idx_purchases_company ON purchases(company_id);
CREATE INDEX idx_purchase_orders_company ON purchase_orders(company_id);
CREATE INDEX idx_purchase_order_items_order ON purchase_order_items(purchase_order_id);
CREATE INDEX idx_production_orders_company ON production_orders(company_id);
CREATE INDEX idx_production_inputs_production ON production_inputs(production_id);
CREATE INDEX idx_production_outputs_production ON production_outputs(production_id);
CREATE INDEX idx_production_expenses_production ON production_expenses(production_id);
CREATE INDEX idx_sales_order_items_order ON sales_order_items(sales_order_id);
CREATE INDEX idx_sales_company ON sales(company_id);
CREATE INDEX idx_pos_sessions_company_user ON pos_sessions(company_id, user_id);

INSERT INTO roles (name, created_at, updated_at) VALUES
('admin', NOW(), NOW());

INSERT INTO companies (name, rut, email, created_at, updated_at) VALUES
('GoCreative', '', 'contacto@gocreative.cl', NOW(), NOW());

INSERT INTO users (company_id, name, email, password, role_id, created_at, updated_at) VALUES
(1, 'E Isla', 'eisla@gocreative.cl', '$2y$12$Aa7Lucu.iaa3mUMBZjxAyO96KI0d6yNaKuOD/Rdru1FsOhn9Kmtga', 1, NOW(), NOW());

INSERT INTO user_companies (user_id, company_id, created_at) VALUES
(1, 1, NOW());

CREATE TABLE hr_departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE hr_positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE hr_contract_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    max_duration_months INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE hr_health_providers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    provider_type VARCHAR(20) NOT NULL DEFAULT 'fonasa',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE hr_pension_funds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE hr_work_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    weekly_hours INT NOT NULL DEFAULT 45,
    start_time TIME NULL,
    end_time TIME NULL,
    lunch_break_minutes INT NOT NULL DEFAULT 60,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE hr_payroll_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    item_type VARCHAR(20) NOT NULL DEFAULT 'haber',
    taxable TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE hr_employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    department_id INT NULL,
    position_id INT NULL,
    health_provider_id INT NULL,
    pension_fund_id INT NULL,
    rut VARCHAR(50) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    nationality VARCHAR(100) NULL,
    birth_date DATE NULL,
    civil_status VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    address VARCHAR(255) NULL,
    hire_date DATE NOT NULL,
    termination_date DATE NULL,
    health_provider VARCHAR(100) NULL,
    health_plan VARCHAR(150) NULL,
    pension_fund VARCHAR(100) NULL,
    pension_rate DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    health_rate DECIMAL(5,2) NOT NULL DEFAULT 7.00,
    unemployment_rate DECIMAL(5,2) NOT NULL DEFAULT 0.60,
    dependents_count INT NOT NULL DEFAULT 0,
    payment_method VARCHAR(50) NULL,
    bank_name VARCHAR(100) NULL,
    bank_account_type VARCHAR(50) NULL,
    bank_account_number VARCHAR(50) NULL,
    qr_token VARCHAR(100) NULL,
    face_descriptor TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (department_id) REFERENCES hr_departments(id),
    FOREIGN KEY (position_id) REFERENCES hr_positions(id),
    FOREIGN KEY (health_provider_id) REFERENCES hr_health_providers(id),
    FOREIGN KEY (pension_fund_id) REFERENCES hr_pension_funds(id)
);

CREATE TABLE hr_contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    employee_id INT NOT NULL,
    contract_type_id INT NULL,
    department_id INT NULL,
    position_id INT NULL,
    schedule_id INT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    salary DECIMAL(12,2) NOT NULL,
    weekly_hours INT NOT NULL DEFAULT 45,
    status VARCHAR(20) NOT NULL DEFAULT 'vigente',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id),
    FOREIGN KEY (contract_type_id) REFERENCES hr_contract_types(id),
    FOREIGN KEY (department_id) REFERENCES hr_departments(id),
    FOREIGN KEY (position_id) REFERENCES hr_positions(id),
    FOREIGN KEY (schedule_id) REFERENCES hr_work_schedules(id)
);

CREATE TABLE hr_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    employee_id INT NOT NULL,
    date DATE NOT NULL,
    check_in TIME NULL,
    check_out TIME NULL,
    worked_hours DECIMAL(5,2) NULL,
    overtime_hours DECIMAL(5,2) NOT NULL DEFAULT 0,
    absence_type VARCHAR(100) NULL,
    notes VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id)
);

CREATE TABLE hr_payrolls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    employee_id INT NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    base_salary DECIMAL(12,2) NOT NULL,
    bonuses DECIMAL(12,2) NOT NULL DEFAULT 0,
    other_earnings DECIMAL(12,2) NOT NULL DEFAULT 0,
    other_deductions DECIMAL(12,2) NOT NULL DEFAULT 0,
    taxable_income DECIMAL(12,2) NOT NULL DEFAULT 0,
    pension_deduction DECIMAL(12,2) NOT NULL DEFAULT 0,
    health_deduction DECIMAL(12,2) NOT NULL DEFAULT 0,
    unemployment_deduction DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_deductions DECIMAL(12,2) NOT NULL DEFAULT 0,
    net_pay DECIMAL(12,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'borrador',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id)
);

CREATE TABLE hr_payroll_lines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payroll_id INT NOT NULL,
    payroll_item_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (payroll_id) REFERENCES hr_payrolls(id),
    FOREIGN KEY (payroll_item_id) REFERENCES hr_payroll_items(id)
);

CREATE TABLE accounting_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(150) NOT NULL,
    type VARCHAR(30) NOT NULL,
    level INT NOT NULL DEFAULT 1,
    parent_id INT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (parent_id) REFERENCES accounting_accounts(id)
);

CREATE TABLE accounting_periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    period VARCHAR(20) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'abierto',
    closed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE accounting_journals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    entry_number VARCHAR(50) NOT NULL,
    entry_date DATE NOT NULL,
    description VARCHAR(255) NULL,
    source VARCHAR(20) NOT NULL DEFAULT 'manual',
    status VARCHAR(20) NOT NULL DEFAULT 'borrador',
    created_by INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE accounting_journal_lines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journal_id INT NOT NULL,
    account_id INT NOT NULL,
    line_description VARCHAR(255) NULL,
    debit DECIMAL(12,2) NOT NULL DEFAULT 0,
    credit DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (journal_id) REFERENCES accounting_journals(id),
    FOREIGN KEY (account_id) REFERENCES accounting_accounts(id)
);

CREATE TABLE tax_periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    period VARCHAR(20) NOT NULL,
    iva_debito DECIMAL(12,2) NOT NULL DEFAULT 0,
    iva_credito DECIMAL(12,2) NOT NULL DEFAULT 0,
    remanente DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_retenciones DECIMAL(12,2) NOT NULL DEFAULT 0,
    impuesto_unico DECIMAL(12,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE tax_withholdings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    period_id INT NULL,
    type VARCHAR(50) NOT NULL,
    base_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    rate DECIMAL(5,2) NOT NULL DEFAULT 0,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (period_id) REFERENCES tax_periods(id)
);

CREATE TABLE honorarios_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    provider_name VARCHAR(150) NOT NULL,
    provider_rut VARCHAR(50) NULL,
    document_number VARCHAR(50) NOT NULL,
    issue_date DATE NOT NULL,
    gross_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    retention_rate DECIMAL(5,2) NOT NULL DEFAULT 13,
    retention_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    net_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    paid_at DATE NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE fixed_assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NULL,
    acquisition_date DATE NOT NULL,
    acquisition_value DECIMAL(12,2) NOT NULL DEFAULT 0,
    depreciation_method VARCHAR(30) NOT NULL DEFAULT 'linea_recta',
    useful_life_months INT NOT NULL DEFAULT 0,
    accumulated_depreciation DECIMAL(12,2) NOT NULL DEFAULT 0,
    book_value DECIMAL(12,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE bank_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    bank_name VARCHAR(150) NULL,
    account_number VARCHAR(80) NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    current_balance DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE bank_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    bank_account_id INT NOT NULL,
    transaction_date DATE NOT NULL,
    description VARCHAR(255) NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'deposito',
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    balance DECIMAL(12,2) NOT NULL DEFAULT 0,
    reference VARCHAR(150) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id)
);

CREATE TABLE inventory_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    product_id INT NULL,
    produced_product_id INT NULL,
    movement_date DATE NOT NULL,
    movement_type VARCHAR(20) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    reference_type VARCHAR(50) NULL,
    reference_id INT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (produced_product_id) REFERENCES produced_products(id)
);

CREATE TABLE document_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(120) NOT NULL,
    color VARCHAR(20) NOT NULL DEFAULT '#6c757d',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_document_categories_company (company_id),
    CONSTRAINT fk_document_categories_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON DELETE CASCADE
);

CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    category_id INT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size INT NOT NULL DEFAULT 0,
    is_favorite TINYINT(1) NOT NULL DEFAULT 0,
    download_count INT NOT NULL DEFAULT 0,
    last_downloaded_at DATETIME NULL,
    deleted_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_documents_company (company_id),
    INDEX idx_documents_category (category_id),
    CONSTRAINT fk_documents_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_documents_category
        FOREIGN KEY (category_id) REFERENCES document_categories(id)
        ON DELETE SET NULL
);

CREATE TABLE document_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    user_id INT NOT NULL,
    shared_by_user_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_document_shares_document (document_id),
    INDEX idx_document_shares_user (user_id),
    CONSTRAINT fk_document_shares_document
        FOREIGN KEY (document_id) REFERENCES documents(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_document_shares_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_document_shares_shared_by
        FOREIGN KEY (shared_by_user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE calendar_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    created_by_user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    event_type VARCHAR(20) NOT NULL DEFAULT 'meeting',
    location VARCHAR(150) NULL,
    start_at DATETIME NOT NULL,
    end_at DATETIME NULL,
    all_day TINYINT(1) NOT NULL DEFAULT 0,
    reminder_minutes INT NULL,
    class_name VARCHAR(100) NOT NULL DEFAULT 'bg-primary-subtle text-primary',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_calendar_events_company (company_id),
    INDEX idx_calendar_events_start (start_at),
    CONSTRAINT fk_calendar_events_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_calendar_events_user
        FOREIGN KEY (created_by_user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE calendar_event_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    document_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY idx_calendar_event_document_unique (event_id, document_id),
    INDEX idx_calendar_event_documents_event (event_id),
    INDEX idx_calendar_event_documents_document (document_id),
    CONSTRAINT fk_calendar_event_documents_event
        FOREIGN KEY (event_id) REFERENCES calendar_events(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_calendar_event_documents_document
        FOREIGN KEY (document_id) REFERENCES documents(id)
        ON DELETE CASCADE
);

CREATE TABLE calendar_event_attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY idx_calendar_event_attendee_unique (event_id, user_id),
    INDEX idx_calendar_event_attendees_event (event_id),
    INDEX idx_calendar_event_attendees_user (user_id),
    CONSTRAINT fk_calendar_event_attendees_event
        FOREIGN KEY (event_id) REFERENCES calendar_events(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_calendar_event_attendees_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS sii_activity_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL,
    name VARCHAR(255) NOT NULL,
    UNIQUE KEY uniq_sii_activity_code (code),
    INDEX idx_sii_activity_name (name)
);

INSERT IGNORE INTO sii_activity_codes (code, name) VALUES
('011111', 'Cultivo de trigo'),
('011112', 'Cultivo de maíz'),
('011113', 'Cultivo de arroz'),
('011119', 'Otros cultivos de cereales'),
('011120', 'Cultivo de legumbres y semillas oleaginosas'),
('011131', 'Cultivo de papa'),
('011139', 'Cultivo de raíces y tubérculos n.c.p.'),
('011200', 'Cultivo de hortalizas'),
('011300', 'Cultivo de frutas'),
('011400', 'Cultivo de uvas'),
('011500', 'Cultivo de frutos oleaginosos'),
('011600', 'Cultivo de plantas para preparar bebidas'),
('011900', 'Otros cultivos permanentes'),
('012110', 'Cultivo de flores'),
('012120', 'Cultivo de plantas vivas y productos de vivero'),
('012130', 'Producción de semillas'),
('012140', 'Producción de plántulas'),
('012200', 'Producción de hongos y trufas'),
('012900', 'Otros cultivos no permanentes n.c.p.'),
('013000', 'Cultivo de plantas de vivero'),
('014111', 'Cría de ganado bovino'),
('014112', 'Engorda de ganado bovino'),
('014120', 'Cría de ganado equino'),
('014130', 'Cría de ovinos y caprinos'),
('014141', 'Cría de porcinos'),
('014200', 'Cría de aves de corral'),
('014300', 'Cría de otros animales'),
('014400', 'Producción de leche'),
('014500', 'Producción de huevos'),
('014600', 'Apicultura'),
('014900', 'Otras actividades de apoyo a la ganadería'),
('016100', 'Actividades de apoyo a la agricultura'),
('016200', 'Actividades de apoyo a la ganadería'),
('016300', 'Actividades posteriores a la cosecha'),
('016400', 'Tratamiento de semillas para propagación'),
('021000', 'Silvicultura'),
('022000', 'Extracción de madera'),
('023000', 'Recolección de productos forestales'),
('024000', 'Servicios de apoyo a la silvicultura'),
('031100', 'Pesca marítima'),
('031200', 'Pesca de agua dulce'),
('032100', 'Acuicultura marítima'),
('032200', 'Acuicultura de agua dulce'),
('051000', 'Extracción de carbón de piedra'),
('052000', 'Extracción de lignito'),
('061000', 'Extracción de petróleo crudo'),
('062000', 'Extracción de gas natural'),
('071000', 'Extracción de minerales de hierro'),
('072100', 'Extracción de minerales de uranio y torio'),
('072910', 'Extracción de cobre'),
('072920', 'Extracción de otros minerales metálicos no ferrosos'),
('081000', 'Extracción de piedra, arena y arcilla'),
('089100', 'Extracción de minerales para productos químicos'),
('089200', 'Extracción de turba'),
('089300', 'Extracción de sal'),
('089900', 'Otras actividades de explotación de minas y canteras'),
('101010', 'Elaboración y conservación de carne'),
('101020', 'Elaboración y conservación de productos de la pesca'),
('101030', 'Elaboración y conservación de frutas, legumbres y hortalizas'),
('101040', 'Elaboración de aceites y grasas'),
('101050', 'Elaboración de productos lácteos'),
('101060', 'Elaboración de productos de molinería'),
('101070', 'Elaboración de almidones y productos derivados'),
('101080', 'Elaboración de productos de panadería'),
('101090', 'Elaboración de otros productos alimenticios'),
('102000', 'Elaboración de alimentos preparados para animales'),
('110100', 'Destilación de bebidas alcohólicas'),
('110200', 'Elaboración de vinos'),
('110300', 'Elaboración de cervezas'),
('110400', 'Elaboración de bebidas no alcohólicas'),
('120000', 'Elaboración de productos de tabaco'),
('131100', 'Preparación e hilatura de fibras textiles'),
('131200', 'Tejeduría de productos textiles'),
('131300', 'Acabado de productos textiles'),
('139100', 'Fabricación de tejidos de punto'),
('139200', 'Fabricación de otros productos textiles'),
('141000', 'Confección de prendas de vestir'),
('142000', 'Fabricación de artículos de piel'),
('143000', 'Fabricación de prendas de vestir de punto'),
('151100', 'Curtido y adobo de cuero'),
('151200', 'Fabricación de calzado'),
('152000', 'Fabricación de artículos de cuero'),
('161000', 'Aserrado y cepillado de madera'),
('162100', 'Fabricación de productos de madera'),
('170110', 'Fabricación de celulosa'),
('170120', 'Fabricación de papel y cartón'),
('170200', 'Fabricación de envases de papel'),
('181100', 'Impresión'),
('181200', 'Servicios relacionados con la impresión'),
('182000', 'Reproducción de soportes grabados'),
('191000', 'Fabricación de productos de hornos de coque'),
('192000', 'Fabricación de productos de la refinación del petróleo'),
('201100', 'Fabricación de sustancias químicas básicas'),
('201200', 'Fabricación de fertilizantes'),
('201300', 'Fabricación de plásticos y caucho sintético'),
('202100', 'Fabricación de plaguicidas'),
('202200', 'Fabricación de pinturas'),
('202300', 'Fabricación de jabones y detergentes'),
('202900', 'Fabricación de otros productos químicos'),
('210000', 'Fabricación de productos farmacéuticos'),
('221100', 'Fabricación de neumáticos'),
('221900', 'Fabricación de otros productos de caucho'),
('222000', 'Fabricación de productos de plástico'),
('231000', 'Fabricación de vidrio'),
('239100', 'Fabricación de productos de cerámica'),
('239200', 'Fabricación de productos refractarios'),
('239300', 'Fabricación de productos de arcilla'),
('239400', 'Fabricación de cemento'),
('239500', 'Fabricación de yeso'),
('239600', 'Fabricación de productos de hormigón'),
('239900', 'Fabricación de otros productos minerales no metálicos'),
('241000', 'Industrias básicas de hierro y acero'),
('242000', 'Industrias básicas de metales preciosos y no ferrosos'),
('243100', 'Fundición de hierro y acero'),
('243200', 'Fundición de metales no ferrosos'),
('251100', 'Fabricación de productos metálicos'),
('251200', 'Fabricación de tanques y depósitos'),
('252000', 'Fabricación de armas y municiones'),
('259100', 'Forja y estampado de metales'),
('259200', 'Tratamiento y revestimiento de metales'),
('259300', 'Fabricación de artículos de ferretería'),
('259900', 'Fabricación de otros productos elaborados de metal'),
('261000', 'Fabricación de componentes electrónicos'),
('262000', 'Fabricación de computadores y periféricos'),
('263000', 'Fabricación de equipos de comunicación'),
('264000', 'Fabricación de aparatos electrónicos de consumo'),
('265100', 'Fabricación de instrumentos de medición'),
('265200', 'Fabricación de relojes'),
('266000', 'Fabricación de equipos de radiación'),
('267000', 'Fabricación de instrumentos ópticos'),
('268000', 'Fabricación de soportes magnéticos y ópticos'),
('271000', 'Fabricación de motores eléctricos'),
('272000', 'Fabricación de pilas y baterías'),
('273100', 'Fabricación de cables de fibra óptica'),
('273200', 'Fabricación de cables'),
('273300', 'Fabricación de dispositivos de cableado'),
('274000', 'Fabricación de equipos de iluminación'),
('275000', 'Fabricación de aparatos de uso doméstico'),
('279000', 'Fabricación de otros equipos eléctricos'),
('281100', 'Fabricación de motores y turbinas'),
('281200', 'Fabricación de bombas y compresores'),
('281300', 'Fabricación de grifos y válvulas'),
('281400', 'Fabricación de cojinetes y engranajes'),
('281500', 'Fabricación de hornos y quemadores'),
('281600', 'Fabricación de equipos de elevación'),
('281700', 'Fabricación de maquinaria para la construcción'),
('281800', 'Fabricación de maquinaria para la agricultura'),
('281900', 'Fabricación de maquinaria de uso general'),
('282100', 'Fabricación de maquinaria para metalurgia'),
('282200', 'Fabricación de maquinaria para minería'),
('282300', 'Fabricación de maquinaria para industria alimentaria'),
('282400', 'Fabricación de maquinaria para la industria textil'),
('282500', 'Fabricación de maquinaria para la industria del papel'),
('282900', 'Fabricación de otras maquinarias especiales'),
('291000', 'Fabricación de vehículos automotores'),
('292000', 'Fabricación de carrocerías y remolques'),
('293000', 'Fabricación de partes y piezas de vehículos'),
('301100', 'Construcción de buques'),
('301200', 'Construcción de embarcaciones de recreo'),
('302000', 'Fabricación de locomotoras y material rodante'),
('303000', 'Fabricación de aeronaves'),
('304000', 'Fabricación de vehículos militares'),
('309100', 'Fabricación de motocicletas'),
('309200', 'Fabricación de bicicletas'),
('309900', 'Fabricación de otros equipos de transporte'),
('310000', 'Fabricación de muebles'),
('321100', 'Fabricación de joyas'),
('321200', 'Fabricación de bisutería'),
('322000', 'Fabricación de instrumentos musicales'),
('323000', 'Fabricación de artículos deportivos'),
('324000', 'Fabricación de juegos y juguetes'),
('325000', 'Fabricación de instrumentos médicos'),
('329000', 'Otras industrias manufactureras n.c.p.'),
('331100', 'Reparación de productos metálicos'),
('331200', 'Reparación de maquinaria'),
('331300', 'Reparación de equipos electrónicos'),
('331400', 'Reparación de equipos eléctricos'),
('331500', 'Reparación de equipos de transporte'),
('331900', 'Reparación de otros equipos'),
('332000', 'Instalación de maquinaria y equipos'),
('351000', 'Generación de energía eléctrica'),
('352000', 'Transmisión de energía eléctrica'),
('353000', 'Distribución de energía eléctrica'),
('360000', 'Captación y distribución de agua'),
('370000', 'Evacuación de aguas residuales'),
('381100', 'Recolección de desechos'),
('381200', 'Tratamiento y eliminación de desechos'),
('382100', 'Recuperación de materiales'),
('390000', 'Actividades de descontaminación'),
('410010', 'Construcción de edificios'),
('410020', 'Construcción de viviendas'),
('421000', 'Construcción de carreteras'),
('422000', 'Construcción de proyectos de servicio público'),
('429000', 'Construcción de otras obras de ingeniería'),
('431100', 'Demolición'),
('431200', 'Preparación del terreno'),
('432100', 'Instalación eléctrica'),
('432200', 'Instalaciones de gas y calefacción'),
('432900', 'Otras instalaciones para obras de construcción'),
('433000', 'Terminación y acabado de edificios'),
('439000', 'Otras actividades especializadas de construcción'),
('451001', 'Venta de vehículos automotores'),
('452001', 'Mantenimiento y reparación de vehículos automotores'),
('453000', 'Venta de partes y piezas para vehículos'),
('454000', 'Venta de motocicletas y accesorios'),
('461000', 'Venta al por mayor a cambio de una retribución'),
('462000', 'Venta al por mayor de materias primas agropecuarias'),
('463000', 'Venta al por mayor de alimentos'),
('464100', 'Venta al por mayor de textiles'),
('464200', 'Venta al por mayor de prendas de vestir'),
('464300', 'Venta al por mayor de calzado'),
('464901', 'Venta al por mayor de maquinaria'),
('464902', 'Venta al por mayor de artículos eléctricos'),
('464903', 'Venta al por mayor de productos farmacéuticos'),
('464904', 'Venta al por mayor de combustibles'),
('464905', 'Venta al por mayor de materiales de construcción'),
('464906', 'Venta al por mayor de metales'),
('464907', 'Venta al por mayor de madera'),
('464908', 'Venta al por mayor de equipos médicos'),
('464909', 'Venta al por mayor de otros productos'),
('465100', 'Venta al por mayor de computadores'),
('465200', 'Venta al por mayor de equipos de telecomunicaciones'),
('465300', 'Venta al por mayor de equipos electrónicos'),
('466100', 'Venta al por mayor de maquinaria agrícola'),
('466200', 'Venta al por mayor de maquinaria para la minería'),
('466300', 'Venta al por mayor de maquinaria para la construcción'),
('466901', 'Venta al por mayor de otros tipos de maquinaria'),
('466902', 'Venta al por mayor de vehículos'),
('466903', 'Venta al por mayor de equipos de transporte'),
('466904', 'Venta al por mayor de productos químicos'),
('466909', 'Venta al por mayor de otros productos'),
('469000', 'Venta al por mayor no especializada'),
('471100', 'Venta al por menor en comercios no especializados'),
('471900', 'Venta al por menor en otros comercios no especializados'),
('472100', 'Venta al por menor de alimentos'),
('472200', 'Venta al por menor de bebidas'),
('472300', 'Venta al por menor de tabaco'),
('472400', 'Venta al por menor de combustibles'),
('472500', 'Venta al por menor de textiles'),
('472600', 'Venta al por menor de prendas de vestir'),
('472700', 'Venta al por menor de calzado'),
('472900', 'Venta al por menor de otros productos'),
('473000', 'Venta al por menor de combustibles para vehículos'),
('474100', 'Venta al por menor de computadores'),
('474200', 'Venta al por menor de equipos de telecomunicaciones'),
('474300', 'Venta al por menor de equipos electrónicos'),
('475100', 'Venta al por menor de textiles'),
('475200', 'Venta al por menor de ferretería'),
('475900', 'Venta al por menor de otros productos en comercios especializados'),
('476100', 'Venta al por menor de libros'),
('476200', 'Venta al por menor de periódicos'),
('476300', 'Venta al por menor de productos culturales'),
('476400', 'Venta al por menor de aparatos electrónicos'),
('476500', 'Venta al por menor de música'),
('477100', 'Venta al por menor de prendas de vestir'),
('477200', 'Venta al por menor de calzado'),
('477300', 'Venta al por menor de productos farmacéuticos'),
('477400', 'Venta al por menor de productos de cosmética'),
('477500', 'Venta al por menor de productos médicos'),
('477600', 'Venta al por menor de artículos deportivos'),
('477700', 'Venta al por menor de artículos recreativos'),
('477800', 'Venta al por menor de otros productos en comercios especializados'),
('478100', 'Venta al por menor en puestos de alimentos'),
('478200', 'Venta al por menor en puestos de textiles'),
('478900', 'Venta al por menor en otros puestos'),
('479100', 'Venta al por menor por internet'),
('479900', 'Venta al por menor por otros medios'),
('491100', 'Transporte interurbano de pasajeros por ferrocarril'),
('491200', 'Transporte de carga por ferrocarril'),
('492100', 'Transporte urbano de pasajeros'),
('492200', 'Transporte interurbano de pasajeros'),
('492300', 'Transporte de carga por carretera'),
('493000', 'Transporte por oleoducto'),
('501100', 'Transporte marítimo de pasajeros'),
('501200', 'Transporte marítimo de carga'),
('502100', 'Transporte fluvial de pasajeros'),
('502200', 'Transporte fluvial de carga'),
('511000', 'Transporte aéreo de pasajeros'),
('512000', 'Transporte aéreo de carga'),
('521000', 'Depósito y almacenamiento'),
('522100', 'Servicios auxiliares de transporte'),
('522200', 'Servicios auxiliares de transporte marítimo'),
('522300', 'Servicios auxiliares de transporte aéreo'),
('522400', 'Manipulación de carga'),
('522900', 'Otras actividades de apoyo al transporte'),
('531000', 'Actividades de correo'),
('532000', 'Actividades de mensajería'),
('551000', 'Alojamiento'),
('552000', 'Actividades de campamento'),
('559000', 'Otros tipos de alojamiento'),
('561000', 'Restaurantes y servicios de comida'),
('562000', 'Servicios de catering'),
('563000', 'Actividades de bebidas'),
('581100', 'Edición de libros'),
('581200', 'Edición de periódicos'),
('581300', 'Edición de revistas'),
('581900', 'Otras actividades de edición'),
('582000', 'Edición de programas informáticos'),
('591100', 'Producción de películas'),
('591200', 'Postproducción'),
('591300', 'Distribución de películas'),
('591400', 'Proyección de películas'),
('592000', 'Actividades de grabación sonora'),
('601000', 'Transmisión radial'),
('602000', 'Programación televisiva'),
('611000', 'Telecomunicaciones'),
('612000', 'Telecomunicaciones inalámbricas'),
('613000', 'Telecomunicaciones por satélite'),
('619000', 'Otras telecomunicaciones'),
('620100', 'Actividades de programación informática'),
('620200', 'Consultoría de informática'),
('620300', 'Gestión de instalaciones informáticas'),
('620900', 'Otras actividades de tecnología de la información'),
('631100', 'Procesamiento de datos'),
('631200', 'Portales web'),
('639100', 'Actividades de agencias de noticias'),
('639900', 'Otras actividades de servicios de información'),
('641100', 'Banca central'),
('641900', 'Otros servicios de intermediación monetaria'),
('642000', 'Actividades de sociedades de cartera'),
('643000', 'Fondos y sociedades de inversión'),
('649100', 'Arrendamiento financiero'),
('649200', 'Otros servicios financieros'),
('649300', 'Financiamiento de consumo'),
('649900', 'Otros servicios financieros n.c.p.'),
('651100', 'Seguros'),
('651200', 'Reaseguros'),
('652000', 'Planes de pensiones'),
('653000', 'Servicios auxiliares de seguros'),
('661100', 'Administración de mercados financieros'),
('661200', 'Corretaje de valores'),
('661900', 'Otras actividades auxiliares de servicios financieros'),
('662100', 'Evaluación de riesgos'),
('662200', 'Corretaje de seguros'),
('662900', 'Otras actividades auxiliares de seguros'),
('663000', 'Administración de fondos'),
('681000', 'Actividades inmobiliarias'),
('682000', 'Alquiler de bienes inmuebles'),
('691000', 'Actividades jurídicas'),
('692000', 'Actividades de contabilidad'),
('701000', 'Actividades de oficinas principales'),
('702000', 'Actividades de consultoría de gestión'),
('711000', 'Actividades de arquitectura'),
('712000', 'Ensayos y análisis técnicos'),
('721000', 'Investigación y desarrollo experimental en ciencias naturales'),
('722000', 'Investigación y desarrollo experimental en ciencias sociales'),
('731000', 'Publicidad'),
('732000', 'Investigación de mercados'),
('741000', 'Actividades de diseño especializado'),
('742000', 'Actividades de fotografía'),
('749000', 'Otras actividades profesionales'),
('750000', 'Actividades veterinarias'),
('771000', 'Alquiler de vehículos'),
('772100', 'Alquiler de artículos recreativos'),
('772200', 'Alquiler de videos'),
('772900', 'Alquiler de otros bienes'),
('773000', 'Alquiler de maquinaria y equipo'),
('774000', 'Arrendamiento de propiedad intelectual'),
('781000', 'Actividades de empleo'),
('782000', 'Actividades de agencias de empleo'),
('783000', 'Otras actividades de suministro de recursos humanos'),
('791100', 'Actividades de agencias de viaje'),
('791200', 'Actividades de operadores turísticos'),
('799000', 'Otros servicios de reservas'),
('801000', 'Actividades de seguridad privada'),
('802000', 'Actividades de servicios de seguridad'),
('803000', 'Investigaciones'),
('811000', 'Actividades de limpieza'),
('812100', 'Limpieza general de edificios'),
('812900', 'Otras actividades de limpieza'),
('813000', 'Servicios de paisajismo'),
('821100', 'Actividades administrativas'),
('821900', 'Servicios de apoyo a oficinas'),
('822000', 'Actividades de call center'),
('823000', 'Organización de convenciones'),
('829100', 'Agencias de cobranza'),
('829900', 'Otros servicios de apoyo a empresas'),
('841100', 'Administración pública'),
('842100', 'Relaciones exteriores'),
('842200', 'Defensa'),
('842300', 'Orden público'),
('843000', 'Seguridad social'),
('851000', 'Enseñanza preescolar'),
('852100', 'Enseñanza primaria'),
('852200', 'Enseñanza secundaria'),
('853100', 'Educación superior'),
('853200', 'Educación técnica'),
('854100', 'Educación deportiva'),
('854200', 'Educación cultural'),
('854900', 'Otras actividades de enseñanza'),
('855000', 'Servicios de apoyo a la enseñanza'),
('861000', 'Actividades de hospitales'),
('862000', 'Actividades médicas y odontológicas'),
('869000', 'Otras actividades de atención de la salud'),
('871000', 'Instituciones de atención de salud'),
('872000', 'Atención a personas con discapacidad'),
('873000', 'Atención a personas mayores'),
('879000', 'Otras actividades de asistencia social'),
('881000', 'Actividades de servicios sociales'),
('889000', 'Otros servicios sociales'),
('900000', 'Actividades creativas y artísticas'),
('910100', 'Actividades de bibliotecas'),
('910200', 'Actividades de museos'),
('910300', 'Actividades de jardines botánicos'),
('920000', 'Actividades de juegos de azar'),
('931100', 'Actividades deportivas'),
('931200', 'Actividades de clubes deportivos'),
('931900', 'Otras actividades deportivas'),
('932100', 'Parques de atracciones'),
('932900', 'Otras actividades de entretenimiento'),
('941100', 'Actividades de organizaciones empresariales'),
('941200', 'Actividades de organizaciones profesionales'),
('942000', 'Actividades de sindicatos'),
('949100', 'Actividades de organizaciones religiosas'),
('949200', 'Actividades de organizaciones políticas'),
('949900', 'Otras actividades asociativas'),
('951100', 'Reparación de computadores'),
('951200', 'Reparación de equipos de comunicación'),
('952100', 'Reparación de aparatos electrónicos'),
('952200', 'Reparación de electrodomésticos'),
('952300', 'Reparación de calzado y artículos de cuero'),
('952400', 'Reparación de muebles'),
('952900', 'Reparación de otros bienes'),
('960100', 'Lavado y limpieza de vehículos'),
('960200', 'Peluquería y otros tratamientos de belleza'),
('960300', 'Pompas fúnebres'),
('960900', 'Otras actividades de servicios personales');
CREATE TABLE IF NOT EXISTS document_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(120) NOT NULL,
    color VARCHAR(20) NOT NULL DEFAULT '#6c757d',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_document_categories_company (company_id),
    CONSTRAINT fk_document_categories_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    category_id INT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size INT NOT NULL DEFAULT 0,
    is_favorite TINYINT(1) NOT NULL DEFAULT 0,
    download_count INT NOT NULL DEFAULT 0,
    last_downloaded_at DATETIME NULL,
    deleted_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_documents_company (company_id),
    INDEX idx_documents_category (category_id),
    CONSTRAINT fk_documents_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_documents_category
        FOREIGN KEY (category_id) REFERENCES document_categories(id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS document_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    user_id INT NOT NULL,
    shared_by_user_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_document_shares_document (document_id),
    INDEX idx_document_shares_user (user_id),
    CONSTRAINT fk_document_shares_document
        FOREIGN KEY (document_id) REFERENCES documents(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_document_shares_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_document_shares_shared_by
        FOREIGN KEY (shared_by_user_id) REFERENCES users(id)
        ON DELETE CASCADE
);
START TRANSACTION;

CREATE TABLE IF NOT EXISTS calendar_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    created_by_user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    event_type VARCHAR(20) NOT NULL DEFAULT 'meeting',
    location VARCHAR(150) NULL,
    start_at DATETIME NOT NULL,
    end_at DATETIME NULL,
    all_day TINYINT(1) NOT NULL DEFAULT 0,
    reminder_minutes INT NULL,
    class_name VARCHAR(100) NOT NULL DEFAULT 'bg-primary-subtle text-primary',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_calendar_events_company (company_id),
    INDEX idx_calendar_events_start (start_at),
    CONSTRAINT fk_calendar_events_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_calendar_events_user
        FOREIGN KEY (created_by_user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS calendar_event_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    document_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY idx_calendar_event_document_unique (event_id, document_id),
    INDEX idx_calendar_event_documents_event (event_id),
    INDEX idx_calendar_event_documents_document (document_id),
    CONSTRAINT fk_calendar_event_documents_event
        FOREIGN KEY (event_id) REFERENCES calendar_events(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_calendar_event_documents_document
        FOREIGN KEY (document_id) REFERENCES documents(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS calendar_event_attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY idx_calendar_event_attendee_unique (event_id, user_id),
    INDEX idx_calendar_event_attendees_event (event_id),
    INDEX idx_calendar_event_attendees_user (user_id),
    CONSTRAINT fk_calendar_event_attendees_event
        FOREIGN KEY (event_id) REFERENCES calendar_events(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_calendar_event_attendees_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

COMMIT;
START TRANSACTION;

SET @companies_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'companies' AND COLUMN_NAME = 'giro'
);
SET @sql := IF(@companies_giro = 0, 'ALTER TABLE companies ADD COLUMN giro VARCHAR(150) NULL AFTER address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @companies_activity_code := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'companies' AND COLUMN_NAME = 'activity_code'
);
SET @sql := IF(@companies_activity_code = 0, 'ALTER TABLE companies ADD COLUMN activity_code VARCHAR(50) NULL AFTER giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @companies_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'companies' AND COLUMN_NAME = 'commune'
);
SET @sql := IF(@companies_commune = 0, 'ALTER TABLE companies ADD COLUMN commune VARCHAR(120) NULL AFTER activity_code;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @companies_city := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'companies' AND COLUMN_NAME = 'city'
);
SET @sql := IF(@companies_city = 0, 'ALTER TABLE companies ADD COLUMN city VARCHAR(120) NULL AFTER commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @company_id_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'service_types'
      AND COLUMN_NAME = 'company_id'
);

SET @sql := IF(
    @company_id_exists = 0,
    'ALTER TABLE service_types ADD COLUMN company_id INT NULL AFTER id;',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE service_types
SET company_id = (SELECT id FROM companies ORDER BY id LIMIT 1)
WHERE company_id IS NULL;

SET @fk_service_types_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'service_types'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
      AND CONSTRAINT_NAME = 'fk_service_types_company'
);

SET @sql := IF(
    @fk_service_types_exists = 0,
    'ALTER TABLE service_types MODIFY company_id INT NOT NULL, ADD CONSTRAINT fk_service_types_company FOREIGN KEY (company_id) REFERENCES companies(id);',
    'ALTER TABLE service_types MODIFY company_id INT NOT NULL;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @company_id_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'system_services'
      AND COLUMN_NAME = 'company_id'
);

SET @sql := IF(
    @company_id_exists = 0,
    'ALTER TABLE system_services ADD COLUMN company_id INT NULL AFTER id;',
    'SELECT 1;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE system_services
SET company_id = (SELECT id FROM companies ORDER BY id LIMIT 1)
WHERE company_id IS NULL;

SET @fk_system_services_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'system_services'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
      AND CONSTRAINT_NAME = 'fk_system_services_company'
);

SET @sql := IF(
    @fk_system_services_exists = 0,
    'ALTER TABLE system_services MODIFY company_id INT NOT NULL, ADD CONSTRAINT fk_system_services_company FOREIGN KEY (company_id) REFERENCES companies(id);',
    'ALTER TABLE system_services MODIFY company_id INT NOT NULL;'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS commercial_briefs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    contact_name VARCHAR(150) NULL,
    contact_email VARCHAR(150) NULL,
    contact_phone VARCHAR(50) NULL,
    service_summary VARCHAR(150) NULL,
    expected_budget DECIMAL(12,2) NULL,
    desired_start_date DATE NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'nuevo',
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

CREATE TABLE IF NOT EXISTS sales_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    brief_id INT NULL,
    order_number VARCHAR(50) NOT NULL,
    order_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    total DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (brief_id) REFERENCES commercial_briefs(id)
);

CREATE TABLE IF NOT EXISTS service_renewals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NOT NULL,
    service_id INT NULL,
    renewal_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    reminder_days INT NOT NULL DEFAULT 15,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    contact_name VARCHAR(150) NULL,
    tax_id VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    address VARCHAR(255) NULL,
    giro VARCHAR(150) NULL,
    activity_code VARCHAR(50) NULL,
    commune VARCHAR(120) NULL,
    city VARCHAR(120) NULL,
    website VARCHAR(150) NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

SET @suppliers_contact_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'contact_name'
);
SET @sql := IF(@suppliers_contact_name = 0, 'ALTER TABLE suppliers ADD COLUMN contact_name VARCHAR(150) NULL AFTER name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @suppliers_tax_id := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'tax_id'
);
SET @sql := IF(@suppliers_tax_id = 0, 'ALTER TABLE suppliers ADD COLUMN tax_id VARCHAR(50) NULL AFTER contact_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @suppliers_website := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'website'
);
SET @sql := IF(@suppliers_website = 0, 'ALTER TABLE suppliers ADD COLUMN website VARCHAR(150) NULL AFTER address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @suppliers_notes := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'notes'
);
SET @sql := IF(@suppliers_notes = 0, 'ALTER TABLE suppliers ADD COLUMN notes TEXT NULL AFTER website;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @suppliers_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'giro'
);
SET @sql := IF(@suppliers_giro = 0, 'ALTER TABLE suppliers ADD COLUMN giro VARCHAR(150) NULL AFTER address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @suppliers_activity_code := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'activity_code'
);
SET @sql := IF(@suppliers_activity_code = 0, 'ALTER TABLE suppliers ADD COLUMN activity_code VARCHAR(50) NULL AFTER giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @suppliers_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'commune'
);
SET @sql := IF(@suppliers_commune = 0, 'ALTER TABLE suppliers ADD COLUMN commune VARCHAR(120) NULL AFTER activity_code;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @suppliers_city := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'city'
);
SET @sql := IF(@suppliers_city = 0, 'ALTER TABLE suppliers ADD COLUMN city VARCHAR(120) NULL AFTER commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS product_families (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS product_subfamilies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    family_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (family_id) REFERENCES product_families(id)
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    supplier_id INT NULL,
    family_id INT NULL,
    subfamily_id INT NULL,
    name VARCHAR(150) NOT NULL,
    sku VARCHAR(100) NULL,
    description TEXT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    stock_min INT NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

CREATE TABLE IF NOT EXISTS purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    supplier_id INT NOT NULL,
    reference VARCHAR(100) NULL,
    purchase_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    tax DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

CREATE TABLE IF NOT EXISTS purchase_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS pos_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    user_id INT NOT NULL,
    opening_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    closing_amount DECIMAL(12,2) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'abierto',
    opened_at DATETIME NOT NULL,
    closed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    client_id INT NULL,
    pos_session_id INT NULL,
    channel VARCHAR(20) NOT NULL DEFAULT 'venta',
    numero VARCHAR(50) NOT NULL,
    sale_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pagado',
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    tax DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (pos_session_id) REFERENCES pos_sessions(id)
);

CREATE TABLE IF NOT EXISTS sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NULL,
    service_id INT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE IF NOT EXISTS sale_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    method VARCHAR(50) NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id)
);

SET @idx_products_company := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND INDEX_NAME = 'idx_products_company'
);
SET @sql := IF(@idx_products_company = 0, 'CREATE INDEX idx_products_company ON products(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_products_supplier := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND INDEX_NAME = 'idx_products_supplier'
);
SET @sql := IF(@idx_products_supplier = 0, 'CREATE INDEX idx_products_supplier ON products(supplier_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @family_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'products'
      AND COLUMN_NAME = 'family_id'
);
SET @sql := IF(@family_exists = 0, 'ALTER TABLE products ADD COLUMN family_id INT NULL AFTER supplier_id;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @subfamily_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'products'
      AND COLUMN_NAME = 'subfamily_id'
);
SET @sql := IF(@subfamily_exists = 0, 'ALTER TABLE products ADD COLUMN subfamily_id INT NULL AFTER family_id;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_purchases_company := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND INDEX_NAME = 'idx_purchases_company'
);
SET @sql := IF(@idx_purchases_company = 0, 'CREATE INDEX idx_purchases_company ON purchases(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_sales_company := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND INDEX_NAME = 'idx_sales_company'
);
SET @sql := IF(@idx_sales_company = 0, 'CREATE INDEX idx_sales_company ON sales(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_product_families_company := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_families' AND INDEX_NAME = 'idx_product_families_company'
);
SET @sql := IF(@idx_product_families_company = 0, 'CREATE INDEX idx_product_families_company ON product_families(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_product_subfamilies_company := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_subfamilies' AND INDEX_NAME = 'idx_product_subfamilies_company'
);
SET @sql := IF(@idx_product_subfamilies_company = 0, 'CREATE INDEX idx_product_subfamilies_company ON product_subfamilies(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_pos_sessions_company_user := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pos_sessions' AND INDEX_NAME = 'idx_pos_sessions_company_user'
);
SET @sql := IF(@idx_pos_sessions_company_user = 0, 'CREATE INDEX idx_pos_sessions_company_user ON pos_sessions(company_id, user_id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sale_pos_col := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'pos_session_id'
);
SET @sql := IF(@sale_pos_col = 0, 'ALTER TABLE sales ADD COLUMN pos_session_id INT NULL AFTER client_id, ADD CONSTRAINT fk_sales_pos_session FOREIGN KEY (pos_session_id) REFERENCES pos_sessions(id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sale_items_service_col := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sale_items' AND COLUMN_NAME = 'service_id'
);
SET @sql := IF(@sale_items_service_col = 0, 'ALTER TABLE sale_items ADD COLUMN service_id INT NULL AFTER product_id, MODIFY product_id INT NULL, ADD CONSTRAINT fk_sale_items_service FOREIGN KEY (service_id) REFERENCES services(id);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS hr_departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_contract_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    max_duration_months INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_health_providers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    provider_type VARCHAR(20) NOT NULL DEFAULT 'fonasa',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_pension_funds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_work_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    weekly_hours INT NOT NULL DEFAULT 45,
    start_time TIME NULL,
    end_time TIME NULL,
    lunch_break_minutes INT NOT NULL DEFAULT 60,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_payroll_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    item_type VARCHAR(20) NOT NULL DEFAULT 'haber',
    taxable TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS hr_employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    department_id INT NULL,
    position_id INT NULL,
    health_provider_id INT NULL,
    pension_fund_id INT NULL,
    rut VARCHAR(50) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    nationality VARCHAR(100) NULL,
    birth_date DATE NULL,
    civil_status VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    address VARCHAR(255) NULL,
    hire_date DATE NOT NULL,
    termination_date DATE NULL,
    health_provider VARCHAR(100) NULL,
    health_plan VARCHAR(150) NULL,
    pension_fund VARCHAR(100) NULL,
    pension_rate DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    health_rate DECIMAL(5,2) NOT NULL DEFAULT 7.00,
    unemployment_rate DECIMAL(5,2) NOT NULL DEFAULT 0.60,
    dependents_count INT NOT NULL DEFAULT 0,
    payment_method VARCHAR(50) NULL,
    bank_name VARCHAR(100) NULL,
    bank_account_type VARCHAR(50) NULL,
    bank_account_number VARCHAR(50) NULL,
    qr_token VARCHAR(100) NULL,
    face_descriptor TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (department_id) REFERENCES hr_departments(id),
    FOREIGN KEY (position_id) REFERENCES hr_positions(id),
    FOREIGN KEY (health_provider_id) REFERENCES hr_health_providers(id),
    FOREIGN KEY (pension_fund_id) REFERENCES hr_pension_funds(id)
);

SET @hr_employees_health_provider_id := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'health_provider_id'
);
SET @sql := IF(@hr_employees_health_provider_id = 0, 'ALTER TABLE hr_employees ADD COLUMN health_provider_id INT NULL AFTER position_id;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_pension_fund_id := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'pension_fund_id'
);
SET @sql := IF(@hr_employees_pension_fund_id = 0, 'ALTER TABLE hr_employees ADD COLUMN pension_fund_id INT NULL AFTER health_provider_id;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_qr_token := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'qr_token'
);
SET @sql := IF(@hr_employees_qr_token = 0, 'ALTER TABLE hr_employees ADD COLUMN qr_token VARCHAR(100) NULL AFTER bank_account_number;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_face_descriptor := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'face_descriptor'
);
SET @sql := IF(@hr_employees_face_descriptor = 0, 'ALTER TABLE hr_employees ADD COLUMN face_descriptor TEXT NULL AFTER qr_token;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_nationality := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'nationality'
);
SET @sql := IF(@hr_employees_nationality = 0, 'ALTER TABLE hr_employees ADD COLUMN nationality VARCHAR(100) NULL AFTER last_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_birth_date := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'birth_date'
);
SET @sql := IF(@hr_employees_birth_date = 0, 'ALTER TABLE hr_employees ADD COLUMN birth_date DATE NULL AFTER nationality;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_civil_status := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'civil_status'
);
SET @sql := IF(@hr_employees_civil_status = 0, 'ALTER TABLE hr_employees ADD COLUMN civil_status VARCHAR(50) NULL AFTER birth_date;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_health_provider := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'health_provider'
);
SET @sql := IF(@hr_employees_health_provider = 0, 'ALTER TABLE hr_employees ADD COLUMN health_provider VARCHAR(100) NULL AFTER termination_date;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_health_plan := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'health_plan'
);
SET @sql := IF(@hr_employees_health_plan = 0, 'ALTER TABLE hr_employees ADD COLUMN health_plan VARCHAR(150) NULL AFTER health_provider;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_pension_fund := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'pension_fund'
);
SET @sql := IF(@hr_employees_pension_fund = 0, 'ALTER TABLE hr_employees ADD COLUMN pension_fund VARCHAR(100) NULL AFTER health_plan;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_pension_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'pension_rate'
);
SET @sql := IF(@hr_employees_pension_rate = 0, 'ALTER TABLE hr_employees ADD COLUMN pension_rate DECIMAL(5,2) NOT NULL DEFAULT 10.00 AFTER pension_fund;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_health_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'health_rate'
);
SET @sql := IF(@hr_employees_health_rate = 0, 'ALTER TABLE hr_employees ADD COLUMN health_rate DECIMAL(5,2) NOT NULL DEFAULT 7.00 AFTER pension_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_unemployment_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'unemployment_rate'
);
SET @sql := IF(@hr_employees_unemployment_rate = 0, 'ALTER TABLE hr_employees ADD COLUMN unemployment_rate DECIMAL(5,2) NOT NULL DEFAULT 0.60 AFTER health_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_dependents := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'dependents_count'
);
SET @sql := IF(@hr_employees_dependents = 0, 'ALTER TABLE hr_employees ADD COLUMN dependents_count INT NOT NULL DEFAULT 0 AFTER unemployment_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_payment_method := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'payment_method'
);
SET @sql := IF(@hr_employees_payment_method = 0, 'ALTER TABLE hr_employees ADD COLUMN payment_method VARCHAR(50) NULL AFTER dependents_count;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_bank_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'bank_name'
);
SET @sql := IF(@hr_employees_bank_name = 0, 'ALTER TABLE hr_employees ADD COLUMN bank_name VARCHAR(100) NULL AFTER payment_method;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_bank_account_type := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'bank_account_type'
);
SET @sql := IF(@hr_employees_bank_account_type = 0, 'ALTER TABLE hr_employees ADD COLUMN bank_account_type VARCHAR(50) NULL AFTER bank_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_employees_bank_account_number := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_employees' AND COLUMN_NAME = 'bank_account_number'
);
SET @sql := IF(@hr_employees_bank_account_number = 0, 'ALTER TABLE hr_employees ADD COLUMN bank_account_number VARCHAR(50) NULL AFTER bank_account_type;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS hr_contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    employee_id INT NOT NULL,
    contract_type_id INT NULL,
    department_id INT NULL,
    position_id INT NULL,
    schedule_id INT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    salary DECIMAL(12,2) NOT NULL,
    weekly_hours INT NOT NULL DEFAULT 45,
    status VARCHAR(20) NOT NULL DEFAULT 'vigente',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id),
    FOREIGN KEY (contract_type_id) REFERENCES hr_contract_types(id),
    FOREIGN KEY (department_id) REFERENCES hr_departments(id),
    FOREIGN KEY (position_id) REFERENCES hr_positions(id),
    FOREIGN KEY (schedule_id) REFERENCES hr_work_schedules(id)
);

CREATE TABLE IF NOT EXISTS hr_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    employee_id INT NOT NULL,
    date DATE NOT NULL,
    check_in TIME NULL,
    check_out TIME NULL,
    worked_hours DECIMAL(5,2) NULL,
    overtime_hours DECIMAL(5,2) NOT NULL DEFAULT 0,
    absence_type VARCHAR(100) NULL,
    notes VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id)
);

CREATE TABLE IF NOT EXISTS hr_payrolls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    employee_id INT NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    base_salary DECIMAL(12,2) NOT NULL,
    bonuses DECIMAL(12,2) NOT NULL DEFAULT 0,
    other_earnings DECIMAL(12,2) NOT NULL DEFAULT 0,
    other_deductions DECIMAL(12,2) NOT NULL DEFAULT 0,
    taxable_income DECIMAL(12,2) NOT NULL DEFAULT 0,
    pension_deduction DECIMAL(12,2) NOT NULL DEFAULT 0,
    health_deduction DECIMAL(12,2) NOT NULL DEFAULT 0,
    unemployment_deduction DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_deductions DECIMAL(12,2) NOT NULL DEFAULT 0,
    net_pay DECIMAL(12,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'borrador',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id)
);

SET @hr_payrolls_other_earnings := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'other_earnings'
);
SET @sql := IF(@hr_payrolls_other_earnings = 0, 'ALTER TABLE hr_payrolls ADD COLUMN other_earnings DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER bonuses;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_payrolls_other_deductions := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'other_deductions'
);
SET @sql := IF(@hr_payrolls_other_deductions = 0, 'ALTER TABLE hr_payrolls ADD COLUMN other_deductions DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER other_earnings;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_payrolls_taxable_income := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'taxable_income'
);
SET @sql := IF(@hr_payrolls_taxable_income = 0, 'ALTER TABLE hr_payrolls ADD COLUMN taxable_income DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER other_deductions;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_payrolls_pension_deduction := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'pension_deduction'
);
SET @sql := IF(@hr_payrolls_pension_deduction = 0, 'ALTER TABLE hr_payrolls ADD COLUMN pension_deduction DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER taxable_income;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_payrolls_health_deduction := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'health_deduction'
);
SET @sql := IF(@hr_payrolls_health_deduction = 0, 'ALTER TABLE hr_payrolls ADD COLUMN health_deduction DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER pension_deduction;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_payrolls_unemployment_deduction := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'unemployment_deduction'
);
SET @sql := IF(@hr_payrolls_unemployment_deduction = 0, 'ALTER TABLE hr_payrolls ADD COLUMN unemployment_deduction DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER health_deduction;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @hr_payrolls_total_deductions := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hr_payrolls' AND COLUMN_NAME = 'total_deductions'
);
SET @sql := IF(@hr_payrolls_total_deductions = 0, 'ALTER TABLE hr_payrolls ADD COLUMN total_deductions DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER unemployment_deduction;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS hr_payroll_lines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payroll_id INT NOT NULL,
    payroll_item_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (payroll_id) REFERENCES hr_payrolls(id),
    FOREIGN KEY (payroll_item_id) REFERENCES hr_payroll_items(id)
);

CREATE TABLE IF NOT EXISTS accounting_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(150) NOT NULL,
    type VARCHAR(30) NOT NULL,
    level INT NOT NULL DEFAULT 1,
    parent_id INT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (parent_id) REFERENCES accounting_accounts(id)
);

CREATE TABLE IF NOT EXISTS accounting_periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    period VARCHAR(20) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'abierto',
    closed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS accounting_journals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    entry_number VARCHAR(50) NOT NULL,
    entry_date DATE NOT NULL,
    description VARCHAR(255) NULL,
    source VARCHAR(20) NOT NULL DEFAULT 'manual',
    status VARCHAR(20) NOT NULL DEFAULT 'borrador',
    created_by INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS accounting_journal_lines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journal_id INT NOT NULL,
    account_id INT NOT NULL,
    line_description VARCHAR(255) NULL,
    debit DECIMAL(12,2) NOT NULL DEFAULT 0,
    credit DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (journal_id) REFERENCES accounting_journals(id),
    FOREIGN KEY (account_id) REFERENCES accounting_accounts(id)
);

CREATE TABLE IF NOT EXISTS tax_periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    period VARCHAR(20) NOT NULL,
    iva_debito DECIMAL(12,2) NOT NULL DEFAULT 0,
    iva_credito DECIMAL(12,2) NOT NULL DEFAULT 0,
    remanente DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_retenciones DECIMAL(12,2) NOT NULL DEFAULT 0,
    impuesto_unico DECIMAL(12,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS tax_withholdings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    period_id INT NULL,
    type VARCHAR(50) NOT NULL,
    base_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    rate DECIMAL(5,2) NOT NULL DEFAULT 0,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (period_id) REFERENCES tax_periods(id)
);

CREATE TABLE IF NOT EXISTS honorarios_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    provider_name VARCHAR(150) NOT NULL,
    provider_rut VARCHAR(50) NULL,
    document_number VARCHAR(50) NOT NULL,
    issue_date DATE NOT NULL,
    gross_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    retention_rate DECIMAL(5,2) NOT NULL DEFAULT 13,
    retention_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    net_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    paid_at DATE NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS fixed_assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NULL,
    acquisition_date DATE NOT NULL,
    acquisition_value DECIMAL(12,2) NOT NULL DEFAULT 0,
    depreciation_method VARCHAR(30) NOT NULL DEFAULT 'linea_recta',
    useful_life_months INT NOT NULL DEFAULT 0,
    accumulated_depreciation DECIMAL(12,2) NOT NULL DEFAULT 0,
    book_value DECIMAL(12,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS bank_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    bank_name VARCHAR(150) NULL,
    account_number VARCHAR(80) NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    current_balance DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS bank_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    bank_account_id INT NOT NULL,
    transaction_date DATE NOT NULL,
    description VARCHAR(255) NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'deposito',
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    balance DECIMAL(12,2) NOT NULL DEFAULT 0,
    reference VARCHAR(150) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id)
);

CREATE TABLE IF NOT EXISTS inventory_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    product_id INT NOT NULL,
    movement_date DATE NOT NULL,
    movement_type VARCHAR(20) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    reference_type VARCHAR(50) NULL,
    reference_id INT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

SET @invoices_sii_document_type := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_document_type'
);
SET @sql := IF(@invoices_sii_document_type = 0, 'ALTER TABLE invoices ADD COLUMN sii_document_type VARCHAR(50) NULL AFTER total;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_document_number := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_document_number'
);
SET @sql := IF(@invoices_sii_document_number = 0, 'ALTER TABLE invoices ADD COLUMN sii_document_number VARCHAR(50) NULL AFTER sii_document_type;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_receiver_rut := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_receiver_rut'
);
SET @sql := IF(@invoices_sii_receiver_rut = 0, 'ALTER TABLE invoices ADD COLUMN sii_receiver_rut VARCHAR(50) NULL AFTER sii_document_number;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_receiver_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_receiver_name'
);
SET @sql := IF(@invoices_sii_receiver_name = 0, 'ALTER TABLE invoices ADD COLUMN sii_receiver_name VARCHAR(150) NULL AFTER sii_receiver_rut;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_receiver_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_receiver_giro'
);
SET @sql := IF(@invoices_sii_receiver_giro = 0, 'ALTER TABLE invoices ADD COLUMN sii_receiver_giro VARCHAR(150) NULL AFTER sii_receiver_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_receiver_activity_code := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_receiver_activity_code'
);
SET @sql := IF(@invoices_sii_receiver_activity_code = 0, 'ALTER TABLE invoices ADD COLUMN sii_receiver_activity_code VARCHAR(50) NULL AFTER sii_receiver_giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_receiver_address := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_receiver_address'
);
SET @sql := IF(@invoices_sii_receiver_address = 0, 'ALTER TABLE invoices ADD COLUMN sii_receiver_address VARCHAR(255) NULL AFTER sii_receiver_activity_code;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_receiver_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_receiver_commune'
);
SET @sql := IF(@invoices_sii_receiver_commune = 0, 'ALTER TABLE invoices ADD COLUMN sii_receiver_commune VARCHAR(100) NULL AFTER sii_receiver_address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_receiver_city := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_receiver_city'
);
SET @sql := IF(@invoices_sii_receiver_city = 0, 'ALTER TABLE invoices ADD COLUMN sii_receiver_city VARCHAR(100) NULL AFTER sii_receiver_commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_tax_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_tax_rate'
);
SET @sql := IF(@invoices_sii_tax_rate = 0, 'ALTER TABLE invoices ADD COLUMN sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19 AFTER sii_receiver_city;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invoices_sii_exempt_amount := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sii_exempt_amount'
);
SET @sql := IF(@invoices_sii_exempt_amount = 0, 'ALTER TABLE invoices ADD COLUMN sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER sii_tax_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_document_type := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_document_type'
);
SET @sql := IF(@quotes_sii_document_type = 0, 'ALTER TABLE quotes ADD COLUMN sii_document_type VARCHAR(50) NULL AFTER total;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_document_number := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_document_number'
);
SET @sql := IF(@quotes_sii_document_number = 0, 'ALTER TABLE quotes ADD COLUMN sii_document_number VARCHAR(50) NULL AFTER sii_document_type;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_receiver_rut := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_receiver_rut'
);
SET @sql := IF(@quotes_sii_receiver_rut = 0, 'ALTER TABLE quotes ADD COLUMN sii_receiver_rut VARCHAR(50) NULL AFTER sii_document_number;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_receiver_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_receiver_name'
);
SET @sql := IF(@quotes_sii_receiver_name = 0, 'ALTER TABLE quotes ADD COLUMN sii_receiver_name VARCHAR(150) NULL AFTER sii_receiver_rut;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_receiver_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_receiver_giro'
);
SET @sql := IF(@quotes_sii_receiver_giro = 0, 'ALTER TABLE quotes ADD COLUMN sii_receiver_giro VARCHAR(150) NULL AFTER sii_receiver_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_receiver_activity_code := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_receiver_activity_code'
);
SET @sql := IF(@quotes_sii_receiver_activity_code = 0, 'ALTER TABLE quotes ADD COLUMN sii_receiver_activity_code VARCHAR(50) NULL AFTER sii_receiver_giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_receiver_address := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_receiver_address'
);
SET @sql := IF(@quotes_sii_receiver_address = 0, 'ALTER TABLE quotes ADD COLUMN sii_receiver_address VARCHAR(255) NULL AFTER sii_receiver_activity_code;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_receiver_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_receiver_commune'
);
SET @sql := IF(@quotes_sii_receiver_commune = 0, 'ALTER TABLE quotes ADD COLUMN sii_receiver_commune VARCHAR(100) NULL AFTER sii_receiver_address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_receiver_city := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_receiver_city'
);
SET @sql := IF(@quotes_sii_receiver_city = 0, 'ALTER TABLE quotes ADD COLUMN sii_receiver_city VARCHAR(100) NULL AFTER sii_receiver_commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_tax_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_tax_rate'
);
SET @sql := IF(@quotes_sii_tax_rate = 0, 'ALTER TABLE quotes ADD COLUMN sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19 AFTER sii_receiver_city;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @quotes_sii_exempt_amount := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'sii_exempt_amount'
);
SET @sql := IF(@quotes_sii_exempt_amount = 0, 'ALTER TABLE quotes ADD COLUMN sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER sii_tax_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_document_type := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_document_type'
);
SET @sql := IF(@purchases_sii_document_type = 0, 'ALTER TABLE purchases ADD COLUMN sii_document_type VARCHAR(50) NULL AFTER total;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_document_number := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_document_number'
);
SET @sql := IF(@purchases_sii_document_number = 0, 'ALTER TABLE purchases ADD COLUMN sii_document_number VARCHAR(50) NULL AFTER sii_document_type;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_receiver_rut := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_rut'
);
SET @sql := IF(@purchases_sii_receiver_rut = 0, 'ALTER TABLE purchases ADD COLUMN sii_receiver_rut VARCHAR(50) NULL AFTER sii_document_number;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_receiver_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_name'
);
SET @sql := IF(@purchases_sii_receiver_name = 0, 'ALTER TABLE purchases ADD COLUMN sii_receiver_name VARCHAR(150) NULL AFTER sii_receiver_rut;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_receiver_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_giro'
);
SET @sql := IF(@purchases_sii_receiver_giro = 0, 'ALTER TABLE purchases ADD COLUMN sii_receiver_giro VARCHAR(150) NULL AFTER sii_receiver_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_receiver_activity_code := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_activity_code'
);
SET @sql := IF(@purchases_sii_receiver_activity_code = 0, 'ALTER TABLE purchases ADD COLUMN sii_receiver_activity_code VARCHAR(50) NULL AFTER sii_receiver_giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_receiver_address := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_address'
);
SET @sql := IF(@purchases_sii_receiver_address = 0, 'ALTER TABLE purchases ADD COLUMN sii_receiver_address VARCHAR(255) NULL AFTER sii_receiver_activity_code;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_receiver_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_commune'
);
SET @sql := IF(@purchases_sii_receiver_commune = 0, 'ALTER TABLE purchases ADD COLUMN sii_receiver_commune VARCHAR(100) NULL AFTER sii_receiver_address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_receiver_city := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_receiver_city'
);
SET @sql := IF(@purchases_sii_receiver_city = 0, 'ALTER TABLE purchases ADD COLUMN sii_receiver_city VARCHAR(100) NULL AFTER sii_receiver_commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_tax_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_tax_rate'
);
SET @sql := IF(@purchases_sii_tax_rate = 0, 'ALTER TABLE purchases ADD COLUMN sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19 AFTER sii_receiver_city;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @purchases_sii_exempt_amount := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'sii_exempt_amount'
);
SET @sql := IF(@purchases_sii_exempt_amount = 0, 'ALTER TABLE purchases ADD COLUMN sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER sii_tax_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_document_type := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_document_type'
);
SET @sql := IF(@sales_sii_document_type = 0, 'ALTER TABLE sales ADD COLUMN sii_document_type VARCHAR(50) NULL AFTER total;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_document_number := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_document_number'
);
SET @sql := IF(@sales_sii_document_number = 0, 'ALTER TABLE sales ADD COLUMN sii_document_number VARCHAR(50) NULL AFTER sii_document_type;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_receiver_rut := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_receiver_rut'
);
SET @sql := IF(@sales_sii_receiver_rut = 0, 'ALTER TABLE sales ADD COLUMN sii_receiver_rut VARCHAR(50) NULL AFTER sii_document_number;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_receiver_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_receiver_name'
);
SET @sql := IF(@sales_sii_receiver_name = 0, 'ALTER TABLE sales ADD COLUMN sii_receiver_name VARCHAR(150) NULL AFTER sii_receiver_rut;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_receiver_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_receiver_giro'
);
SET @sql := IF(@sales_sii_receiver_giro = 0, 'ALTER TABLE sales ADD COLUMN sii_receiver_giro VARCHAR(150) NULL AFTER sii_receiver_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_receiver_activity_code := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_receiver_activity_code'
);
SET @sql := IF(@sales_sii_receiver_activity_code = 0, 'ALTER TABLE sales ADD COLUMN sii_receiver_activity_code VARCHAR(50) NULL AFTER sii_receiver_giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_receiver_address := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_receiver_address'
);
SET @sql := IF(@sales_sii_receiver_address = 0, 'ALTER TABLE sales ADD COLUMN sii_receiver_address VARCHAR(255) NULL AFTER sii_receiver_activity_code;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_receiver_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_receiver_commune'
);
SET @sql := IF(@sales_sii_receiver_commune = 0, 'ALTER TABLE sales ADD COLUMN sii_receiver_commune VARCHAR(100) NULL AFTER sii_receiver_address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_receiver_city := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_receiver_city'
);
SET @sql := IF(@sales_sii_receiver_city = 0, 'ALTER TABLE sales ADD COLUMN sii_receiver_city VARCHAR(100) NULL AFTER sii_receiver_commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_tax_rate := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_tax_rate'
);
SET @sql := IF(@sales_sii_tax_rate = 0, 'ALTER TABLE sales ADD COLUMN sii_tax_rate DECIMAL(5,2) NOT NULL DEFAULT 19 AFTER sii_receiver_city;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sales_sii_exempt_amount := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sii_exempt_amount'
);
SET @sql := IF(@sales_sii_exempt_amount = 0, 'ALTER TABLE sales ADD COLUMN sii_exempt_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER sii_tax_rate;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
CREATE TABLE IF NOT EXISTS chile_communes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commune VARCHAR(150) NOT NULL,
    city VARCHAR(150) NOT NULL,
    region VARCHAR(150) NOT NULL,
    UNIQUE KEY uniq_chile_communes_commune (commune),
    INDEX idx_chile_communes_city (city),
    INDEX idx_chile_communes_region (region)
);

CREATE TABLE IF NOT EXISTS regions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    UNIQUE KEY uniq_regions_name (name)
);

CREATE TABLE IF NOT EXISTS cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    UNIQUE KEY uniq_cities_region_name (region_id, name),
    INDEX idx_cities_name (name),
    FOREIGN KEY (region_id) REFERENCES regions(id)
);

CREATE TABLE IF NOT EXISTS communes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    UNIQUE KEY uniq_communes_city_name (city_id, name),
    INDEX idx_communes_name (name),
    FOREIGN KEY (city_id) REFERENCES cities(id)
);

CREATE TABLE IF NOT EXISTS sii_activity_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL,
    name VARCHAR(255) NOT NULL,
    UNIQUE KEY uniq_sii_activity_code (code),
    INDEX idx_sii_activity_name (name)
);

SET @clients_activity_code := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'activity_code'
);
SET @sql := IF(@clients_activity_code = 0, 'ALTER TABLE clients ADD COLUMN activity_code VARCHAR(50) NULL AFTER giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_commune := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'commune'
);
SET @sql := IF(@clients_commune = 0, 'ALTER TABLE clients ADD COLUMN commune VARCHAR(120) NULL AFTER activity_code;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_city := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'city'
);
SET @sql := IF(@clients_city = 0, 'ALTER TABLE clients ADD COLUMN city VARCHAR(120) NULL AFTER commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT IGNORE INTO chile_communes (commune, city, region) VALUES
('Arica', 'Arica', 'Arica y Parinacota'),
('Camarones', 'Camarones', 'Arica y Parinacota'),
('Putre', 'Putre', 'Arica y Parinacota'),
('General Lagos', 'General Lagos', 'Arica y Parinacota'),
('Iquique', 'Iquique', 'Tarapacá'),
('Alto Hospicio', 'Alto Hospicio', 'Tarapacá'),
('Pozo Almonte', 'Pozo Almonte', 'Tarapacá'),
('Camiña', 'Camiña', 'Tarapacá'),
('Colchane', 'Colchane', 'Tarapacá'),
('Huara', 'Huara', 'Tarapacá'),
('Pica', 'Pica', 'Tarapacá'),
('Antofagasta', 'Antofagasta', 'Antofagasta'),
('Mejillones', 'Mejillones', 'Antofagasta'),
('Sierra Gorda', 'Sierra Gorda', 'Antofagasta'),
('Taltal', 'Taltal', 'Antofagasta'),
('Calama', 'Calama', 'Antofagasta'),
('Ollagüe', 'Ollagüe', 'Antofagasta'),
('San Pedro de Atacama', 'San Pedro de Atacama', 'Antofagasta'),
('Tocopilla', 'Tocopilla', 'Antofagasta'),
('María Elena', 'María Elena', 'Antofagasta'),
('Copiapó', 'Copiapó', 'Atacama'),
('Caldera', 'Caldera', 'Atacama'),
('Tierra Amarilla', 'Tierra Amarilla', 'Atacama'),
('Chañaral', 'Chañaral', 'Atacama'),
('Diego de Almagro', 'Diego de Almagro', 'Atacama'),
('Vallenar', 'Vallenar', 'Atacama'),
('Alto del Carmen', 'Alto del Carmen', 'Atacama'),
('Freirina', 'Freirina', 'Atacama'),
('Huasco', 'Huasco', 'Atacama'),
('La Serena', 'La Serena', 'Coquimbo'),
('Coquimbo', 'Coquimbo', 'Coquimbo'),
('Andacollo', 'Andacollo', 'Coquimbo'),
('La Higuera', 'La Higuera', 'Coquimbo'),
('Paiguano', 'Paiguano', 'Coquimbo'),
('Vicuña', 'Vicuña', 'Coquimbo'),
('Illapel', 'Illapel', 'Coquimbo'),
('Canela', 'Canela', 'Coquimbo'),
('Los Vilos', 'Los Vilos', 'Coquimbo'),
('Salamanca', 'Salamanca', 'Coquimbo'),
('Ovalle', 'Ovalle', 'Coquimbo'),
('Combarbalá', 'Combarbalá', 'Coquimbo'),
('Monte Patria', 'Monte Patria', 'Coquimbo'),
('Punitaqui', 'Punitaqui', 'Coquimbo'),
('Río Hurtado', 'Río Hurtado', 'Coquimbo'),
('Valparaíso', 'Valparaíso', 'Valparaíso'),
('Casablanca', 'Casablanca', 'Valparaíso'),
('Concón', 'Concón', 'Valparaíso'),
('Juan Fernández', 'Juan Fernández', 'Valparaíso'),
('Puchuncaví', 'Puchuncaví', 'Valparaíso'),
('Quintero', 'Quintero', 'Valparaíso'),
('Viña del Mar', 'Viña del Mar', 'Valparaíso'),
('Isla de Pascua', 'Isla de Pascua', 'Valparaíso'),
('Los Andes', 'Los Andes', 'Valparaíso'),
('Calle Larga', 'Calle Larga', 'Valparaíso'),
('Rinconada', 'Rinconada', 'Valparaíso'),
('San Esteban', 'San Esteban', 'Valparaíso'),
('La Ligua', 'La Ligua', 'Valparaíso'),
('Cabildo', 'Cabildo', 'Valparaíso'),
('Papudo', 'Papudo', 'Valparaíso'),
('Petorca', 'Petorca', 'Valparaíso'),
('Zapallar', 'Zapallar', 'Valparaíso'),
('Quillota', 'Quillota', 'Valparaíso'),
('La Calera', 'La Calera', 'Valparaíso'),
('Hijuelas', 'Hijuelas', 'Valparaíso'),
('La Cruz', 'La Cruz', 'Valparaíso'),
('Nogales', 'Nogales', 'Valparaíso'),
('San Antonio', 'San Antonio', 'Valparaíso'),
('Algarrobo', 'Algarrobo', 'Valparaíso'),
('Cartagena', 'Cartagena', 'Valparaíso'),
('El Quisco', 'El Quisco', 'Valparaíso'),
('El Tabo', 'El Tabo', 'Valparaíso'),
('Santo Domingo', 'Santo Domingo', 'Valparaíso'),
('San Felipe', 'San Felipe', 'Valparaíso'),
('Catemu', 'Catemu', 'Valparaíso'),
('Llaillay', 'Llaillay', 'Valparaíso'),
('Panquehue', 'Panquehue', 'Valparaíso'),
('Putaendo', 'Putaendo', 'Valparaíso'),
('Santa María', 'Santa María', 'Valparaíso'),
('Limache', 'Limache', 'Valparaíso'),
('Olmué', 'Olmué', 'Valparaíso'),
('Quilpué', 'Quilpué', 'Valparaíso'),
('Villa Alemana', 'Villa Alemana', 'Valparaíso'),
('Santiago', 'Santiago', 'Metropolitana de Santiago'),
('Cerrillos', 'Cerrillos', 'Metropolitana de Santiago'),
('Cerro Navia', 'Cerro Navia', 'Metropolitana de Santiago'),
('Conchalí', 'Conchalí', 'Metropolitana de Santiago'),
('El Bosque', 'El Bosque', 'Metropolitana de Santiago'),
('Estación Central', 'Estación Central', 'Metropolitana de Santiago'),
('Huechuraba', 'Huechuraba', 'Metropolitana de Santiago'),
('Independencia', 'Independencia', 'Metropolitana de Santiago'),
('La Cisterna', 'La Cisterna', 'Metropolitana de Santiago'),
('La Florida', 'La Florida', 'Metropolitana de Santiago'),
('La Granja', 'La Granja', 'Metropolitana de Santiago'),
('La Pintana', 'La Pintana', 'Metropolitana de Santiago'),
('La Reina', 'La Reina', 'Metropolitana de Santiago'),
('Las Condes', 'Las Condes', 'Metropolitana de Santiago'),
('Lo Barnechea', 'Lo Barnechea', 'Metropolitana de Santiago'),
('Lo Espejo', 'Lo Espejo', 'Metropolitana de Santiago'),
('Lo Prado', 'Lo Prado', 'Metropolitana de Santiago'),
('Macul', 'Macul', 'Metropolitana de Santiago'),
('Maipú', 'Maipú', 'Metropolitana de Santiago'),
('Ñuñoa', 'Ñuñoa', 'Metropolitana de Santiago'),
('Pedro Aguirre Cerda', 'Pedro Aguirre Cerda', 'Metropolitana de Santiago'),
('Peñalolén', 'Peñalolén', 'Metropolitana de Santiago'),
('Providencia', 'Providencia', 'Metropolitana de Santiago'),
('Pudahuel', 'Pudahuel', 'Metropolitana de Santiago'),
('Quilicura', 'Quilicura', 'Metropolitana de Santiago'),
('Quinta Normal', 'Quinta Normal', 'Metropolitana de Santiago'),
('Recoleta', 'Recoleta', 'Metropolitana de Santiago'),
('Renca', 'Renca', 'Metropolitana de Santiago'),
('San Joaquín', 'San Joaquín', 'Metropolitana de Santiago'),
('San Miguel', 'San Miguel', 'Metropolitana de Santiago'),
('San Ramón', 'San Ramón', 'Metropolitana de Santiago'),
('Vitacura', 'Vitacura', 'Metropolitana de Santiago'),
('Puente Alto', 'Puente Alto', 'Metropolitana de Santiago'),
('Pirque', 'Pirque', 'Metropolitana de Santiago'),
('San José de Maipo', 'San José de Maipo', 'Metropolitana de Santiago'),
('Colina', 'Colina', 'Metropolitana de Santiago'),
('Lampa', 'Lampa', 'Metropolitana de Santiago'),
('Tiltil', 'Tiltil', 'Metropolitana de Santiago'),
('San Bernardo', 'San Bernardo', 'Metropolitana de Santiago'),
('Buin', 'Buin', 'Metropolitana de Santiago'),
('Calera de Tango', 'Calera de Tango', 'Metropolitana de Santiago'),
('Paine', 'Paine', 'Metropolitana de Santiago'),
('Melipilla', 'Melipilla', 'Metropolitana de Santiago'),
('Alhué', 'Alhué', 'Metropolitana de Santiago'),
('Curacaví', 'Curacaví', 'Metropolitana de Santiago'),
('María Pinto', 'María Pinto', 'Metropolitana de Santiago'),
('San Pedro', 'San Pedro', 'Metropolitana de Santiago'),
('Talagante', 'Talagante', 'Metropolitana de Santiago'),
('El Monte', 'El Monte', 'Metropolitana de Santiago'),
('Isla de Maipo', 'Isla de Maipo', 'Metropolitana de Santiago'),
('Padre Hurtado', 'Padre Hurtado', 'Metropolitana de Santiago'),
('Peñaflor', 'Peñaflor', 'Metropolitana de Santiago'),
('Rancagua', 'Rancagua', 'Libertador General Bernardo O\'Higgins'),
('Codegua', 'Codegua', 'Libertador General Bernardo O\'Higgins'),
('Coinco', 'Coinco', 'Libertador General Bernardo O\'Higgins'),
('Coltauco', 'Coltauco', 'Libertador General Bernardo O\'Higgins'),
('Doñihue', 'Doñihue', 'Libertador General Bernardo O\'Higgins'),
('Graneros', 'Graneros', 'Libertador General Bernardo O\'Higgins'),
('Las Cabras', 'Las Cabras', 'Libertador General Bernardo O\'Higgins'),
('Machalí', 'Machalí', 'Libertador General Bernardo O\'Higgins'),
('Malloa', 'Malloa', 'Libertador General Bernardo O\'Higgins'),
('Mostazal', 'Mostazal', 'Libertador General Bernardo O\'Higgins'),
('Olivar', 'Olivar', 'Libertador General Bernardo O\'Higgins'),
('Peumo', 'Peumo', 'Libertador General Bernardo O\'Higgins'),
('Pichidegua', 'Pichidegua', 'Libertador General Bernardo O\'Higgins'),
('Quinta de Tilcoco', 'Quinta de Tilcoco', 'Libertador General Bernardo O\'Higgins'),
('Rengo', 'Rengo', 'Libertador General Bernardo O\'Higgins'),
('Requínoa', 'Requínoa', 'Libertador General Bernardo O\'Higgins'),
('San Vicente', 'San Vicente', 'Libertador General Bernardo O\'Higgins'),
('San Fernando', 'San Fernando', 'Libertador General Bernardo O\'Higgins'),
('Chimbarongo', 'Chimbarongo', 'Libertador General Bernardo O\'Higgins'),
('Lolol', 'Lolol', 'Libertador General Bernardo O\'Higgins'),
('Nancagua', 'Nancagua', 'Libertador General Bernardo O\'Higgins'),
('Palmilla', 'Palmilla', 'Libertador General Bernardo O\'Higgins'),
('Peralillo', 'Peralillo', 'Libertador General Bernardo O\'Higgins'),
('Placilla', 'Placilla', 'Libertador General Bernardo O\'Higgins'),
('Pumanque', 'Pumanque', 'Libertador General Bernardo O\'Higgins'),
('Santa Cruz', 'Santa Cruz', 'Libertador General Bernardo O\'Higgins'),
('Pichilemu', 'Pichilemu', 'Libertador General Bernardo O\'Higgins'),
('La Estrella', 'La Estrella', 'Libertador General Bernardo O\'Higgins'),
('Litueche', 'Litueche', 'Libertador General Bernardo O\'Higgins'),
('Marchihue', 'Marchihue', 'Libertador General Bernardo O\'Higgins'),
('Navidad', 'Navidad', 'Libertador General Bernardo O\'Higgins'),
('Paredones', 'Paredones', 'Libertador General Bernardo O\'Higgins'),
('Talca', 'Talca', 'Maule'),
('Constitución', 'Constitución', 'Maule'),
('Curepto', 'Curepto', 'Maule'),
('Empedrado', 'Empedrado', 'Maule'),
('Maule', 'Maule', 'Maule'),
('Pelarco', 'Pelarco', 'Maule'),
('Pencahue', 'Pencahue', 'Maule'),
('Río Claro', 'Río Claro', 'Maule'),
('San Clemente', 'San Clemente', 'Maule'),
('San Rafael', 'San Rafael', 'Maule'),
('Cauquenes', 'Cauquenes', 'Maule'),
('Chanco', 'Chanco', 'Maule'),
('Pelluhue', 'Pelluhue', 'Maule'),
('Curicó', 'Curicó', 'Maule'),
('Hualañé', 'Hualañé', 'Maule'),
('Licantén', 'Licantén', 'Maule'),
('Molina', 'Molina', 'Maule'),
('Rauco', 'Rauco', 'Maule'),
('Romeral', 'Romeral', 'Maule'),
('Sagrada Familia', 'Sagrada Familia', 'Maule'),
('Teno', 'Teno', 'Maule'),
('Vichuquén', 'Vichuquén', 'Maule'),
('Linares', 'Linares', 'Maule'),
('Colbún', 'Colbún', 'Maule'),
('Longaví', 'Longaví', 'Maule'),
('Parral', 'Parral', 'Maule'),
('Retiro', 'Retiro', 'Maule'),
('San Javier', 'San Javier', 'Maule'),
('Villa Alegre', 'Villa Alegre', 'Maule'),
('Yerbas Buenas', 'Yerbas Buenas', 'Maule'),
('Chillán', 'Chillán', 'Ñuble'),
('Bulnes', 'Bulnes', 'Ñuble'),
('Chillán Viejo', 'Chillán Viejo', 'Ñuble'),
('Cobquecura', 'Cobquecura', 'Ñuble'),
('Coelemu', 'Coelemu', 'Ñuble'),
('Coihueco', 'Coihueco', 'Ñuble'),
('El Carmen', 'El Carmen', 'Ñuble'),
('Ninhue', 'Ninhue', 'Ñuble'),
('Ñiquén', 'Ñiquén', 'Ñuble'),
('Pemuco', 'Pemuco', 'Ñuble'),
('Pinto', 'Pinto', 'Ñuble'),
('Portezuelo', 'Portezuelo', 'Ñuble'),
('Quillón', 'Quillón', 'Ñuble'),
('Quirihue', 'Quirihue', 'Ñuble'),
('Ránquil', 'Ránquil', 'Ñuble'),
('San Carlos', 'San Carlos', 'Ñuble'),
('San Fabián', 'San Fabián', 'Ñuble'),
('San Ignacio', 'San Ignacio', 'Ñuble'),
('San Nicolás', 'San Nicolás', 'Ñuble'),
('Trehuaco', 'Trehuaco', 'Ñuble'),
('Yungay', 'Yungay', 'Ñuble'),
('Concepción', 'Concepción', 'Biobío'),
('Coronel', 'Coronel', 'Biobío'),
('Chiguayante', 'Chiguayante', 'Biobío'),
('Florida', 'Florida', 'Biobío'),
('Hualqui', 'Hualqui', 'Biobío'),
('Lota', 'Lota', 'Biobío'),
('Penco', 'Penco', 'Biobío'),
('San Pedro de la Paz', 'San Pedro de la Paz', 'Biobío'),
('Santa Juana', 'Santa Juana', 'Biobío'),
('Talcahuano', 'Talcahuano', 'Biobío'),
('Tomé', 'Tomé', 'Biobío'),
('Lebu', 'Lebu', 'Biobío'),
('Arauco', 'Arauco', 'Biobío'),
('Cañete', 'Cañete', 'Biobío'),
('Contulmo', 'Contulmo', 'Biobío'),
('Curanilahue', 'Curanilahue', 'Biobío'),
('Los Álamos', 'Los Álamos', 'Biobío'),
('Tirúa', 'Tirúa', 'Biobío'),
('Los Ángeles', 'Los Ángeles', 'Biobío'),
('Antuco', 'Antuco', 'Biobío'),
('Cabrero', 'Cabrero', 'Biobío'),
('Laja', 'Laja', 'Biobío'),
('Mulchén', 'Mulchén', 'Biobío'),
('Nacimiento', 'Nacimiento', 'Biobío'),
('Negrete', 'Negrete', 'Biobío'),
('Quilaco', 'Quilaco', 'Biobío'),
('Quilleco', 'Quilleco', 'Biobío'),
('San Rosendo', 'San Rosendo', 'Biobío'),
('Santa Bárbara', 'Santa Bárbara', 'Biobío'),
('Tucapel', 'Tucapel', 'Biobío'),
('Yumbel', 'Yumbel', 'Biobío'),
('Alto Biobío', 'Alto Biobío', 'Biobío'),
('Temuco', 'Temuco', 'Araucanía'),
('Carahue', 'Carahue', 'Araucanía'),
('Cunco', 'Cunco', 'Araucanía'),
('Curarrehue', 'Curarrehue', 'Araucanía'),
('Freire', 'Freire', 'Araucanía'),
('Galvarino', 'Galvarino', 'Araucanía'),
('Gorbea', 'Gorbea', 'Araucanía'),
('Lautaro', 'Lautaro', 'Araucanía'),
('Loncoche', 'Loncoche', 'Araucanía'),
('Melipeuco', 'Melipeuco', 'Araucanía'),
('Nueva Imperial', 'Nueva Imperial', 'Araucanía'),
('Padre Las Casas', 'Padre Las Casas', 'Araucanía'),
('Perquenco', 'Perquenco', 'Araucanía'),
('Pitrufquén', 'Pitrufquén', 'Araucanía'),
('Pucón', 'Pucón', 'Araucanía'),
('Saavedra', 'Saavedra', 'Araucanía'),
('Teodoro Schmidt', 'Teodoro Schmidt', 'Araucanía'),
('Toltén', 'Toltén', 'Araucanía'),
('Vilcún', 'Vilcún', 'Araucanía'),
('Villarrica', 'Villarrica', 'Araucanía'),
('Cholchol', 'Cholchol', 'Araucanía'),
('Angol', 'Angol', 'Araucanía'),
('Collipulli', 'Collipulli', 'Araucanía'),
('Curacautín', 'Curacautín', 'Araucanía'),
('Ercilla', 'Ercilla', 'Araucanía'),
('Lonquimay', 'Lonquimay', 'Araucanía'),
('Los Sauces', 'Los Sauces', 'Araucanía'),
('Lumaco', 'Lumaco', 'Araucanía'),
('Purén', 'Purén', 'Araucanía'),
('Renaico', 'Renaico', 'Araucanía'),
('Traiguén', 'Traiguén', 'Araucanía'),
('Victoria', 'Victoria', 'Araucanía'),
('Valdivia', 'Valdivia', 'Los Ríos'),
('Corral', 'Corral', 'Los Ríos'),
('Lanco', 'Lanco', 'Los Ríos'),
('Los Lagos', 'Los Lagos', 'Los Ríos'),
('Máfil', 'Máfil', 'Los Ríos'),
('Mariquina', 'Mariquina', 'Los Ríos'),
('Paillaco', 'Paillaco', 'Los Ríos'),
('Panguipulli', 'Panguipulli', 'Los Ríos'),
('La Unión', 'La Unión', 'Los Ríos'),
('Futrono', 'Futrono', 'Los Ríos'),
('Lago Ranco', 'Lago Ranco', 'Los Ríos'),
('Río Bueno', 'Río Bueno', 'Los Ríos'),
('Puerto Montt', 'Puerto Montt', 'Los Lagos'),
('Calbuco', 'Calbuco', 'Los Lagos'),
('Cochamó', 'Cochamó', 'Los Lagos'),
('Fresia', 'Fresia', 'Los Lagos'),
('Frutillar', 'Frutillar', 'Los Lagos'),
('Los Muermos', 'Los Muermos', 'Los Lagos'),
('Llanquihue', 'Llanquihue', 'Los Lagos'),
('Maullín', 'Maullín', 'Los Lagos'),
('Puerto Varas', 'Puerto Varas', 'Los Lagos'),
('Castro', 'Castro', 'Los Lagos'),
('Ancud', 'Ancud', 'Los Lagos'),
('Chonchi', 'Chonchi', 'Los Lagos'),
('Curaco de Vélez', 'Curaco de Vélez', 'Los Lagos'),
('Dalcahue', 'Dalcahue', 'Los Lagos'),
('Puqueldón', 'Puqueldón', 'Los Lagos'),
('Queilén', 'Queilén', 'Los Lagos'),
('Quellón', 'Quellón', 'Los Lagos'),
('Quemchi', 'Quemchi', 'Los Lagos'),
('Quinchao', 'Quinchao', 'Los Lagos'),
('Osorno', 'Osorno', 'Los Lagos'),
('Puerto Octay', 'Puerto Octay', 'Los Lagos'),
('Purranque', 'Purranque', 'Los Lagos'),
('Puyehue', 'Puyehue', 'Los Lagos'),
('Río Negro', 'Río Negro', 'Los Lagos'),
('San Juan de la Costa', 'San Juan de la Costa', 'Los Lagos'),
('San Pablo', 'San Pablo', 'Los Lagos'),
('Chaitén', 'Chaitén', 'Los Lagos'),
('Futaleufú', 'Futaleufú', 'Los Lagos'),
('Hualaihué', 'Hualaihué', 'Los Lagos'),
('Palena', 'Palena', 'Los Lagos'),
('Coyhaique', 'Coyhaique', 'Aysén del General Carlos Ibáñez del Campo'),
('Lago Verde', 'Lago Verde', 'Aysén del General Carlos Ibáñez del Campo'),
('Aysén', 'Aysén', 'Aysén del General Carlos Ibáñez del Campo'),
('Cisnes', 'Cisnes', 'Aysén del General Carlos Ibáñez del Campo'),
('Guaitecas', 'Guaitecas', 'Aysén del General Carlos Ibáñez del Campo'),
('Cochrane', 'Cochrane', 'Aysén del General Carlos Ibáñez del Campo'),
('O\'Higgins', 'O\'Higgins', 'Aysén del General Carlos Ibáñez del Campo'),
('Tortel', 'Tortel', 'Aysén del General Carlos Ibáñez del Campo'),
('Chile Chico', 'Chile Chico', 'Aysén del General Carlos Ibáñez del Campo'),
('Río Ibáñez', 'Río Ibáñez', 'Aysén del General Carlos Ibáñez del Campo'),
('Punta Arenas', 'Punta Arenas', 'Magallanes y de la Antártica Chilena'),
('Laguna Blanca', 'Laguna Blanca', 'Magallanes y de la Antártica Chilena'),
('Río Verde', 'Río Verde', 'Magallanes y de la Antártica Chilena'),
('San Gregorio', 'San Gregorio', 'Magallanes y de la Antártica Chilena'),
('Cabo de Hornos', 'Cabo de Hornos', 'Magallanes y de la Antártica Chilena'),
('Antártica', 'Antártica', 'Magallanes y de la Antártica Chilena'),
('Porvenir', 'Porvenir', 'Magallanes y de la Antártica Chilena'),
('Primavera', 'Primavera', 'Magallanes y de la Antártica Chilena'),
('Timaukel', 'Timaukel', 'Magallanes y de la Antártica Chilena'),
('Natales', 'Natales', 'Magallanes y de la Antártica Chilena'),
('Torres del Paine', 'Torres del Paine', 'Magallanes y de la Antártica Chilena');

INSERT IGNORE INTO regions (name)
SELECT DISTINCT region
FROM chile_communes
ORDER BY region;

INSERT IGNORE INTO cities (name, region_id)
SELECT DISTINCT chile_communes.city, regions.id
FROM chile_communes
JOIN regions ON regions.name = chile_communes.region
ORDER BY chile_communes.city;

INSERT IGNORE INTO communes (name, city_id)
SELECT chile_communes.commune, cities.id
FROM chile_communes
JOIN regions ON regions.name = chile_communes.region
JOIN cities ON cities.name = chile_communes.city AND cities.region_id = regions.id
ORDER BY chile_communes.commune;

INSERT IGNORE INTO sii_activity_codes (code, name) VALUES
('011111', 'Cultivo de trigo'),
('011112', 'Cultivo de maíz'),
('011113', 'Cultivo de arroz'),
('011119', 'Otros cultivos de cereales'),
('011120', 'Cultivo de legumbres y semillas oleaginosas'),
('011131', 'Cultivo de papa'),
('011139', 'Cultivo de raíces y tubérculos n.c.p.'),
('011200', 'Cultivo de hortalizas'),
('011300', 'Cultivo de frutas'),
('011400', 'Cultivo de uvas'),
('011500', 'Cultivo de frutos oleaginosos'),
('011600', 'Cultivo de plantas para preparar bebidas'),
('011900', 'Otros cultivos permanentes'),
('012110', 'Cultivo de flores'),
('012120', 'Cultivo de plantas vivas y productos de vivero'),
('012130', 'Producción de semillas'),
('012140', 'Producción de plántulas'),
('012200', 'Producción de hongos y trufas'),
('012900', 'Otros cultivos no permanentes n.c.p.'),
('013000', 'Cultivo de plantas de vivero'),
('014111', 'Cría de ganado bovino'),
('014112', 'Engorda de ganado bovino'),
('014120', 'Cría de ganado equino'),
('014130', 'Cría de ovinos y caprinos'),
('014141', 'Cría de porcinos'),
('014200', 'Cría de aves de corral'),
('014300', 'Cría de otros animales'),
('014400', 'Producción de leche'),
('014500', 'Producción de huevos'),
('014600', 'Apicultura'),
('014900', 'Otras actividades de apoyo a la ganadería'),
('016100', 'Actividades de apoyo a la agricultura'),
('016200', 'Actividades de apoyo a la ganadería'),
('016300', 'Actividades posteriores a la cosecha'),
('016400', 'Tratamiento de semillas para propagación'),
('021000', 'Silvicultura'),
('022000', 'Extracción de madera'),
('023000', 'Recolección de productos forestales'),
('024000', 'Servicios de apoyo a la silvicultura'),
('031100', 'Pesca marítima'),
('031200', 'Pesca de agua dulce'),
('032100', 'Acuicultura marítima'),
('032200', 'Acuicultura de agua dulce'),
('051000', 'Extracción de carbón de piedra'),
('052000', 'Extracción de lignito'),
('061000', 'Extracción de petróleo crudo'),
('062000', 'Extracción de gas natural'),
('071000', 'Extracción de minerales de hierro'),
('072100', 'Extracción de minerales de uranio y torio'),
('072910', 'Extracción de cobre'),
('072920', 'Extracción de otros minerales metálicos no ferrosos'),
('081000', 'Extracción de piedra, arena y arcilla'),
('089100', 'Extracción de minerales para productos químicos'),
('089200', 'Extracción de turba'),
('089300', 'Extracción de sal'),
('089900', 'Otras actividades de explotación de minas y canteras'),
('101010', 'Elaboración y conservación de carne'),
('101020', 'Elaboración y conservación de productos de la pesca'),
('101030', 'Elaboración y conservación de frutas, legumbres y hortalizas'),
('101040', 'Elaboración de aceites y grasas'),
('101050', 'Elaboración de productos lácteos'),
('101060', 'Elaboración de productos de molinería'),
('101070', 'Elaboración de almidones y productos derivados'),
('101080', 'Elaboración de productos de panadería'),
('101090', 'Elaboración de otros productos alimenticios'),
('102000', 'Elaboración de alimentos preparados para animales'),
('110100', 'Destilación de bebidas alcohólicas'),
('110200', 'Elaboración de vinos'),
('110300', 'Elaboración de cervezas'),
('110400', 'Elaboración de bebidas no alcohólicas'),
('120000', 'Elaboración de productos de tabaco'),
('131100', 'Preparación e hilatura de fibras textiles'),
('131200', 'Tejeduría de productos textiles'),
('131300', 'Acabado de productos textiles'),
('139100', 'Fabricación de tejidos de punto'),
('139200', 'Fabricación de otros productos textiles'),
('141000', 'Confección de prendas de vestir'),
('142000', 'Fabricación de artículos de piel'),
('143000', 'Fabricación de prendas de vestir de punto'),
('151100', 'Curtido y adobo de cuero'),
('151200', 'Fabricación de calzado'),
('152000', 'Fabricación de artículos de cuero'),
('161000', 'Aserrado y cepillado de madera'),
('162100', 'Fabricación de productos de madera'),
('170110', 'Fabricación de celulosa'),
('170120', 'Fabricación de papel y cartón'),
('170200', 'Fabricación de envases de papel'),
('181100', 'Impresión'),
('181200', 'Servicios relacionados con la impresión'),
('182000', 'Reproducción de soportes grabados'),
('191000', 'Fabricación de productos de hornos de coque'),
('192000', 'Fabricación de productos de la refinación del petróleo'),
('201100', 'Fabricación de sustancias químicas básicas'),
('201200', 'Fabricación de fertilizantes'),
('201300', 'Fabricación de plásticos y caucho sintético'),
('202100', 'Fabricación de plaguicidas'),
('202200', 'Fabricación de pinturas'),
('202300', 'Fabricación de jabones y detergentes'),
('202900', 'Fabricación de otros productos químicos'),
('210000', 'Fabricación de productos farmacéuticos'),
('221100', 'Fabricación de neumáticos'),
('221900', 'Fabricación de otros productos de caucho'),
('222000', 'Fabricación de productos de plástico'),
('231000', 'Fabricación de vidrio'),
('239100', 'Fabricación de productos de cerámica'),
('239200', 'Fabricación de productos refractarios'),
('239300', 'Fabricación de productos de arcilla'),
('239400', 'Fabricación de cemento'),
('239500', 'Fabricación de yeso'),
('239600', 'Fabricación de productos de hormigón'),
('239900', 'Fabricación de otros productos minerales no metálicos'),
('241000', 'Industrias básicas de hierro y acero'),
('242000', 'Industrias básicas de metales preciosos y no ferrosos'),
('243100', 'Fundición de hierro y acero'),
('243200', 'Fundición de metales no ferrosos'),
('251100', 'Fabricación de productos metálicos'),
('251200', 'Fabricación de tanques y depósitos'),
('252000', 'Fabricación de armas y municiones'),
('259100', 'Forja y estampado de metales'),
('259200', 'Tratamiento y revestimiento de metales'),
('259300', 'Fabricación de artículos de ferretería'),
('259900', 'Fabricación de otros productos elaborados de metal'),
('261000', 'Fabricación de componentes electrónicos'),
('262000', 'Fabricación de computadores y periféricos'),
('263000', 'Fabricación de equipos de comunicación'),
('264000', 'Fabricación de aparatos electrónicos de consumo'),
('265100', 'Fabricación de instrumentos de medición'),
('265200', 'Fabricación de relojes'),
('266000', 'Fabricación de equipos de radiación'),
('267000', 'Fabricación de instrumentos ópticos'),
('268000', 'Fabricación de soportes magnéticos y ópticos'),
('271000', 'Fabricación de motores eléctricos'),
('272000', 'Fabricación de pilas y baterías'),
('273100', 'Fabricación de cables de fibra óptica'),
('273200', 'Fabricación de cables'),
('273300', 'Fabricación de dispositivos de cableado'),
('274000', 'Fabricación de equipos de iluminación'),
('275000', 'Fabricación de aparatos de uso doméstico'),
('279000', 'Fabricación de otros equipos eléctricos'),
('281100', 'Fabricación de motores y turbinas'),
('281200', 'Fabricación de bombas y compresores'),
('281300', 'Fabricación de grifos y válvulas'),
('281400', 'Fabricación de cojinetes y engranajes'),
('281500', 'Fabricación de hornos y quemadores'),
('281600', 'Fabricación de equipos de elevación'),
('281700', 'Fabricación de maquinaria para la construcción'),
('281800', 'Fabricación de maquinaria para la agricultura'),
('281900', 'Fabricación de maquinaria de uso general'),
('282100', 'Fabricación de maquinaria para metalurgia'),
('282200', 'Fabricación de maquinaria para minería'),
('282300', 'Fabricación de maquinaria para industria alimentaria'),
('282400', 'Fabricación de maquinaria para la industria textil'),
('282500', 'Fabricación de maquinaria para la industria del papel'),
('282900', 'Fabricación de otras maquinarias especiales'),
('291000', 'Fabricación de vehículos automotores'),
('292000', 'Fabricación de carrocerías y remolques'),
('293000', 'Fabricación de partes y piezas de vehículos'),
('301100', 'Construcción de buques'),
('301200', 'Construcción de embarcaciones de recreo'),
('302000', 'Fabricación de locomotoras y material rodante'),
('303000', 'Fabricación de aeronaves'),
('304000', 'Fabricación de vehículos militares'),
('309100', 'Fabricación de motocicletas'),
('309200', 'Fabricación de bicicletas'),
('309900', 'Fabricación de otros equipos de transporte'),
('310000', 'Fabricación de muebles'),
('321100', 'Fabricación de joyas'),
('321200', 'Fabricación de bisutería'),
('322000', 'Fabricación de instrumentos musicales'),
('323000', 'Fabricación de artículos deportivos'),
('324000', 'Fabricación de juegos y juguetes'),
('325000', 'Fabricación de instrumentos médicos'),
('329000', 'Otras industrias manufactureras n.c.p.'),
('331100', 'Reparación de productos metálicos'),
('331200', 'Reparación de maquinaria'),
('331300', 'Reparación de equipos electrónicos'),
('331400', 'Reparación de equipos eléctricos'),
('331500', 'Reparación de equipos de transporte'),
('331900', 'Reparación de otros equipos'),
('332000', 'Instalación de maquinaria y equipos'),
('351000', 'Generación de energía eléctrica'),
('352000', 'Transmisión de energía eléctrica'),
('353000', 'Distribución de energía eléctrica'),
('360000', 'Captación y distribución de agua'),
('370000', 'Evacuación de aguas residuales'),
('381100', 'Recolección de desechos'),
('381200', 'Tratamiento y eliminación de desechos'),
('382100', 'Recuperación de materiales'),
('390000', 'Actividades de descontaminación'),
('410010', 'Construcción de edificios'),
('410020', 'Construcción de viviendas'),
('421000', 'Construcción de carreteras'),
('422000', 'Construcción de proyectos de servicio público'),
('429000', 'Construcción de otras obras de ingeniería'),
('431100', 'Demolición'),
('431200', 'Preparación del terreno'),
('432100', 'Instalación eléctrica'),
('432200', 'Instalaciones de gas y calefacción'),
('432900', 'Otras instalaciones para obras de construcción'),
('433000', 'Terminación y acabado de edificios'),
('439000', 'Otras actividades especializadas de construcción'),
('451001', 'Venta de vehículos automotores'),
('452001', 'Mantenimiento y reparación de vehículos automotores'),
('453000', 'Venta de partes y piezas para vehículos'),
('454000', 'Venta de motocicletas y accesorios'),
('461000', 'Venta al por mayor a cambio de una retribución'),
('462000', 'Venta al por mayor de materias primas agropecuarias'),
('463000', 'Venta al por mayor de alimentos'),
('464100', 'Venta al por mayor de textiles'),
('464200', 'Venta al por mayor de prendas de vestir'),
('464300', 'Venta al por mayor de calzado'),
('464901', 'Venta al por mayor de maquinaria'),
('464902', 'Venta al por mayor de artículos eléctricos'),
('464903', 'Venta al por mayor de productos farmacéuticos'),
('464904', 'Venta al por mayor de combustibles'),
('464905', 'Venta al por mayor de materiales de construcción'),
('464906', 'Venta al por mayor de metales'),
('464907', 'Venta al por mayor de madera'),
('464908', 'Venta al por mayor de equipos médicos'),
('464909', 'Venta al por mayor de otros productos'),
('465100', 'Venta al por mayor de computadores'),
('465200', 'Venta al por mayor de equipos de telecomunicaciones'),
('465300', 'Venta al por mayor de equipos electrónicos'),
('466100', 'Venta al por mayor de maquinaria agrícola'),
('466200', 'Venta al por mayor de maquinaria para la minería'),
('466300', 'Venta al por mayor de maquinaria para la construcción'),
('466901', 'Venta al por mayor de otros tipos de maquinaria'),
('466902', 'Venta al por mayor de vehículos'),
('466903', 'Venta al por mayor de equipos de transporte'),
('466904', 'Venta al por mayor de productos químicos'),
('466909', 'Venta al por mayor de otros productos'),
('469000', 'Venta al por mayor no especializada'),
('471100', 'Venta al por menor en comercios no especializados'),
('471900', 'Venta al por menor en otros comercios no especializados'),
('472100', 'Venta al por menor de alimentos'),
('472200', 'Venta al por menor de bebidas'),
('472300', 'Venta al por menor de tabaco'),
('472400', 'Venta al por menor de combustibles'),
('472500', 'Venta al por menor de textiles'),
('472600', 'Venta al por menor de prendas de vestir'),
('472700', 'Venta al por menor de calzado'),
('472900', 'Venta al por menor de otros productos'),
('473000', 'Venta al por menor de combustibles para vehículos'),
('474100', 'Venta al por menor de computadores'),
('474200', 'Venta al por menor de equipos de telecomunicaciones'),
('474300', 'Venta al por menor de equipos electrónicos'),
('475100', 'Venta al por menor de textiles'),
('475200', 'Venta al por menor de ferretería'),
('475900', 'Venta al por menor de otros productos en comercios especializados'),
('476100', 'Venta al por menor de libros'),
('476200', 'Venta al por menor de periódicos'),
('476300', 'Venta al por menor de productos culturales'),
('476400', 'Venta al por menor de aparatos electrónicos'),
('476500', 'Venta al por menor de música'),
('477100', 'Venta al por menor de prendas de vestir'),
('477200', 'Venta al por menor de calzado'),
('477300', 'Venta al por menor de productos farmacéuticos'),
('477400', 'Venta al por menor de productos de cosmética'),
('477500', 'Venta al por menor de productos médicos'),
('477600', 'Venta al por menor de artículos deportivos'),
('477700', 'Venta al por menor de artículos recreativos'),
('477800', 'Venta al por menor de otros productos en comercios especializados'),
('478100', 'Venta al por menor en puestos de alimentos'),
('478200', 'Venta al por menor en puestos de textiles'),
('478900', 'Venta al por menor en otros puestos'),
('479100', 'Venta al por menor por internet'),
('479900', 'Venta al por menor por otros medios'),
('491100', 'Transporte interurbano de pasajeros por ferrocarril'),
('491200', 'Transporte de carga por ferrocarril'),
('492100', 'Transporte urbano de pasajeros'),
('492200', 'Transporte interurbano de pasajeros'),
('492300', 'Transporte de carga por carretera'),
('493000', 'Transporte por oleoducto'),
('501100', 'Transporte marítimo de pasajeros'),
('501200', 'Transporte marítimo de carga'),
('502100', 'Transporte fluvial de pasajeros'),
('502200', 'Transporte fluvial de carga'),
('511000', 'Transporte aéreo de pasajeros'),
('512000', 'Transporte aéreo de carga'),
('521000', 'Depósito y almacenamiento'),
('522100', 'Servicios auxiliares de transporte'),
('522200', 'Servicios auxiliares de transporte marítimo'),
('522300', 'Servicios auxiliares de transporte aéreo'),
('522400', 'Manipulación de carga'),
('522900', 'Otras actividades de apoyo al transporte'),
('531000', 'Actividades de correo'),
('532000', 'Actividades de mensajería'),
('551000', 'Alojamiento'),
('552000', 'Actividades de campamento'),
('559000', 'Otros tipos de alojamiento'),
('561000', 'Restaurantes y servicios de comida'),
('562000', 'Servicios de catering'),
('563000', 'Actividades de bebidas'),
('581100', 'Edición de libros'),
('581200', 'Edición de periódicos'),
('581300', 'Edición de revistas'),
('581900', 'Otras actividades de edición'),
('582000', 'Edición de programas informáticos'),
('591100', 'Producción de películas'),
('591200', 'Postproducción'),
('591300', 'Distribución de películas'),
('591400', 'Proyección de películas'),
('592000', 'Actividades de grabación sonora'),
('601000', 'Transmisión radial'),
('602000', 'Programación televisiva'),
('611000', 'Telecomunicaciones'),
('612000', 'Telecomunicaciones inalámbricas'),
('613000', 'Telecomunicaciones por satélite'),
('619000', 'Otras telecomunicaciones'),
('620100', 'Actividades de programación informática'),
('620200', 'Consultoría de informática'),
('620300', 'Gestión de instalaciones informáticas'),
('620900', 'Otras actividades de tecnología de la información'),
('631100', 'Procesamiento de datos'),
('631200', 'Portales web'),
('639100', 'Actividades de agencias de noticias'),
('639900', 'Otras actividades de servicios de información'),
('641100', 'Banca central'),
('641900', 'Otros servicios de intermediación monetaria'),
('642000', 'Actividades de sociedades de cartera'),
('643000', 'Fondos y sociedades de inversión'),
('649100', 'Arrendamiento financiero'),
('649200', 'Otros servicios financieros'),
('649300', 'Financiamiento de consumo'),
('649900', 'Otros servicios financieros n.c.p.'),
('651100', 'Seguros'),
('651200', 'Reaseguros'),
('652000', 'Planes de pensiones'),
('653000', 'Servicios auxiliares de seguros'),
('661100', 'Administración de mercados financieros'),
('661200', 'Corretaje de valores'),
('661900', 'Otras actividades auxiliares de servicios financieros'),
('662100', 'Evaluación de riesgos'),
('662200', 'Corretaje de seguros'),
('662900', 'Otras actividades auxiliares de seguros'),
('663000', 'Administración de fondos'),
('681000', 'Actividades inmobiliarias'),
('682000', 'Alquiler de bienes inmuebles'),
('691000', 'Actividades jurídicas'),
('692000', 'Actividades de contabilidad'),
('701000', 'Actividades de oficinas principales'),
('702000', 'Actividades de consultoría de gestión'),
('711000', 'Actividades de arquitectura'),
('712000', 'Ensayos y análisis técnicos'),
('721000', 'Investigación y desarrollo experimental en ciencias naturales'),
('722000', 'Investigación y desarrollo experimental en ciencias sociales'),
('731000', 'Publicidad'),
('732000', 'Investigación de mercados'),
('741000', 'Actividades de diseño especializado'),
('742000', 'Actividades de fotografía'),
('749000', 'Otras actividades profesionales'),
('750000', 'Actividades veterinarias'),
('771000', 'Alquiler de vehículos'),
('772100', 'Alquiler de artículos recreativos'),
('772200', 'Alquiler de videos'),
('772900', 'Alquiler de otros bienes'),
('773000', 'Alquiler de maquinaria y equipo'),
('774000', 'Arrendamiento de propiedad intelectual'),
('781000', 'Actividades de empleo'),
('782000', 'Actividades de agencias de empleo'),
('783000', 'Otras actividades de suministro de recursos humanos'),
('791100', 'Actividades de agencias de viaje'),
('791200', 'Actividades de operadores turísticos'),
('799000', 'Otros servicios de reservas'),
('801000', 'Actividades de seguridad privada'),
('802000', 'Actividades de servicios de seguridad'),
('803000', 'Investigaciones'),
('811000', 'Actividades de limpieza'),
('812100', 'Limpieza general de edificios'),
('812900', 'Otras actividades de limpieza'),
('813000', 'Servicios de paisajismo'),
('821100', 'Actividades administrativas'),
('821900', 'Servicios de apoyo a oficinas'),
('822000', 'Actividades de call center'),
('823000', 'Organización de convenciones'),
('829100', 'Agencias de cobranza'),
('829900', 'Otros servicios de apoyo a empresas'),
('841100', 'Administración pública'),
('842100', 'Relaciones exteriores'),
('842200', 'Defensa'),
('842300', 'Orden público'),
('843000', 'Seguridad social'),
('851000', 'Enseñanza preescolar'),
('852100', 'Enseñanza primaria'),
('852200', 'Enseñanza secundaria'),
('853100', 'Educación superior'),
('853200', 'Educación técnica'),
('854100', 'Educación deportiva'),
('854200', 'Educación cultural'),
('854900', 'Otras actividades de enseñanza'),
('855000', 'Servicios de apoyo a la enseñanza'),
('861000', 'Actividades de hospitales'),
('862000', 'Actividades médicas y odontológicas'),
('869000', 'Otras actividades de atención de la salud'),
('871000', 'Instituciones de atención de salud'),
('872000', 'Atención a personas con discapacidad'),
('873000', 'Atención a personas mayores'),
('879000', 'Otras actividades de asistencia social'),
('881000', 'Actividades de servicios sociales'),
('889000', 'Otros servicios sociales'),
('900000', 'Actividades creativas y artísticas'),
('910100', 'Actividades de bibliotecas'),
('910200', 'Actividades de museos'),
('910300', 'Actividades de jardines botánicos'),
('920000', 'Actividades de juegos de azar'),
('931100', 'Actividades deportivas'),
('931200', 'Actividades de clubes deportivos'),
('931900', 'Otras actividades deportivas'),
('932100', 'Parques de atracciones'),
('932900', 'Otras actividades de entretenimiento'),
('941100', 'Actividades de organizaciones empresariales'),
('941200', 'Actividades de organizaciones profesionales'),
('942000', 'Actividades de sindicatos'),
('949100', 'Actividades de organizaciones religiosas'),
('949200', 'Actividades de organizaciones políticas'),
('949900', 'Otras actividades asociativas'),
('951100', 'Reparación de computadores'),
('951200', 'Reparación de equipos de comunicación'),
('952100', 'Reparación de aparatos electrónicos'),
('952200', 'Reparación de electrodomésticos'),
('952300', 'Reparación de calzado y artículos de cuero'),
('952400', 'Reparación de muebles'),
('952900', 'Reparación de otros bienes'),
('960100', 'Lavado y limpieza de vehículos'),
('960200', 'Peluquería y otros tratamientos de belleza'),
('960300', 'Pompas fúnebres'),
('960900', 'Otras actividades de servicios personales');
START TRANSACTION;

SET @clients_billing_email := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'billing_email'
);
SET @sql := IF(@clients_billing_email = 0, 'ALTER TABLE clients ADD COLUMN billing_email VARCHAR(150) NULL AFTER email;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_phone := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'phone'
);
SET @sql := IF(@clients_phone = 0, 'ALTER TABLE clients ADD COLUMN phone VARCHAR(50) NULL AFTER billing_email;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_address := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'address'
);
SET @sql := IF(@clients_address = 0, 'ALTER TABLE clients ADD COLUMN address VARCHAR(255) NULL AFTER phone;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_giro := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'giro'
);
SET @sql := IF(@clients_giro = 0, 'ALTER TABLE clients ADD COLUMN giro VARCHAR(150) NULL AFTER address;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_activity_code := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'activity_code'
);
SET @sql := IF(@clients_activity_code = 0, 'ALTER TABLE clients ADD COLUMN activity_code VARCHAR(50) NULL AFTER giro;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_commune := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'commune'
);
SET @sql := IF(@clients_commune = 0, 'ALTER TABLE clients ADD COLUMN commune VARCHAR(120) NULL AFTER activity_code;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_city := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'city'
);
SET @sql := IF(@clients_city = 0, 'ALTER TABLE clients ADD COLUMN city VARCHAR(120) NULL AFTER commune;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_contact := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'contact'
);
SET @sql := IF(@clients_contact = 0, 'ALTER TABLE clients ADD COLUMN contact VARCHAR(150) NULL AFTER city;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_mandante_name := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'mandante_name'
);
SET @sql := IF(@clients_mandante_name = 0, 'ALTER TABLE clients ADD COLUMN mandante_name VARCHAR(150) NULL AFTER contact;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_mandante_rut := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'mandante_rut'
);
SET @sql := IF(@clients_mandante_rut = 0, 'ALTER TABLE clients ADD COLUMN mandante_rut VARCHAR(50) NULL AFTER mandante_name;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_mandante_phone := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'mandante_phone'
);
SET @sql := IF(@clients_mandante_phone = 0, 'ALTER TABLE clients ADD COLUMN mandante_phone VARCHAR(50) NULL AFTER mandante_rut;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_mandante_email := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'mandante_email'
);
SET @sql := IF(@clients_mandante_email = 0, 'ALTER TABLE clients ADD COLUMN mandante_email VARCHAR(150) NULL AFTER mandante_phone;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_avatar_path := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'avatar_path'
);
SET @sql := IF(@clients_avatar_path = 0, 'ALTER TABLE clients ADD COLUMN avatar_path VARCHAR(255) NULL AFTER mandante_email;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_portal_token := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'portal_token'
);
SET @sql := IF(@clients_portal_token = 0, 'ALTER TABLE clients ADD COLUMN portal_token VARCHAR(64) NULL AFTER avatar_path;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_portal_password := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'portal_password'
);
SET @sql := IF(@clients_portal_password = 0, 'ALTER TABLE clients ADD COLUMN portal_password VARCHAR(255) NULL AFTER portal_token;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_notes := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'notes'
);
SET @sql := IF(@clients_notes = 0, 'ALTER TABLE clients ADD COLUMN notes TEXT NULL AFTER portal_password;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @clients_status := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'status'
);
SET @sql := IF(@clients_status = 0, 'ALTER TABLE clients ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT ''activo'' AFTER notes;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_clients_portal_token := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'clients'
      AND INDEX_NAME = 'idx_clients_portal_token'
);
SET @sql := IF(@idx_clients_portal_token = 0, 'CREATE UNIQUE INDEX idx_clients_portal_token ON clients(portal_token);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_clients_status := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'clients'
      AND INDEX_NAME = 'idx_clients_status'
);
SET @sql := IF(@idx_clients_status = 0, 'CREATE INDEX idx_clients_status ON clients(status);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
START TRANSACTION;

CREATE TABLE IF NOT EXISTS production_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    production_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'completada',
    total_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS production_inputs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    production_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (production_id) REFERENCES production_orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS production_outputs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    production_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (production_id) REFERENCES production_orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS production_expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    production_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (production_id) REFERENCES production_orders(id)
);

SET @idx_prod_orders := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_orders' AND INDEX_NAME = 'idx_production_orders_company'
);
SET @sql := IF(@idx_prod_orders = 0, 'CREATE INDEX idx_production_orders_company ON production_orders(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_prod_inputs := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_inputs' AND INDEX_NAME = 'idx_production_inputs_production'
);
SET @sql := IF(@idx_prod_inputs = 0, 'CREATE INDEX idx_production_inputs_production ON production_inputs(production_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_prod_outputs := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_outputs' AND INDEX_NAME = 'idx_production_outputs_production'
);
SET @sql := IF(@idx_prod_outputs = 0, 'CREATE INDEX idx_production_outputs_production ON production_outputs(production_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_prod_expenses := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_expenses' AND INDEX_NAME = 'idx_production_expenses_production'
);
SET @sql := IF(@idx_prod_expenses = 0, 'CREATE INDEX idx_production_expenses_production ON production_expenses(production_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

COMMIT;
START TRANSACTION;

CREATE TABLE IF NOT EXISTS purchase_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    supplier_id INT NOT NULL,
    reference VARCHAR(100) NULL,
    order_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

CREATE TABLE IF NOT EXISTS purchase_order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

SET @idx_po_company := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_orders' AND INDEX_NAME = 'idx_purchase_orders_company'
);
SET @sql := IF(@idx_po_company = 0, 'CREATE INDEX idx_purchase_orders_company ON purchase_orders(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_po_items := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_order_items' AND INDEX_NAME = 'idx_purchase_order_items_order'
);
SET @sql := IF(@idx_po_items = 0, 'CREATE INDEX idx_purchase_order_items_order ON purchase_order_items(purchase_order_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

COMMIT;
START TRANSACTION;

CREATE TABLE IF NOT EXISTS sales_order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sales_order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (sales_order_id) REFERENCES sales_orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

SET @idx_sales_order_items := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_order_items' AND INDEX_NAME = 'idx_sales_order_items_order'
);
SET @sql := IF(@idx_sales_order_items = 0, 'CREATE INDEX idx_sales_order_items_order ON sales_order_items(sales_order_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

COMMIT;
START TRANSACTION;

-- Familias y subfamilias
CREATE TABLE IF NOT EXISTS product_families (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE IF NOT EXISTS product_subfamilies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    family_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (family_id) REFERENCES product_families(id)
);

-- Productos con vínculos a familias/subfamilias
SET @family_id_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'family_id'
);
SET @sql := IF(@family_id_exists = 0, 'ALTER TABLE products ADD COLUMN family_id INT NULL AFTER supplier_id;', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @subfamily_id_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'subfamily_id'
);
SET @sql := IF(@subfamily_id_exists = 0, 'ALTER TABLE products ADD COLUMN subfamily_id INT NULL AFTER family_id;', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- POS: sesiones, pagos y referencia en ventas
CREATE TABLE IF NOT EXISTS pos_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    user_id INT NOT NULL,
    opening_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    closing_amount DECIMAL(12,2) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'abierto',
    opened_at DATETIME NOT NULL,
    closed_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

SET @pos_col := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'pos_session_id'
);
SET @sql := IF(@pos_col = 0, 'ALTER TABLE sales ADD COLUMN pos_session_id INT NULL AFTER client_id, ADD CONSTRAINT fk_sales_pos_session FOREIGN KEY (pos_session_id) REFERENCES pos_sessions(id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS sale_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    method VARCHAR(50) NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id)
);

-- Ítems de venta para productos o servicios
SET @service_col := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sale_items' AND COLUMN_NAME = 'service_id'
);
SET @sql := IF(@service_col = 0, 'ALTER TABLE sale_items ADD COLUMN service_id INT NULL AFTER product_id, MODIFY product_id INT NULL, ADD CONSTRAINT fk_sale_items_service FOREIGN KEY (service_id) REFERENCES services(id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Índices
SET @idx_pf := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_families' AND INDEX_NAME = 'idx_product_families_company'
);
SET @sql := IF(@idx_pf = 0, 'CREATE INDEX idx_product_families_company ON product_families(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_psf := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_subfamilies' AND INDEX_NAME = 'idx_product_subfamilies_company'
);
SET @sql := IF(@idx_psf = 0, 'CREATE INDEX idx_product_subfamilies_company ON product_subfamilies(company_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_pos := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pos_sessions' AND INDEX_NAME = 'idx_pos_sessions_company_user'
);
SET @sql := IF(@idx_pos = 0, 'CREATE INDEX idx_pos_sessions_company_user ON pos_sessions(company_id, user_id);', 'SELECT 1;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

COMMIT;
