CREATE TABLE "utilisateurs" (
  "id" integer PRIMARY KEY,
  "nom" varchar(100),
  "prenom" varchar(100),
  "email" varchar(255) UNIQUE,
  "mot_de_passe_hash" varchar(255),
  "date_creation" timestamp,
  "date_naissance" timestamp,
  "telephone" varchar(20),
  "compte_actif" boolean
);

CREATE TABLE "administrateurs" (
  "utilisateur_id" integer PRIMARY KEY,
  "ville_id" integer
);

CREATE TABLE "citoyens" (
  "utilisateur_id" integer PRIMARY KEY,
  "ville_id" integer
);

CREATE TABLE "sondages" (
  "id" integer PRIMARY KEY,
  "titre" varchar(255),
  "description" text,
  "date_debut" timestamp,
  "date_fin" timestamp,
  "administrateur_id" integer
);

CREATE TABLE "questions_sondage" (
  "id" integer PRIMARY KEY,
  "sondage_id" integer,
  "question" text
);

CREATE TABLE "reponses_sondage" (
  "id" integer PRIMARY KEY,
  "question_id" integer,
  "libelle" text
);

CREATE TABLE "votes_sondage" (
  "id" integer PRIMARY KEY,
  "citoyen_id" integer,
  "question_id" integer,
  "reponse_id" integer,
  "date_vote" timestamp
);

CREATE TABLE "types_signalement" (
  "id" integer PRIMARY KEY,
  "nom" varchar(100)
);

CREATE TABLE "signalements" (
  "id" integer PRIMARY KEY,
  "titre" varchar(255),
  "etat" varchar(255),
  "description" text,
  "latitude" decimal,
  "longitude" decimal,
  "type_id" integer,
  "citoyen_id" integer,
  "date_creation" timestamp,
  "date_modification" timestamp
);

CREATE TABLE "Commentaire" (
  "id" integer PRIMARY KEY,
  "titre" varchar(255),
  "description" text,
  "auteur" integer,
  "date_creation" timestamp,
  "date_modification" timestamp
);

CREATE TABLE "Evenement" (
  "id" integer PRIMARY KEY,
  "titre" varchar(255),
  "dateEv" date,
  "heureDeb" heure,
  "heureFin" heure,
  "administrateur_id" integer,
  "ville_id" integer,
  "type" integer
);

CREATE TABLE "Ville" (
  "id" integer PRIMARY KEY,
  "Nom" varchar(255),
  "slogan" varchar(255),
  "logo" varchar(255),
  "themeCouleur" varchar(255),
  "date_creation" date
);

CREATE TABLE "reseau_sociale" (
  "id" integer PRIMARY KEY,
  "ville_id" integer,
  "plateform" varchar(255),
  "lien" varchar(255)
);

CREATE TABLE "Type_ev" (
  "id" integer PRIMARY KEY,
  "nom" varchar(255)
);

ALTER TABLE "administrateurs" ADD FOREIGN KEY ("utilisateur_id") REFERENCES "utilisateurs" ("id");

ALTER TABLE "citoyens" ADD FOREIGN KEY ("utilisateur_id") REFERENCES "utilisateurs" ("id");

ALTER TABLE "sondages" ADD FOREIGN KEY ("administrateur_id") REFERENCES "administrateurs" ("utilisateur_id");

ALTER TABLE "questions_sondage" ADD FOREIGN KEY ("sondage_id") REFERENCES "sondages" ("id");

ALTER TABLE "reponses_sondage" ADD FOREIGN KEY ("question_id") REFERENCES "questions_sondage" ("id");

ALTER TABLE "votes_sondage" ADD FOREIGN KEY ("citoyen_id") REFERENCES "citoyens" ("utilisateur_id");

ALTER TABLE "votes_sondage" ADD FOREIGN KEY ("reponse_id") REFERENCES "reponses_sondage" ("id");

ALTER TABLE "signalements" ADD FOREIGN KEY ("type_id") REFERENCES "types_signalement" ("id");

ALTER TABLE "signalements" ADD FOREIGN KEY ("citoyen_id") REFERENCES "citoyens" ("utilisateur_id");

ALTER TABLE "utilisateurs" ADD FOREIGN KEY ("id") REFERENCES "Commentaire" ("auteur");

ALTER TABLE "Evenement" ADD FOREIGN KEY ("administrateur_id") REFERENCES "administrateurs" ("utilisateur_id");

ALTER TABLE "administrateurs" ADD FOREIGN KEY ("ville_id") REFERENCES "Ville" ("id");

ALTER TABLE "reseau_sociale" ADD FOREIGN KEY ("ville_id") REFERENCES "Ville" ("id");

ALTER TABLE "Evenement" ADD FOREIGN KEY ("type") REFERENCES "Type_ev" ("id");
