-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 19 jan. 2025 à 14:56
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
-- Structure de la table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pool` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `em_id` int DEFAULT NULL,
  `pm_id` int DEFAULT NULL,
  `dm_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `responsable_em_id` (`em_id`),
  KEY `pm_id` (`pm_id`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `pool`, `em_id`, `pm_id`, `dm_id`) VALUES
(1, 'Delta 1-2', 4, 6, 0),
(2, 'Delta 1-3', 7, 7, 0),
(3, 'Delta 1-4', 8, 8, 0),
(4, 'Delta 1-5', 9, 9, 0),
(5, 'Delta 1-6', 10, 10, 0),
(6, 'Delta 1-7', 11, 11, 0),
(7, 'Delta 1-8', 12, 12, 0),
(8, 'Delta 2-1', 13, 13, 0),
(9, 'Delta 2-2', 14, 14, 0),
(10, 'Delta 2-3', 15, 15, 0),
(11, 'Delta 2-4', 16, 16, 0),
(12, 'Delta 2-5', 17, 17, 0),
(13, 'Delta 2-6', 18, 18, 0),
(14, 'DELTA', NULL, NULL, 0),
(15, 'BREL', NULL, NULL, 0),
(16, 'Brel 1-1', NULL, NULL, 0),
(17, 'Brel 1-2', NULL, NULL, 0),
(18, 'Brel 1-3', NULL, NULL, 0),
(19, 'Brel 1-4', NULL, NULL, 0),
(20, 'Brel 1-5', NULL, NULL, 0),
(21, 'Brel 1-6', NULL, NULL, 0),
(22, 'Brel 1-7', NULL, NULL, 0),
(23, 'Brel 1-8', NULL, NULL, 0),
(24, 'Brel 2-1', NULL, NULL, 0),
(25, 'Brel 2-2', NULL, NULL, 0),
(26, 'Brel 2-3', NULL, NULL, 0),
(27, 'Brel 2-4', NULL, NULL, 0),
(28, 'Brel 2-5', NULL, NULL, 0),
(29, 'Brel 2-6', NULL, NULL, 0),
(30, 'Brel 2-7', NULL, NULL, 0),
(31, 'Brel 2-8', NULL, NULL, 0),
(32, 'Brel 2-9', NULL, NULL, 0),
(33, 'Ha 1-1', NULL, NULL, 0),
(34, 'Ha 1-2', NULL, NULL, 0),
(35, 'Ha 1-3', NULL, NULL, 0),
(36, 'Ha 1-4', NULL, NULL, 0),
(37, 'Ha 1-5', NULL, NULL, 0),
(38, 'Ha 1-6', NULL, NULL, 0),
(39, 'Ha 2-1', NULL, NULL, 0),
(40, 'Ha 2-2', NULL, NULL, 0),
(41, 'Ha 2-3', NULL, NULL, 0),
(42, 'Ha 2-4', NULL, NULL, 0),
(43, 'Ha 2-5', NULL, NULL, 0),
(44, 'Ha 2-6', NULL, NULL, 0),
(45, 'Ha 2-7', NULL, NULL, 0),
(46, 'Ha 3-1', NULL, NULL, 0),
(47, 'Ha 3-2', NULL, NULL, 0),
(48, 'Ha 3-3', NULL, NULL, 0),
(49, 'Ha 3-4', NULL, NULL, 0),
(50, 'Ha 3-6', NULL, NULL, 0),
(51, 'Pike 1-1', NULL, NULL, 0),
(52, 'Pike 1-3', NULL, NULL, 0),
(53, 'Pike 1-4', NULL, NULL, 0),
(54, 'Pike 1-5', NULL, NULL, 0),
(55, 'Pike 1-6', NULL, NULL, 0),
(56, 'Pike 1-7', NULL, NULL, 0),
(57, 'Pike 2-1', NULL, NULL, 0),
(58, 'Pike 2-2', NULL, NULL, 0),
(59, 'Pike 2-3', NULL, NULL, 0),
(60, 'Pike 2-4', NULL, NULL, 0),
(61, 'Pike 2-5', NULL, NULL, 0),
(62, 'Pike 2-6', NULL, NULL, 0),
(63, 'Pike 2-7', NULL, NULL, 0),
(64, 'Marly 1-1', NULL, NULL, 0),
(65, 'Marly 1-2', NULL, NULL, 0),
(66, 'Marly 1-3', NULL, NULL, 0),
(67, 'Marly 1-4', NULL, NULL, 0),
(68, 'Marly 1-5', NULL, NULL, 0),
(69, 'Marly 1-6', NULL, NULL, 0),
(70, 'Marly 1-7', NULL, NULL, 0),
(71, 'Marly 1-8', NULL, NULL, 0),
(72, 'Marly 1-9', NULL, NULL, 0),
(73, 'Marly 1-10', NULL, NULL, 0),
(74, 'Marly 1-11', NULL, NULL, 0),
(75, 'HAREM', NULL, NULL, 0),
(76, 'PIKE', NULL, NULL, 0),
(77, 'MARLY', NULL, NULL, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
