-- Création de la base de données
CREATE DATABASE IF NOT EXISTS spib_gestion CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE spib_gestion;

-- Table : Services
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_service VARCHAR(100) NOT NULL,
    responsable_em_id INT NULL
);

-- Table : Départements
CREATE TABLE departements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_departement VARCHAR(100) NOT NULL,
    responsable_dm_id INT NULL
);

-- Table : Utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    matricule VARCHAR(50) UNIQUE NOT NULL,
    service_id INT NOT NULL,
    responsable_direct_id INT NULL,
    dm_id INT NULL,
    em_id INT NULL,
    pm_id INT NULL,
    role ENUM('PM', 'EM', 'DM', 'RH', 'salarié') NOT NULL,
    pool VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (responsable_direct_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (dm_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (em_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (pm_id) REFERENCES utilisateurs(id)
);

-- Ajout des clés étrangères pour les tables services et départements
ALTER TABLE services ADD FOREIGN KEY (responsable_em_id) REFERENCES utilisateurs(id);
ALTER TABLE departements ADD FOREIGN KEY (responsable_dm_id) REFERENCES utilisateurs(id);

-- Table : Entretiens
CREATE TABLE entretiens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    type_action ENUM('entretien', 'appel_bienveillant', 'welcome_back') NOT NULL,
    date_action DATETIME NOT NULL,
    feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Table : Calendrier
CREATE TABLE calendrier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    date_heure DATETIME NOT NULL,
    responsable_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (responsable_id) REFERENCES utilisateurs(id)
);

-- Table : Prise de notes
CREATE TABLE prise_de_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entretien_id INT NOT NULL,
    note TEXT NOT NULL,
    date_note TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entretien_id) REFERENCES entretiens(id)
);

-- Table : Mesures RH
CREATE TABLE mesures_rh (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    type_mesure ENUM('mise_a_pied', 'licenciement') NOT NULL,
    date_mesure DATETIME NOT NULL,
    feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);
