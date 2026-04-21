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
        $this->validate($data);
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
        $this->validate($data);
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

    private function validate($data) {
        $fullName = trim($data['fullName'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $eventId = (int) ($data['eventId'] ?? 0);

        if (strlen($fullName) < 3 || strlen($fullName) > 120) {
            throw new InvalidArgumentException('Le nom complet doit contenir entre 3 et 120 caracteres.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Veuillez saisir une adresse email valide.');
        }
        if (!preg_match('/^[2459][0-9]{7}$/', $phone)) {
            throw new InvalidArgumentException('Le telephone doit contenir 8 chiffres et commencer par 2, 4, 5 ou 9.');
        }
        if ($eventId <= 0) {
            throw new InvalidArgumentException('Veuillez choisir un evenement.');
        }
        if (!$this->eventExists($eventId)) {
            throw new InvalidArgumentException('L evenement choisi est introuvable.');
        }
    }

    private function eventExists($eventId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM evenement WHERE id_evenement = ?");
        $stmt->execute(array($eventId));
        return (int) $stmt->fetchColumn() > 0;
    }
}
?>
