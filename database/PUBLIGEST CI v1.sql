-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 01 mai 2025 à 11:07
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `PUBLIGEST_CI`
--

-- --------------------------------------------------------

--
-- Structure de la table `CANDIDAT`
--

CREATE TABLE `CANDIDAT` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `prenoms` varchar(100) NOT NULL,
  `sexe` varchar(10) DEFAULT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(100) NOT NULL,
  `nationalite` varchar(50) NOT NULL,
  `situation_matrimoniale` varchar(50) DEFAULT NULL,
  `telephone_principal` varchar(20) DEFAULT NULL,
  `telephone_secondaire` varchar(20) DEFAULT NULL,
  `num_identification` varchar(100) DEFAULT NULL,
  `type_piece` varchar(50) DEFAULT NULL,
  `num_piece` varchar(100) DEFAULT NULL,
  `expiration_piece` date DEFAULT NULL,
  `adresse_postale` varchar(150) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `departement` varchar(100) DEFAULT NULL,
  `commune` varchar(100) DEFAULT NULL,
  `lieu_residence` varchar(100) DEFAULT NULL,
  `type_candidat` varchar(50) DEFAULT NULL,
  `num_inscription` varchar(100) DEFAULT NULL,
  `permis` tinyint(1) DEFAULT NULL,
  `type_permis` varchar(50) DEFAULT NULL,
  `handicap` tinyint(1) DEFAULT NULL,
  `nom_pere` varchar(100) DEFAULT NULL,
  `nom_mere` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `civilite` varchar(10) DEFAULT NULL,
  `genre` varchar(10) DEFAULT NULL,
  `photo_identite` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `CANDIDAT`
--

INSERT INTO `CANDIDAT` (`id`, `utilisateur_id`, `nom`, `prenoms`, `sexe`, `date_naissance`, `lieu_naissance`, `nationalite`, `situation_matrimoniale`, `telephone_principal`, `telephone_secondaire`, `num_identification`, `type_piece`, `num_piece`, `expiration_piece`, `adresse_postale`, `region`, `departement`, `commune`, `lieu_residence`, `type_candidat`, `num_inscription`, `permis`, `type_permis`, `handicap`, `nom_pere`, `nom_mere`, `created_at`, `updated_at`, `civilite`, `genre`, `photo_identite`) VALUES
(2, 2, 'kouame', 'israel', 'M', '2025-04-15', 'guiglo', 'ivoirienne', 'Célibataire', '0566720763', '0566720763', NULL, 'Passeport', '23INP00246', '2025-04-22', 'lk', 'guiglo', 'yakro', 'YAKRO', 'ad', 'Externe', 'CAND-20250423-68093C2F97E9E', 1, 'B', 1, 'RASAQ', 'ILHYAS', '2025-04-23 19:14:55', '2025-04-23 19:14:55', NULL, NULL, NULL),
(3, 3, 'kouame', 'israel', 'M', '2025-04-15', 'guiglo', 'ivoirienne', 'Célibataire', '0566720763', '0566720763', NULL, 'Passeport', '23INP00246', '2025-04-22', 'lk', 'guiglo', 'yakro', 'YAKRO', 'ad', 'Externe', 'CAND-20250423-68093D8CCFA69', 1, 'B', 1, 'RASAQ', 'ILHYAS', '2025-04-23 19:20:44', '2025-04-23 19:20:44', NULL, NULL, NULL),
(4, 4, 'kouame', 'israel', 'M', '2025-04-02', 'k', 'ivoirienne', 'Marié(e)', '0566720763', '0566720763', NULL, 'Passeport', '23INP00246', '2025-04-12', 'lk', 'guiglo', 'yakro', 'YAKRO', 'yakro', 'Interne', 'CAND-20250423-680945AAC8ED2', 1, 'C', 1, 'israel', 'kouame', '2025-04-23 19:55:22', '2025-04-23 19:55:22', NULL, NULL, NULL),
(5, 5, 'ILHYAS', 'RASAQ', 'M', '2025-04-25', 'k', 'ivoirienne', 'Divorcé(e)', '0501357844', '0566720763', NULL, 'Passeport', '23INP00246', '2025-05-06', 'INP HB', 'guiglo', 'yakro', 'YAKRO', 'yakro', 'Externe', 'CAND-20250423-680968561076F', 1, 'A', 1, 'RASAQ', 'ILHYAS', '2025-04-23 22:23:18', '2025-04-23 22:23:18', NULL, NULL, NULL),
(6, 8, 'kouame ', 'israel pierre n\'godio junior', 'M', '2006-05-01', 'guiglo', 'ivoirienne', 'Célibataire', '0566720763', '0709684792', NULL, 'CNI', '23INP00246', '2029-11-29', '00225', 'guiglo', 'yakro', 'YAKRO', 'yakro', 'Interne', NULL, 0, '', 0, 'kouame kouame pierre ', 'kouakou adjo cecile', '2025-04-25 16:56:31', '2025-04-25 16:56:31', NULL, NULL, NULL),
(7, 9, 'kouakou', 'konan bertin', 'M', '2025-04-19', 'guiglo', 'ivoirienne', 'Célibataire', '0566720763', '0709684792', NULL, 'CNI', '23INP00246', '2028-08-25', 'lk', 'guiglo', 'yakro', 'YAKRO', 'yakro', 'Interne', 'CAND-20250425-680BC3F2E2EB8', 0, '', 0, 'kouame kouame pierre ', 'kouakou adjo ', '2025-04-25 17:18:42', '2025-04-25 17:18:42', NULL, NULL, NULL),
(8, 10, 'kouamekkkk', 'israeljhbib', 'M', '2025-05-01', 'guiglo', 'ivoirienne', 'Marié(e)', '0566720763', '0566720763', NULL, 'CNI', '23INP00246', '2025-04-29', 'lk', 'guiglo', 'yakro', 'YAKRO', 'yakro', 'Externe', 'CAND-20250425-680BC749C8B9F', 1, 'B', 1, 'RASAQ', 'ILHYAS', '2025-04-25 17:32:57', '2025-04-25 17:32:57', NULL, NULL, NULL),
(9, 11, 'kouame', 'israel', 'M', '2025-04-16', 'guiglo', 'ivoirienne', 'Marié(e)', '0566720763', '0566720763', NULL, 'Passeport', '23INP00246', '2025-04-30', 'lk', 'guiglo', 'yakro', 'YAKRO', 'ad', 'Externe', 'CAND-20250425-680BD12423D25', 1, 'B', 1, 'israel', 'kouame', '2025-04-25 18:15:00', '2025-04-25 18:15:00', NULL, NULL, NULL),
(10, 13, 'kouakou', 'amoin geraldine larissa', 'F', '2004-07-14', 'akekro', 'ivoirienne', 'Célibataire', '0709684792', '0566720763', NULL, 'CNI', '23INP00246', '2029-07-31', '00225FHB', 'ABIDJAN', 'COCODY', 'COCODY', 'UNIVERSITE', 'Interne', 'CAND-20250426-680D4029744D4', 0, '', 0, 'kouakou thierry', 'kouakou veronique', '2025-04-26 20:20:57', '2025-04-26 20:20:57', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `CENTRE_EXAMEN`
--

CREATE TABLE `CENTRE_EXAMEN` (
  `id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `ville` varchar(100) NOT NULL,
  `lieu` varchar(150) NOT NULL,
  `capacite` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `CENTRE_EXAMEN`
--

INSERT INTO `CENTRE_EXAMEN` (`id`, `session_id`, `ville`, `lieu`, `capacite`, `created_at`) VALUES
(4, 6, 'YAKRO2', 'a cote de la ville', 100, '2025-04-25 00:06:38'),
(5, 7, '23deq', 'a cote de guigolo', 100, '2025-04-25 17:42:04'),
(6, 8, 'abidjan', 'lycee technique d\'abidjan', 50, '2025-04-25 19:57:54'),
(7, 9, 'Abidjan', 'COCODY LYCEE CLASSIQUE', 200, '2025-04-26 20:27:31');

-- --------------------------------------------------------

--
-- Structure de la table `CONCOURS`
--

CREATE TABLE `CONCOURS` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `niveau_requis` varchar(50) DEFAULT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `ministere` varchar(150) DEFAULT NULL,
  `domaine_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `CONCOURS`
--

INSERT INTO `CONCOURS` (`id`, `nom`, `description`, `niveau_requis`, `categorie`, `ministere`, `domaine_id`, `created_at`) VALUES
(2, 'AGENT TECHNIQUE D\'IMPRIMERIE	', 'comptablek', 'BAC', 'B3', 'DIRECTION DES CONCOURS (MFP)', 5, '2025-04-24 16:44:03'),
(3, 'test', 'test', 'test', 'test', 'test', 2, '2025-04-24 20:09:29'),
(4, 'testaaaa', '', '', '', '', 6, '2025-04-24 21:08:01'),
(7, 'ARCHITECTE', 'construction de batiment dans le domaine publique', 'license en BTP', 'D1+', 'MFP - DIRECTION DES CONCOURS', 9, '2025-04-25 19:56:11'),
(8, 'COMMISSAIRES DE POLICE	', 'grade ajeudant du commisarriat general de GUIGLO', 'BAC', 'D1+', 'MINISTERE DE L\'INTERIEUR ET DE LA SECURITE', 2, '2025-04-26 20:24:54');

-- --------------------------------------------------------

--
-- Structure de la table `DIPLOME`
--

CREATE TABLE `DIPLOME` (
  `id` int(11) NOT NULL,
  `candidat_id` int(11) DEFAULT NULL,
  `nom` varchar(150) DEFAULT NULL,
  `niveau` varchar(50) DEFAULT NULL,
  `annee_obtention` int(11) DEFAULT NULL,
  `etablissement` varchar(150) DEFAULT NULL,
  `scan_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `DIPLOME`
--

INSERT INTO `DIPLOME` (`id`, `candidat_id`, `nom`, `niveau`, `annee_obtention`, `etablissement`, `scan_url`, `created_at`) VALUES
(1, 2, 'israel kouame', 'BAC+2', 2006, 'lmz', 'uploads/documents/2_diplome_1745435695.jpg', '2025-04-23 19:14:55'),
(2, 3, 'israel kouame', 'BAC+2', 2006, 'lmz', 'uploads/documents/3_diplome_1745436044.jpg', '2025-04-23 19:20:44'),
(3, 4, 'BEPC', NULL, 2006, 'cmz', '4_diplome_0_1745438122.jpg', '2025-04-23 19:55:22'),
(4, 4, 'CEPE', NULL, 2010, 'cmz', '4_diplome_1_1745438122.pdf', '2025-04-23 19:55:22'),
(5, 5, 'BAC+1', NULL, 2006, 'cmz', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/5_diplome_0_1745446998.jpg', '2025-04-23 22:23:18'),
(6, 5, 'BEPC', NULL, 2200, 'cmz', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/5_diplome_1_1745446998.pdf', '2025-04-23 22:23:18'),
(9, 7, 'CEPE', NULL, 2016, 'lycee moderne zagne', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/7_diplome_0_1745601522.png', '2025-04-25 17:18:42'),
(10, 8, 'BEPC', NULL, 1, 'lycee moderne zagne', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/8_diplome_0_1745602377.pdf', '2025-04-25 17:32:57'),
(11, 9, 'BAC+1', NULL, 2000, 'lycee moderne zagne', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/9_diplome_0_1745604900.png', '2025-04-25 18:15:00'),
(13, 10, 'BAC', NULL, 2014, 'lycee moderne zagne', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/10_diplome_0_1745698857.pdf', '2025-04-26 20:20:57'),
(14, 10, 'BEPC', NULL, 2020, 'lycee moderne zagne', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/10_diplome_1_1745698857.png', '2025-04-26 20:20:57');

-- --------------------------------------------------------

--
-- Structure de la table `DOCUMENT`
--

CREATE TABLE `DOCUMENT` (
  `id` int(11) NOT NULL,
  `candidat_id` int(11) DEFAULT NULL,
  `type_document` varchar(100) DEFAULT NULL,
  `fichier_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `DOCUMENT`
--

INSERT INTO `DOCUMENT` (`id`, `candidat_id`, `type_document`, `fichier_url`, `created_at`) VALUES
(5, 2, 'Pièce d\'identité', 'uploads/documents/2_piece_identite_1745435695.jpg', '2025-04-23 19:14:55'),
(6, 2, 'Photo d\'identité', 'uploads/documents/2_photo_1745435695.jpg', '2025-04-23 19:14:55'),
(7, 2, 'CV', 'uploads/documents/2_cv_1745435695.pdf', '2025-04-23 19:14:55'),
(8, 3, 'Pièce d\'identité', 'uploads/documents/3_piece_identite_1745436044.jpg', '2025-04-23 19:20:44'),
(9, 3, 'Photo d\'identité', 'uploads/documents/3_photo_1745436044.jpg', '2025-04-23 19:20:44'),
(10, 3, 'CV', 'uploads/documents/3_cv_1745436044.pdf', '2025-04-23 19:20:44'),
(11, 4, 'Carte d\'identité', '4_piece_identite_1745438122.jpg', '2025-04-23 19:55:22'),
(12, 4, 'CMU', '4_cmu_1745438122.jpg', '2025-04-23 19:55:22'),
(13, 4, 'Extrait de naissance', '4_extrait_naissance_1745438122.jpg', '2025-04-23 19:55:22'),
(14, 4, 'Photo d\'identité', '4_photo_1745438122.jpg', '2025-04-23 19:55:22'),
(15, 5, 'Carte d\'identité', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/5_piece_identite_1745446998.jpg', '2025-04-23 22:23:18'),
(16, 5, 'CMU', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/5_cmu_1745446998.jpg', '2025-04-23 22:23:18'),
(17, 5, 'Extrait de naissance', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/5_extrait_naissance_1745446998.pdf', '2025-04-23 22:23:18'),
(18, 5, 'Photo d\'identité', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/5_photo_1745446998.jpg', '2025-04-23 22:23:18'),
(29, 5, 'Autre', 'uploads/documents/680b90a041de6.pdf', '2025-04-25 13:39:44'),
(30, 5, 'visite medical', 'uploads/documents/680b92af80d3d.png', '2025-04-25 13:48:31'),
(31, 5, 'Attestation', 'uploads/documents/680b92d08a098.pdf', '2025-04-25 13:49:04'),
(33, 7, 'Carte d\'identité', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/7_piece_identite_1745601522.png', '2025-04-25 17:18:42'),
(34, 7, 'CMU', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/7_cmu_1745601522.pdf', '2025-04-25 17:18:42'),
(35, 7, 'Extrait de naissance', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/7_extrait_naissance_1745601522.pdf', '2025-04-25 17:18:42'),
(36, 7, 'Photo d\'identité', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/7_photo_1745601522.jpeg', '2025-04-25 17:18:42'),
(37, 8, 'Carte d\'identité', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/8_piece_identite_1745602377.pdf', '2025-04-25 17:32:57'),
(38, 8, 'CMU', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/8_cmu_1745602377.jpg', '2025-04-25 17:32:57'),
(39, 8, 'Extrait de naissance', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/8_extrait_naissance_1745602377.jpg', '2025-04-25 17:32:57'),
(40, 8, 'Photo d\'identité', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/8_photo_1745602377.jpg', '2025-04-25 17:32:57'),
(41, 9, 'Carte d\'identité', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/9_piece_identite_1745604900.jpg', '2025-04-25 18:15:00'),
(42, 9, 'CMU', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/9_cmu_1745604900.jpg', '2025-04-25 18:15:00'),
(43, 9, 'Extrait de naissance', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/9_extrait_naissance_1745604900.jpg', '2025-04-25 18:15:00'),
(44, 9, 'Photo d\'identité', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/9_photo_1745604900.jpeg', '2025-04-25 18:15:00'),
(45, 7, 'convocation', 'convocation_kouakou_konan bertin_test.pdf', '2025-04-25 19:27:37'),
(46, 7, 'convocation', 'uploads/convocations/convocation_7_1745609313.pdf', '2025-04-25 19:28:33'),
(47, 7, 'convocation', 'convocation_kouakou_konan bertin_test.pdf', '2025-04-25 20:41:17'),
(48, 7, 'convocation', 'uploads/documents/convocation_7_1745613729.pdf', '2025-04-25 20:42:09'),
(49, 7, 'convocation', 'convocation_kouakou_konan bertin_test.pdf', '2025-04-25 21:37:55'),
(50, 7, 'convocation', 'convocation_kouakou_konan bertin_test.pdf', '2025-04-26 12:17:08'),
(51, 7, 'convocation', 'convocation_kouakou_konan bertin_test.pdf', '2025-04-26 12:17:17'),
(53, 10, 'Carte d\'identité', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/10_piece_identite_1745698857.jpeg', '2025-04-26 20:20:57'),
(54, 10, 'CMU', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/10_cmu_1745698857.png', '2025-04-26 20:20:57'),
(55, 10, 'Extrait de naissance', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/10_extrait_naissance_1745698857.pdf', '2025-04-26 20:20:57'),
(56, 10, 'Photo d\'identité', '/Users/admin/Documents/Kouame_Israël_projet_web1/src/php/../../uploads/documents/10_photo_1745698857.png', '2025-04-26 20:20:57'),
(57, 10, 'convocation', 'convocation_kouakou_amoin geraldine larissa_COMMISSAIRES DE POLICE	.pdf', '2025-04-26 20:34:09'),
(58, 10, 'convocation', 'uploads/documents/convocation_10_1745699676.pdf', '2025-04-26 20:34:36'),
(59, 10, 'convocation', 'convocation_kouakou_amoin geraldine larissa_COMMISSAIRES DE POLICE	.pdf', '2025-05-01 08:55:44'),
(60, 10, 'convocation', 'convocation_kouakou_amoin geraldine larissa_COMMISSAIRES DE POLICE	.pdf', '2025-05-01 08:57:28'),
(61, 10, 'convocation', 'uploads/documents/convocation_10_1746089874.pdf', '2025-05-01 08:57:54'),
(62, 10, 'convocation', 'convocation_kouakou_amoin geraldine larissa_COMMISSAIRES DE POLICE	.pdf', '2025-05-01 09:02:40'),
(63, 10, 'convocation', 'uploads/documents/convocation_10_1746090163.pdf', '2025-05-01 09:02:43');

-- --------------------------------------------------------

--
-- Structure de la table `DOMAINE`
--

CREATE TABLE `DOMAINE` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `DOMAINE`
--

INSERT INTO `DOMAINE` (`id`, `nom`, `description`, `created_at`) VALUES
(2, 'CONCOUR DIRECT', 'Peut faire acte de candidature tout ivoirien diplômé à la recherche d\'un emploi et remplissant les conditions de candidature figurant dans le communiqué ou l\'arrêté d\'ouverture du concours visé.,', '2025-04-24 15:42:27'),
(3, 'CONCOUR RECRUTEMENT', 'Peut faire acte de candidature tout ivoirien diplômé à la recherche d\'un emploi et remplissant les conditions de candidature figurant dans le communiqué d\'ouverture du concours visé.\r\n', '2025-04-24 16:41:54'),
(5, 'test', 'test', '2025-04-24 20:08:07'),
(6, 'test2', 's', '2025-04-25 00:01:19'),
(9, 'recrutement', 'recrutement de cadre dans des domaines specifiques ', '2025-04-25 19:55:15');

-- --------------------------------------------------------

--
-- Structure de la table `INSCRIPTION`
--

CREATE TABLE `INSCRIPTION` (
  `id` int(11) NOT NULL,
  `candidat_id` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `centre_id` int(11) DEFAULT NULL,
  `date_inscription` date NOT NULL,
  `statut` enum('valide','en_attente','rejete') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `INSCRIPTION`
--

INSERT INTO `INSCRIPTION` (`id`, `candidat_id`, `session_id`, `centre_id`, `date_inscription`, `statut`, `created_at`) VALUES
(2, 7, 6, 4, '2025-04-25', 'valide', '2025-04-25 17:56:14'),
(3, 5, 7, 5, '2025-04-25', 'rejete', '2025-04-25 18:49:27'),
(4, 7, 8, 6, '2025-04-25', 'valide', '2025-04-25 19:58:43'),
(5, 10, 9, 7, '2025-04-26', 'valide', '2025-04-26 20:28:12');

-- --------------------------------------------------------

--
-- Structure de la table `PAIEMENT`
--

CREATE TABLE `PAIEMENT` (
  `id` int(11) NOT NULL,
  `inscription_id` int(11) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `mode_paiement` varchar(50) DEFAULT NULL,
  `date_paiement` date NOT NULL,
  `statut` enum('valide','en_attente','echoue') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `PAIEMENT`
--

INSERT INTO `PAIEMENT` (`id`, `inscription_id`, `montant`, `mode_paiement`, `date_paiement`, `statut`, `created_at`) VALUES
(2, 2, 20000.00, 'Orange Money', '2025-04-25', 'valide', '2025-04-25 19:22:37'),
(3, 4, 50000.00, 'MTN Mobile Money', '2025-04-25', 'valide', '2025-04-25 19:59:05'),
(4, 5, 30000.00, 'MTN Mobile Money', '2025-04-26', 'valide', '2025-04-26 20:30:30');

-- --------------------------------------------------------

--
-- Structure de la table `RESULTAT`
--

CREATE TABLE `RESULTAT` (
  `id` int(11) NOT NULL,
  `inscription_id` int(11) DEFAULT NULL,
  `note` decimal(5,2) DEFAULT NULL,
  `decision` enum('admis','rejete','en_attente') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `RESULTAT`
--

INSERT INTO `RESULTAT` (`id`, `inscription_id`, `note`, `decision`, `created_at`) VALUES
(2, 2, 300.00, 'admis', '2025-04-25 19:36:32'),
(3, 5, 500.00, 'admis', '2025-04-26 20:37:25');

-- --------------------------------------------------------

--
-- Structure de la table `SESSION_CONCOURS`
--

CREATE TABLE `SESSION_CONCOURS` (
  `id` int(11) NOT NULL,
  `concours_id` int(11) DEFAULT NULL,
  `date_ouverture` date NOT NULL,
  `date_cloture` date NOT NULL,
  `nb_places` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `SESSION_CONCOURS`
--

INSERT INTO `SESSION_CONCOURS` (`id`, `concours_id`, `date_ouverture`, `date_cloture`, `nb_places`, `created_at`) VALUES
(6, 3, '2025-04-10', '2025-04-30', 1000, '2025-04-25 00:04:41'),
(7, 4, '2025-04-29', '2025-05-14', 100, '2025-04-25 17:41:27'),
(8, 7, '2025-04-25', '2025-05-10', 50, '2025-04-25 19:57:00'),
(9, 8, '2025-04-26', '2025-04-27', 500, '2025-04-26 20:25:53');

-- --------------------------------------------------------

--
-- Structure de la table `UTILISATEUR`
--

CREATE TABLE `UTILISATEUR` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `login` varchar(50) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','candidat') DEFAULT 'candidat',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `UTILISATEUR`
--

INSERT INTO `UTILISATEUR` (`id`, `email`, `login`, `mot_de_passe`, `role`, `created_at`, `updated_at`) VALUES
(2, 'ose.ado23@inphb.ci', NULL, '$2y$12$R9HzsipyR5MCVrhHP5u.QuO4qbNESFdF/NZPd4UCJnOY6FLV.mCDC', 'candidat', '2025-04-23 19:14:55', '2025-04-23 19:14:55'),
(3, 'bahouaelyse@gmail.com', NULL, '$2y$12$pf3lSLcSYIhYc2kFGUEQ.ufTnxwZM54I2LKTs7ds2AkZiSzw5Oguq', 'candidat', '2025-04-23 19:20:44', '2025-04-23 19:20:44'),
(4, 'rasaq.ilhyas23@inphb.ci', NULL, '$2y$12$tsc1UfI789l4xXP0muJrvey9LSMN/pd3536nnRdXWbnD4IGnHF.2W', 'candidat', '2025-04-23 19:55:22', '2025-04-23 19:55:22'),
(5, 'carola.ediawo@gmail.com', NULL, '$2y$12$HWoiqU7TO/eSosohX5bTN.5ncQi/KbPODBPEMgacjX71DcqY2iVYO', 'candidat', '2025-04-23 22:23:18', '2025-04-23 22:23:18'),
(7, 'Ureon2006@gmail.com', NULL, '$2y$12$4SujMms8Q.0kIFR8mc.jiOwkArodbxi03SgkFas1ULea5aiV4dY66', 'admin', '2025-04-23 22:58:21', '2025-04-23 22:58:21'),
(8, 'kouameisrael2006@gmail.com', NULL, '$2y$12$.DaSfePuH6hyd2KiBdkE4unVAPhsmXFxNlyX9g2Nw9ClhJ2nXwnvy', 'candidat', '2025-04-25 16:56:31', '2025-04-25 16:56:31'),
(9, 'kouameisrael20006@gmail.com', NULL, '$2y$12$k5NqbpAODF6Gdd4IS7zzlOBSMXsfwaOOpz9duhd2K1XKfPyINdsG6', 'candidat', '2025-04-25 17:18:42', '2025-04-25 17:18:42'),
(10, 'test@gmail.com', NULL, '$2y$12$xgcBTbXqF6lZ4fxZosA5zujn26VReW5QVf9Uslc2WSqXVBvY1mtei', 'candidat', '2025-04-25 17:32:57', '2025-04-25 17:32:57'),
(11, 'qwerty@gmail.com', NULL, '$2y$12$okFtzrhs6Y.HA1zG9R62zuzX6kvzBQi5Fk6PRXVeHU8aWrBb/iALO', 'candidat', '2025-04-25 18:15:00', '2025-04-25 18:15:00'),
(12, 'noelliekouame@inphb.ci', NULL, '$2y$12$uRVgvbtR0KG0Ih1BdPqck.K0Pg4MVIPHFX14DEqFL/B82wlExcN3W', 'admin', '2025-04-26 12:25:10', '2025-04-26 12:25:10'),
(13, 'kouakougeraldine@gmail.com', NULL, '$2y$12$g7WToZAIN6iByD5ZtiaJs.e/XGPNs9YFhPUaKzqxNY7urUhj4yyVK', 'candidat', '2025-04-26 20:20:57', '2025-04-26 20:20:57');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `CANDIDAT`
--
ALTER TABLE `CANDIDAT`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `CENTRE_EXAMEN`
--
ALTER TABLE `CENTRE_EXAMEN`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`);

--
-- Index pour la table `CONCOURS`
--
ALTER TABLE `CONCOURS`
  ADD PRIMARY KEY (`id`),
  ADD KEY `domaine_id` (`domaine_id`);

--
-- Index pour la table `DIPLOME`
--
ALTER TABLE `DIPLOME`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidat_id` (`candidat_id`);

--
-- Index pour la table `DOCUMENT`
--
ALTER TABLE `DOCUMENT`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidat_id` (`candidat_id`);

--
-- Index pour la table `DOMAINE`
--
ALTER TABLE `DOMAINE`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `INSCRIPTION`
--
ALTER TABLE `INSCRIPTION`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidat_id` (`candidat_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `centre_id` (`centre_id`);

--
-- Index pour la table `PAIEMENT`
--
ALTER TABLE `PAIEMENT`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inscription_id` (`inscription_id`);

--
-- Index pour la table `RESULTAT`
--
ALTER TABLE `RESULTAT`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inscription_id` (`inscription_id`);

--
-- Index pour la table `SESSION_CONCOURS`
--
ALTER TABLE `SESSION_CONCOURS`
  ADD PRIMARY KEY (`id`),
  ADD KEY `concours_id` (`concours_id`);

--
-- Index pour la table `UTILISATEUR`
--
ALTER TABLE `UTILISATEUR`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `CANDIDAT`
--
ALTER TABLE `CANDIDAT`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `CENTRE_EXAMEN`
--
ALTER TABLE `CENTRE_EXAMEN`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `CONCOURS`
--
ALTER TABLE `CONCOURS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `DIPLOME`
--
ALTER TABLE `DIPLOME`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `DOCUMENT`
--
ALTER TABLE `DOCUMENT`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT pour la table `DOMAINE`
--
ALTER TABLE `DOMAINE`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `INSCRIPTION`
--
ALTER TABLE `INSCRIPTION`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `PAIEMENT`
--
ALTER TABLE `PAIEMENT`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `RESULTAT`
--
ALTER TABLE `RESULTAT`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `SESSION_CONCOURS`
--
ALTER TABLE `SESSION_CONCOURS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `UTILISATEUR`
--
ALTER TABLE `UTILISATEUR`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `CANDIDAT`
--
ALTER TABLE `CANDIDAT`
  ADD CONSTRAINT `candidat_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `UTILISATEUR` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `CENTRE_EXAMEN`
--
ALTER TABLE `CENTRE_EXAMEN`
  ADD CONSTRAINT `centre_examen_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `SESSION_CONCOURS` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `CONCOURS`
--
ALTER TABLE `CONCOURS`
  ADD CONSTRAINT `concours_ibfk_1` FOREIGN KEY (`domaine_id`) REFERENCES `DOMAINE` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `DIPLOME`
--
ALTER TABLE `DIPLOME`
  ADD CONSTRAINT `diplome_ibfk_1` FOREIGN KEY (`candidat_id`) REFERENCES `CANDIDAT` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `DOCUMENT`
--
ALTER TABLE `DOCUMENT`
  ADD CONSTRAINT `document_ibfk_1` FOREIGN KEY (`candidat_id`) REFERENCES `CANDIDAT` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `INSCRIPTION`
--
ALTER TABLE `INSCRIPTION`
  ADD CONSTRAINT `inscription_ibfk_1` FOREIGN KEY (`candidat_id`) REFERENCES `CANDIDAT` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscription_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `SESSION_CONCOURS` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscription_ibfk_3` FOREIGN KEY (`centre_id`) REFERENCES `CENTRE_EXAMEN` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `PAIEMENT`
--
ALTER TABLE `PAIEMENT`
  ADD CONSTRAINT `paiement_ibfk_1` FOREIGN KEY (`inscription_id`) REFERENCES `INSCRIPTION` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `RESULTAT`
--
ALTER TABLE `RESULTAT`
  ADD CONSTRAINT `resultat_ibfk_1` FOREIGN KEY (`inscription_id`) REFERENCES `INSCRIPTION` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `SESSION_CONCOURS`
--
ALTER TABLE `SESSION_CONCOURS`
  ADD CONSTRAINT `session_concours_ibfk_1` FOREIGN KEY (`concours_id`) REFERENCES `CONCOURS` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
