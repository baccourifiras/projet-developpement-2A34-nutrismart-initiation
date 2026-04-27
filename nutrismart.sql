-- ===================================
-- Base de données NutriSmart
-- Plateforme de nutrition pour régimes spéciaux
-- ===================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS NutriSmart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE NutriSmart;

-- ===================================
-- Table: produit
-- ===================================
DROP TABLE IF EXISTS commande;
DROP TABLE IF EXISTS produit;

CREATE TABLE produit (
    id_produit INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    categorie ENUM('plan', 'premium', 'coaching', 'guide') NOT NULL,
    regime_cible VARCHAR(50),
    type_vente ENUM('abonnement', 'achat_unique') NOT NULL,
    prix DECIMAL(6,2) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    date_ajout DATETIME DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- Table: commande
-- ===================================
CREATE TABLE commande (
    id_commande INT PRIMARY KEY AUTO_INCREMENT,
    id_utilisateur INT NOT NULL,
    id_produit INT NOT NULL,
    quantite INT DEFAULT 1,
    prix_total DECIMAL(6,2) NOT NULL,
    statut ENUM('en_attente', 'confirmee', 'annulee') NOT NULL,
    mode_paiement VARCHAR(30),
    date_commande DATETIME DEFAULT NOW(),
    date_paiement DATETIME,
    FOREIGN KEY (id_produit) REFERENCES produit(id_produit) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- Données de test: Produits
-- ===================================
INSERT INTO produit (nom, description, categorie, regime_cible, type_vente, prix, disponible) VALUES
('Plan Diabète Premium', 'Plan nutritionnel complet adapté aux personnes diabétiques avec suivi hebdomadaire et recettes personnalisées. Inclut un calculateur de glucides et des conseils d''experts.', 'plan', 'diabete', 'abonnement', 29.99, TRUE),

('Guide Vegan Débutant', 'Guide complet pour débuter un régime vegan en toute sérénité. Contient 50 recettes, liste de courses, et conseils nutritionnels pour éviter les carences.', 'guide', 'vegan', 'achat_unique', 14.99, TRUE),

('Coaching Sans Gluten', 'Accompagnement personnalisé par un nutritionniste spécialisé dans les régimes sans gluten. Séances individuelles et plan alimentaire sur mesure.', 'coaching', 'sans_gluten', 'abonnement', 79.99, TRUE),

('Fonctionnalité Premium Multi-Régimes', 'Accès illimité à tous les plans nutritionnels, calculateurs avancés, et base de données de 1000+ recettes adaptées à tous les régimes spéciaux.', 'premium', 'multi', 'abonnement', 49.99, TRUE),

('Plan Vegan Sport', 'Programme nutritionnel vegan optimisé pour les sportifs. Riche en protéines végétales, avec planning de repas pré et post-entraînement.', 'plan', 'vegan', 'achat_unique', 24.99, TRUE),

('Guide Diabète et Voyage', 'Guide pratique pour gérer son diabète en voyage : conseils, liste de contrôle, recettes nomades, et traductions médicales en 10 langues.', 'guide', 'diabete', 'achat_unique', 9.99, FALSE);

-- ===================================
-- Données de test: Commandes
-- ===================================
INSERT INTO commande (id_utilisateur, id_produit, quantite, prix_total, statut, mode_paiement, date_commande) VALUES
(1, 1, 1, 29.99, 'confirmee', 'carte_bancaire', '2026-04-10 14:30:00'),
(2, 2, 1, 14.99, 'confirmee', 'paypal', '2026-04-11 09:15:00'),
(1, 3, 1, 79.99, 'en_attente', 'virement', '2026-04-12 16:45:00'),
(3, 4, 1, 49.99, 'confirmee', 'carte_bancaire', '2026-04-13 11:20:00');

-- ===================================
-- Vérification des données
-- ===================================
SELECT 'Produits insérés:' as Info;
SELECT * FROM produit;

SELECT 'Commandes insérées:' as Info;
SELECT c.*, p.nom as nom_produit FROM commande c 
INNER JOIN produit p ON c.id_produit = p.id_produit;
