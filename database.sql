-- #####################################################################
--  BASE DE DATOS DEL TABLÓN
--  MySQL ejecuta este archivo solo la PRIMERA vez que crea la BD.
--  Si lo cambio, tengo que borrar la BD y volver a crearla:
--      docker compose down -v
--      docker compose up -d
--
--  Relaciones:
--    users 1 ──< N posts       (un usuario escribe muchos anuncios)
--    users 1 ──< N comments    (un usuario escribe muchos comentarios)
--    posts 1 ──< N comments    (un anuncio tiene muchos comentarios)
-- #####################################################################

-- ---------------------------------------------------------------------
--  Usuarios
--  password es VARCHAR(255) porque guarda el HASH de password_hash()
--  (unos 60 caracteres), nunca la contraseña en texto plano.
--  email es UNIQUE: no puede haber dos usuarios con el mismo email.
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
--  Anuncios. user_id = quién lo escribió (clave foránea a users)
-- ---------------------------------------------------------------------
CREATE TABLE posts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    title      VARCHAR(150) NOT NULL,
    body       TEXT         NOT NULL,
    category   VARCHAR(50)  NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ---------------------------------------------------------------------
--  Comentarios. Cada uno pertenece a un anuncio (post_id)
--  y a un usuario (user_id)
--  ON DELETE CASCADE: si borro un anuncio, MySQL borra también sus
--  comentarios. Sin esto, MySQL no deja borrar un anuncio que tenga
--  comentarios (se quedarían apuntando a un anuncio que ya no existe).
-- ---------------------------------------------------------------------
CREATE TABLE comments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    post_id    INT  NOT NULL,
    user_id    INT  NOT NULL,
    body       TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ---------------------------------------------------------------------
--  Datos de prueba. Los dos usuarios tienen la contraseña: 1234
--  (lo que se guarda es el hash que devuelve password_hash('1234', ...))
-- ---------------------------------------------------------------------
INSERT INTO users (username, email, password) VALUES
('ana',  'ana@tablon.test',  '$2y$10$qb/3kQyZY7P9fyMxSJgN3u8dvHa.E8cg2XkzD./8B/ssi/2u5GrHW'),
('luis', 'luis@tablon.test', '$2y$10$qb/3kQyZY7P9fyMxSJgN3u8dvHa.E8cg2XkzD./8B/ssi/2u5GrHW');

INSERT INTO posts (user_id, title, body, category) VALUES
(1, 'Vendo libro de Entorno Servidor', 'Está como nuevo, 10 euros.', 'Venta'),
(2, 'Busco grupo para el proyecto', 'Me falta grupo para el proyecto de DAW.', 'Clase'),
(2, 'Cargador olvidado en el aula 21', 'Es un cargador USB-C negro.', 'Objetos perdidos');

INSERT INTO comments (post_id, user_id, body) VALUES
(1, 2, '¿Lo tienes todavía?'),
(1, 1, 'Sí, todavía lo tengo.');
