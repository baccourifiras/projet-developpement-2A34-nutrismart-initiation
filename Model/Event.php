<?php

class Event {
    private $id;
    private $title;
    private $description;
    private $date;
    private $time;
    private $location;
    private $categoryId;
    private $seats;
    private $image;

    public function __construct($id = 0, $title = '', $description = '', $date = '', $time = '', $location = '', $categoryId = 0, $seats = 0, $image = '') {
        $this->id          = $id;
        $this->title       = $title;
        $this->description = $description;
        $this->date        = $date;
        $this->time        = $time;
        $this->location    = $location;
        $this->categoryId  = $categoryId;
        $this->seats       = $seats;
        $this->image       = $image;
    }

    // Getters
    public function getId()          { return $this->id; }
    public function getTitle()       { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getDate()        { return $this->date; }
    public function getTime()        { return $this->time; }
    public function getLocation()    { return $this->location; }
    public function getCategoryId()  { return $this->categoryId; }
    public function getSeats()       { return $this->seats; }
    public function getImage()       { return $this->image; }

    // Setters
    public function setId($id)                   { $this->id = $id; }
    public function setTitle($title)             { $this->title = $title; }
    public function setDescription($description) { $this->description = $description; }
    public function setDate($date)               { $this->date = $date; }
    public function setTime($time)               { $this->time = $time; }
    public function setLocation($location)       { $this->location = $location; }
    public function setCategoryId($categoryId)   { $this->categoryId = $categoryId; }
    public function setSeats($seats)             { $this->seats = $seats; }
    public function setImage($image)             { $this->image = $image; }

    public function show() {
        echo '<table border="1">';
        echo '<tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Description</th>
        <th>Date</th>
        <th>Heure</th>
        <th>Lieu</th>
        <th>Catégorie</th>
        <th>Places</th>
        <th>Image</th>
        </tr>';
        echo '<tr>';
        echo '<td>' . $this->getId()          . '</td>';
        echo '<td>' . $this->getTitle()       . '</td>';
        echo '<td>' . $this->getDescription() . '</td>';
        echo '<td>' . $this->getDate()        . '</td>';
        echo '<td>' . $this->getTime()        . '</td>';
        echo '<td>' . $this->getLocation()    . '</td>';
        echo '<td>' . $this->getCategoryId()  . '</td>';
        echo '<td>' . $this->getSeats()       . '</td>';
        echo '<td>' . $this->getImage()       . '</td>';
        echo '</tr>';
        echo '</table>';
    }
}
?>
