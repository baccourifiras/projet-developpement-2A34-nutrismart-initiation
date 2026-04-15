<?php
require_once __DIR__ . '/../Model/Participant.php';

class ParticipantController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $participants = $this->pdo->query(
            "SELECT id_participant AS id, nom AS fullName, email,
                    telephone AS phone, id_evenement AS eventId,
                    date_inscription AS registeredAt
             FROM participant
             ORDER BY id_participant"
        )->fetchAll();

        foreach ($participants as &$participant) {
            $participant['eventId'] = (int) $participant['eventId'];
        }
        unset($participant);

        return $participants;
    }

    public function add($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO participant (nom, email, telephone, id_evenement, date_inscription)
             VALUES (?, ?, ?, ?, CURDATE())"
        );
        $stmt->execute(array(
            trim($data['fullName'] ?? ''),
            trim($data['email'] ?? ''),
            trim($data['phone'] ?? ''),
            (int) ($data['eventId'] ?? 0)
        ));
    }

    public function update($data) {
        $stmt = $this->pdo->prepare(
            "UPDATE participant SET nom = ?, email = ?, telephone = ?, id_evenement = ?
             WHERE id_participant = ?"
        );
        $stmt->execute(array(
            trim($data['fullName'] ?? ''),
            trim($data['email'] ?? ''),
            trim($data['phone'] ?? ''),
            (int) ($data['eventId'] ?? 0),
            $this->requireId($data)
        ));
    }

    public function delete($data) {
        $this->pdo->prepare("DELETE FROM participant WHERE id_participant = ?")->execute(array($this->requireId($data)));
    }

    public function showParticipant($participant) {
        echo '<table border="1">';
        echo '<tr><th>ID</th><th>Nom complet</th><th>Email</th><th>TÃ©lÃ©phone</th><th>Ã‰vÃ©nement</th><th>Date inscription</th></tr>';
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

    private function requireId($data) {
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        if ($id <= 0) {
            throw new InvalidArgumentException('ID invalide.');
        }
        return $id;
    }
}
?>