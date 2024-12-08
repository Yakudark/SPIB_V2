-- Table des congés
CREATE TABLE IF NOT EXISTS `conges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `jours_restants` int NOT NULL DEFAULT 25,
  `annee` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `utilisateur_annee` (`utilisateur_id`, `annee`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des formations
CREATE TABLE IF NOT EXISTS `formations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` enum('en_cours','terminee','annulee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_cours',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des documents
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des demandes
CREATE TABLE IF NOT EXISTS `demandes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `type` enum('conge','formation','document','autre') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('en_cours','approuvee','refusee','annulee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_cours',
  `date_demande` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_reponse` timestamp NULL DEFAULT NULL,
  `repondu_par` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `repondu_par` (`repondu_par`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des données de test pour les congés
INSERT INTO `conges` (`utilisateur_id`, `jours_restants`, `annee`)
SELECT 
    id as utilisateur_id,
    25 as jours_restants,
    YEAR(CURRENT_DATE) as annee
FROM utilisateurs
WHERE NOT EXISTS (
    SELECT 1 FROM conges c 
    WHERE c.utilisateur_id = utilisateurs.id 
    AND c.annee = YEAR(CURRENT_DATE)
);

-- Insertion des données de test pour les formations
INSERT INTO `formations` (`utilisateur_id`, `titre`, `description`, `date_debut`, `date_fin`, `statut`)
SELECT 
    id as utilisateur_id,
    'Formation initiale' as titre,
    'Formation de base pour les nouveaux employés' as description,
    DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH) as date_debut,
    DATE_ADD(CURRENT_DATE, INTERVAL 2 MONTH) as date_fin,
    'en_cours' as statut
FROM utilisateurs
WHERE role = 'salarié'
LIMIT 5;
