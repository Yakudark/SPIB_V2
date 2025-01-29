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
-- Structure de la table `action_types`
--

DROP TABLE IF EXISTS `action_types`;
CREATE TABLE IF NOT EXISTS `action_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `action_types`
--

INSERT INTO `action_types` (`id`, `nom`, `description`, `created_at`) VALUES
(1, 'Point attention nouvelle période', 'Si nouvelle période prévoir un entretien', '2024-12-16 14:48:54'),
(2, 'Appel bienveillant', 'Appel avec l\'agent pour prendre de ses nouvelles, aider sur les démarches administratives, être présent pour l\'agent', '2024-12-16 14:48:54'),
(3, 'Entretien Welcome Back', 'Sert à accueillir l\'agent et l\'informer de ce qui s\'est passé dans l\'entreprise durant son absence, voir ce qui doit être fait pour aider à une reprise en douceur', '2024-12-16 14:48:54'),
(4, 'Entretien absentéisme informel', 'S\'entretenir avec l\'agent pour voir s\'il n\'y a pas un problème sous-jacent et mettre en place des actions pour éviter l\'aggravation de la situation', '2024-12-16 14:48:54'),
(5, 'Entretien absentéisme formel', 'Entretien formel concernant les absences nombreuses et répétées qui perturbent l\'organisation du service', '2024-12-16 14:48:54'),
(6, 'Pas d\'action', 'Aucune action nécessaire', '2024-12-16 14:48:54'),
(7, 'Entretien mensuel', 'Entretien mensuel de suivi avec l\'agent', '2024-12-16 18:45:37'),
(8, 'Point technique', 'Discussion sur les aspects techniques du travail', '2024-12-16 18:45:37'),
(9, 'Evaluation', 'Evaluation des performances', '2024-12-16 18:45:37'),
(10, 'Formation', 'Session de formation', '2024-12-16 18:45:37'),
(11, 'Briefing', 'Briefing d\'équipe ou individuel', '2024-12-16 18:45:37'),
(12, 'Debriefing', 'Debriefing après une action ou un projet', '2024-12-16 18:45:37'),
(13, 'Accompagnement', 'Session d\'accompagnement sur le terrain', '2024-12-16 18:45:37'),
(14, 'Recadrage', 'Entretien de recadrage', '2024-12-16 18:45:37'),
(15, 'Autre', 'Autre type d\'action', '2024-12-16 18:45:37');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
