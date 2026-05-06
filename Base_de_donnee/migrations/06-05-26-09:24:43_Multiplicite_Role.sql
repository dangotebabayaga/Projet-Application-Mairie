-- Supprime l'ancien champ role unique
ALTER TABLE utilisateurs DROP COLUMN role;

-- Nouvelle table des rôles disponibles
CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(20) NOT NULL UNIQUE
);

-- Insertion des rôles de base
INSERT INTO roles (nom) VALUES ('citoyen'), ('elu'), ('administrateur');

-- Table de liaison utilisateur <-> rôles (plusieurs rôles possibles)
CREATE TABLE utilisateur_roles (
    utilisateur_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,
    PRIMARY KEY (utilisateur_id, role_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Permissions pour app_administrateur
GRANT ALL ON roles TO app_administrateur;
GRANT ALL ON utilisateur_roles TO app_admin;
GRANT ALL ON roles_id_seq TO app_admin;

-- Permissions pour app_citoyen
GRANT SELECT ON roles TO app_citoyen;
GRANT SELECT ON utilisateur_roles TO app_citoyen;