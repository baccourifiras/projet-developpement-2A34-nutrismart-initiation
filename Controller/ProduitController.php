<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Produit.php';

/**
 * Contrôleur Produit
 * Gère toutes les opérations CRUD sur les produits
 */
class ProduitController {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Config::getConnexion();
    }
    
    /**
     * Récupère tous les produits de la base de données
     * @return array Tableau d'objets Produit
     */
    public function listProduits() {
        try {
            $sql = "SELECT * FROM produit ORDER BY date_ajout DESC";
            $stmt = $this->pdo->query($sql);
            $produits = [];
            
            while ($row = $stmt->fetch()) {
                $produits[] = new Produit(
                    $row['id_produit'],
                    $row['nom'],
                    $row['description'],
                    $row['categorie'],
                    $row['regime_cible'],
                    $row['type_vente'],
                    $row['prix'],
                    $row['disponible'],
                    $row['date_ajout']
                );
            }
            
            return $produits;
        } catch (PDOException $e) {
            die("Erreur lors de la récupération des produits : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère un produit par son ID
     * @param int $id ID du produit
     * @return Produit|null
     */
    public function getProduit($id) {
        try {
            $sql = "SELECT * FROM produit WHERE id_produit = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();
            
            if ($row) {
                return new Produit(
                    $row['id_produit'],
                    $row['nom'],
                    $row['description'],
                    $row['categorie'],
                    $row['regime_cible'],
                    $row['type_vente'],
                    $row['prix'],
                    $row['disponible'],
                    $row['date_ajout']
                );
            }
            
            return null;
        } catch (PDOException $e) {
            die("Erreur lors de la récupération du produit : " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute un nouveau produit dans la base de données
     * @param Produit $produit
     */
    public function addProduit($produit) {
        try {
            $sql = "INSERT INTO produit (nom, description, categorie, regime_cible, type_vente, prix, disponible) 
                    VALUES (:nom, :description, :categorie, :regime_cible, :type_vente, :prix, :disponible)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nom' => $produit->getNom(),
                'description' => $produit->getDescription(),
                'categorie' => $produit->getCategorie(),
                'regime_cible' => $produit->getRegimeCible(),
                'type_vente' => $produit->getTypeVente(),
                'prix' => $produit->getPrix(),
                'disponible' => $produit->getDisponible() ? 1 : 0
            ]);
            
            header('Location: index.php?page=backoffice&action=list');
            exit();
        } catch (PDOException $e) {
            die("Erreur lors de l'ajout du produit : " . $e->getMessage());
        }
    }
    
    /**
     * Met à jour un produit existant
     * @param Produit $produit
     */
    public function updateProduit($produit) {
        try {
            $sql = "UPDATE produit 
                    SET nom = :nom, description = :description, categorie = :categorie, 
                        regime_cible = :regime_cible, type_vente = :type_vente, 
                        prix = :prix, disponible = :disponible 
                    WHERE id_produit = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $produit->getIdProduit(),
                'nom' => $produit->getNom(),
                'description' => $produit->getDescription(),
                'categorie' => $produit->getCategorie(),
                'regime_cible' => $produit->getRegimeCible(),
                'type_vente' => $produit->getTypeVente(),
                'prix' => $produit->getPrix(),
                'disponible' => $produit->getDisponible() ? 1 : 0
            ]);
            
            header('Location: index.php?page=backoffice&action=list');
            exit();
        } catch (PDOException $e) {
            die("Erreur lors de la mise à jour du produit : " . $e->getMessage());
        }
    }
    
    /**
     * Supprime un produit par son ID
     * @param int $id
     */
    public function deleteProduit($id) {
        try {
            $sql = "DELETE FROM produit WHERE id_produit = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            header('Location: index.php?page=backoffice&action=list');
            exit();
        } catch (PDOException $e) {
            die("Erreur lors de la suppression du produit : " . $e->getMessage());
        }
    }
}
