<?php
/**
 * Modèle ListeCourses
 * Représente une liste de courses
 */
class ListeCourses
{
    private $id;
    private $articlesAcheter;
    private $budget;
    private $dateCreation;
    private $stockId;
    private $stockType;

    public function __construct($articlesAcheter = '', $budget = 0, $dateCreation = '', $stockId = null, $id = null, $stockType = null)
    {
        $this->id              = $id;
        $this->articlesAcheter = $articlesAcheter;
        $this->budget          = $budget;
        $this->dateCreation    = $dateCreation;
        $this->stockId         = $stockId;
        $this->stockType       = $stockType;
    }

    // --- Getters ---
    public function getId()              { return $this->id; }
    public function getArticlesAcheter() { return $this->articlesAcheter; }
    public function getBudget()          { return $this->budget; }
    public function getDateCreation()    { return $this->dateCreation; }
    public function getStockId()         { return $this->stockId; }
    public function getStockType()       { return $this->stockType; }

    // --- Setters ---
    public function setId($id)                    { $this->id = $id; }
    public function setArticlesAcheter($articles) { $this->articlesAcheter = $articles; }
    public function setBudget($budget)            { $this->budget = $budget; }
    public function setDateCreation($date)        { $this->dateCreation = $date; }
    public function setStockId($stockId)          { $this->stockId = $stockId; }
    public function setStockType($stockType)      { $this->stockType = $stockType; }


    // --- Méthodes SQL (CRUD) ---

    public function addListe()
    {
        $db    = config::getConnexion();
        $sql   = "INSERT INTO liste_courses (articles_a_acheter, budget, date_creation, stock_id)
                  VALUES (:articles_a_acheter, :budget, :date_creation, :stock_id)";
        $query = $db->prepare($sql);
        $query->execute([
            'articles_a_acheter' => $this->articlesAcheter,
            'budget'             => $this->budget,
            'date_creation'      => $this->dateCreation,
            'stock_id'           => $this->stockId,
        ]);
    }

    public function updateListe($id)
    {
        $db    = config::getConnexion();
        $sql   = "UPDATE liste_courses
                  SET articles_a_acheter = :articles_a_acheter,
                      budget = :budget,
                      date_creation = :date_creation,
                      stock_id = :stock_id
                  WHERE id = :id";
        $query = $db->prepare($sql);
        $query->execute([
            'id'                 => $id,
            'articles_a_acheter' => $this->articlesAcheter,
            'budget'             => $this->budget,
            'date_creation'      => $this->dateCreation,
            'stock_id'           => $this->stockId,
        ]);
    }

    public static function deleteListe($id)
    {
        $db    = config::getConnexion();
        $query = $db->prepare("DELETE FROM liste_courses WHERE id = :id");
        $query->execute(['id' => $id]);
    }

    public static function getListes()
    {
        $db    = config::getConnexion();
        $sql   = "SELECT lc.*, s.type AS stock_type
                  FROM liste_courses lc
                  LEFT JOIN stock s ON lc.stock_id = s.id
                  ORDER BY lc.id DESC";
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public static function getListeById($id)
    {
        $db    = config::getConnexion();
        $query = $db->prepare("SELECT * FROM liste_courses WHERE id = :id");
        $query->execute(['id' => $id]);
        return $query->fetch();
    }
}
?>
