-- ============================================================
-- NutriSmart - Module Planning de menus
-- À exécuter APRÈS notifications_module.sql
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- Table : planning_menu
-- Une entrée = 1 recette assignée à 1 date + 1 moment de la journée.
-- Contrainte unique sur (date, moment) : une seule recette par case.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `planning_menu` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT,
  `date_jour`     DATE         NOT NULL,
  `moment`        ENUM('petit_dej','dejeuner','diner','collation') NOT NULL DEFAULT 'dejeuner',
  `id_recette`    INT(11)      NOT NULL,
  `nb_personnes`  INT(11)      NOT NULL DEFAULT 2,
  `notes`         VARCHAR(255) DEFAULT NULL,
  `date_creation` DATETIME     NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_date_moment` (`date_jour`, `moment`),
  KEY `idx_planning_recette` (`id_recette`),
  KEY `idx_planning_date`    (`date_jour`),
  CONSTRAINT `fk_planning_recette`
    FOREIGN KEY (`id_recette`) REFERENCES `recettes`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- Données de démonstration : un planning pour la semaine en cours
-- Utilise des dates relatives à NOW() pour rester pertinent.
-- ------------------------------------------------------------
INSERT INTO `planning_menu` (`date_jour`, `moment`, `id_recette`, `nb_personnes`, `notes`) VALUES
  (CURDATE(),                            'dejeuner', 1, 4, 'Plat principal'),
  (CURDATE(),                            'diner',    3, 2, NULL),
  (DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'dejeuner', 4, 4, NULL),
  (DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'diner',    2, 2, 'Léger'),
  (DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'dejeuner', 3, 4, NULL),
  (DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'diner',    1, 2, NULL);

COMMIT;
