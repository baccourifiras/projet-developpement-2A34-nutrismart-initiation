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

    public function participantCountByEvent($participants, $eventId) {
        $count = 0;

        foreach ($participants as $participant) {
            if ((int) $participant['eventId'] === (int) $eventId) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Recherche exacte par id + tri securise via whitelist.
     * Le parametre $id correspond a id_participant.
     *
     * @param int|null $id
     * @param string $sortField
     * @param string $sortDir
     * @return array
     */
    public function searchByIdAndSort(?int $id, string $sortField = 'id', string $sortDir = 'ASC'): array
    {
        $sortDir = strtoupper(trim((string) $sortDir));
        if (!in_array($sortDir, ['ASC', 'DESC'], true)) {
            $sortDir = 'ASC';
        }

        $sortField = strtolower(trim((string) $sortField));

        $sortMap = [
            'id' => 'p.id_participant',
            'fullname' => 'p.nom',
            'email' => 'p.email',
            'phone' => 'p.telephone',
            'event' => 'e.titre',
            'registeredat' => 'p.date_inscription',
        ];

        $sortColumn = $sortMap[$sortField] ?? $sortMap['id'];
        $needsEventJoin = $sortField === 'event';

        $sql = "SELECT
                    p.id_participant AS id,
                    p.nom AS fullName,
                    p.email AS email,
                    p.telephone AS phone,
                    p.id_evenement AS eventId,
                    p.date_inscription AS registeredAt
                FROM participant p";

        if ($needsEventJoin) {
            $sql .= " LEFT JOIN evenement e ON e.id_evenement = p.id_evenement";
        }

        $params = [];
        if ($id !== null && $id > 0) {
            $sql .= " WHERE p.id_participant = ?";
            $params[] = $id;
        }

        $sql .= " ORDER BY {$sortColumn} {$sortDir}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $participants = $stmt->fetchAll();

        foreach ($participants as &$participant) {
            $participant['eventId'] = (int) $participant['eventId'];
        }
        unset($participant);

        return $participants;
    }

    public function add($data) {
        $participant = $this->prepareParticipantData($data);
        if (!$this->eventExists($participant['eventId'])) {
            throw new InvalidArgumentException('L evenement choisi est introuvable.');
        }
        $stmt = $this->pdo->prepare(
            "INSERT INTO participant (nom, email, telephone, id_evenement, date_inscription)
             VALUES (?, ?, ?, ?, CURDATE())"
        );
        $stmt->execute(array(
            $participant['fullName'],
            $participant['email'],
            $participant['phone'],
            $participant['eventId']
        ));
    }

    public function update($data) {
        $participant = $this->prepareParticipantData($data);
        if (!$this->eventExists($participant['eventId'])) {
            throw new InvalidArgumentException('L evenement choisi est introuvable.');
        }
        $stmt = $this->pdo->prepare(
            "UPDATE participant SET nom = ?, email = ?, telephone = ?, id_evenement = ?
             WHERE id_participant = ?"
        );
        $stmt->execute(array(
            $participant['fullName'],
            $participant['email'],
            $participant['phone'],
            $participant['eventId'],
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

    private function prepareParticipantData($data) {
        return array(
            'fullName' => trim((string) ($data['fullName'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'phone' => trim((string) ($data['phone'] ?? '')),
            'eventId' => (int) ($data['eventId'] ?? 0)
        );
    }

    private function eventExists($eventId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM evenement WHERE id_evenement = ?");
        $stmt->execute(array($eventId));
        return (int) $stmt->fetchColumn() > 0;
    }
}
?>
