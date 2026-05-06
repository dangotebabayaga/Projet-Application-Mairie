-- Création du rôle elu
DO $$
BEGIN
   IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'app_elu') THEN
      CREATE ROLE app_elu LOGIN PASSWORD 'secure_elu_pass';
   END IF;
END
$$;

GRANT USAGE ON SCHEMA public TO app_elu;

-- Lecture
GRANT SELECT ON
    ville,
    utilisateurs,
    sondages,
    choix,
    liste_choix_sondage,
    types_signalement,
    signalements,
    evenement,
    type_ev,
    reseau_sociale,
    commentaire
TO app_elu;

-- Écriture
GRANT INSERT, UPDATE ON ville TO app_elu;
GRANT INSERT, UPDATE, DELETE ON evenement TO app_elu;
GRANT INSERT, UPDATE, DELETE ON sondages TO app_elu;
GRANT INSERT, UPDATE ON liste_choix_sondage TO app_elu;
GRANT INSERT, UPDATE ON choix TO app_elu;

-- Séquences
GRANT USAGE, SELECT ON SEQUENCE
    evenement_id_seq,
    sondages_id_seq,
    choix_id_seq
TO app_elu;