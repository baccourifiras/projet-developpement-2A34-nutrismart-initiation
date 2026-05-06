-- ============================================================
-- NutriSmart — Export complet de la base de données
-- Généré le : 2026-05-06
-- Pour importer : phpMyAdmin → Importer → Choisir ce fichier
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS `nutrismart`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `nutrismart`;

-- ============================================================
-- TABLE : categorie
-- ============================================================
DROP TABLE IF EXISTS `categorie`;
CREATE TABLE `categorie` (
  `id_categorie`  INT(11)      NOT NULL AUTO_INCREMENT,
  `nom_categorie` VARCHAR(100) NOT NULL,
  `description`   TEXT         DEFAULT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categorie` (`id_categorie`, `nom_categorie`, `description`) VALUES
(1,  'Nutrition sportive',   'Événements pour améliorer les performances sportives grâce à une alimentation adaptée.'),
(2,  'Régime minceur',       'Ateliers et conférences pour la perte de poids et le rééquilibrage alimentaire.'),
(3,  'Alimentation saine',   'Conseils pratiques pour adopter une nutrition équilibrée au quotidien.'),
(6,  'Sports collectifs',    'Événements où plusieurs joueurs jouent en équipe.'),
(9,  'Nutrition sportive',   'Alimentation adaptée aux sportifs et à la performance physique'),
(10, 'Régimes alimentaires', 'Programmes de régimes équilibrés et perte de poids'),
(11, 'Bien-être',            'Conseils pour un mode de vie sain et équilibré'),
(12, 'Végétarisme',          'Alimentation végétarienne et végane');

-- ============================================================
-- TABLE : evenement
-- ============================================================
DROP TABLE IF EXISTS `evenement`;
CREATE TABLE `evenement` (
  `id_evenement`    INT(11)        NOT NULL AUTO_INCREMENT,
  `titre`           VARCHAR(150)   NOT NULL,
  `description`     TEXT           NOT NULL,
  `date_evenement`  DATE           NOT NULL,
  `heure_evenement` TIME           DEFAULT NULL,
  `lieu`            VARCHAR(150)   NOT NULL,
  `image`           TEXT           DEFAULT NULL,
  `id_categorie`    INT(11)        NOT NULL,
  `places`          INT(11)        NOT NULL DEFAULT 0,
  `google_maps_link` TEXT          DEFAULT NULL,
  `latitude`        DECIMAL(10,7)  DEFAULT NULL,
  `longitude`       DECIMAL(10,7)  DEFAULT NULL,
  PRIMARY KEY (`id_evenement`),
  KEY `id_categorie` (`id_categorie`),
  CONSTRAINT `evenement_ibfk_1`
    FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `evenement` (`id_evenement`, `titre`, `description`, `date_evenement`, `heure_evenement`, `lieu`, `image`, `id_categorie`, `places`, `google_maps_link`, `latitude`, `longitude`) VALUES
(1,  'Atelier repas équilibré',
     'Boostez vos performances à Tunis ! Notre atelier vous dévoilera les secrets d\'une nutrition sportive optimale pour une énergie illimitée.',
     '2026-05-12', '10:00:00', 'Tunis',
     'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=1200&q=80',
     1, 30, 'https://www.google.com/maps?q=36.5325917,8.9780271', 36.5325917, 8.9780271),

(2,  'Conférence nutrition sportive',
     'Une conférence dédiée à la nutrition avant et après l\'activité physique.',
     '2026-05-14', '14:00:00', 'Sfax',
     'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1200&q=80',
     1, 50, 'https://www.google.com/maps?q=36.6067089,8.1650389', 36.6067089, 8.1650389),

(3,  'Journée régime minceur',
     'Une journée d\'accompagnement avec des conseils sur le régime minceur.',
     '2026-05-22', '09:30:00', 'Sousse',
     'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=1200&q=80',
     2, 40, 'https://www.google.com/maps?q=36.9867583,9.3955076', 36.9867583, 9.3955076),

(7,  'Tournoi de Football Universitaire',
     'Gagner le tournoi et encourager le fair-play entre les équipes.',
     '2026-05-22', '20:00:00', 'Stade Municipal',
     'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1200&q=80',
     6, 11, 'https://www.google.com/maps?q=37.0919926,9.9887693', 37.0919926, 9.9887693),

(13, 'Atelier Nutrition Sportive',
     'Découvrez les secrets d\'une alimentation optimale pour améliorer vos performances sportives.',
     '2026-05-13', '10:00:00', 'Tunis, Salle des sports El Menzah',
     'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=1200&q=80',
     1, 30, 'https://www.google.com/maps?q=36.7934503,12.0212400', 36.7934503, 12.0212400),

(14, 'Conférence Régimes Équilibrés',
     'Une journée dédiée à comprendre les différents régimes alimentaires et leurs effets sur la santé.',
     '2026-05-20', '09:00:00', 'Sfax, Hôtel Novotel',
     'https://images.unsplash.com/photo-1498837167922-ddd27525d352?auto=format&fit=crop&w=1200&q=80',
     2, 50, 'https://www.google.com/maps?q=37.0025527,9.9887693', 37.0025527, 9.9887693),

(15, 'Journée Bien-être & Nutrition',
     'À Sousse, une journée au Centre culturel pour révéler le pouvoir de l\'alimentation saine ! Inspirez votre corps et votre esprit vers une vitalité épanouie.',
     '2026-05-27', '08:30:00', 'Sousse, Centre culturel',
     'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=1200&q=80',
     3, 40, 'https://www.google.com/maps?q=36.2673072,10.6259764', 36.2673072, 10.6259764);

-- ============================================================
-- TABLE : participant
-- ============================================================
DROP TABLE IF EXISTS `participant`;
CREATE TABLE `participant` (
  `id_participant`   INT(11)      NOT NULL AUTO_INCREMENT,
  `nom`              VARCHAR(100) NOT NULL,
  `email`            VARCHAR(150) NOT NULL,
  `telephone`        VARCHAR(20)  DEFAULT NULL,
  `id_evenement`     INT(11)      NOT NULL,
  `date_inscription` DATE         DEFAULT NULL,
  PRIMARY KEY (`id_participant`),
  KEY `id_evenement` (`id_evenement`),
  CONSTRAINT `participant_ibfk_1`
    FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `participant` (`id_participant`, `nom`, `email`, `telephone`, `id_evenement`, `date_inscription`) VALUES
(1,  'Amine Abidi',   'amine@example.com',        '22111222',   1, '2026-04-09'),
(3,  'Firas',         'firasbaccouri@gmail.com',   '22134772',   3, '2026-04-15'),
(4,  'Test User',     'amineabidi582@gmail.com',   '22365874',   7, '2026-04-15'),
(5,  'Labidi Amine',  'amineabidi582@gmail.com',   '24147970',   3, '2026-04-15'),
(8,  'Amine Abidi',   'amineabidi582@gmail.com',   '24159654',   3, '2026-04-15'),
(17, 'AmineGamer',    'hgvsjcjacy@gmail.com',      '45846236',   2, '2026-04-29'),
(18, 'Labidi Amine',  'kakadgvegv@gmail.com',      '98546321',   1, '2026-04-29'),
(19, 'Labidi Amine',  'amineabidi582@gmail.com',   '24159654',   2, '2026-05-02'),
(20, 'Amine Abidi',   'hfaidhabidi21@gmail.com',   '24159654',   7, '2026-05-02'),
(21, 'Amine Abidi',   'amineabidi582@gmail.com',   '24159654',   1, '2026-05-02'),
(23, 'Labidi Amine',  'amineabidi582@gmail.com',   '24147875',   3, '2026-05-06'),
(24, 'Amine Abidi',   'amineabidi582@gmail.com',   '20000000',   2, '2026-05-06');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- FIN — NutriSmart Database Export
-- ============================================================
