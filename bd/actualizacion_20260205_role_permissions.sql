USE gocreative_ges;

CREATE TABLE IF NOT EXISTS role_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    permission_key VARCHAR(120) NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY idx_role_permission_unique (role_id, permission_key),
    INDEX idx_role_permissions_role (role_id),
    CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
