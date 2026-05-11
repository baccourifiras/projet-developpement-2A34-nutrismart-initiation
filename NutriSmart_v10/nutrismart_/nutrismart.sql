-- ============================================================
-- NutriSmart — Base de données : Gestion des Utilisateurs
-- À importer dans phpMyAdmin ou via la commande :
--   mysql -u root -p nutrismart < nutrismart_utilisateurs.sql
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `nutrismart`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `nutrismart`;

-- ============================================================
-- Table : utilisateur
-- ============================================================

DROP TABLE IF EXISTS `historique`;
DROP TABLE IF EXISTS `utilisateur`;

CREATE TABLE `utilisateur` (
  `id_user`          INT(11)      NOT NULL AUTO_INCREMENT,
  `nom`              VARCHAR(50)  NOT NULL,
  `prenom`           VARCHAR(50)  NOT NULL,
  `email`            VARCHAR(100) NOT NULL,
  `mot_de_passe`     VARCHAR(255) NOT NULL COMMENT 'Mot de passe en clair',
  `role`             ENUM('admin','nutritionniste','client') NOT NULL DEFAULT 'client',
  `provider_login`   ENUM('google','facebook','local')      NOT NULL DEFAULT 'local',
  `face_descriptor`  TEXT         DEFAULT NULL COMMENT 'Descripteur facial JSON (128 valeurs float, face-api.js)',
  `date_inscription` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- Table : historique
-- ============================================================

CREATE TABLE `historique` (
  `id_historique` INT(11)      NOT NULL AUTO_INCREMENT,
  `id_user`       INT(11)      DEFAULT NULL,
  `action`        VARCHAR(100) DEFAULT NULL,
  `date_action`   DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `navigateur`    VARCHAR(150) DEFAULT NULL,
  `statut`        ENUM('succes','echec') NOT NULL DEFAULT 'succes',
  `email_tente`   VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`id_historique`),
  FOREIGN KEY (`id_user`) REFERENCES `utilisateur`(`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- Données de démonstration
-- ============================================================

INSERT INTO `utilisateur`
  (`nom`, `prenom`, `email`, `mot_de_passe`, `role`, `provider_login`, `date_inscription`)
VALUES
  ('Sahari',         'Zayneb',    'zayneb.sahari@gmail.com',    'Zeineb25',   'admin',          'local',    '2026-01-01 07:00:00'),
  ('Administrateur', 'NutriSmart','admin@nutrismart.tn',        'Admin1234',  'admin',          'local',    '2026-01-01 08:00:00'),
  ('Ben Salem',      'Sana',      'sana.bensalem@mail.tn',      'Admin1234',  'nutritionniste', 'local',    '2026-02-10 10:30:00'),
  ('Trabelsi',       'Ahmed',     'ahmed.trabelsi@gmail.com',   'Admin1234',  'client',         'google',   '2026-03-05 14:20:00'),
  ('Miled',          'Rania',     'rania.miled@mail.tn',        'Admin1234',  'client',         'facebook', '2026-03-18 09:00:00'),
  ('Chaabane',       'Youssef',   'youssef.chaabane@mail.tn',   'Admin1234',  'nutritionniste', 'local',    '2026-04-01 11:15:00');

COMMIT;

-- ============================================================
-- Table : bilan_sante (Mini-chatbot post-connexion)
-- ============================================================

CREATE TABLE IF NOT EXISTS `bilan_sante` (
  `id_bilan`       INT(11)      NOT NULL AUTO_INCREMENT,
  `id_user`        INT(11)      NOT NULL,
  `date_bilan`     DATE         NOT NULL,
  `fatigue`        TINYINT(1)   DEFAULT NULL COMMENT '1=épuisé 2=fatigué 3=normal 4=bien 5=excellent',
  `humeur`         TINYINT(1)   DEFAULT NULL COMMENT '1=triste 2=stressé 3=neutre 4=bien 5=heureux',
  `hydratation`    TINYINT(1)   DEFAULT NULL COMMENT '1=pas du tout 2=peu 3=moyen 4=bien 5=très bien',
  `appetit`        TINYINT(1)   DEFAULT NULL COMMENT '1=aucun 2=faible 3=normal 4=bon 5=très bon',
  `sommeil`        TINYINT(1)   DEFAULT NULL COMMENT '1=très mal 2=mal 3=moyen 4=bien 5=très bien',
  `conseil_genere` TEXT         DEFAULT NULL,
  PRIMARY KEY (`id_bilan`),
  UNIQUE KEY `unique_user_day` (`id_user`, `date_bilan`),
  FOREIGN KEY (`id_user`) REFERENCES `utilisateur`(`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

