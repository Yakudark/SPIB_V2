-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 19 déc. 2024 à 21:00
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
-- Structure de la table `demandes_conges`
--

DROP TABLE IF EXISTS `demandes_conges`;
CREATE TABLE IF NOT EXISTS `demandes_conges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nb_jours` int NOT NULL,
  `statut` enum('en_attente','approuve','refuse') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `commentaire` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `reponse_commentaire` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_demande` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_reponse` timestamp NULL DEFAULT NULL,
  `repondu_par` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `repondu_par` (`repondu_par`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `demandes_conges`
--

INSERT INTO `demandes_conges` (`id`, `utilisateur_id`, `date_debut`, `date_fin`, `nb_jours`, `statut`, `commentaire`, `reponse_commentaire`, `date_demande`, `date_reponse`, `repondu_par`) VALUES
(1, 19, '2024-12-14', '2024-12-21', 5, 'en_attente', 'vacance', NULL, '2024-12-14 10:24:21', NULL, NULL),
(2, 19, '2024-12-07', '2024-12-07', 0, 'en_attente', 'malade kof kof sniirf', NULL, '2024-12-14 10:33:17', NULL, NULL),
(3, 19, '2024-12-07', '2024-12-15', 5, 'en_attente', 'je vous emmerde', NULL, '2024-12-14 10:40:26', NULL, NULL),
(4, 19, '2024-11-30', '2024-11-30', 0, 'en_attente', 'veux pas bosser et je vous emmerde XD', NULL, '2024-12-14 10:44:47', NULL, NULL),
(5, 20, '2024-12-19', '2024-12-28', 7, 'en_attente', '', NULL, '2024-12-19 16:22:31', NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
