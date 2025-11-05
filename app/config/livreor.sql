-- fichier: livreor.sql
-- Crée la BDD et les tables pour le projet Livre d'or

CREATE DATABASE IF NOT EXISTS `livreor` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `livreor`;

-- table utilisateurs
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- table commentaires
CREATE TABLE IF NOT EXISTS `commentaires` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `commentaire` TEXT NOT NULL,
  `id_utilisateur` INT UNSIGNED NOT NULL,
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_user` (`id_utilisateur`),
  CONSTRAINT `commentaires_ibfk_user` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exemple d'utilisateur (mot de passe: demo123) -- hash PHP généré via password_hash
-- Remplacez le hash si vous voulez un mot de passe différent
INSERT INTO `utilisateurs` (`login`, `password`) VALUES ('demo', '$2y$10$EXAMPLEHASHPLACEHOLDERxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

-- Exemple de commentaire (à supprimer si nécessaire)
-- INSERT INTO `commentaires` (`commentaire`, `id_utilisateur`) VALUES ('Bonjour, voici un super site !', 1);