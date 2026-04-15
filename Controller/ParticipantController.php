<?php
require_once __DIR__ . '/../Model/Participant.php';

class ParticipantController {

    public function showParticipant($participant) {
        echo '<table border="1">';
        echo '<tr><th>ID</th><th>Nom complet</th><th>Email</th><th>Téléphone</th><th>Événement</th><th>Date inscription</th></tr>';
        echo '<tr>';
        echo '<td>' . $participant->getId()           . '</td>';
        echo '<td>' . $participant->getFullName()     . '</td>';
        echo '<td>' . $participant->getEmail()        . '</td>';
        echo '<td>' . $participant->getPhone()        . '</td>';
        echo '<td>' . $participant->getEventId()      . '</td>';
        echo '<td>' . $participant->getRegisteredAt() . '</td>';
        echo '</tr>';
        echo '</table>';
    }

    public function addParticipant($fullName, $email, $phone, $eventId) {
        $participant = new Participant(0, $fullName, $email, $phone, $eventId, date('Y-m-d'));
        return $participant;
    }
}
?>
