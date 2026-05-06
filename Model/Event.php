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
    private $googleMapsLink;
    private $latitude;
    private $longitude;

    public function __construct($id = 0, $title = '', $description = '', $date = '', $time = '',
                                $location = '', $categoryId = 0, $seats = 0, $image = '',
                                $googleMapsLink = '', $latitude = null, $longitude = null) {
        $this->id             = $id;
        $this->title          = $title;
        $this->description    = $description;
        $this->date           = $date;
        $this->time           = $time;
        $this->location       = $location;
        $this->categoryId     = $categoryId;
        $this->seats          = $seats;
        $this->image          = $image;
        $this->googleMapsLink = $googleMapsLink;
        $this->latitude       = $latitude;
        $this->longitude      = $longitude;
    }

    public function getId()             { return $this->id; }
    public function getTitle()          { return $this->title; }
    public function getDescription()    { return $this->description; }
    public function getDate()           { return $this->date; }
    public function getTime()           { return $this->time; }
    public function getLocation()       { return $this->location; }
    public function getCategoryId()     { return $this->categoryId; }
    public function getSeats()          { return $this->seats; }
    public function getImage()          { return $this->image; }
    public function getGoogleMapsLink() { return $this->googleMapsLink; }
    public function getLatitude()       { return $this->latitude; }
    public function getLongitude()      { return $this->longitude; }

    public function setId($id)                         { $this->id = $id; }
    public function setTitle($title)                   { $this->title = $title; }
    public function setDescription($description)       { $this->description = $description; }
    public function setDate($date)                     { $this->date = $date; }
    public function setTime($time)                     { $this->time = $time; }
    public function setLocation($location)             { $this->location = $location; }
    public function setCategoryId($categoryId)         { $this->categoryId = $categoryId; }
    public function setSeats($seats)                   { $this->seats = $seats; }
    public function setImage($image)                   { $this->image = $image; }
    public function setGoogleMapsLink($googleMapsLink) { $this->googleMapsLink = $googleMapsLink; }
    public function setLatitude($latitude)             { $this->latitude = $latitude; }
    public function setLongitude($longitude)           { $this->longitude = $longitude; }
}
?>
