-- Mettre à jour la structure de la table conges
DROP TABLE IF EXISTS `conges`;
CREATE TABLE `conges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `conges_total` int NOT NULL DEFAULT 25,
  `conges_pris` int NOT NULL DEFAULT 0,
  `conges_restant` int NOT NULL DEFAULT 25,
  `annee` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `utilisateur_annee` (`utilisateur_id`, `annee`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Créer une table pour les demandes de congés
CREATE TABLE `demandes_conges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nb_jours` int NOT NULL,
  `statut` enum('en_attente','approuve','refuse') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `reponse_commentaire` text COLLATE utf8mb4_unicode_ci,
  `date_demande` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_reponse` timestamp NULL DEFAULT NULL,
  `repondu_par` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `repondu_par` (`repondu_par`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insérer les données initiales pour les congés
INSERT INTO `conges` (`utilisateur_id`, `conges_total`, `conges_pris`, `conges_restant`, `annee`)
SELECT 
    id as utilisateur_id,
    25 as conges_total,
    0 as conges_pris,
    25 as conges_restant,
    YEAR(CURRENT_DATE) as annee
FROM utilisateurs
WHERE NOT EXISTS (
    SELECT 1 FROM conges c 
    WHERE c.utilisateur_id = utilisateurs.id 
    AND c.annee = YEAR(CURRENT_DATE)
);
