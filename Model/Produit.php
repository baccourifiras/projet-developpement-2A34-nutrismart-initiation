<?php
/**
 * Modèle Produit
 * Représente un produit NutriSmart (plan nutritionnel, coaching, guide, etc.)
 */
class Produit {
    private $id_produit;
    private $nom;
    private $description;
    private $categorie;
    private $regime_cible;
    private $type_vente;
    private $prix;
    private $disponible;
    private $date_ajout;
    
    /**
     * Constructeur paramétré
     */
    public function __construct($id_produit = null, $nom = '', $description = '', $categorie = 'plan', 
                                $regime_cible = '', $type_vente = 'achat_unique', $prix = 0.0, 
                                $disponible = true, $date_ajout = null) {
        $this->id_produit = $id_produit;
        $this->nom = $nom;
        $this->description = $description;
        $this->categorie = $categorie;
        $this->regime_cible = $regime_cible;
        $this->type_vente = $type_vente;
        $this->prix = $prix;
        $this->disponible = $disponible;
        $this->date_ajout = $date_ajout;
    }
    
    // Getters
    public function getIdProduit() { return $this->id_produit; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getCategorie() { return $this->categorie; }
    public function getRegimeCible() { return $this->regime_cible; }
    public function getTypeVente() { return $this->type_vente; }
    public function getPrix() { return $this->prix; }
    public function getDisponible() { return $this->disponible; }
    public function getDateAjout() { return $this->date_ajout; }
    
    // Setters
    public function setIdProduit($id_produit) { $this->id_produit = $id_produit; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setDescription($description) { $this->description = $description; }
    public function setCategorie($categorie) { $this->categorie = $categorie; }
    public function setRegimeCible($regime_cible) { $this->regime_cible = $regime_cible; }
    public function setTypeVente($type_vente) { $this->type_vente = $type_vente; }
    public function setPrix($prix) { $this->prix = $prix; }
    public function setDisponible($disponible) { $this->disponible = $disponible; }
    public function setDateAjout($date_ajout) { $this->date_ajout = $date_ajout; }
    
    /**
     * Affiche les informations du produit dans une ligne de tableau HTML
     */
    public function show() {
        $disponibleText = $this->disponible ? 'Oui' : 'Non';
        $disponibleClass = $this->disponible ? 'badge-success' : 'badge-danger';
        
        echo "<tr>";
        echo "<td>{$this->id_produit}</td>";
        echo "<td>{$this->nom}</td>";
        echo "<td><span class='badge badge-primary'>{$this->categorie}</span></td>";
        echo "<td><span class='badge badge-info'>{$this->regime_cible}</span></td>";
        echo "<td>{$this->type_vente}</td>";
        echo "<td>{$this->prix} €</td>";
        echo "<td><span class='badge {$disponibleClass}'>{$disponibleText}</span></td>";
        echo "<td>{$this->date_ajout}</td>";
        echo "<td>
                <a href='index.php?page=backoffice&action=update&id={$this->id_produit}' class='btn btn-warning btn-sm'>Modifier</a>
                <a href='index.php?page=backoffice&action=delete&id={$this->id_produit}' class='btn btn-danger btn-sm' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce produit ?\")'>Supprimer</a>
              </td>";
        echo "</tr>";
    }
}
