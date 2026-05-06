CREATE TABLE "villes" (
  "id" integer PRIMARY KEY,
  "nom_ville" varchar,
  "slogan" varchar,
  "logo_url" varchar,
  "couleurs_theme" varchar,
  "date_creation" timestamp
);

CREATE TABLE "quartiers" (
  "id" integer PRIMARY KEY,
  "nom" varchar,
  "ville_id" integer
);

CREATE TABLE "categories_citoyens" (
  "id" integer PRIMARY KEY,
  "libelle" varchar,
  "ville_id" integer
);

CREATE TABLE "utilisateurs" (
  "id" integer PRIMARY KEY,
  "nom" varchar,
  "prenom" varchar,
  "email" varchar UNIQUE,
  "mot_de_passe_hash" varchar,
  "role" varchar,
  "adresse" varchar,
  "ville_id" integer,
  "quartier_id" integer,
  "categorie_id" integer,
  "telephone" varchar,
  "date_naissance" date,
  "consentement_rgpd" boolean,
  "date_consentement_rgpd" timestamp,
  "derniere_connexion" timestamp,
  "compte_actif" boolean,
  "date_creation" timestamp
);

CREATE TABLE "fichiers" (
  "id" integer PRIMARY KEY,
  "nom_original" varchar,
  "nom_stockage" varchar UNIQUE,
  "chemin" varchar,
  "type_mime" varchar,
  "taille_octets" bigint,
  "hash_sha256" varchar,
  "uploade_par" integer,
  "date_upload" timestamp
);

CREATE TABLE "signalements" (
  "id" integer PRIMARY KEY,
  "titre" varchar,
  "description" text,
  "fichier_id" integer,
  "latitude" decimal,
  "longitude" decimal,
  "adresse" varchar,
  "type_signalement" varchar,
  "etat" varchar,
  "citoyen_id" integer,
  "quartier_id" integer,
  "date_creation" timestamp,
  "date_modification" timestamp
);

CREATE TABLE "historique_signalements" (
  "id" integer PRIMARY KEY,
  "signalement_id" integer,
  "ancien_etat" varchar,
  "nouvel_etat" varchar,
  "commentaire" text,
  "modifie_par" integer,
  "date_modification" timestamp
);

CREATE TABLE "commentaires_signalement" (
  "id" integer PRIMARY KEY,
  "signalement_id" integer,
  "auteur_id" integer,
  "contenu" text,
  "date_creation" timestamp,
  "modifie" boolean,
  "date_modification" timestamp
);

CREATE TABLE "sondages" (
  "id" integer PRIMARY KEY,
  "titre" varchar,
  "description" text,
  "date_debut" timestamp,
  "date_fin" timestamp,
  "quartier_id" integer,
  "categorie_id" integer,
  "ville_id" integer,
  "cree_par" integer
);

CREATE TABLE "questions_sondage" (
  "id" integer PRIMARY KEY,
  "sondage_id" integer,
  "question" text,
  "ordre" integer
);

CREATE TABLE "reponses_sondage" (
  "id" integer PRIMARY KEY,
  "question_id" integer,
  "choix_reponse" text
);

CREATE TABLE "votes_sondage" (
  "id" integer PRIMARY KEY,
  "sondage_id" integer,
  "citoyen_id" integer,
  "question_id" integer,
  "reponse_id" integer,
  "date_vote" timestamp
);

CREATE TABLE "thematiques_evenement" (
  "id" integer PRIMARY KEY,
  "nom" varchar,
  "ville_id" integer
);

CREATE TABLE "evenements" (
  "id" integer PRIMARY KEY,
  "titre" varchar,
  "description" text,
  "date_debut" timestamp,
  "date_fin" timestamp,
  "lieu" varchar,
  "thematique_id" integer,
  "ville_id" integer,
  "cree_par" integer
);

CREATE TABLE "evenements_utilisateur" (
  "id" integer PRIMARY KEY,
  "utilisateur_id" integer,
  "evenement_id" integer,
  "date_ajout" timestamp
);

CREATE TABLE "actualites" (
  "id" integer PRIMARY KEY,
  "titre" varchar,
  "contenu" text,
  "type_source" varchar,
  "url_source" varchar,
  "auteur_id" integer,
  "date_publication" timestamp,
  "ville_id" integer
);

CREATE TABLE "reseaux_sociaux" (
  "id" integer PRIMARY KEY,
  "ville_id" integer,
  "plateforme" varchar,
  "url" varchar
);

CREATE TABLE "logs_actions" (
  "id" integer PRIMARY KEY,
  "utilisateur_id" integer,
  "action" varchar,
  "details" text,
  "adresse_ip" varchar,
  "date_action" timestamp
);

CREATE TABLE "notifications" (
  "id" integer PRIMARY KEY,
  "destinataire_id" integer,
  "titre" varchar,
  "message" text,
  "type" varchar,
  "lue" boolean,
  "date_creation" timestamp,
  "date_lecture" timestamp
);

ALTER TABLE "quartiers" ADD FOREIGN KEY ("ville_id") REFERENCES "villes" ("id");

ALTER TABLE "categories_citoyens" ADD FOREIGN KEY ("ville_id") REFERENCES "villes" ("id");

ALTER TABLE "utilisateurs" ADD FOREIGN KEY ("ville_id") REFERENCES "villes" ("id");

ALTER TABLE "utilisateurs" ADD FOREIGN KEY ("quartier_id") REFERENCES "quartiers" ("id");

ALTER TABLE "utilisateurs" ADD FOREIGN KEY ("categorie_id") REFERENCES "categories_citoyens" ("id");

ALTER TABLE "fichiers" ADD FOREIGN KEY ("uploade_par") REFERENCES "utilisateurs" ("id");

ALTER TABLE "signalements" ADD FOREIGN KEY ("citoyen_id") REFERENCES "utilisateurs" ("id");

ALTER TABLE "signalements" ADD FOREIGN KEY ("quartier_id") REFERENCES "quartiers" ("id");

ALTER TABLE "signalements" ADD FOREIGN KEY ("fichier_id") REFERENCES "fichiers" ("id");

ALTER TABLE "historique_signalements" ADD FOREIGN KEY ("signalement_id") REFERENCES "signalements" ("id");

ALTER TABLE "historique_signalements" ADD FOREIGN KEY ("modifie_par") REFERENCES "utilisateurs" ("id");

ALTER TABLE "commentaires_signalement" ADD FOREIGN KEY ("signalement_id") REFERENCES "signalements" ("id");

ALTER TABLE "commentaires_signalement" ADD FOREIGN KEY ("auteur_id") REFERENCES "utilisateurs" ("id");

ALTER TABLE "sondages" ADD FOREIGN KEY ("ville_id") REFERENCES "villes" ("id");

ALTER TABLE "sondages" ADD FOREIGN KEY ("quartier_id") REFERENCES "quartiers" ("id");

ALTER TABLE "sondages" ADD FOREIGN KEY ("categorie_id") REFERENCES "categories_citoyens" ("id");

ALTER TABLE "sondages" ADD FOREIGN KEY ("cree_par") REFERENCES "utilisateurs" ("id");

ALTER TABLE "questions_sondage" ADD FOREIGN KEY ("sondage_id") REFERENCES "sondages" ("id");

ALTER TABLE "reponses_sondage" ADD FOREIGN KEY ("question_id") REFERENCES "questions_sondage" ("id");

ALTER TABLE "votes_sondage" ADD FOREIGN KEY ("sondage_id") REFERENCES "sondages" ("id");

ALTER TABLE "votes_sondage" ADD FOREIGN KEY ("citoyen_id") REFERENCES "utilisateurs" ("id");

ALTER TABLE "votes_sondage" ADD FOREIGN KEY ("question_id") REFERENCES "questions_sondage" ("id");

ALTER TABLE "votes_sondage" ADD FOREIGN KEY ("reponse_id") REFERENCES "reponses_sondage" ("id");

ALTER TABLE "thematiques_evenement" ADD FOREIGN KEY ("ville_id") REFERENCES "villes" ("id");

ALTER TABLE "evenements" ADD FOREIGN KEY ("thematique_id") REFERENCES "thematiques_evenement" ("id");

ALTER TABLE "evenements" ADD FOREIGN KEY ("ville_id") REFERENCES "villes" ("id");

ALTER TABLE "evenements" ADD FOREIGN KEY ("cree_par") REFERENCES "utilisateurs" ("id");

ALTER TABLE "evenements_utilisateur" ADD FOREIGN KEY ("utilisateur_id") REFERENCES "utilisateurs" ("id");

ALTER TABLE "evenements_utilisateur" ADD FOREIGN KEY ("evenement_id") REFERENCES "evenements" ("id");

ALTER TABLE "actualites" ADD FOREIGN KEY ("ville_id") REFERENCES "villes" ("id");

ALTER TABLE "actualites" ADD FOREIGN KEY ("auteur_id") REFERENCES "utilisateurs" ("id");

ALTER TABLE "reseaux_sociaux" ADD FOREIGN KEY ("ville_id") REFERENCES "villes" ("id");

ALTER TABLE "logs_actions" ADD FOREIGN KEY ("utilisateur_id") REFERENCES "utilisateurs" ("id");

ALTER TABLE "notifications" ADD FOREIGN KEY ("destinataire_id") REFERENCES "utilisateurs" ("id");
