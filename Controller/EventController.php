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
                    places AS seats,
                    google_maps_link AS googleMapsLink,
                    latitude, longitude
             FROM evenement
             ORDER BY id_evenement"
        )->fetchAll();

        foreach ($events as &$event) {
            $event['categoryId'] = (int) $event['categoryId'];
            $event['seats']      = (int) $event['seats'];
            $event['latitude']   = $event['latitude']  !== null ? (float) $event['latitude']  : null;
            $event['longitude']  = $event['longitude'] !== null ? (float) $event['longitude'] : null;
            if (empty($event['image'])) {
                $event['image'] = self::DEFAULT_EVENT_IMAGE;
            }
            if (!isset($event['googleMapsLink'])) {
                $event['googleMapsLink'] = '';
            }
        }
        unset($event);

        return $events;
    }

    public function formatDateFr($date) {
        if (!$date) return '-';
        $timestamp = strtotime($date);
        return $timestamp ? date('d/m/Y', $timestamp) : '-';
    }

    public function eventTitleById($events, $eventId) {
        foreach ($events as $event) {
            if ((int) $event['id'] === (int) $eventId) {
                return $event['title'];
            }
        }
        return 'Evenement introuvable';
    }

    public function searchByIdAndSort(?int $id, string $sortField = 'id', string $sortDir = 'ASC'): array
    {
        $sortDir = strtoupper(trim((string) $sortDir));
        if (!in_array($sortDir, ['ASC', 'DESC'], true)) {
            $sortDir = 'ASC';
        }

        $sortField = strtolower(trim((string) $sortField));
        $sortMap = [
            'id'       => 'e.id_evenement',
            'title'    => 'e.titre',
            'category' => 'c.nom_categorie',
            'date'     => 'e.date_evenement',
            'location' => 'e.lieu',
        ];

        $sortColumn       = $sortMap[$sortField] ?? $sortMap['id'];
        $needsCategoryJoin = $sortField === 'category';

        $sql = "SELECT
                    e.id_evenement AS id,
                    e.titre AS title,
                    e.description,
                    e.date_evenement AS date,
                    e.heure_evenement AS time,
                    e.lieu AS location,
                    e.image,
                    e.id_categorie AS categoryId,
                    e.places AS seats,
                    e.google_maps_link AS googleMapsLink,
                    e.latitude,
                    e.longitude
                FROM evenement e";

        if ($needsCategoryJoin) {
            $sql .= " LEFT JOIN categorie c ON c.id_categorie = e.id_categorie";
        }

        $params = [];
        if ($id !== null && $id > 0) {
            $sql .= " WHERE e.id_evenement = ?";
            $params[] = $id;
        }

        $sql .= " ORDER BY {$sortColumn} {$sortDir}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $events = $stmt->fetchAll();

        foreach ($events as &$event) {
            $event['categoryId'] = (int) $event['categoryId'];
            $event['seats']      = (int) $event['seats'];
            $event['latitude']   = $event['latitude']  !== null ? (float) $event['latitude']  : null;
            $event['longitude']  = $event['longitude'] !== null ? (float) $event['longitude'] : null;
            if (empty($event['image'])) {
                $event['image'] = self::DEFAULT_EVENT_IMAGE;
            }
            if (!isset($event['googleMapsLink'])) {
                $event['googleMapsLink'] = '';
            }
        }
        unset($event);

        return $events;
    }

    public function add($data) {
        $event = $this->prepareEventData($data);
        if (!$this->categoryExists($event['categoryId'])) {
            throw new InvalidArgumentException('La categorie choisie est introuvable.');
        }
        $stmt = $this->pdo->prepare(
            "INSERT INTO evenement
             (titre, description, date_evenement, heure_evenement, lieu, image,
              id_categorie, places, google_maps_link, latitude, longitude)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $event['title'],
            $event['description'],
            $event['date'],
            $event['time'],
            $event['location'],
            $event['image'],
            $event['categoryId'],
            $event['seats'],
            $event['googleMapsLink'],
            $event['latitude'],
            $event['longitude'],
        ]);
    }

    public function update($data) {
        $event = $this->prepareEventData($data);
        if (!$this->categoryExists($event['categoryId'])) {
            throw new InvalidArgumentException('La categorie choisie est introuvable.');
        }
        $stmt = $this->pdo->prepare(
            "UPDATE evenement
             SET titre = ?, description = ?, date_evenement = ?, heure_evenement = ?,
                 lieu = ?, image = ?, id_categorie = ?, places = ?,
                 google_maps_link = ?, latitude = ?, longitude = ?
             WHERE id_evenement = ?"
        );
        $stmt->execute([
            $event['title'],
            $event['description'],
            $event['date'],
            $event['time'],
            $event['location'],
            $event['image'],
            $event['categoryId'],
            $event['seats'],
            $event['googleMapsLink'],
            $event['latitude'],
            $event['longitude'],
            $this->requireId($data),
        ]);
    }

    public function delete($data) {
        $id = $this->requireId($data);
        $this->pdo->beginTransaction();
        $this->pdo->prepare("DELETE FROM participant WHERE id_evenement = ?")->execute([$id]);
        $this->pdo->prepare("DELETE FROM evenement WHERE id_evenement = ?")->execute([$id]);
        $this->pdo->commit();
    }

    // ── Privés ──────────────────────────────────────────────

    private function requireId($data) {
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        if ($id <= 0) throw new InvalidArgumentException('ID invalide.');
        return $id;
    }

    private function prepareEventData($data) {
        $lat = isset($data['latitude'])  && $data['latitude']  !== '' ? (float) $data['latitude']  : null;
        $lng = isset($data['longitude']) && $data['longitude'] !== '' ? (float) $data['longitude'] : null;

        return [
            'title'          => trim((string) ($data['title']       ?? '')),
            'description'    => trim((string) ($data['description'] ?? '')),
            'date'           => isset($data['date']) ? (string) $data['date'] : '',
            'time'           => isset($data['time']) && $data['time'] !== '' ? (string) $data['time'] : null,
            'location'       => trim((string) ($data['location']    ?? '')),
            'image'          => trim((string) ($data['image']       ?? '')),
            'categoryId'     => (int) ($data['categoryId'] ?? 0),
            'seats'          => (int) ($data['seats']      ?? 0),
            'googleMapsLink' => trim((string) ($data['googleMapsLink'] ?? '')),
            'latitude'       => $lat,
            'longitude'      => $lng,
        ];
    }

    private function categoryExists($categoryId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM categorie WHERE id_categorie = ?");
        $stmt->execute([$categoryId]);
        return (int) $stmt->fetchColumn() > 0;
    }

}
?>
