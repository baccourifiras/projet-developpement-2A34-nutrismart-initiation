-- ============================================================
-- Migration : Ajout de la colonne face_descriptor
-- À exécuter dans phpMyAdmin ou via :
--   mysql -u root -p nutrismart < add_face_descriptor.sql
-- ============================================================

USE `nutrismart`;

ALTER TABLE `utilisateur`
  ADD COLUMN `face_descriptor` TEXT DEFAULT NULL
    COMMENT 'Descripteur facial JSON (128 valeurs float, face-api.js)'
  AFTER `provider_login`;
