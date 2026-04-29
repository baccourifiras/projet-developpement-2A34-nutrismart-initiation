<?php
/**
 * Modèle Stock
 * Représente un produit alimentaire en stock
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


    // --- Méthodes SQL (CRUD) ---

    public function addStock()
    {
        $db    = config::getConnexion();
        $sql   = "INSERT INTO stock (type, produits, date_expiration, seuil_minimum)
                  VALUES (:type, :produits, :date_expiration, :seuil_minimum)";
        $query = $db->prepare($sql);
        $query->execute([
            'type'            => $this->type,
            'produits'        => $this->produits,
            'date_expiration' => $this->dateExpiration,
            'seuil_minimum'   => $this->seuilMinimum,
        ]);
    }

    public function updateStock($id)
    {
        $db    = config::getConnexion();
        $sql   = "UPDATE stock
                  SET type = :type,
                      produits = :produits,
                      date_expiration = :date_expiration,
                      seuil_minimum = :seuil_minimum
                  WHERE id = :id";
        $query = $db->prepare($sql);
        $query->execute([
            'id'              => $id,
            'type'            => $this->type,
            'produits'        => $this->produits,
            'date_expiration' => $this->dateExpiration,
            'seuil_minimum'   => $this->seuilMinimum,
        ]);
    }

    public static function deleteStock($id)
    {
        $db    = config::getConnexion();
        $query = $db->prepare("DELETE FROM stock WHERE id = :id");
        $query->execute(['id' => $id]);
    }

    public static function getStocks()
    {
        $db    = config::getConnexion();
        $query = $db->prepare("SELECT * FROM stock ORDER BY id DESC");
        $query->execute();
        return $query->fetchAll();
    }

    public static function getStockById($id)
    {
        $db    = config::getConnexion();
        $query = $db->prepare("SELECT * FROM stock WHERE id = :id");
        $query->execute(['id' => $id]);
        return $query->fetch();
    }
}
?>
