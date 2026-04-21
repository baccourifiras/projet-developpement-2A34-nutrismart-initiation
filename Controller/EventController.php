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
        $this->validate($data);
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
        $this->validate($data);
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

    private function validate($data) {
        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $date = $data['date'] ?? '';
        $time = $data['time'] ?? '';
        $location = trim($data['location'] ?? '');
        $image = trim($data['image'] ?? '');
        $categoryId = (int) ($data['categoryId'] ?? 0);
        $seats = (int) ($data['seats'] ?? 0);

        if (strlen($title) < 3 || strlen($title) > 120) {
            throw new InvalidArgumentException('Le titre doit contenir entre 3 et 120 caracteres.');
        }
        if ($categoryId <= 0) {
            throw new InvalidArgumentException('Veuillez choisir une categorie.');
        }
        if (!$this->categoryExists($categoryId)) {
            throw new InvalidArgumentException('La categorie choisie est introuvable.');
        }
        if (!$this->validDate($date)) {
            throw new InvalidArgumentException('Veuillez saisir une date valide.');
        }
        if (!$this->validTime($time)) {
            throw new InvalidArgumentException('Veuillez saisir une heure valide.');
        }
        if (strlen($location) < 3 || strlen($location) > 120) {
            throw new InvalidArgumentException('Le lieu doit contenir entre 3 et 120 caracteres.');
        }
        if ($seats < 1) {
            throw new InvalidArgumentException('Le nombre de places doit etre superieur a 0.');
        }
        if (strlen($description) < 10 || strlen($description) > 1000) {
            throw new InvalidArgumentException('La description doit contenir entre 10 et 1000 caracteres.');
        }
        if ($image !== '' && !filter_var($image, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('L URL de l image est invalide.');
        }
    }

    private function categoryExists($categoryId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM categorie WHERE id_categorie = ?");
        $stmt->execute(array($categoryId));
        return (int) $stmt->fetchColumn() > 0;
    }

    private function validDate($date) {
        $parts = explode('-', $date);
        return count($parts) === 3 && checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0]);
    }

    private function validTime($time) {
        return (bool) preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', (string) $time);
    }
}
?>
