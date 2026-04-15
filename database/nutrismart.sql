-- ============================================================
-- NutriSmart — Base de données (version originale corrigée)
-- Compatible avec la structure du projet original
-- ============================================================

CREATE DATABASE IF NOT EXISTS `nutrismart`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `nutrismart`;

DROP TABLE IF EXISTS `liste_courses`;
DROP TABLE IF EXISTS `stock`;

-- Table stock SANS quantite et unite
CREATE TABLE `stock` (
  `id`              INT(11)      NOT NULL AUTO_INCREMENT,
  `type`            VARCHAR(50)  DEFAULT NULL,
  `produits`        VARCHAR(255) DEFAULT NULL,
  `date_expiration` DATE         DEFAULT NULL,
  `seuil_minimum`   FLOAT        DEFAULT NULL,
  `taux_gaspillage` FLOAT        DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table liste_courses
CREATE TABLE `liste_courses` (
  `id`                 INT(11)      NOT NULL AUTO_INCREMENT,
  `articles_a_acheter` VARCHAR(255) DEFAULT NULL,
  `budget`             FLOAT        DEFAULT NULL,
  `date_creation`      DATE         DEFAULT NULL,
  `stock_id`           INT(11)      DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_id` (`stock_id`),
  CONSTRAINT `lc_stock_fk`
    FOREIGN KEY (`stock_id`) REFERENCES `stock` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Données de démonstration
INSERT INTO `stock` (`type`, `produits`, `date_expiration`, `seuil_minimum`, `taux_gaspillage`) VALUES
('Légumes',           'Tomates cerises',   '2026-04-25', 5,  8.5),
('Produits laitiers', 'Lait demi-écrémé',  '2026-04-20', 3,  12.0),
('Céréales',          'Quinoa bio',         '2026-12-01', 2,  3.2),
('Fruits',            'Pommes golden',      '2026-04-28', 4,  15.0),
('Légumineuses',      'Lentilles vertes',   '2027-01-01', 1,  2.0);

INSERT INTO `liste_courses` (`articles_a_acheter`, `budget`, `date_creation`, `stock_id`) VALUES
('Tomates cerises, lait, pain complet', 120.00, '2026-04-13', 1),
('Yaourts nature, fromage blanc',        85.50, '2026-04-12', 2),
('Quinoa, lentilles, pois chiches',      95.00, '2026-04-11', 3);
