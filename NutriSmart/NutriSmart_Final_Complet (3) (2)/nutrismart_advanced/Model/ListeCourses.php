<?php
/**
 * MODELE — ListeCourses
 * Contient UNIQUEMENT : constructeur + getters + setters
 * Aucune méthode SQL ici — le SQL est dans le Contrôleur
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
}
?>
