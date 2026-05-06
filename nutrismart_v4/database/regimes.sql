-- =====================================================================
-- NutriSmart - CRUD Regime / Suivi_regime / Historique_recommandation
-- Base : nutrismart  (MariaDB / MySQL)
-- A importer dans phpMyAdmin apres avoir cree la base `nutrismart`.
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET NAMES utf8mb4 */;

-- ---------------------------------------------------------------------
-- Table `regime` (entite parente)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `regime` (
  `id_regime`       INT(11)      NOT NULL AUTO_INCREMENT,
  `type_regime`     ENUM('cut','bulk','equilibre') NOT NULL DEFAULT 'equilibre',
  `calories_cible`  INT(11)      NOT NULL,
  `date_debut`      DATE         NOT NULL,
  `poids_initial`   FLOAT        NOT NULL,
  `duree`           INT(11)      NOT NULL,
  PRIMARY KEY (`id_regime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Table `suivi_regime` : un regime peut avoir plusieurs suivis
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `suivi_regime` (
  `id_suivi`             INT(11) NOT NULL AUTO_INCREMENT,
  `id_regime`            INT(11) NOT NULL,
  `date`                 DATE    NOT NULL,
  `poids`                FLOAT   NOT NULL,
  `calories_consommees`  INT(11) NOT NULL,
  PRIMARY KEY (`id_suivi`),
  KEY `fk_suivi_regime` (`id_regime`),
  CONSTRAINT `fk_suivi_regime`
      FOREIGN KEY (`id_regime`) REFERENCES `regime` (`id_regime`)
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Table `historique_recommandation` : un regime -> plusieurs recommandations
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `historique_recommandation` (
  `id_historique`  INT(11) NOT NULL AUTO_INCREMENT,
  `id_regime`      INT(11) NOT NULL,
  `recommandation` TEXT    NOT NULL,
  `date`           DATE    NOT NULL,
  PRIMARY KEY (`id_historique`),
  KEY `fk_historique_regime` (`id_regime`),
  CONSTRAINT `fk_historique_regime`
      FOREIGN KEY (`id_regime`) REFERENCES `regime` (`id_regime`)
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Jeu de donnees de demonstration
-- ---------------------------------------------------------------------
INSERT INTO `regime` (`type_regime`, `calories_cible`, `date_debut`, `poids_initial`, `duree`) VALUES
('cut',       1800, '2026-01-15', 82.5, 60),
('bulk',      3000, '2026-02-01', 68.0, 90),
('equilibre', 2200, '2026-03-10', 75.0, 45);

INSERT INTO `suivi_regime` (`id_regime`, `date`, `poids`, `calories_consommees`) VALUES
(1, '2026-01-16', 82.3, 1780),
(1, '2026-01-20', 81.8, 1820),
(1, '2026-01-25', 81.1, 1750),
(2, '2026-02-02', 68.3, 3050),
(2, '2026-02-10', 69.2, 2980),
(3, '2026-03-11', 74.9, 2200);

INSERT INTO `historique_recommandation` (`id_regime`, `recommandation`, `date`) VALUES
(1, 'Reduire les glucides le soir pour optimiser la perte de gras.', '2026-01-17'),
(1, 'Ajouter 30 min de cardio 3 fois par semaine.',                   '2026-01-22'),
(2, 'Augmenter les proteines a 1.8g par kg de poids de corps.',       '2026-02-03'),
(2, 'Privilegier les glucides complexes apres l entrainement.',       '2026-02-12'),
(3, 'Maintenir un equilibre entre les 3 macronutriments.',            '2026-03-12');

COMMIT;
