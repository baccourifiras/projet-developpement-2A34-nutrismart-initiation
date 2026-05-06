-- ============================================================
-- NutriSmart - Module Recettes + Ingrédients
-- Script d'extension de la base existante `nutrismart`
--
-- À exécuter APRÈS nutrismart.sql sur la base `nutrismart`.
-- Crée 3 tables : recettes, ingredients, recette_ingredient (pivot)
-- Relation Many-to-Many avec quantité + unité dans le pivot.
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- Table : recettes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `recettes` (
  `id`             INT(11)      NOT NULL AUTO_INCREMENT,
  `nom`            VARCHAR(150) NOT NULL,
  `description`    TEXT         NOT NULL,
  `duree`          INT(11)      NOT NULL DEFAULT 0 COMMENT 'durée en minutes',
  `niveau`         ENUM('facile','moyen','difficile') NOT NULL DEFAULT 'facile',
  `image`          VARCHAR(255) DEFAULT NULL,
  `date_creation`  DATETIME     NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_recettes_nom`    (`nom`),
  KEY `idx_recettes_niveau` (`niveau`),
  KEY `idx_recettes_date`   (`date_creation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- Table : ingredients
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ingredients` (
  `id`              INT(11)      NOT NULL AUTO_INCREMENT,
  `nom`             VARCHAR(120) NOT NULL,
  `categorie`       VARCHAR(60)  NOT NULL DEFAULT 'Autre' COMMENT 'Légume, Fruit, Viande, Poisson, Céréale, Épice, Produit laitier, Autre',
  `quantite_stock`  DECIMAL(10,2) NOT NULL DEFAULT 0,
  `unite`           VARCHAR(20)  NOT NULL DEFAULT 'g' COMMENT 'g, kg, ml, L, unité, càs, càc, pincée',
  `date_ajout`      DATETIME     NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_ingredient_nom` (`nom`),
  KEY `idx_ingredients_categorie` (`categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- Table pivot : recette_ingredient
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `recette_ingredient` (
  `id_recette`    INT(11)       NOT NULL,
  `id_ingredient` INT(11)       NOT NULL,
  `quantite`      DECIMAL(10,2) NOT NULL DEFAULT 0,
  `unite`         VARCHAR(20)   NOT NULL DEFAULT 'g',
  PRIMARY KEY (`id_recette`,`id_ingredient`),
  KEY `idx_pivot_ingredient` (`id_ingredient`),
  CONSTRAINT `fk_pivot_recette`
    FOREIGN KEY (`id_recette`)    REFERENCES `recettes`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pivot_ingredient`
    FOREIGN KEY (`id_ingredient`) REFERENCES `ingredients`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- Données de démonstration
-- ------------------------------------------------------------
INSERT INTO `ingredients` (`nom`,`categorie`,`quantite_stock`,`unite`) VALUES
  ('Tomate',           'Légume',          25.00, 'unité'),
  ('Oignon',           'Légume',          18.00, 'unité'),
  ('Ail',              'Légume',          12.00, 'unité'),
  ('Huile d''olive',   'Autre',            3.50, 'L'),
  ('Poulet',           'Viande',           4.20, 'kg'),
  ('Riz basmati',      'Céréale',          8.00, 'kg'),
  ('Sel',              'Épice',          500.00, 'g'),
  ('Poivre noir',      'Épice',          120.00, 'g'),
  ('Citron',           'Fruit',           14.00, 'unité'),
  ('Persil frais',     'Légume',           6.00, 'unité'),
  ('Beurre',           'Produit laitier',  2.10, 'kg'),
  ('Saumon',           'Poisson',          3.00, 'kg'),
  ('Pâtes',            'Céréale',          5.50, 'kg'),
  ('Parmesan',         'Produit laitier',  0.80, 'kg'),
  ('Basilic',          'Légume',           4.00, 'unité');

INSERT INTO `recettes` (`nom`,`description`,`duree`,`niveau`,`image`) VALUES
  ('Poulet rôti aux herbes',
   'Un poulet doré au four, parfumé à l''ail, au citron et aux herbes fraîches. Plat familial par excellence, simple et savoureux.',
   75,'moyen','https://images.unsplash.com/photo-1532550907401-a500c9a57435?auto=format&fit=crop&w=1200&q=80'),
  ('Saumon grillé au citron',
   'Un pavé de saumon mariné puis grillé, accompagné d''un filet de citron et d''herbes fraîches. Léger et plein de saveurs.',
   25,'facile','https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&w=1200&q=80'),
  ('Pâtes au pesto maison',
   'Des pâtes al dente nappées d''un pesto frais préparé minute avec basilic, ail, parmesan et huile d''olive.',
   20,'facile','https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=1200&q=80'),
  ('Riz pilaf aux légumes',
   'Un riz parfumé cuit avec oignon et ail, idéal en accompagnement ou en plat végétarien complet.',
   35,'facile','https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=1200&q=80');

-- Liens recettes ↔ ingrédients (M:N) avec quantités
INSERT INTO `recette_ingredient` (`id_recette`,`id_ingredient`,`quantite`,`unite`) VALUES
  -- Poulet rôti aux herbes
  (1, 5, 1.50, 'kg'), (1, 3, 4.00, 'unité'), (1, 9, 1.00, 'unité'),
  (1, 4, 30.00,'ml'), (1, 7, 5.00, 'g'),    (1, 8, 2.00, 'g'),
  (1,10, 1.00, 'unité'),
  -- Saumon grillé au citron
  (2,12, 0.40, 'kg'), (2, 9, 1.00, 'unité'), (2, 4, 15.00,'ml'),
  (2, 7, 3.00, 'g'),  (2, 8, 1.00, 'g'),    (2,10, 0.50, 'unité'),
  -- Pâtes au pesto maison
  (3,13, 0.30, 'kg'), (3,15, 1.00, 'unité'), (3, 3, 2.00, 'unité'),
  (3,14, 0.05, 'kg'), (3, 4, 50.00,'ml'),   (3, 7, 4.00, 'g'),
  -- Riz pilaf aux légumes
  (4, 6, 0.30, 'kg'), (4, 2, 1.00, 'unité'), (4, 3, 2.00, 'unité'),
  (4, 4, 20.00,'ml'), (4, 7, 4.00, 'g'),    (4, 1, 2.00, 'unité');

COMMIT;
