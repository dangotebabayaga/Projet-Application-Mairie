CREATE TABLE "utilisateurs" (
  "id" SERIAL PRIMARY KEY,
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
  "utilisateur_id" SERIAL PRIMARY KEY,
  "ville_id" integer
);

CREATE TABLE "citoyens" (
  "utilisateur_id" SERIAL PRIMARY KEY,
  "ville_id" integer
);

CREATE TABLE "sondages" (
  "id" SERIAL PRIMARY KEY,
  "titre" varchar(255),
  "description" text,
  "date_debut" timestamp,
  "date_fin" timestamp,
  "administrateur_id" integer
);

CREATE TABLE "votes_sondage" (
  "id" SERIAL PRIMARY KEY,
  "citoyen_id" integer,
  "date_vote" timestamp,
  "id_sondage" integer
);

CREATE TABLE "types_signalement" (
  "id" SERIAL PRIMARY KEY,
  "nom" varchar(100)
);

CREATE TABLE "signalements" (
  "id" SERIAL PRIMARY KEY,
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
  "id" SERIAL PRIMARY KEY,
  "titre" varchar(255),
  "description" text,
  "auteur" integer,
  "date_creation" timestamp,
  "date_modification" timestamp
);

CREATE TABLE "Evenement" (
  "id" SERIAL PRIMARY KEY,
  "titre" varchar(255),
  "dateEv" date,
  "heureDeb" heure,
  "heureFin" heure,
  "administrateur_id" integer,
  "ville_id" integer,
  "type" integer
);

CREATE TABLE "Ville" (
  "id" SERIAL PRIMARY KEY,
  "Nom" varchar(255),
  "slogan" varchar(255),
  "logo" varchar(255),
  "themeCouleur" varchar(255),
  "date_creation" date
);

CREATE TABLE "reseau_sociale" (
  "id" SERIAL PRIMARY KEY,
  "ville_id" integer,
  "plateform" varchar(255),
  "lien" varchar(255)
);

CREATE TABLE "Type_ev" (
  "id" SERIAL PRIMARY KEY,
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
