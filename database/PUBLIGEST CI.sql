-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mer. 23 avr. 2025 à 17:18
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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Structure de la table `DOMAINE`
--

CREATE TABLE `DOMAINE` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Structure de la table `UTILISATEUR`
--

CREATE TABLE `UTILISATEUR` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','candidat') DEFAULT 'candidat',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `CANDIDAT`
--
ALTER TABLE `CANDIDAT`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `CENTRE_EXAMEN`
--
ALTER TABLE `CENTRE_EXAMEN`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `CONCOURS`
--
ALTER TABLE `CONCOURS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `DIPLOME`
--
ALTER TABLE `DIPLOME`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `DOCUMENT`
--
ALTER TABLE `DOCUMENT`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `DOMAINE`
--
ALTER TABLE `DOMAINE`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `INSCRIPTION`
--
ALTER TABLE `INSCRIPTION`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `PAIEMENT`
--
ALTER TABLE `PAIEMENT`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `RESULTAT`
--
ALTER TABLE `RESULTAT`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `SESSION_CONCOURS`
--
ALTER TABLE `SESSION_CONCOURS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `UTILISATEUR`
--
ALTER TABLE `UTILISATEUR`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
