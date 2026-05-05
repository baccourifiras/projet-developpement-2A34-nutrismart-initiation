<?php
/**
 * MODELE — Stock
 * Contient UNIQUEMENT : constructeur + getters + setters
 * Aucune méthode SQL ici — le SQL est dans le Contrôleur
 */
class Stock
{
    private $id;
    private $type;
    private $produits;
    private $dateExpiration;
    private $seuilMinimum;

    public function __construct($type = '', $produits = '', $dateExpiration = '', $seuilMinimum = 0, $id = 0)
    {
        $this->id             = $id;
        $this->type           = $type;
        $this->produits       = $produits;
        $this->dateExpiration = $dateExpiration;
        $this->seuilMinimum   = $seuilMinimum;
    }

    // --- Getters ---
    public function getId()             { return $this->id; }
    public function getType()           { return $this->type; }
    public function getProduits()       { return $this->produits; }
    public function getDateExpiration() { return $this->dateExpiration; }
    public function getSeuilMinimum()   { return $this->seuilMinimum; }

    // --- Setters ---
    public function setId($id)               { $this->id = $id; }
    public function setType($type)           { $this->type = $type; }
    public function setProduits($produits)   { $this->produits = $produits; }
    public function setDateExpiration($date) { $this->dateExpiration = $date; }
    public function setSeuilMinimum($seuil)  { $this->seuilMinimum = $seuil; }
}
?>
