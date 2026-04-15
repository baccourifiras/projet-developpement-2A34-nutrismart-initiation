-- ============================================================
-- NutriSmart — Migration : ajout colonnes quantite + unite
-- À exécuter dans phpMyAdmin sur la base nutrismart
-- ============================================================

USE `nutrismart`;

-- Étape 1 : Ajouter les colonnes manquantes à la table stock
ALTER TABLE `stock`
  ADD COLUMN IF NOT EXISTS `quantite`  FLOAT       DEFAULT 0    AFTER `produits`,
  ADD COLUMN IF NOT EXISTS `unite`     VARCHAR(20) DEFAULT NULL  AFTER `quantite`;

-- Étape 2 : Mettre des valeurs par défaut pour les lignes existantes
UPDATE `stock` SET `quantite` = 1, `unite` = 'unité' WHERE `quantite` IS NULL OR `unite` IS NULL;

-- Vérification : voir la structure finale
DESCRIBE `stock`;
