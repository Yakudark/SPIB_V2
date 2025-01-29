-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 08 déc. 2024 à 06:22
-- Version du serveur : 8.0.31
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `stib_gestion`
--

-- --------------------------------------------------------

--
-- Structure de la table `calendrier`
--

DROP TABLE IF EXISTS `calendrier`;
CREATE TABLE IF NOT EXISTS `calendrier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `date_heure` datetime NOT NULL,
  `responsable_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `responsable_id` (`responsable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connexions`
--

DROP TABLE IF EXISTS `connexions`;
CREATE TABLE IF NOT EXISTS `connexions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `matricule` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `derniere_connexion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`matricule`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `connexions`
--

INSERT INTO `connexions` (`id`, `utilisateur_id`, `matricule`, `password`, `derniere_connexion`) VALUES
(1, 3, '81911', '123456', NULL),
(2, 4, '1384', '123456', NULL),
(3, 5, '5242', '123456', NULL),
(4, 6, '5249', '123456', NULL),
(5, 7, '70643', '123456', NULL),
(6, 8, '67764', '123456', NULL),
(7, 9, '64068', '123456', NULL),
(8, 10, '64103', '123456', NULL),
(9, 11, '64703', '123456', NULL),
(10, 12, '69618', '123456', NULL),
(11, 13, '3346', '123456', NULL),
(12, 14, '68711', '123456', NULL),
(13, 15, '66062', '123456', NULL),
(14, 16, '8234', '123456', NULL),
(15, 17, '712', '123456', NULL),
(16, 18, '64223', '123456', NULL),
(17, 19, '1001', '123456', NULL),
(18, 20, '1002', '123456', NULL),
(19, 21, '1003', '123456', NULL),
(20, 22, '1004', '123456', NULL),
(21, 23, '1005', '123456', NULL),
(22, 24, '1006', '123456', NULL),
(23, 25, '1007', '123456', NULL),
(24, 26, '1008', '123456', NULL),
(25, 27, '1009', '123456', NULL),
(26, 28, '1010', '123456', NULL),
(27, 29, '1011', '123456', NULL),
(28, 30, '1012', '123456', NULL),
(29, 31, '1013', '123456', NULL),
(30, 32, '1014', '123456', NULL),
(31, 33, '1015', '123456', NULL),
(32, 34, '1016', '123456', NULL),
(33, 35, '1017', '123456', NULL),
(34, 36, '1018', '123456', NULL),
(35, 37, '1019', '123456', NULL),
(36, 38, '1020', '123456', NULL),
(37, 39, '0001', '123456', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `departements`
--

DROP TABLE IF EXISTS `departements`;
CREATE TABLE IF NOT EXISTS `departements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_departement` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `responsable_dm_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `responsable_dm_id` (`responsable_dm_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `departements`
--

INSERT INTO `departements` (`id`, `nom_departement`, `responsable_dm_id`) VALUES
(1, 'Pool Delta 1', 3),
(2, 'Pool Delta 2', 3);

-- --------------------------------------------------------

--
-- Structure de la table `entretiens`
--

DROP TABLE IF EXISTS `entretiens`;
CREATE TABLE IF NOT EXISTS `entretiens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `type_action` enum('entretien','appel_bienveillant','welcome_back') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_action` datetime NOT NULL,
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mesures_rh`
--

DROP TABLE IF EXISTS `mesures_rh`;
CREATE TABLE IF NOT EXISTS `mesures_rh` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `type_mesure` enum('mise_a_pied','licenciement') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_mesure` datetime NOT NULL,
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `prise_de_notes`
--

DROP TABLE IF EXISTS `prise_de_notes`;
CREATE TABLE IF NOT EXISTS `prise_de_notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `entretien_id` int NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_note` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `entretien_id` (`entretien_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_service` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `responsable_em_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `responsable_em_id` (`responsable_em_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `nom_service`, `responsable_em_id`) VALUES
(1, 'Pool Delta 1-2', 6),
(2, 'Pool Delta 1-3', 7),
(3, 'Pool Delta 1-4', 8),
(4, 'Pool Delta 1-5', 9),
(5, 'Pool Delta 1-6', 10),
(6, 'Pool Delta 1-7', 11),
(7, 'Pool Delta 1-8', 12),
(8, 'Pool Delta 2-1', 13),
(9, 'Pool Delta 2-2', 14),
(10, 'Pool Delta 2-3', 15),
(11, 'Pool Delta 2-4', 16),
(12, 'Pool Delta 2-5', 17),
(13, 'Pool Delta 2-6', 18);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_id` int NOT NULL,
  `responsable_direct_id` int DEFAULT NULL,
  `dm_id` int DEFAULT NULL,
  `em_id` int DEFAULT NULL,
  `pm_id` int DEFAULT NULL,
  `role` enum('PM','EM','DM','RH','salarié','SuperAdmin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `pool` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`),
  KEY `service_id` (`service_id`),
  KEY `responsable_direct_id` (`responsable_direct_id`),
  KEY `dm_id` (`dm_id`),
  KEY `em_id` (`em_id`),
  KEY `pm_id` (`pm_id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `matricule`, `service_id`, `responsable_direct_id`, `dm_id`, `em_id`, `pm_id`, `role`, `pool`, `created_at`) VALUES
(3, 'Lambot', 'Ludovic', '81911', 0, NULL, NULL, NULL, NULL, 'DM', NULL, '2024-12-08 05:41:42'),
(4, 'Ma-Motingiya', 'Mabita', '1384', 0, NULL, 3, NULL, NULL, 'EM', NULL, '2024-12-08 05:41:42'),
(5, 'Ahallal', 'Nordine', '5242', 0, NULL, 3, NULL, NULL, 'EM', NULL, '2024-12-08 05:41:42'),
(6, 'Devadas', 'Appana', '5249', 0, NULL, 3, 4, NULL, 'PM', 'Pool Delta 1-2', '2024-12-08 05:41:43'),
(7, 'Maboundou', 'Haufray', '70643', 0, NULL, 3, 4, NULL, 'PM', 'Pool Delta 1-3', '2024-12-08 05:41:43'),
(8, 'Mohammed', 'Atia', '67764', 0, NULL, 3, 4, NULL, 'PM', 'Pool Delta 1-4', '2024-12-08 05:41:43'),
(9, 'Lahcene', 'Hassan', '64068', 0, NULL, 3, 4, NULL, 'PM', 'Pool Delta 1-5', '2024-12-08 05:41:43'),
(10, 'de Silva-Pedras', 'Francisco', '64103', 0, NULL, 3, 4, NULL, 'PM', 'Pool Delta 1-6', '2024-12-08 05:41:43'),
(11, 'Delcroix', 'Michael', '64703', 0, NULL, 3, 4, NULL, 'PM', 'Pool Delta 1-7', '2024-12-08 05:41:43'),
(12, 'Chauvaux', 'Benoît', '69618', 0, NULL, 3, 4, NULL, 'PM', 'Pool Delta 1-8', '2024-12-08 05:41:43'),
(13, 'Roekens', 'Denis', '3346', 0, NULL, 3, 5, NULL, 'PM', 'Pool Delta 2-1', '2024-12-08 05:41:43'),
(14, 'Fayçal', 'Barfi', '68711', 0, NULL, 3, 5, NULL, 'PM', 'Pool Delta 2-2', '2024-12-08 05:41:43'),
(15, 'Bouchrit', 'Rachid', '66062', 0, NULL, 3, 5, NULL, 'PM', 'Pool Delta 2-3', '2024-12-08 05:41:43'),
(16, 'Mommaert', 'Daniel', '8234', 0, NULL, 3, 5, NULL, 'PM', 'Pool Delta 2-4', '2024-12-08 05:41:43'),
(17, 'Ben Fredj', 'Medhi', '712', 0, NULL, 3, 5, NULL, 'PM', 'Pool Delta 2-5', '2024-12-08 05:41:43'),
(18, 'Dari', 'Nordine', '64223', 0, NULL, 3, 5, NULL, 'PM', 'Pool Delta 2-6', '2024-12-08 05:41:43'),
(19, 'FICT1', 'Salarié1', '1001', 0, NULL, 3, 4, 6, 'salarié', 'Pool Delta 1-2', '2024-12-08 05:46:49'),
(20, 'FICT2', 'Salarié2', '1002', 0, NULL, 3, 4, 6, 'salarié', 'Pool Delta 1-2', '2024-12-08 05:46:49'),
(21, 'FICT3', 'Salarié3', '1003', 0, NULL, 3, 4, 7, 'salarié', 'Pool Delta 1-3', '2024-12-08 05:46:49'),
(22, 'FICT4', 'Salarié4', '1004', 0, NULL, 3, 4, 7, 'salarié', 'Pool Delta 1-3', '2024-12-08 05:46:49'),
(23, 'FICT5', 'Salarié5', '1005', 0, NULL, 3, 4, 8, 'salarié', 'Pool Delta 1-4', '2024-12-08 05:46:49'),
(24, 'FICT6', 'Salarié6', '1006', 0, NULL, 3, 4, 9, 'salarié', 'Pool Delta 1-5', '2024-12-08 05:46:49'),
(25, 'FICT7', 'Salarié7', '1007', 0, NULL, 3, 4, 9, 'salarié', 'Pool Delta 1-5', '2024-12-08 05:46:49'),
(26, 'FICT8', 'Salarié8', '1008', 0, NULL, 3, 4, 10, 'salarié', 'Pool Delta 1-6', '2024-12-08 05:46:49'),
(27, 'FICT9', 'Salarié9', '1009', 0, NULL, 3, 4, 11, 'salarié', 'Pool Delta 1-7', '2024-12-08 05:46:49'),
(28, 'FICT10', 'Salarié10', '1010', 0, NULL, 3, 4, 12, 'salarié', 'Pool Delta 1-8', '2024-12-08 05:46:49'),
(29, 'FICT11', 'Salarié11', '1011', 0, NULL, 3, 5, 13, 'salarié', 'Pool Delta 2-1', '2024-12-08 05:46:49'),
(30, 'FICT12', 'Salarié12', '1012', 0, NULL, 3, 5, 14, 'salarié', 'Pool Delta 2-2', '2024-12-08 05:46:49'),
(31, 'FICT13', 'Salarié13', '1013', 0, NULL, 3, 5, 15, 'salarié', 'Pool Delta 2-3', '2024-12-08 05:46:49'),
(32, 'FICT14', 'Salarié14', '1014', 0, NULL, 3, 5, 15, 'salarié', 'Pool Delta 2-3', '2024-12-08 05:46:49'),
(33, 'FICT15', 'Salarié15', '1015', 0, NULL, 3, 5, 16, 'salarié', 'Pool Delta 2-4', '2024-12-08 05:46:49'),
(34, 'FICT16', 'Salarié16', '1016', 0, NULL, 3, 5, 17, 'salarié', 'Pool Delta 2-5', '2024-12-08 05:46:49'),
(35, 'FICT17', 'Salarié17', '1017', 0, NULL, 3, 5, 17, 'salarié', 'Pool Delta 2-5', '2024-12-08 05:46:49'),
(36, 'FICT18', 'Salarié18', '1018', 0, NULL, 3, 5, 18, 'salarié', 'Pool Delta 2-6', '2024-12-08 05:46:49'),
(37, 'FICT19', 'Salarié19', '1019', 0, NULL, 3, 5, 18, 'salarié', 'Pool Delta 2-6', '2024-12-08 05:46:49'),
(38, 'FICT20', 'Salarié20', '1020', 0, NULL, 3, 5, 18, 'salarié', 'Pool Delta 2-7', '2024-12-08 05:46:49'),
(39, 'Super', 'Admin', '0001', 0, NULL, NULL, NULL, NULL, 'SuperAdmin', NULL, '2024-12-08 06:07:05');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
