-- ============================================================
-- Extension de la table notifications pour le module planning
-- À exécuter APRÈS planning_module.sql
-- ============================================================

START TRANSACTION;

-- Ajouter une colonne `type` pour distinguer les origines
ALTER TABLE `notifications`
  ADD COLUMN IF NOT EXISTS `type` VARCHAR(40) NOT NULL DEFAULT 'stock_bas' AFTER `id`,
  ADD COLUMN IF NOT EXISTS `lien` VARCHAR(255) DEFAULT NULL COMMENT 'URL cible optionnelle' AFTER `message`,
  ADD KEY `idx_notif_type` (`type`);

-- Rendre id_ingredient nullable (les notifications planning n'en ont pas)
ALTER TABLE `notifications`
  MODIFY `id_ingredient` INT(11) DEFAULT NULL;

-- Param : seuil de répétition de recettes (pour alerte)
INSERT INTO `parametres` (`cle`, `valeur`) VALUES
  ('planning_repetition_seuil', '2')
ON DUPLICATE KEY UPDATE valeur = VALUES(valeur);

COMMIT;
