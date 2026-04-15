<?php
require_once __DIR__ . '/../Model/Event.php';

class EventController {

    const DEFAULT_EVENT_IMAGE = 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=1200&q=80';

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $events = $this->pdo->query(
            "SELECT id_evenement AS id, titre AS title, description,
                    date_evenement AS date, heure_evenement AS time,
                    lieu AS location, image, id_categorie AS categoryId,
                    places AS seats
             FROM evenement
             ORDER BY id_evenement"
        )->fetchAll();

        foreach ($events as &$event) {
            $event['categoryId'] = (int) $event['categoryId'];
            $event['seats'] = (int) $event['seats'];
            if (empty($event['image'])) {
                $event['image'] = self::DEFAULT_EVENT_IMAGE;
            }
        }
        unset($event);

        return $events;
    }

    public function add($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO evenement (titre, description, date_evenement, heure_evenement, lieu, image, id_categorie, places)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute(array(
            trim($data['title'] ?? ''),
            trim($data['description'] ?? ''),
            $data['date'] ?? '',
            $data['time'] ?? null,
            trim($data['location'] ?? ''),
            trim($data['image'] ?? ''),
            (int) ($data['categoryId'] ?? 0),
            (int) ($data['seats'] ?? 0)
        ));
    }

    public function update($data) {
        $stmt = $this->pdo->prepare(
            "UPDATE evenement
             SET titre = ?, description = ?, date_evenement = ?, heure_evenement = ?,
                 lieu = ?, image = ?, id_categorie = ?, places = ?
             WHERE id_evenement = ?"
        );
        $stmt->execute(array(
            trim($data['title'] ?? ''),
            trim($data['description'] ?? ''),
            $data['date'] ?? '',
            $data['time'] ?? null,
            trim($data['location'] ?? ''),
            trim($data['image'] ?? ''),
            (int) ($data['categoryId'] ?? 0),
            (int) ($data['seats'] ?? 0),
            $this->requireId($data)
        ));
    }

    public function delete($data) {
        $id = $this->requireId($data);
        $this->pdo->beginTransaction();
        $this->pdo->prepare("DELETE FROM participant WHERE id_evenement = ?")->execute(array($id));
        $this->pdo->prepare("DELETE FROM evenement WHERE id_evenement = ?")->execute(array($id));
        $this->pdo->commit();
    }

    public function showEvent($event) {
        echo '<table border="1">';
        echo '<tr><th>ID</th><th>Titre</th><th>Description</th><th>Date</th><th>Heure</th><th>Lieu</th><th>CatÃ©gorie</th><th>Places</th><th>Image</th></tr>';
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

    private function requireId($data) {
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        if ($id <= 0) {
            throw new InvalidArgumentException('ID invalide.');
        }
        return $id;
    }
}
?>