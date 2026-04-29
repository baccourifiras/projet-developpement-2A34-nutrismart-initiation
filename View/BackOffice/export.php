<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Config.php';
require_once __DIR__ . '/../../Controller/CategoryController.php';
require_once __DIR__ . '/../../Controller/EventController.php';
require_once __DIR__ . '/../../Controller/ParticipantController.php';

$table = isset($_GET['table']) ? (string) $_GET['table'] : 'categories';

$pdo = config::getConnexion();
$categoryController = new CategoryController($pdo);
$eventController = new EventController($pdo);
$participantController = new ParticipantController($pdo);

function parse_id_param($raw)
{
    if ($raw === null || $raw === '') return null;
    $tmp = (int) $raw;
    return $tmp > 0 ? $tmp : null;
}

function parse_sort_param($raw, $default)
{
    if ($raw === null || $raw === '') return $default;
    return strtolower(trim((string) $raw));
}

function parse_dir_param($raw, $default)
{
    if ($raw === null || $raw === '') return $default;
    return strtoupper(trim((string) $raw));
}

$title = 'Export PDF';

// ============================
// Donnees à afficher
// ============================
$categoriesTable = [];
$eventsTable = [];
$participantsTable = [];
$categoriesAll = [];
$eventsAll = [];

if ($table === 'categories') {
    $catId = parse_id_param($_GET['cat_id'] ?? null);
    $catSort = parse_sort_param($_GET['cat_sort'] ?? null, 'id');
    $catDir = parse_dir_param($_GET['cat_dir'] ?? null, 'ASC');

    $categoriesTable = $categoryController->searchByIdAndSort($catId, $catSort, $catDir);
    $title = 'Export PDF - Categories';
}

if ($table === 'events') {
    $evtId = parse_id_param($_GET['evt_id'] ?? null);
    $evtSort = parse_sort_param($_GET['evt_sort'] ?? null, 'id');
    $evtDir = parse_dir_param($_GET['evt_dir'] ?? null, 'ASC');

    $categoriesAll = $categoryController->getAll();
    $eventsTable = $eventController->searchByIdAndSort($evtId, $evtSort, $evtDir);
    $title = 'Export PDF - Evenements';
}

if ($table === 'participants') {
    $parId = parse_id_param($_GET['par_id'] ?? null);
    $parSort = parse_sort_param($_GET['par_sort'] ?? null, 'id');
    $parDir = parse_dir_param($_GET['par_dir'] ?? null, 'ASC');

    $eventsAll = $eventController->getAll();
    $participantsTable = $participantController->searchByIdAndSort($parId, $parSort, $parDir);
    $title = 'Export PDF - Participants';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="style.css?v=export" />
    <style>
        /* Reset minimal pour eviter la grille sidebar du BackOffice */
        body {
            display: block;
            grid-template-columns: unset;
            min-height: auto;
            padding: 24px;
            background: #ffffff;
        }
        .sidebar { display: none; }
        .main { padding: 0; }
        .table-wrapper { border-color: rgba(23,153,95,.16); }
        @media print {
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <h1 style="margin: 0 0 16px; font-size: 28px;"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if ($table === 'categories'): ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($categoriesTable as $category): ?>
                    <tr>
                        <td><span class="id-badge"><?= htmlspecialchars((string) $category['id'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($category['description'] ?: '-'), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($table === 'events'): ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Categorie</th>
                    <th>Date</th>
                    <th>Lieu</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($eventsTable as $event): ?>
                    <tr>
                        <td><span class="id-badge"><?= htmlspecialchars((string) $event['id'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><?= htmlspecialchars((string) $event['title'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $categoryController->categoryNameById($categoriesAll, $event['categoryId']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $eventController->formatDateFr($event['date']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $event['location'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($table === 'participants'): ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Participant</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    <th>Evenement</th>
                    <th>Inscrit le</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($participantsTable as $participant): ?>
                    <tr>
                        <td><span class="id-badge"><?= htmlspecialchars((string) $participant['id'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><?= htmlspecialchars((string) $participant['fullName'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $participant['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $participant['phone'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $eventController->eventTitleById($eventsAll, $participant['eventId']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $participant['registeredAt'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Aucun tableau a exporter.</p>
    <?php endif; ?>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>
</html>

