<?php
require_once __DIR__ . '/../../Config.php';
require_once __DIR__ . '/../../Controller/EventController.php';

header('Content-Type: application/json; charset=UTF-8');

function normalizeIsoDate(?string $value): ?string
{
    $value = trim((string) $value);
    if ($value === '') return null;
    $ts = strtotime($value);
    if ($ts === false) return null;
    return date('Y-m-d', $ts);
}

try {
    $pdo = config::getConnexion();
    $eventController = new EventController($pdo);
    $events = $eventController->getAll();

    // FullCalendar peut envoyer start/end pour filtrer
    $start = normalizeIsoDate($_GET['start'] ?? null);
    $end = normalizeIsoDate($_GET['end'] ?? null);

    $payload = [];

    foreach ($events as $event) {
        $date = isset($event['date']) ? (string) $event['date'] : '';
        if ($date === '') continue;

        if ($start !== null && $date < $start) continue;
        if ($end !== null && $date >= $end) continue; // end exclusif

        $time = isset($event['time']) ? (string) $event['time'] : '';
        $hasTime = $time !== '';

        $payload[] = [
            'id' => (string) ($event['id'] ?? ''),
            'title' => (string) ($event['title'] ?? ''),
            'start' => $hasTime ? ($date . 'T' . $time) : $date,
            'allDay' => !$hasTime,
            'extendedProps' => [
                'location' => (string) ($event['location'] ?? ''),
                'seats' => (int) ($event['seats'] ?? 0),
                'description' => (string) ($event['description'] ?? ''),
                'image' => (string) ($event['image'] ?? ''),
                'categoryId' => (int) ($event['categoryId'] ?? 0),
            ],
        ];
    }

    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Impossible de charger les événements.',
        'details' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}

