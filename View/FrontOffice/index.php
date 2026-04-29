<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Config.php';
require_once __DIR__ . '/../../Controller/CategoryController.php';
require_once __DIR__ . '/../../Controller/EventController.php';
require_once __DIR__ . '/../../Controller/ParticipantController.php';

$categories = array();
$events = array();
$participants = array();
$loadError = '';
$flashSuccess = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : '';
$flashError = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart - Front Office Événements</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <!-- =====================================================
       NAVBAR FIXE - visible sur toutes les pages
       ===================================================== -->
  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">NutriSmart</div>
      <div class="slogan">Eat Smart Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="accueil.php">Accueil</a>
      <a href="index.php">Événements</a>
      <a href="tache-categories.php">Tâche 1</a>
      <a href="tache-evenements.php">Tâche 2</a>
      <a href="tache-participants.php">Tâche 3</a>
      <a href="tache-design.php">Tâche 4</a>
      <a href="tache-javascript.php">Tâche 5</a>
      <a href="../backoffice/index.php" class="nav-dashboard">Dashboard</a>
    </div>
  </nav>

  <header class="hero">
        </div>
      </div>
      <div class="search-overlay" id="searchOverlay"></div>
    </nav>
    <div class="hero-content">
      <div>
        <p class="badge">Application nutrition & bien-être</p>
        <h1>Découvrez les événements nutritionnels par catégorie</h1>
        <p class="subtitle">NutriSmart aide les utilisateurs à trouver des ateliers, conférences et programmes autour de la nutrition, de la santé et des régimes alimentaires. Choisissez une catégorie pour afficher les événements correspondants et participez facilement.</p>
        <a class="cta" href="#categories">Voir les catégories</a>
      </div>
    </div>
  </header>

  <main class="container">
    <section id="categories" class="section">
      <div class="section-title-row">
        <div>
          <p class="section-kicker">Catégories</p>
          <h2>Liste des catégories</h2>
        </div>
        <button id="showAllBtn" class="secondary-btn">Afficher tout</button>
      </div>
      <div id="categoryList" class="category-grid">
        <?php if ($loadError): ?>
          <div class="no-data reveal visible">Impossible de charger la base de donnees : <?= htmlspecialchars((string) $loadError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php elseif (empty($categories)): ?>
          <div class="no-data reveal visible">Aucune categorie disponible.</div>
        <?php else: ?>
          <?php foreach ($categories as $index => $category): ?>
	            <article class="category-card reveal" data-category-id="<?= htmlspecialchars((string) $category['id'], ENT_QUOTES, 'UTF-8') ?>" data-category-name="<?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?>" data-reveal-delay="<?= (int) $index * 70 ?>">
              <span class="shine"></span>
              <span class="category-icon"><?= htmlspecialchars((string) $categoryController->categoryInitials($category['name']), ENT_QUOTES, 'UTF-8') ?></span>
              <h3><?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?></h3>
              <p><?= htmlspecialchars((string) ($category['description'] ?: 'Categorie disponible.'), ENT_QUOTES, 'UTF-8') ?></p>
              <span class="category-tag">Voir les evenements</span>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <section id="events" class="section">
      <div class="section-title-row">
        <div>
          <p class="section-kicker">Événements</p>
          <h2 id="eventsTitle">Tous les événements</h2>
        </div>
      </div>
      <div id="eventList" class="event-grid">
        <?php if ($loadError): ?>
          <div class="no-data reveal visible"><?= htmlspecialchars((string) $loadError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php elseif (empty($events)): ?>
          <div class="no-data reveal visible">Aucun evenement disponible.</div>
        <?php else: ?>
          <?php foreach ($events as $index => $event): ?>
            <article class="event-card reveal" data-category-id="<?= htmlspecialchars((string) $event['categoryId'], ENT_QUOTES, 'UTF-8') ?>" data-reveal-delay="<?= (int) $index * 90 ?>">
              <img class="event-image" src="<?= htmlspecialchars((string) $event['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) $event['title'], ENT_QUOTES, 'UTF-8') ?>">
              <div class="event-content">
                <span class="event-badge"><?= htmlspecialchars((string) $categoryController->categoryNameById($categories, $event['categoryId']), ENT_QUOTES, 'UTF-8') ?></span>
                <h3><?= htmlspecialchars((string) $event['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= htmlspecialchars((string) $event['description'], ENT_QUOTES, 'UTF-8') ?></p>
                <p class="meta"><strong>Date :</strong> <?= htmlspecialchars((string) $eventController->formatDateFr($event['date']), ENT_QUOTES, 'UTF-8') ?> a <?= htmlspecialchars((string) $event['time'], ENT_QUOTES, 'UTF-8') ?></p>
                <p class="meta"><strong>Lieu :</strong> <?= htmlspecialchars((string) $event['location'], ENT_QUOTES, 'UTF-8') ?></p>
                <p class="meta"><strong>Places :</strong> <?= htmlspecialchars((string) $event['seats'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="event-actions">
                  <span class="counter"><?= $participantController->participantCountByEvent($participants, $event['id']) ?> participant(s)</span>
	                  <button class="primary-btn" data-event-id="<?= htmlspecialchars((string) $event['id'], ENT_QUOTES, 'UTF-8') ?>" data-event-title="<?= htmlspecialchars((string) $event['title'], ENT_QUOTES, 'UTF-8') ?>">Participer</button>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <section id="about" class="section info-section">
      <div class="section-title-row">
        <div>
          <p class="section-kicker">À propos</p>
          <h2>NutriSmart, régimes et nutrition</h2>
        </div>
      </div>
      <div class="info-grid">
        <article class="info-card">
          <h3>Nutrition équilibrée</h3>
          <p>Notre application présente des événements éducatifs pour apprendre à mieux manger, organiser ses repas et adopter de bonnes habitudes alimentaires.</p>
        </article>
        <article class="info-card">
          <h3>Suivi des régimes</h3>
          <p>Les catégories permettent de retrouver facilement des événements liés aux régimes minceur, sportifs, végétariens ou rééquilibrage alimentaire.</p>
        </article>
        <article class="info-card">
          <h3>Expérience simple</h3>
          <p>cette page affiche les catégories et les événements, alors que le Dashboard permet d'ajouter de nouvelles catégories et de nouveaux événements visibles immédiatement.</p>
        </article>
      </div>
      <p class="conclusion"><strong>Conclusion :</strong> NutriSmart est une application moderne qui relie nutrition, régimes et événements de sensibilisation pour offrir une plateforme simple, claire et utile.</p>
    </section>
  </main>

  <div id="registerModal" class="modal hidden">
    <div class="modal-card">
      <button class="close-btn" id="closeModalBtn">×</button>
      <h3>Participer à l'événement</h3>
      <p id="selectedEventText" class="selected-event"></p>
      <form id="participantForm" class="form-grid" method="POST" action="../../Controller/NutrismartController.php?action=addParticipant">
        <input type="hidden" id="participantEventId" name="eventId" />
        <input type="hidden" name="redirect" value="/nutrismart_evenement/View/FrontOffice/index.php" />
        <div>
          <label for="fullName">Nom complet</label>
          <input id="fullName" name="fullName" required type="text" minlength="3" maxlength="120" placeholder="Votre nom" title="Le nom complet doit contenir entre 3 et 120 caracteres." />
        </div>
        <div>
          <label for="email">Email</label>
          <input id="email" name="email" required type="email" maxlength="160" placeholder="votre@email.com" title="Veuillez saisir une adresse email valide." />
        </div>
        <div>
          <label for="phone">Téléphone</label>
          <input id="phone" name="phone" required type="text" minlength="8" maxlength="8" pattern="[2459][0-9]{7}" placeholder="22111222" title="Le telephone doit contenir 8 chiffres et commencer par 2, 4, 5 ou 9." />
        </div>
        <button type="submit" class="primary-btn">Valider la participation</button>
      </form>
      <p id="messageBox" class="message"><?= htmlspecialchars((string) $flashError, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
  </div>

	  <?php if ($flashSuccess === 'add' || (isset($_GET['success']) && $_GET['success'] === 'add')): ?>
    <div class="front-success-toast" id="frontSuccessToast">
      <strong>Participation enregistree</strong>
      <span>Votre inscription a ete ajoutee avec succes.</span>
    </div>
  <?php endif; ?>
  <script src="script.js?v=20260428-structure"></script>
</body>
</html>

