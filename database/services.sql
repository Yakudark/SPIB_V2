-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 11 jan. 2025 à 06:59
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
-- Structure de la table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_service` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `responsable_em_id` int DEFAULT NULL,
  `pm_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `responsable_em_id` (`responsable_em_id`),
  KEY `pm_id` (`pm_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `nom_service`, `responsable_em_id`, `pm_id`) VALUES
(1, 'Pool Delta 1-2', 6, 6),
(2, 'Pool Delta 1-3', 7, 7),
(3, 'Pool Delta 1-4', 8, 8),
(4, 'Pool Delta 1-5', 9, 9),
(5, 'Pool Delta 1-6', 10, 10),
(6, 'Pool Delta 1-7', 11, 11),
(7, 'Pool Delta 1-8', 12, 12),
(8, 'Pool Delta 2-1', 13, 13),
(9, 'Pool Delta 2-2', 14, 14),
(10, 'Pool Delta 2-3', 15, 15),
(11, 'Pool Delta 2-4', 16, 16),
(12, 'Pool Delta 2-5', 17, 17),
(13, 'Pool Delta 2-6', 18, 18);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
