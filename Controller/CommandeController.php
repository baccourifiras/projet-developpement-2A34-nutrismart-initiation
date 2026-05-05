<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Commande.php';
require_once __DIR__ . '/../Util/EmailService.php';

/**
 * Contrôleur Commande
 * Gère toutes les opérations sur les commandes
 */
class CommandeController {
    private $pdo;
    private $emailService;
    
    public function __construct() {
        $this->pdo = Config::getConnexion();
        $this->emailService = new EmailService();
    }
    
    /**
     * Récupère toutes les commandes avec les informations du produit
     * @return array
     */
    public function listCommandes() {
        try {
            $sql = "SELECT c.*, p.nom as nom_produit 
                    FROM commande c 
                    INNER JOIN produit p ON c.id_produit = p.id_produit 
                    ORDER BY c.date_commande DESC";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Erreur lors de la récupération des commandes : " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute une nouvelle commande
     * @param Commande $commande
     * @param bool $sendEmail - Envoyer email de confirmation (défaut: false)
     */
    public function addCommande($commande, $sendEmail = false) {
        try {
            $sql = "INSERT INTO commande (id_utilisateur, id_produit, quantite, prix_total, statut, mode_paiement) 
                    VALUES (:id_utilisateur, :id_produit, :quantite, :prix_total, :statut, :mode_paiement)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id_utilisateur' => $commande->getIdUtilisateur(),
                'id_produit' => $commande->getIdProduit(),
                'quantite' => $commande->getQuantite(),
                'prix_total' => $commande->getPrixTotal(),
                'statut' => $commande->getStatut(),
                'mode_paiement' => $commande->getModePaiement()
            ]);
            
            $commandeId = $this->pdo->lastInsertId();
            
            // Envoyer l'email de confirmation seulement si demandé
            if ($sendEmail) {
                // Récupérer le nom du produit pour l'email
                $sqlProduit = "SELECT nom FROM produit WHERE id_produit = :id";
                $stmtProduit = $this->pdo->prepare($sqlProduit);
                $stmtProduit->execute(['id' => $commande->getIdProduit()]);
                $produit = $stmtProduit->fetch();
                
                $this->emailService->envoyerConfirmationCommande(
                    'aniskontra123@gmail.com',
                    'Client NutriSmart',
                    [
                        'id' => $commandeId,
                        'produit' => $produit['nom'] ?? 'Produit',
                        'quantite' => $commande->getQuantite(),
                        'paiement' => $commande->getModePaiement(),
                        'total' => $commande->getPrixTotal() . ' TND'
                    ]
                );
            }
            
            return $commandeId;
        } catch (PDOException $e) {
            die("Erreur lors de l'ajout de la commande : " . $e->getMessage());
        }
    }
    
    /**
     * Met à jour le statut d'une commande
     * @param int $id
     * @param string $statut
     */
    public function updateStatut($id, $statut) {
        try {
            $sql = "UPDATE commande SET statut = :statut WHERE id_commande = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'statut' => $statut
            ]);
            
            // Envoyer l'email de changement de statut
            $this->emailService->envoyerChangementStatut(
                'client@example.com', // TODO: Récupérer l'email réel du client
                'Client NutriSmart',
                ['id' => $id],
                $statut
            );
        } catch (PDOException $e) {
            die("Erreur lors de la mise à jour du statut : " . $e->getMessage());
        }
    }
    
    /**
     * Supprime une commande par son ID
     * @param int $id
     */
    public function deleteCommande($id) {
        try {
            $sql = "DELETE FROM commande WHERE id_commande = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            header('Location: index.php?page=commande&action=list');
            exit();
        } catch (PDOException $e) {
            die("Erreur lors de la suppression de la commande : " . $e->getMessage());
        }
    }
}
