-- ============================================================
-- NutriSmart — Base de données : Gestion des Utilisateurs
-- À importer dans phpMyAdmin ou via la commande :
--   mysql -u root -p nutrismart < nutrismart_utilisateurs.sql
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- Créer la base si elle n'existe pas
CREATE DATABASE IF NOT EXISTS `nutrismart`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `nutrismart`;

-- ============================================================
-- Table : utilisateur
-- ============================================================

DROP TABLE IF EXISTS `utilisateur`;

CREATE TABLE `utilisateur` (
  `id_user`         INT(11)       NOT NULL AUTO_INCREMENT,
  `nom`             VARCHAR(50)   NOT NULL,
  `prenom`          VARCHAR(50)   NOT NULL,
  `email`           VARCHAR(100)  NOT NULL,
  `mot_de_passe`    VARCHAR(255)  NOT NULL,
  `role`            ENUM('admin','nutritionniste','client') NOT NULL DEFAULT 'client',
  `provider_login`  ENUM('google','facebook','local')      NOT NULL DEFAULT 'local',
  `date_inscription` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- Données de démonstration
-- Mots de passe hashés avec bcrypt (password_hash PHP)
-- Mot de passe original pour chaque utilisateur : Admin1234
-- ============================================================

INSERT INTO `utilisateur`
  (`nom`, `prenom`, `email`, `mot_de_passe`, `role`, `provider_login`, `date_inscription`)
VALUES
  ('Administrateur', 'NutriSmart', 'admin@nutrismart.tn',
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
   'admin', 'local', '2026-01-01 08:00:00'),

  ('Ben Salem', 'Sana', 'sana.bensalem@mail.tn',
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
   'nutritionniste', 'local', '2026-02-10 10:30:00'),

  ('Trabelsi', 'Ahmed', 'ahmed.trabelsi@gmail.com',
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
   'client', 'google', '2026-03-05 14:20:00'),

  ('Miled', 'Rania', 'rania.miled@mail.tn',
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
   'client', 'facebook', '2026-03-18 09:00:00'),

  ('Chaabane', 'Youssef', 'youssef.chaabane@mail.tn',
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
   'nutritionniste', 'local', '2026-04-01 11:15:00');

COMMIT;
