-- Resetea la base de datos y crea un usuario super root con una empresa base.
-- Advertencia: elimina TODOS los registros de la base de datos actual.
-- Credenciales super root:
-- Usuario: superroot@spinsquad.cl
-- Contraseña: SpinSquad2025!

USE gocreative_ges;

SET FOREIGN_KEY_CHECKS = 0;

DROP PROCEDURE IF EXISTS truncate_all_tables;
DELIMITER //
CREATE PROCEDURE truncate_all_tables()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE tbl VARCHAR(255);
    DECLARE cur CURSOR FOR
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = DATABASE();
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO tbl;
        IF done THEN
            LEAVE read_loop;
        END IF;
        SET @stmt = CONCAT('TRUNCATE TABLE `', tbl, '`');
        PREPARE trunc_stmt FROM @stmt;
        EXECUTE trunc_stmt;
        DEALLOCATE PREPARE trunc_stmt;
    END LOOP;
    CLOSE cur;
END//
DELIMITER ;

CALL truncate_all_tables();
DROP PROCEDURE truncate_all_tables;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO roles (name, created_at, updated_at)
VALUES ('admin', NOW(), NOW());

INSERT INTO companies (name, rut, email, phone, address, giro, activity_code, commune, city, created_at, updated_at)
VALUES ('Centro de entrenamiento Spin Squad', '', 'contacto@spinsquad.cl', '', '', '', '', '', '', NOW(), NOW());

INSERT INTO users (company_id, name, email, password, role_id, created_at, updated_at)
VALUES (1, 'Super Root', 'superroot@spinsquad.cl', '$2y$12$tIKTo718MqFkKmugLnFsZubWmY/83AvlBWGyX2VABozLEyUtKSjZG', 1, NOW(), NOW());

INSERT INTO user_companies (user_id, company_id, created_at)
VALUES (1, 1, NOW());
