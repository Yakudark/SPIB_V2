-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 11 jan. 2025 à 05:51
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
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `connexions`
--

INSERT INTO `connexions` (`id`, `utilisateur_id`, `matricule`, `password`, `derniere_connexion`) VALUES
(1, 3, '81911', '123456', '2024-12-08 19:43:50'),
(2, 4, '1384', '123456', '2024-12-22 15:59:38'),
(3, 5, '5242', '123456', '2024-12-19 17:42:00'),
(4, 6, '5249', '123456', '2025-01-04 13:23:45'),
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
(17, 19, '1001', '123456', '2025-01-11 06:30:50'),
(18, 20, '1002', '123456', '2024-12-19 22:03:42'),
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
(37, 39, '0001', '123456', '2025-01-11 06:36:59'),
(38, 40, '123789', '123456', '2024-12-08 09:39:32');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
