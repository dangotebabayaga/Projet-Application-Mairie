DROP TABLE IF EXISTS liste_choix_vote CASCADE;
DROP TABLE IF EXISTS liste_choix_sondage CASCADE;
DROP TABLE IF EXISTS votes_sondage CASCADE;
DROP TABLE IF EXISTS sondages CASCADE;
DROP TABLE IF EXISTS choix CASCADE;
DROP TABLE IF EXISTS signalements CASCADE;
DROP TABLE IF EXISTS types_signalement CASCADE;
DROP TABLE IF EXISTS commentaire CASCADE;
DROP TABLE IF EXISTS evenement CASCADE;
DROP TABLE IF EXISTS type_ev CASCADE;
DROP TABLE IF EXISTS reseau_sociale CASCADE;
DROP TABLE IF EXISTS citoyens CASCADE;
DROP TABLE IF EXISTS administrateurs CASCADE;
DROP TABLE IF EXISTS utilisateurs CASCADE;
DROP TABLE IF EXISTS ville CASCADE;

CREATE TABLE utilisateurs (
  id SERIAL PRIMARY KEY,
  nom varchar(100),
  prenom varchar(100),
  email varchar(255) UNIQUE,
  mot_de_passe_hash varchar(255),
  date_creation timestamp,
  date_naissance date,
  telephone varchar(20),
  compte_actif boolean
);

CREATE TABLE ville (
  id SERIAL PRIMARY KEY,
  nom varchar(255),
  slogan varchar(255),
  logo varchar(255),
  themeCouleur varchar(255),
  date_creation date
);

CREATE TABLE administrateurs (
  utilisateur_id integer PRIMARY KEY,
  ville_id integer
);

CREATE TABLE citoyens (
  utilisateur_id integer PRIMARY KEY
);

CREATE TABLE sondages (
  id SERIAL PRIMARY KEY,
  titre varchar(255),
  description text,
  date_debut timestamp,
  date_fin timestamp,
  administrateur_id integer
);

CREATE TABLE votes_sondage (
  id SERIAL PRIMARY KEY,
  citoyen_id integer,
  date_vote timestamp,
  id_sondage integer
);

CREATE TABLE choix (
  id SERIAL PRIMARY KEY,
  nom varchar(255)
);

CREATE TABLE liste_choix_sondage (
  id_sondage integer,
  id_choix integer,
  PRIMARY KEY (id_sondage,id_choix)
);

CREATE TABLE liste_choix_vote (
  id_vote integer,
  id_choix integer,
  PRIMARY KEY (id_vote,id_choix)
);

CREATE TABLE types_signalement (
  id SERIAL PRIMARY KEY,
  nom varchar(100)
);

CREATE TABLE signalements (
  id SERIAL PRIMARY KEY,
  titre varchar(255),
  etat varchar(255),
  description text,
  latitude decimal,
  longitude decimal,
  type_id integer,
  citoyen_id integer,
  date_creation timestamp,
  date_modification timestamp
);

CREATE TABLE commentaire (
  id SERIAL PRIMARY KEY,
  titre varchar(255),
  description text,
  auteur integer,
  date_creation timestamp,
  date_modification timestamp
);

CREATE TABLE type_ev (
  id SERIAL PRIMARY KEY,
  nom varchar(255)
);

CREATE TABLE evenement (
  id SERIAL PRIMARY KEY,
  titre varchar(255),
  dateEv date,
  heureDeb time,
  heureFin time,
  administrateur_id integer,
  ville_id integer,
  type integer
);

CREATE TABLE reseau_sociale (
  id SERIAL PRIMARY KEY,
  ville_id integer,
  plateforme varchar(255),
  lien varchar(255)
);

ALTER TABLE administrateurs
ADD FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id);

ALTER TABLE administrateurs
ADD FOREIGN KEY (ville_id) REFERENCES ville(id);

ALTER TABLE citoyens
ADD FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id);

ALTER TABLE sondages
ADD FOREIGN KEY (administrateur_id) REFERENCES administrateurs(utilisateur_id);

ALTER TABLE votes_sondage
ADD FOREIGN KEY (citoyen_id) REFERENCES citoyens(utilisateur_id);

ALTER TABLE votes_sondage
ADD FOREIGN KEY (id_sondage) REFERENCES sondages(id);

ALTER TABLE liste_choix_sondage
ADD FOREIGN KEY (id_sondage) REFERENCES sondages(id);

ALTER TABLE liste_choix_sondage
ADD FOREIGN KEY (id_choix) REFERENCES choix(id);

ALTER TABLE liste_choix_vote
ADD FOREIGN KEY (id_vote) REFERENCES votes_sondage(id);

ALTER TABLE liste_choix_vote
ADD FOREIGN KEY (id_choix) REFERENCES choix(id);

ALTER TABLE signalements
ADD FOREIGN KEY (type_id) REFERENCES types_signalement(id);

ALTER TABLE signalements
ADD FOREIGN KEY (citoyen_id) REFERENCES citoyens(utilisateur_id);

ALTER TABLE commentaire
ADD FOREIGN KEY (auteur) REFERENCES utilisateurs(id);

ALTER TABLE evenement
ADD FOREIGN KEY (administrateur_id) REFERENCES administrateurs(utilisateur_id);

ALTER TABLE evenement
ADD FOREIGN KEY (ville_id) REFERENCES ville(id);

ALTER TABLE evenement
ADD FOREIGN KEY (type) REFERENCES type_ev(id);

ALTER TABLE reseau_sociale
ADD FOREIGN KEY (ville_id) REFERENCES ville(id);
