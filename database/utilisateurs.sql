-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 19 déc. 2024 à 16:35
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
-- Base de données : `spib_gestion`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `matricule`, `service_id`, `responsable_direct_id`, `dm_id`, `em_id`, `pm_id`, `role`, `pool`, `created_at`) VALUES
(3, 'Lambot', 'Ludovic', '81911', 0, NULL, NULL, NULL, NULL, 'DM', NULL, '2024-12-08 05:41:42'),
(4, 'Ma-Motingiya', 'Mabita', '1384', 0, NULL, 3, NULL, NULL, 'EM', 'Delta 1', '2024-12-08 05:41:42'),
(5, 'Ahallal', 'Nordine', '5242', 0, NULL, 3, NULL, NULL, 'EM', 'Delta 2', '2024-12-08 05:41:42'),
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
(39, 'Super', 'Admin', '0001', 0, NULL, NULL, NULL, NULL, 'SuperAdmin', NULL, '2024-12-08 06:07:05'),
(40, 'RH', 'RH', '123789', 0, NULL, NULL, NULL, NULL, 'RH', NULL, '2024-12-08 06:46:07');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
