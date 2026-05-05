-- NutriSmart — Base de données
-- Réinitialisation complète

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `liste_courses`;
DROP TABLE IF EXISTS `stock`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `stock` (
  `id`              INT(11)      NOT NULL AUTO_INCREMENT,
  `type`            VARCHAR(50)  NOT NULL,
  `produits`        VARCHAR(255) NOT NULL,
  `date_expiration` DATE         NOT NULL,
  `seuil_minimum`   FLOAT        NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `liste_courses` (
  `id`                 INT(11)      NOT NULL AUTO_INCREMENT,
  `articles_a_acheter` VARCHAR(255) NOT NULL,
  `budget`             FLOAT        NOT NULL DEFAULT 0,
  `date_creation`      DATE         NOT NULL,
  `stock_id`           INT(11)      DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`stock_id`) REFERENCES `stock`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- Données de test
INSERT INTO `stock` (`type`, `produits`, `date_expiration`, `seuil_minimum`) VALUES
('Légumes',           'Tomates cerises',   '2026-08-01', 5),
('Produits laitiers', 'Lait demi-écrémé',  '2026-07-10', 3),
('Céréales',          'Quinoa bio',         '2026-12-01', 2);

INSERT INTO `liste_courses` (`articles_a_acheter`, `budget`, `date_creation`, `stock_id`) VALUES
('Tomates, lait, pain', 120.00, '2026-04-13', 1),
('Yaourts, fromage',     85.50, '2026-04-12', 2);
