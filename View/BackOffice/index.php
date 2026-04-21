<?php
require_once __DIR__ . '/../../Config.php';
require_once __DIR__ . '/../../Controller/CategoryController.php';
require_once __DIR__ . '/../../Controller/EventController.php';
require_once __DIR__ . '/../../Controller/ParticipantController.php';

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function formatDateFr($date) {
    if (!$date) return '-';
    $timestamp = strtotime($date);
    return $timestamp ? date('d/m/Y', $timestamp) : '-';
}

function categoryNameById($categories, $categoryId) {
    foreach ($categories as $category) {
        if ((int) $category['id'] === (int) $categoryId) {
            return $category['name'];
        }
    }
    return 'Sans categorie';
}

function eventTitleById($events, $eventId) {
    foreach ($events as $event) {
        if ((int) $event['id'] === (int) $eventId) {
            return $event['title'];
        }
    }
    return 'Evenement introuvable';
}

$categories = array();
$events = array();
$participants = array();
$loadError = '';

try {
    $pdo = config::getConnexion();
    $categoryController = new CategoryController($pdo);
    $eventController = new EventController($pdo);
    $participantController = new ParticipantController($pdo);

    $categories = $categoryController->getAll();
    $events = $eventController->getAll();
    $participants = $participantController->getAll();
} catch (Throwable $error) {
    $loadError = $error->getMessage();
}
/*
 * ============================================================
 * NutriSmart — Fichier PHP
 *
 * Pour l'instant ce fichier est une page HTML standard.
 * Quand le projet sera connecté à MySQL :
 *   - Remplacer les données JavaScript par des requêtes SQL
 *   - Exemple : $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
 *   - Utiliser echo pour injecter les données dans la page
 * ============================================================
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart - Dashboard</title>
  <link rel="stylesheet" href="style.css?v=20260415-toast" />
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
        <strong id="categoryCount"><?= count($categories) ?></strong>
      </div>
      <div class="stats-card">
        <span>Événements</span>
        <strong id="eventCount"><?= count($events) ?></strong>
      </div>
      <div class="stats-card">
        <span>Participants</span>
        <strong id="participantCount"><?= count($participants) ?></strong>
      </div>
    </section>

    <section class="panel intro-panel">
      <p class="kicker">Projet</p>
      <h2>Dashboord NutriSmart</h2>
      <p class="note">Cette page permet d’ajouter des catégories, des événements et des inscriptions avec des identifiants courts et propres. Chaque ajout reste synchronisé avec le front office.</p>
    </section>

    <section id="categorySection" class="panel">
      <div class="panel-header">
        <div>
          <p class="kicker kicker-soft">Gestion</p>
          <h2>Ajouter une nouvelle catégorie</h2>
        </div>
      </div>
      <form id="categoryForm" class="form-grid two-columns" method="POST" action="../../Controller/NutrismartController.php?action=addCategory">
        <div>
          <label for="categoryName">Nom de la catégorie</label>
          <input id="categoryName" name="name" required type="text" minlength="3" maxlength="80" placeholder="Ex : Nutrition sportive" />
        </div>
        <div>
          <label for="categoryDescription">Description</label>
          <input id="categoryDescription" name="description" type="text" maxlength="255" placeholder="Courte description" />
        </div>
        <button class="primary-btn" type="submit">Ajouter la catégorie</button>
      </form>
      <div id="categoryTableContainer">
        <?php if ($loadError): ?>
          <p class="note">Impossible de charger la base de donnees : <?= e($loadError) ?></p>
        <?php else: ?>
          <div class="table-wrapper"><table class="table">
            <thead><tr><th>ID</th><th>Nom</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach ($categories as $category): ?>
                <tr>
                  <td><span class="id-badge"><?= e($category['id']) ?></span></td>
                  <td><span class="small-badge"><?= e($category['name']) ?></span></td>
                  <td><?= e($category['description'] ?: '-') ?></td>
                  <td class="action-cell">
	                    <button class="edit-btn" data-id="<?= e($category['id']) ?>" data-name="<?= e($category['name']) ?>" data-description="<?= e($category['description']) ?>" onclick="afficherModalModifCategorie(this)">Modifier</button>
                    <button class="delete-btn" onclick="afficherConfirmSuppression('Supprimer la categorie <strong><?= e(addslashes($category['name'])) ?></strong> ? Ses evenements et participants seront aussi supprimes.', function(){ supprimerCategorie(<?= e($category['id']) ?>); })">Supprimer</button>
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
        <div>
          <label for="eventTitle">Titre</label>
          <input id="eventTitle" name="title" required type="text" minlength="3" maxlength="120" placeholder="Nom de l'événement" />
        </div>
        <div>
          <label for="eventCategory">Catégorie</label>
          <select id="eventCategory" name="categoryId" required>
            <option value="">Choisir une categorie</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= e($category['id']) ?>"><?= e($category['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label for="eventDate">Date</label>
          <input id="eventDate" name="date" required type="date" />
        </div>
        <div>
          <label for="eventTime">Heure</label>
          <input id="eventTime" name="time" required type="time" />
        </div>
        <div>
          <label for="eventLocation">Lieu</label>
          <input id="eventLocation" name="location" required type="text" minlength="3" maxlength="120" placeholder="Lieu" />
        </div>
        <div>
          <label for="eventSeats">Nombre de places</label>
          <input id="eventSeats" name="seats" required type="number" min="1" placeholder="50" />
        </div>
        <div class="full-width">
          <label for="eventDescription">Description</label>
          <textarea id="eventDescription" name="description" required minlength="10" maxlength="1000" rows="4" placeholder="Description de l'événement"></textarea>
        </div>
        <div class="full-width">
          <label for="eventImage">Image URL</label>
          <input id="eventImage" name="image" type="url" placeholder="https://..." />
        </div>
        <button class="primary-btn" type="submit">Ajouter l'événement</button>
      </form>
      <div id="eventTableContainer">
        <?php if (!$loadError): ?>
          <div class="table-wrapper"><table class="table">
            <thead><tr><th>ID</th><th>Titre</th><th>Categorie</th><th>Date</th><th>Lieu</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach ($events as $event): ?>
                <tr>
                  <td><span class="id-badge"><?= e($event['id']) ?></span></td>
                  <td><?= e($event['title']) ?></td>
                  <td><?= e(categoryNameById($categories, $event['categoryId'])) ?></td>
                  <td><?= e(formatDateFr($event['date'])) ?></td>
                  <td><?= e($event['location']) ?></td>
                  <td class="action-cell">
	                    <button class="edit-btn" data-id="<?= e($event['id']) ?>" data-title="<?= e($event['title']) ?>" data-category-id="<?= e($event['categoryId']) ?>" data-date="<?= e($event['date']) ?>" data-time="<?= e($event['time']) ?>" data-location="<?= e($event['location']) ?>" data-seats="<?= e($event['seats']) ?>" data-description="<?= e($event['description']) ?>" data-image="<?= e($event['image']) ?>" onclick="afficherModalModifEvenement(this)">Modifier</button>
                    <button class="delete-btn" onclick="afficherConfirmSuppression('Supprimer <strong><?= e(addslashes($event['title'])) ?></strong> ? Ses participants seront aussi supprimes.', function(){ supprimerEvenement(<?= e($event['id']) ?>); })">Supprimer</button>
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
      <div id="participantTableContainer">
        <?php if (!$loadError && empty($participants)): ?>
          <p class="note">Aucun participant enregistre pour le moment.</p>
        <?php elseif (!$loadError): ?>
          <div class="table-wrapper"><table class="table">
            <thead><tr><th>ID</th><th>Participant</th><th>Email</th><th>Telephone</th><th>Evenement</th><th>Inscrit le</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach ($participants as $participant): ?>
                <tr>
                  <td><span class="id-badge"><?= e($participant['id']) ?></span></td>
                  <td><?= e($participant['fullName']) ?></td>
                  <td><?= e($participant['email']) ?></td>
                  <td><?= e($participant['phone']) ?></td>
                  <td><?= e(eventTitleById($events, $participant['eventId'])) ?></td>
                  <td><?= e($participant['registeredAt']) ?></td>
                  <td class="action-cell">
	                    <button class="edit-btn" data-id="<?= e($participant['id']) ?>" data-full-name="<?= e($participant['fullName']) ?>" data-email="<?= e($participant['email']) ?>" data-phone="<?= e($participant['phone']) ?>" data-event-id="<?= e($participant['eventId']) ?>" onclick="afficherModalModifParticipant(this)">Modifier</button>
                    <button class="delete-btn" onclick="afficherConfirmSuppression('Supprimer le participant <strong><?= e(addslashes($participant['fullName'])) ?></strong> ?', function(){ supprimerParticipant(<?= e($participant['id']) ?>); })">Supprimer</button>
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
	    <?php foreach ($events as $event): ?>
	      <option value="<?= e($event['id']) ?>"><?= e($event['title']) ?></option>
	    <?php endforeach; ?>
	  </select>

	  <script src="script.js?v=20260415-clean"></script>
</body>
</html>
