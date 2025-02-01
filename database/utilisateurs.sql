-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 01 fév. 2025 à 14:13
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
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dm_id` int DEFAULT NULL,
  `em_id` int DEFAULT NULL,
  `pm_id` int DEFAULT NULL,
  `role` enum('PM','EM','DM','RH','salarié','SuperAdmin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pool` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `derniere_connexion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`),
  KEY `dm_id` (`dm_id`),
  KEY `em_id` (`em_id`),
  KEY `pm_id` (`pm_id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `matricule`, `dm_id`, `em_id`, `pm_id`, `role`, `pool`, `created_at`, `password`, `derniere_connexion`) VALUES
(3, 'Lambot', 'Ludovic', '81911', NULL, NULL, NULL, 'DM', NULL, '2024-12-08 05:41:42', '123456', NULL),
(4, 'Ma-Motingiya', 'Mabita', '1384', 3, NULL, NULL, 'EM', 'Delta 1', '2024-12-08 05:41:42', '123456', NULL),
(5, 'Ahallal', 'Nordine', '5242', 3, NULL, NULL, 'EM', 'Delta 2', '2024-12-08 05:41:42', '123456', NULL),
(6, 'Devadas', 'Appana', '5249', 3, 4, NULL, 'PM', 'Delta 1-2', '2024-12-08 05:41:43', '123456', NULL),
(7, 'Maboundou', 'Haufray', '70643', 3, 4, NULL, 'PM', 'Delta 1-3', '2024-12-08 05:41:43', '123456', NULL),
(8, 'Mohammed', 'Atia', '67764', 3, 4, NULL, 'PM', 'Delta 1-4', '2024-12-08 05:41:43', '123456', NULL),
(9, 'Lahcene', 'Hassan', '64068', 3, 4, NULL, 'PM', 'Delta 1-5', '2024-12-08 05:41:43', '123456', NULL),
(10, 'de Silva-Pedras', 'Francisco', '64103', 3, 4, NULL, 'PM', 'Delta 1-6', '2024-12-08 05:41:43', '123456', NULL),
(11, 'Delcroix', 'Michael', '64703', 3, 4, NULL, 'PM', 'Delta 1-7', '2024-12-08 05:41:43', '123456', NULL),
(12, 'Chauvaux', 'Benoît', '69618', 3, 4, NULL, 'PM', 'Delta 1-8', '2024-12-08 05:41:43', '123456', NULL),
(13, 'Roekens', 'Denis', '3346', 3, 5, NULL, 'PM', 'Delta 2-1', '2024-12-08 05:41:43', '123456', NULL),
(14, 'Fayçal', 'Barfi', '68711', 3, 5, NULL, 'PM', 'Delta 2-2', '2024-12-08 05:41:43', '123456', NULL),
(15, 'Bouchrit', 'Rachid', '66062', 3, 5, NULL, 'PM', 'Delta 2-3', '2024-12-08 05:41:43', '123456', NULL),
(16, 'Mommaert', 'Daniel', '8234', 3, 5, NULL, 'PM', 'Delta 2-4', '2024-12-08 05:41:43', '123456', NULL),
(17, 'Ben Fredj', 'Medhi', '712', 3, 5, NULL, 'PM', 'Delta 2-5', '2024-12-08 05:41:43', '123456', NULL),
(18, 'Dari', 'Nordine', '64223', 3, 5, NULL, 'PM', 'Delta 2-6', '2024-12-08 05:41:43', '123456', NULL),
(19, 'FICT1', 'Salarié1', '1001', 3, 4, 6, 'salarié', 'Delta 1-2', '2024-12-08 05:46:49', '123456', '2025-02-01 14:10:46'),
(20, 'FICT2', 'Salarié2', '1002', 3, 4, 6, 'salarié', 'Delta 1-2', '2024-12-08 05:46:49', '123456', NULL),
(21, 'FICT3', 'Salarié3', '1003', 3, 4, 7, 'salarié', 'Delta 1-3', '2024-12-08 05:46:49', '123456', NULL),
(22, 'FICT4', 'Salarié4', '1004', 3, 4, 7, 'salarié', 'Delta 1-3', '2024-12-08 05:46:49', '123456', NULL),
(23, 'FICT5', 'Salarié5', '1005', 3, 4, 8, 'salarié', 'Delta 1-4', '2024-12-08 05:46:49', '123456', NULL),
(24, 'FICT6', 'Salarié6', '1006', 3, 4, 9, 'salarié', 'Delta 1-5', '2024-12-08 05:46:49', '123456', NULL),
(25, 'FICT7', 'Salarié7', '1007', NULL, NULL, NULL, 'salarié', 'Brel 2-1', '2024-12-08 05:46:49', '123456', NULL),
(26, 'FICT8', 'Salarié8', '1008', 3, 4, 10, 'salarié', 'Delta 1-6', '2024-12-08 05:46:49', '123456', NULL),
(27, 'FICT9', 'Salarié9', '1009', 3, 4, 11, 'salarié', 'Delta 1-7', '2024-12-08 05:46:49', '123456', NULL),
(28, 'FICT10', 'Salarié10', '1010', 3, 4, 12, 'salarié', 'Delta 1-8', '2024-12-08 05:46:49', '123456', NULL),
(29, 'FICT11', 'Salarié11', '1011', 3, 5, 13, 'salarié', 'Delta 2-1', '2024-12-08 05:46:49', '123456', NULL),
(30, 'FICT12', 'Salarié12', '1012', 3, 5, 14, 'salarié', 'Delta 2-2', '2024-12-08 05:46:49', '123456', NULL),
(31, 'FICT13', 'Salarié13', '1013', 3, 5, 15, 'salarié', 'Delta 2-3', '2024-12-08 05:46:49', '123456', NULL),
(32, 'FICT14', 'Salarié14', '1014', 3, 5, 15, 'salarié', 'Delta 2-3', '2024-12-08 05:46:49', '123456', NULL),
(33, 'FICT15', 'Salarié15', '1015', 3, 5, 16, 'salarié', 'Delta 2-4', '2024-12-08 05:46:49', '123456', NULL),
(34, 'FICT16', 'Salarié16', '1016', 3, 5, 17, 'salarié', 'Delta 2-5', '2024-12-08 05:46:49', '123456', NULL),
(35, 'FICT17', 'Salarié17', '1017', 3, 5, 17, 'salarié', 'Delta 2-5', '2024-12-08 05:46:49', '123456', NULL),
(36, 'FICT18', 'Salarié18', '1018', 3, 5, 18, 'salarié', 'Delta 2-6', '2024-12-08 05:46:49', '123456', NULL),
(37, 'FICT19', 'Salarié19', '1019', 3, 5, 18, 'salarié', 'Delta 2-6', '2024-12-08 05:46:49', '123456', NULL),
(38, 'FICT20', 'Salarié20', '1020', 3, 5, 18, 'salarié', 'Delta 2-7', '2024-12-08 05:46:49', '123456', NULL),
(39, 'Super', 'Admin', '0001', NULL, NULL, NULL, 'SuperAdmin', NULL, '2024-12-08 06:07:05', '123456', NULL),
(40, 'RH', 'RH', '123789', NULL, NULL, NULL, 'RH', NULL, '2024-12-08 06:46:07', '123456', NULL),
(43, 'Jane', 'Jane', '778899', NULL, NULL, NULL, 'salarié', 'Brel 1-1', '2025-01-12 06:54:04', '123456', NULL),
(45, 'Rachid', 'Aharag', '63011', NULL, NULL, NULL, 'PM', 'Brel 1-1', '2025-01-19 14:21:09', '123456', NULL),
(46, 'Raquet', 'Grégory', '69551', 3, 4, 12, 'salarié', 'Delta 1-8', '2025-01-31 21:32:02', '123456', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
