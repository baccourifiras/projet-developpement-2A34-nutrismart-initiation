<?php
/**
 * Modèle Commande
 * Représente une commande passée par un utilisateur
 */
class Commande {
    private $id_commande;
    private $id_utilisateur;
    private $id_produit;
    private $quantite;
    private $prix_total;
    private $statut;
    private $mode_paiement;
    private $date_commande;
    private $date_paiement;
    
    /**
     * Constructeur paramétré
     */
    public function __construct($id_commande = null, $id_utilisateur = 0, $id_produit = 0, 
                                $quantite = 1, $prix_total = 0.0, $statut = 'en_attente', 
                                $mode_paiement = '', $date_commande = null, $date_paiement = null) {
        $this->id_commande = $id_commande;
        $this->id_utilisateur = $id_utilisateur;
        $this->id_produit = $id_produit;
        $this->quantite = $quantite;
        $this->prix_total = $prix_total;
        $this->statut = $statut;
        $this->mode_paiement = $mode_paiement;
        $this->date_commande = $date_commande;
        $this->date_paiement = $date_paiement;
    }
    
    // Getters
    public function getIdCommande() { return $this->id_commande; }
    public function getIdUtilisateur() { return $this->id_utilisateur; }
    public function getIdProduit() { return $this->id_produit; }
    public function getQuantite() { return $this->quantite; }
    public function getPrixTotal() { return $this->prix_total; }
    public function getStatut() { return $this->statut; }
    public function getModePaiement() { return $this->mode_paiement; }
    public function getDateCommande() { return $this->date_commande; }
    public function getDatePaiement() { return $this->date_paiement; }
    
    // Setters
    public function setIdCommande($id_commande) { $this->id_commande = $id_commande; }
    public function setIdUtilisateur($id_utilisateur) { $this->id_utilisateur = $id_utilisateur; }
    public function setIdProduit($id_produit) { $this->id_produit = $id_produit; }
    public function setQuantite($quantite) { $this->quantite = $quantite; }
    public function setPrixTotal($prix_total) { $this->prix_total = $prix_total; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setModePaiement($mode_paiement) { $this->mode_paiement = $mode_paiement; }
    public function setDateCommande($date_commande) { $this->date_commande = $date_commande; }
    public function setDatePaiement($date_paiement) { $this->date_paiement = $date_paiement; }
}
