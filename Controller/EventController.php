<?php
require_once __DIR__ . '/../Model/Event.php';

class EventController {

    public function showEvent($event) {
        echo '<table border="1">';
        echo '<tr><th>ID</th><th>Titre</th><th>Description</th><th>Date</th><th>Heure</th><th>Lieu</th><th>Catégorie</th><th>Places</th><th>Image</th></tr>';
        echo '<tr>';
        echo '<td>' . $event->getId()          . '</td>';
        echo '<td>' . $event->getTitle()       . '</td>';
        echo '<td>' . $event->getDescription() . '</td>';
        echo '<td>' . $event->getDate()        . '</td>';
        echo '<td>' . $event->getTime()        . '</td>';
        echo '<td>' . $event->getLocation()    . '</td>';
        echo '<td>' . $event->getCategoryId()  . '</td>';
        echo '<td>' . $event->getSeats()       . '</td>';
        echo '<td>' . $event->getImage()       . '</td>';
        echo '</tr>';
        echo '</table>';
    }

    public function addEvent($title, $description, $date, $time, $location, $categoryId, $seats, $image) {
        $event = new Event(0, $title, $description, $date, $time, $location, $categoryId, $seats, $image);
        return $event;
    }

    public function updateEvent($id, $title, $description, $date, $time, $location, $categoryId, $seats, $image) {
        $event = new Event($id, $title, $description, $date, $time, $location, $categoryId, $seats, $image);
        return $event;
    }
}
?>
