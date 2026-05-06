-- ============================================================
-- NutriSmart - Module Notifications (Basique)
-- Script d'extension - À exécuter APRÈS recettes_module.sql
--
-- Crée la table `notifications` qui stocke les alertes de stock
-- bas. Les notifications sont générées automatiquement par le
-- Model Ingredient::checkStockAlerts().
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- Table : notifications
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT,
  `id_ingredient` INT(11)      NOT NULL,
  `message`       VARCHAR(255) NOT NULL,
  `est_lue`       TINYINT(1)   NOT NULL DEFAULT 0,
  `date_creation` DATETIME     NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notif_lue`         (`est_lue`),
  KEY `idx_notif_ingredient`  (`id_ingredient`),
  KEY `idx_notif_date`        (`date_creation`),
  CONSTRAINT `fk_notif_ingredient`
    FOREIGN KEY (`id_ingredient`) REFERENCES `ingredients`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- Seuil de stock critique (en dessous : alerte)
-- On utilise une table simple pour pouvoir modifier le seuil
-- depuis du code sans toucher la structure.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `parametres` (
  `cle`    VARCHAR(50)  NOT NULL,
  `valeur` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `parametres` (`cle`, `valeur`) VALUES
  ('stock_seuil_critique', '5')
ON DUPLICATE KEY UPDATE valeur = VALUES(valeur);

COMMIT;
