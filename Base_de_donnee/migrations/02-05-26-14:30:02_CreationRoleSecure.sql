-- =========================
-- 1. SUPPRESSION DES ANCIENNES TABLES DE RÔLES
-- =========================
ALTER TABLE sondages DROP CONSTRAINT IF EXISTS sondages_administrateur_id_fkey;
ALTER TABLE votes_sondage DROP CONSTRAINT IF EXISTS votes_sondage_citoyen_id_fkey;
ALTER TABLE signalements DROP CONSTRAINT IF EXISTS signalements_citoyen_id_fkey;
ALTER TABLE evenement DROP CONSTRAINT IF EXISTS evenement_administrateur_id_fkey;
ALTER TABLE commentaire DROP CONSTRAINT IF EXISTS commentaire_auteur_fkey;

DROP TABLE IF EXISTS administrateurs CASCADE;
DROP TABLE IF EXISTS citoyens CASCADE;

-- =========================
-- 2. ADAPTATION DE LA STRUCTURE
-- =========================
-- Les colonnes qui référençaient administrateurs/citoyens pointent maintenant vers Utilisateur(id)
ALTER TABLE sondages
  ADD CONSTRAINT sondages_administrateur_id_fkey
  FOREIGN KEY (administrateur_id) REFERENCES Utilisateur(id);

ALTER TABLE votes_sondage
  RENAME COLUMN citoyen_id TO utilisateur_id;
ALTER TABLE votes_sondage
  ADD CONSTRAINT votes_sondage_utilisateur_id_fkey
  FOREIGN KEY (utilisateur_id) REFERENCES Utilisateur(id);

ALTER TABLE signalements
  RENAME COLUMN citoyen_id TO utilisateur_id;
ALTER TABLE signalements
  ADD CONSTRAINT signalements_utilisateur_id_fkey
  FOREIGN KEY (utilisateur_id) REFERENCES Utilisateur(id);

ALTER TABLE evenement
  ADD CONSTRAINT evenement_administrateur_id_fkey
  FOREIGN KEY (administrateur_id) REFERENCES Utilisateur(id);

ALTER TABLE commentaire
  ADD CONSTRAINT commentaire_auteur_fkey
  FOREIGN KEY (auteur) REFERENCES Utilisateur(id);

-- =========================
-- 3. AJOUT D'UN CHAMP ROLE DANS Utilisateur
-- =========================
-- Pour distinguer administrateur / citoyen sans table séparée
ALTER TABLE Utilisateur
  ADD COLUMN IF NOT EXISTS role varchar(20) NOT NULL DEFAULT 'citoyen'
  CHECK (role IN ('citoyen', 'administrateur'));

-- =========================
-- 4. CREATION DES ROLES DE CONNEXION
-- =========================
DO $$
BEGIN
   IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'app_admin') THEN
      CREATE ROLE app_admin LOGIN PASSWORD 'secure_admin_pass';
   END IF;
   IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'app_citoyen') THEN
      CREATE ROLE app_citoyen LOGIN PASSWORD 'secure_citoyen_pass';
   END IF;
   IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'app_readonly') THEN
      CREATE ROLE app_readonly LOGIN PASSWORD 'secure_read_pass';
   END IF;
END
$$;

-- =========================
-- 5. SECURITE : on enlève PUBLIC
-- =========================
REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON ALL TABLES IN SCHEMA public FROM PUBLIC;
REVOKE ALL ON ALL SEQUENCES IN SCHEMA public FROM PUBLIC;

-- =========================
-- 6. PERMISSIONS app_admin
-- Accès total sur toutes les tables
-- =========================
GRANT USAGE ON SCHEMA public TO app_admin;

GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO app_admin;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO app_admin;

-- =========================
-- 7. PERMISSIONS app_citoyen
-- Lecture : ville, Utilisateur (son propre profil via appli), 
--           sondages, choix, liste_choix_sondage, types_signalement, evenement, type_ev, reseau_sociale, commentaire
-- Écriture : votes_sondage, liste_choix_vote, signalements, commentaire
-- =========================
GRANT USAGE ON SCHEMA public TO app_citoyen;

-- Lecture seule
GRANT SELECT ON
  ville,
  sondages,
  choix,
  liste_choix_sondage,
  types_signalement,
  evenement,
  type_ev,
  reseau_sociale,
  commentaire
TO app_citoyen;

-- Lecture + écriture sur son propre compte (UPDATE limité côté appli)
GRANT SELECT, UPDATE ON Utilisateur TO app_citoyen;

-- Création de votes et signalements
GRANT SELECT, INSERT ON votes_sondage TO app_citoyen;
GRANT SELECT, INSERT ON liste_choix_vote TO app_citoyen;
GRANT SELECT, INSERT, UPDATE ON signalements TO app_citoyen;
GRANT SELECT, INSERT, UPDATE, DELETE ON commentaire TO app_citoyen;

-- Séquences nécessaires pour les INSERT
GRANT USAGE, SELECT ON SEQUENCE
  votes_sondage_id_seq,
  signalements_id_seq,
  commentaire_id_seq
TO app_citoyen;

-- =========================
-- 8. PERMISSIONS app_readonly
-- Accès en lecture sur les données publiques uniquement
-- =========================
GRANT USAGE ON SCHEMA public TO app_readonly;

GRANT SELECT ON
  ville,
  sondages,
  choix,
  liste_choix_sondage,
  evenement,
  type_ev,
  reseau_sociale,
  types_signalement
TO app_readonly;

-- =========================
-- 9. POUR LES FUTURES TABLES
-- =========================
ALTER DEFAULT PRIVILEGES IN SCHEMA public
  GRANT ALL ON TABLES TO app_admin;
ALTER DEFAULT PRIVILEGES IN SCHEMA public
  GRANT ALL ON SEQUENCES TO app_admin;

ALTER DEFAULT PRIVILEGES IN SCHEMA public
  GRANT SELECT ON TABLES TO app_readonly;