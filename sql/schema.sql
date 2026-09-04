CREATE DATABASE IF NOT EXISTS cecam_gestion CHARACTER SET utf8mb4;
USE cecam_gestion;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,
  role ENUM('admin','chef_caisse','agent') NOT NULL DEFAULT 'agent',
  actif TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE societaires (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code_societaire VARCHAR(20) NOT NULL UNIQUE,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100),
  cin VARCHAR(30),
  telephone VARCHAR(20),
  adresse VARCHAR(255),
  date_naissance DATE,
  photo VARCHAR(255),
  piece_identite VARCHAR(255),
  date_adhesion DATE DEFAULT (CURRENT_DATE),
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE comptes_epargne (
  id INT AUTO_INCREMENT PRIMARY KEY,
  societaire_id INT NOT NULL,
  numero_compte VARCHAR(30) NOT NULL UNIQUE,
  type_compte ENUM('DAV','DAT','PLE') NOT NULL DEFAULT 'DAV',
  solde DECIMAL(15,2) NOT NULL DEFAULT 0,
  taux_interet DECIMAL(5,2) DEFAULT 0,
  date_ouverture DATE DEFAULT (CURRENT_DATE),
  date_echeance DATE NULL,
  statut ENUM('actif','cloture') DEFAULT 'actif',
  FOREIGN KEY (societaire_id) REFERENCES societaires(id)
);

CREATE TABLE mouvements_epargne (
  id INT AUTO_INCREMENT PRIMARY KEY,
  compte_id INT NOT NULL,
  type_mouvement ENUM('depot','retrait','interet') NOT NULL,
  montant DECIMAL(15,2) NOT NULL,
  solde_apres DECIMAL(15,2) NOT NULL,
  effectue_par INT,
  date_mouvement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (compte_id) REFERENCES comptes_epargne(id),
  FOREIGN KEY (effectue_par) REFERENCES users(id)
);

CREATE TABLE produits_credit (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  taux_interet DECIMAL(5,2) NOT NULL,
  duree_max_mois INT NOT NULL,
  montant_min DECIMAL(15,2) DEFAULT 0,
  montant_max DECIMAL(15,2) DEFAULT 0
);

CREATE TABLE credits (
  id INT AUTO_INCREMENT PRIMARY KEY,
  societaire_id INT NOT NULL,
  produit_id INT NOT NULL,
  montant DECIMAL(15,2) NOT NULL,
  duree_mois INT NOT NULL,
  taux_interet DECIMAL(5,2) NOT NULL,
  score_risque INT DEFAULT NULL,
  statut ENUM('en_attente','valide','rejete','solde','en_retard') DEFAULT 'en_attente',
  date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_validation DATE,
  valide_par INT,
  date_decaissement DATE,
  FOREIGN KEY (societaire_id) REFERENCES societaires(id),
  FOREIGN KEY (produit_id) REFERENCES produits_credit(id),
  FOREIGN KEY (valide_par) REFERENCES users(id)
);

CREATE TABLE echeances (
  id INT AUTO_INCREMENT PRIMARY KEY,
  credit_id INT NOT NULL,
  numero_echeance INT NOT NULL,
  date_echeance DATE NOT NULL,
  montant_prevu DECIMAL(15,2) NOT NULL,
  montant_paye DECIMAL(15,2) DEFAULT 0,
  statut ENUM('a_venir','payee','en_retard','partielle') DEFAULT 'a_venir',
  FOREIGN KEY (credit_id) REFERENCES credits(id)
);

CREATE TABLE remboursements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  echeance_id INT NOT NULL,
  montant DECIMAL(15,2) NOT NULL,
  penalite DECIMAL(15,2) DEFAULT 0,
  date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  encaisse_par INT,
  FOREIGN KEY (echeance_id) REFERENCES echeances(id),
  FOREIGN KEY (encaisse_par) REFERENCES users(id)
);

CREATE TABLE notifications_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  societaire_id INT,
  type ENUM('sms','ussd') NOT NULL,
  contenu TEXT,
  statut ENUM('envoye','echec') DEFAULT 'envoye',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (societaire_id) REFERENCES societaires(id)
);