-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 20 déc. 2024 à 13:41
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
-- Structure de la table `actions`
--

DROP TABLE IF EXISTS `actions`;
CREATE TABLE IF NOT EXISTS `actions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `agent_id` int NOT NULL,
  `pm_id` int DEFAULT NULL,
  `em_id` int DEFAULT NULL,
  `type_action_id` int NOT NULL,
  `date_action` date NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('planifie','effectue','annule') COLLATE utf8mb4_unicode_ci DEFAULT 'planifie',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `agent_id` (`agent_id`),
  KEY `pm_id` (`pm_id`),
  KEY `em_id` (`em_id`),
  KEY `type_action_id` (`type_action_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `actions`
--

INSERT INTO `actions` (`id`, `agent_id`, `pm_id`, `em_id`, `type_action_id`, `date_action`, `commentaire`, `statut`, `created_at`, `updated_at`) VALUES
(1, 19, 6, NULL, 13, '2024-12-10', '', 'planifie', '2024-12-16 19:02:02', '2024-12-16 19:02:02'),
(2, 20, 6, NULL, 2, '2024-12-28', 'amène toi', 'planifie', '2024-12-19 16:20:56', '2024-12-19 16:20:56'),
(3, 19, 6, NULL, 9, '2025-01-17', 'Evaluation annuelle', 'planifie', '2024-12-19 21:18:35', '2024-12-19 21:18:35'),
(4, 19, 6, NULL, 2, '2024-12-20', '', 'planifie', '2024-12-20 12:47:55', '2024-12-20 12:47:55');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
