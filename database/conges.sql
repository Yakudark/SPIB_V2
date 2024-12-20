-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 20 déc. 2024 à 13:02
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
-- Structure de la table `conges`
--

DROP TABLE IF EXISTS `conges`;
CREATE TABLE IF NOT EXISTS `conges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `conges_total` int NOT NULL DEFAULT '25',
  `conges_pris` int NOT NULL DEFAULT '0',
  `conges_restant` int NOT NULL DEFAULT '25',
  `annee` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `utilisateur_annee` (`utilisateur_id`,`annee`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `conges`
--

INSERT INTO `conges` (`id`, `utilisateur_id`, `conges_total`, `conges_pris`, `conges_restant`, `annee`, `created_at`) VALUES
(1, 3, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(2, 4, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(3, 5, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(4, 6, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(5, 7, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(6, 8, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(7, 9, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(8, 10, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(9, 11, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(10, 12, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(11, 13, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(12, 14, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(13, 15, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(14, 16, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(15, 17, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(16, 18, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(17, 19, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(18, 20, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(19, 21, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(20, 22, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(21, 23, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(22, 24, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(23, 25, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(24, 26, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(25, 27, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(26, 28, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(27, 29, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(28, 30, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(29, 31, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(30, 32, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(31, 33, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(32, 34, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(33, 35, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(34, 36, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(35, 37, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(36, 38, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(37, 39, 25, 0, 25, 2024, '2024-12-08 08:54:19'),
(38, 40, 25, 0, 25, 2024, '2024-12-08 08:54:19');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
