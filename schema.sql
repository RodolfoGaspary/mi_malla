-- Tablas que guardar_proforma.php necesita.
-- clientes y users ya existen; se listan las columnas que el código espera.

-- users: login.php lee id, username, nombre, role, password_hash
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS nombre VARCHAR(120) NULL AFTER username,
    ADD COLUMN IF NOT EXISTS role   VARCHAR(20)  NOT NULL DEFAULT 'user' AFTER nombre;

CREATE TABLE IF NOT EXISTS proformas (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    user_id    INT NOT NULL,
    total_m2   DECIMAL(12,4) NOT NULL DEFAULT 0,
    creado_en  DATETIME NOT NULL,
    CONSTRAINT fk_proformas_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    CONSTRAINT fk_proformas_user    FOREIGN KEY (user_id)    REFERENCES users(id),
    INDEX idx_proformas_cliente (cliente_id),
    INDEX idx_proformas_creado  (creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS proforma_areas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    proforma_id INT NOT NULL,
    tipo        VARCHAR(120) NOT NULL,
    ubicacion   VARCHAR(120) NOT NULL,
    ancho       DECIMAL(10,2) NOT NULL,
    altura      DECIMAL(10,2) NOT NULL,
    metros2     DECIMAL(12,4) NOT NULL,
    CONSTRAINT fk_areas_proforma FOREIGN KEY (proforma_id)
        REFERENCES proformas(id) ON DELETE CASCADE,
    INDEX idx_areas_proforma (proforma_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Búsquedas de clientes por nombre/apellido y por teléfono.
CREATE INDEX idx_clientes_nombres   ON clientes (nombres);
CREATE INDEX idx_clientes_apellidos ON clientes (apellidos);
CREATE INDEX idx_clientes_telf      ON clientes (telf);
