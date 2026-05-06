<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Config.php';
require_once __DIR__ . '/../../Controller/CategoryController.php';
require_once __DIR__ . '/../../Controller/EventController.php';
require_once __DIR__ . '/../../Controller/ParticipantController.php';

$categoriesAll = array();
$eventsAll = array();
$participantsAll = array();

$categoriesTable = array();
$eventsTable = array();
$participantsTable = array();
$loadError = '';
$flashSuccess = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : '';
$flashError = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// ============================
// Parametres recherche/tri GET
// ============================
$catId = null;
$evtId = null;
$parId = null;

if (isset($_GET['cat_id']) && $_GET['cat_id'] !== '') {
    $tmp = (int) $_GET['cat_id'];
    $catId = $tmp > 0 ? $tmp : null;
}
if (isset($_GET['evt_id']) && $_GET['evt_id'] !== '') {
    $tmp = (int) $_GET['evt_id'];
    $evtId = $tmp > 0 ? $tmp : null;
}
if (isset($_GET['par_id']) && $_GET['par_id'] !== '') {
    $tmp = (int) $_GET['par_id'];
    $parId = $tmp > 0 ? $tmp : null;
}

$catSort = isset($_GET['cat_sort']) && $_GET['cat_sort'] !== '' ? (string) $_GET['cat_sort'] : 'id';
$catDir = isset($_GET['cat_dir']) && $_GET['cat_dir'] !== '' ? (string) $_GET['cat_dir'] : 'ASC';
$evtSort = isset($_GET['evt_sort']) && $_GET['evt_sort'] !== '' ? (string) $_GET['evt_sort'] : 'id';
$evtDir = isset($_GET['evt_dir']) && $_GET['evt_dir'] !== '' ? (string) $_GET['evt_dir'] : 'ASC';
$parSort = isset($_GET['par_sort']) && $_GET['par_sort'] !== '' ? (string) $_GET['par_sort'] : 'id';
$parDir = isset($_GET['par_dir']) && $_GET['par_dir'] !== '' ? (string) $_GET['par_dir'] : 'ASC';

$catSortNorm = strtolower(trim($catSort));
$catDirNorm = strtoupper(trim($catDir));
$evtSortNorm = strtolower(trim($evtSort));
$evtDirNorm = strtoupper(trim($evtDir));
$parSortNorm = strtolower(trim($parSort));
$parDirNorm = strtoupper(trim($parDir));

try {
    $pdo = config::getConnexion();
    $categoryController = new CategoryController($pdo);
    $eventController = new EventController($pdo);
    $participantController = new ParticipantController($pdo);

    $categoriesAll = $categoryController->getAll();
    $eventsAll = $eventController->getAll();
    $participantsAll = $participantController->getAll();

    $categoriesTable = $categoryController->searchByIdAndSort($catId, $catSortNorm, $catDirNorm);
    $eventsTable = $eventController->searchByIdAndSort($evtId, $evtSortNorm, $evtDirNorm);
    $participantsTable = $participantController->searchByIdAndSort($parId, $parSortNorm, $parDirNorm);
} catch (Throwable $error) {
    $loadError = $error->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart - Dashboard</title>
  <link rel="stylesheet" href="style.css?v=20260429-anim" />
  <!-- Leaflet CSS — carte interactive gratuite, sans clé API -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark">NS</div>
      <div>
        <h1>NutriSmart</h1>
        <p class="brand-slogan">Eat Smart Live Smart</p>
        <p class="sidebar-text">Administration des événements nutrition, régimes et bien-être.</p>
      </div>
    </div>

    <nav class="menu">
      <a href="#dashboard">Tableau de bord</a>
      <a href="#categorySection">Catégories</a>
      <a href="#eventSection">Événements</a>
      <a href="#participantSection">Participants</a>
      <a href="../frontoffice/tache-categories.php">Tâche Catégories</a>
      <a href="../frontoffice/tache-evenements.php">Tâche Événements</a>
      <a href="../frontoffice/tache-participants.php">Tâche Participants</a>
      <a href="../frontoffice/tache-design.php">Tâche Design CSS</a>
      <a href="../frontoffice/tache-javascript.php">Tâche JavaScript</a>
      <a href="../frontoffice/index.php">Voir Front Office</a>
    </nav>

  </aside>

  <main class="main content">
    <section id="dashboard" class="panel stats-panel">
      <div class="stats-card">
        <span>Catégories</span>
        <strong id="categoryCount"><?= count($categoriesAll) ?></strong>
      </div>
      <div class="stats-card">
        <span>Événements</span>
        <strong id="eventCount"><?= count($eventsAll) ?></strong>
      </div>
      <div class="stats-card">
        <span>Participants</span>
        <strong id="participantCount"><?= count($participantsAll) ?></strong>
      </div>
    </section>

    <section class="panel intro-panel">
      <h2>Dashboord NutriSmart</h2>
      <p class="note">Cette page permet d’ajouter des catégories, des événements et des inscriptions avec des identifiants courts et propres. Chaque ajout reste synchronisé avec le front office. et permet de rechercher et de trier les données ou de les exporter en PDF</p>
      <?php if ($flashError !== ''): ?>
        <p class="note">Erreur de saisie : <?= htmlspecialchars((string) $flashError, ENT_QUOTES, 'UTF-8') ?></p>
      <?php elseif ($flashSuccess !== ''): ?>
        <p class="note">Operation reussie.</p>
      <?php endif; ?>
    </section>

    <section id="categorySection" class="panel">
      <div class="panel-header">
        <div>
          <p class="kicker kicker-soft">Gestion</p>
          <h2>Ajouter une nouvelle catégorie</h2>
        </div>
      </div>
      <form id="categoryForm" class="form-grid two-columns" method="POST" action="../../Controller/NutrismartController.php?action=addCategory">
        <input type="hidden" name="redirect" value="/nutrismart_evenement/View/BackOffice/index.php#categorySection" />
        <div>
          <label for="categoryName">Nom de la catégorie</label>
          <input id="categoryName" name="name" required type="text" minlength="3" maxlength="80" placeholder="Ex : Nutrition sportive" title="Le nom doit contenir entre 3 et 80 caracteres." />
        </div>
        <div>
          <label for="categoryDescription">Description</label>
          <input id="categoryDescription" name="description" type="text" maxlength="255" placeholder="Courte description" title="La description ne doit pas depasser 255 caracteres." />
        </div>
        <button class="primary-btn" type="submit">Ajouter la catégorie</button>
      </form>
      <div class="table-controls">
        <div class="search-group">
          <label for="catSearchId">Rechercher par ID</label>
          <input id="catSearchId" type="number" min="1" placeholder="Ex: 1" value="<?= $catId !== null ? htmlspecialchars((string) $catId, ENT_QUOTES, 'UTF-8') : '' ?>" />
        </div>
        <div class="controls-actions">
          <button class="primary-btn apply-btn" type="button" data-apply-table="categories">Appliquer</button>
          <button class="primary-btn export-pdf-btn" type="button" data-export-table="categories">Exporter PDF</button>
        </div>
      </div>
      <div id="categoryTableContainer">
        <?php if ($loadError): ?>
          <p class="note">Impossible de charger la base de donnees : <?= htmlspecialchars((string) $loadError, ENT_QUOTES, 'UTF-8') ?></p>
        <?php else: ?>
          <div class="table-wrapper"><table class="table">
            <thead>
              <tr>
                <th class="sortable" data-table="categories" data-sort="id">
                  ID
                  <?php if ($catSortNorm === 'id'): ?><span class="sort-indicator"><?= $catDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th class="sortable" data-table="categories" data-sort="name">
                  Nom
                  <?php if ($catSortNorm === 'name'): ?><span class="sort-indicator"><?= $catDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th class="sortable" data-table="categories" data-sort="description">
                  Description
                  <?php if ($catSortNorm === 'description'): ?><span class="sort-indicator"><?= $catDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categoriesTable as $category): ?>
                <tr>
                  <td><span class="id-badge"><?= htmlspecialchars((string) $category['id'], ENT_QUOTES, 'UTF-8') ?></span></td>
                  <td><span class="small-badge"><?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?></span></td>
                  <td><?= htmlspecialchars((string) ($category['description'] ?: '-'), ENT_QUOTES, 'UTF-8') ?></td>
                  <td class="action-cell">
	                    <button class="edit-btn" data-id="<?= htmlspecialchars((string) $category['id'], ENT_QUOTES, 'UTF-8') ?>" data-name="<?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?>" data-description="<?= htmlspecialchars((string) $category['description'], ENT_QUOTES, 'UTF-8') ?>" onclick="afficherModalModifCategorie(this)">Modifier</button>
                    <button class="delete-btn" onclick="afficherConfirmSuppression('Supprimer la categorie <strong><?= htmlspecialchars((string) addslashes($category['name']), ENT_QUOTES, 'UTF-8') ?></strong> ? Ses evenements et participants seront aussi supprimes.', function(){ supprimerCategorie(<?= htmlspecialchars((string) $category['id'], ENT_QUOTES, 'UTF-8') ?>); })">Supprimer</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table></div>
        <?php endif; ?>
      </div>
    </section>

    <section id="eventSection" class="panel">
      <div class="panel-header">
        <div>
          <p class="kicker kicker-soft">Gestion</p>
          <h2>Ajouter un nouvel événement</h2>
        </div>
      </div>
      <form id="eventForm" class="form-grid two-columns" method="POST" action="../../Controller/NutrismartController.php?action=addEvent">
        <input type="hidden" name="redirect" value="/nutrismart_evenement/View/BackOffice/index.php#eventSection" />
        <div>
          <label for="eventTitle">Titre</label>
          <input id="eventTitle" name="title" required type="text" minlength="3" maxlength="120" placeholder="Nom de l'événement" title="Le titre doit contenir entre 3 et 120 caracteres." />
        </div>
        <div>
          <label for="eventCategory">Catégorie</label>
          <select id="eventCategory" name="categoryId" required>
            <option value="">Choisir une categorie</option>
            <?php foreach ($categoriesAll as $category): ?>
              <option value="<?= htmlspecialchars((string) $category['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label for="eventDate">Date</label>
          <input id="eventDate" name="date" required type="date" min="<?= date('Y-m-d') ?>" />
        </div>
        <div>
          <label for="eventTime">Heure</label>
          <input id="eventTime" name="time" required type="time" />
        </div>
        <div>
          <label for="eventLocation">Lieu</label>
          <input id="eventLocation" name="location" required type="text" minlength="3" maxlength="120" placeholder="Lieu" title="Le lieu doit contenir entre 3 et 120 caracteres." />
        </div>
        <div>
          <label for="eventSeats">Nombre de places</label>
          <input id="eventSeats" name="seats" required type="number" min="1" step="1" placeholder="50" title="Le nombre de places doit etre superieur a 0." />
        </div>
        <div class="full-width">
          <label for="eventDescription">Description</label>
          <div class="description-wrapper">
            <textarea id="eventDescription" name="description" required minlength="10" maxlength="1000" rows="4" placeholder="Description de l'événement" title="La description doit contenir entre 10 et 1000 caracteres."></textarea>
            <button type="button" class="ia-btn" id="genererIaBtn" title="Générer une description avec l'IA">
              <span class="ia-btn-icon">✦</span>
              <span class="ia-btn-text">Générer avec IA</span>
            </button>
          </div>
        </div>
        <div class="full-width">
          <label>Image de l'événement</label>
          <div class="upload-wrapper" id="uploadWrapper">
            <!-- Zone de drop / bouton upload -->
            <div class="upload-zone" id="uploadZone">
              <div class="upload-zone-inner">
                <div class="upload-icon-wrap">
                  <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <p class="upload-label">Glissez une photo ici</p>
                <p class="upload-sub">ou</p>
                <button type="button" class="upload-btn" id="uploadBtn">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                  Choisir une photo
                </button>
                <p class="upload-hint">JPG, PNG, WEBP — max 5 Mo</p>
              </div>
            </div>
            <!-- Prévisualisation -->
            <div class="upload-preview hidden" id="uploadPreview">
              <img id="uploadPreviewImg" src="" alt="Aperçu" />
              <div class="upload-preview-overlay">
                <button type="button" class="upload-change-btn" id="uploadChangeBtn">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                  Changer
                </button>
                <button type="button" class="upload-remove-btn" id="uploadRemoveBtn">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                  Supprimer
                </button>
              </div>
              <div class="upload-progress hidden" id="uploadProgress">
                <div class="upload-progress-bar" id="uploadProgressBar"></div>
              </div>
            </div>
            <!-- Input file caché -->
            <input type="file" id="uploadFileInput" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none" />
            <!-- Champ URL final envoyé au serveur -->
            <input type="hidden" id="eventImage" name="image" />
            <!-- Fallback URL manuelle -->
            <div class="upload-url-fallback">
              <span>ou coller une URL :</span>
              <input type="url" id="eventImageUrl" placeholder="https://..." style="flex:1" />
            </div>
          </div>
        </div>
        <!-- Sélecteur de localisation sur carte -->
        <div class="full-width">
          <label>📍 Localisation sur la carte <span class="map-hint">(cliquez sur la carte pour placer l'événement)</span></label>
          <div id="mapPicker" class="map-picker"></div>
          <div class="map-coords-row">
            <span id="mapCoordsText" class="map-coords-text">Aucune position sélectionnée</span>
            <button type="button" class="map-clear-btn" id="mapClearBtn">✕ Effacer</button>
          </div>
          <input type="hidden" id="eventLatitude"  name="latitude" />
          <input type="hidden" id="eventLongitude" name="longitude" />
          <input type="hidden" id="eventGoogleMaps" name="googleMapsLink" />
        </div>
        <button class="primary-btn" type="submit">Ajouter l'événement</button>
      </form>
      <div class="table-controls">
        <div class="search-group">
          <label for="evtSearchId">Rechercher par ID</label>
          <input id="evtSearchId" type="number" min="1" placeholder="Ex: 1" value="<?= $evtId !== null ? htmlspecialchars((string) $evtId, ENT_QUOTES, 'UTF-8') : '' ?>" />
        </div>
        <div class="controls-actions">
          <button class="primary-btn apply-btn" type="button" data-apply-table="events">Appliquer</button>
          <button class="primary-btn export-pdf-btn" type="button" data-export-table="events">Exporter PDF</button>
        </div>
      </div>
      <div id="eventTableContainer">
        <?php if (!$loadError): ?>
          <div class="table-wrapper"><table class="table">
            <thead>
              <tr>
                <th class="sortable" data-table="events" data-sort="id">
                  ID
                  <?php if ($evtSortNorm === 'id'): ?><span class="sort-indicator"><?= $evtDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th class="sortable" data-table="events" data-sort="title">
                  Titre
                  <?php if ($evtSortNorm === 'title'): ?><span class="sort-indicator"><?= $evtDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th class="sortable" data-table="events" data-sort="category">
                  Categorie
                  <?php if ($evtSortNorm === 'category'): ?><span class="sort-indicator"><?= $evtDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th class="sortable" data-table="events" data-sort="date">
                  Date
                  <?php if ($evtSortNorm === 'date'): ?><span class="sort-indicator"><?= $evtDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th class="sortable" data-table="events" data-sort="location">
                  Lieu
                  <?php if ($evtSortNorm === 'location'): ?><span class="sort-indicator"><?= $evtDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th>Actions</th>
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
                  <td class="action-cell">
	                    <button class="edit-btn" data-id="<?= htmlspecialchars((string) $event['id'], ENT_QUOTES, 'UTF-8') ?>" data-title="<?= htmlspecialchars((string) $event['title'], ENT_QUOTES, 'UTF-8') ?>" data-category-id="<?= htmlspecialchars((string) $event['categoryId'], ENT_QUOTES, 'UTF-8') ?>" data-date="<?= htmlspecialchars((string) $event['date'], ENT_QUOTES, 'UTF-8') ?>" data-time="<?= htmlspecialchars((string) $event['time'], ENT_QUOTES, 'UTF-8') ?>" data-location="<?= htmlspecialchars((string) $event['location'], ENT_QUOTES, 'UTF-8') ?>" data-seats="<?= htmlspecialchars((string) $event['seats'], ENT_QUOTES, 'UTF-8') ?>" data-description="<?= htmlspecialchars((string) $event['description'], ENT_QUOTES, 'UTF-8') ?>" data-image="<?= htmlspecialchars((string) $event['image'], ENT_QUOTES, 'UTF-8') ?>" data-google-maps-link="<?= htmlspecialchars((string) ($event['googleMapsLink'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" data-latitude="<?= htmlspecialchars((string) ($event['latitude'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" data-longitude="<?= htmlspecialchars((string) ($event['longitude'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" onclick="afficherModalModifEvenement(this)">Modifier</button>
                    <button class="mail-btn" onclick="envoyerRappel(<?= (int)$event['id'] ?>, '<?= htmlspecialchars((string) addslashes($event['title']), ENT_QUOTES, 'UTF-8') ?>')" title="Envoyer un rappel par email aux participants">📧 Rappel</button>
                    <button class="delete-btn" onclick="afficherConfirmSuppression('Supprimer <strong><?= htmlspecialchars((string) addslashes($event['title']), ENT_QUOTES, 'UTF-8') ?></strong> ? Ses participants seront aussi supprimes.', function(){ supprimerEvenement(<?= htmlspecialchars((string) $event['id'], ENT_QUOTES, 'UTF-8') ?>); })">Supprimer</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table></div>
        <?php endif; ?>
      </div>
    </section>

    <section id="participantSection" class="panel">
      <div class="panel-header">
        <div>
          <p class="kicker kicker-soft">Suivi</p>
          <h2>Liste des participants</h2>
        </div>
      </div>
      <div class="table-controls">
        <div class="search-group">
          <label for="parSearchId">Rechercher par ID</label>
          <input id="parSearchId" type="number" min="1" placeholder="Ex: 1" value="<?= $parId !== null ? htmlspecialchars((string) $parId, ENT_QUOTES, 'UTF-8') : '' ?>" />
        </div>
        <div class="controls-actions">
          <button class="primary-btn apply-btn" type="button" data-apply-table="participants">Appliquer</button>
          <button class="primary-btn export-pdf-btn" type="button" data-export-table="participants">Exporter PDF</button>
        </div>
      </div>
      <div id="participantTableContainer">
        <?php if (!$loadError && empty($participantsTable)): ?>
          <p class="note">Aucun participant enregistre pour le moment.</p>
        <?php elseif (!$loadError): ?>
          <div class="table-wrapper"><table class="table">
            <thead>
              <tr>
                <th class="sortable" data-table="participants" data-sort="id">
                  ID
                  <?php if ($parSortNorm === 'id'): ?><span class="sort-indicator"><?= $parDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th class="sortable" data-table="participants" data-sort="fullName">
                  Participant
                  <?php if ($parSortNorm === 'fullname'): ?><span class="sort-indicator"><?= $parDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th class="sortable" data-table="participants" data-sort="email">
                  Email
                  <?php if ($parSortNorm === 'email'): ?><span class="sort-indicator"><?= $parDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th class="sortable" data-table="participants" data-sort="phone">
                  Telephone
                  <?php if ($parSortNorm === 'phone'): ?><span class="sort-indicator"><?= $parDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th class="sortable" data-table="participants" data-sort="event">
                  Evenement
                  <?php if ($parSortNorm === 'event'): ?><span class="sort-indicator"><?= $parDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th class="sortable" data-table="participants" data-sort="registeredAt">
                  Inscrit le
                  <?php if ($parSortNorm === 'registeredat'): ?><span class="sort-indicator"><?= $parDirNorm === 'DESC' ? '▼' : '▲' ?></span><?php endif; ?>
                </th>
                <th>Actions</th>
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
                  <td class="action-cell">
	                    <button class="edit-btn" data-id="<?= htmlspecialchars((string) $participant['id'], ENT_QUOTES, 'UTF-8') ?>" data-full-name="<?= htmlspecialchars((string) $participant['fullName'], ENT_QUOTES, 'UTF-8') ?>" data-email="<?= htmlspecialchars((string) $participant['email'], ENT_QUOTES, 'UTF-8') ?>" data-phone="<?= htmlspecialchars((string) $participant['phone'], ENT_QUOTES, 'UTF-8') ?>" data-event-id="<?= htmlspecialchars((string) $participant['eventId'], ENT_QUOTES, 'UTF-8') ?>" onclick="afficherModalModifParticipant(this)">Modifier</button>
                    <button class="delete-btn" onclick="afficherConfirmSuppression('Supprimer le participant <strong><?= htmlspecialchars((string) addslashes($participant['fullName']), ENT_QUOTES, 'UTF-8') ?></strong> ?', function(){ supprimerParticipant(<?= htmlspecialchars((string) $participant['id'], ENT_QUOTES, 'UTF-8') ?>); })">Supprimer</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table></div>
        <?php endif; ?>
      </div>
    </section>
	  </main>

	  <select id="eventOptionsSource" hidden>
	    <?php foreach ($eventsAll as $event): ?>
	      <option value="<?= htmlspecialchars((string) $event['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $event['title'], ENT_QUOTES, 'UTF-8') ?></option>
	    <?php endforeach; ?>
	  </select>

	  <!-- Leaflet JS — carte interactive -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="script.js?v=20260430-maps"></script>
</body>
</html>
