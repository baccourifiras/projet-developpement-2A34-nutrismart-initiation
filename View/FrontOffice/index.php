<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Config.php';
require_once __DIR__ . '/../../Controller/CategoryController.php';
require_once __DIR__ . '/../../Controller/EventController.php';
require_once __DIR__ . '/../../Controller/ParticipantController.php';

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function categoryInitials($name) {
    $words = preg_split('/\s+/', trim($name));
    $first = isset($words[0][0]) ? $words[0][0] : 'N';
    $second = isset($words[1][0]) ? $words[1][0] : '';
    return strtoupper($first . $second);
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

function participantCountByEvent($participants, $eventId) {
    $count = 0;
    foreach ($participants as $participant) {
        if ((int) $participant['eventId'] === (int) $eventId) {
            $count++;
        }
    }
    return $count;
}

$categories = array();
$events = array();
$participants = array();
$loadError = '';
$flashSuccess = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : '';
unset($_SESSION['flash_success']);

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
          <div class="no-data reveal visible">Impossible de charger la base de donnees : <?= e($loadError) ?></div>
        <?php elseif (empty($categories)): ?>
          <div class="no-data reveal visible">Aucune categorie disponible.</div>
        <?php else: ?>
          <?php foreach ($categories as $index => $category): ?>
	            <article class="category-card reveal" data-category-id="<?= e($category['id']) ?>" data-category-name="<?= e($category['name']) ?>" style="transition-delay: <?= (int) $index * 70 ?>ms">
              <span class="shine"></span>
              <span class="category-icon"><?= e(categoryInitials($category['name'])) ?></span>
              <h3><?= e($category['name']) ?></h3>
              <p><?= e($category['description'] ?: 'Categorie disponible.') ?></p>
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
          <div class="no-data reveal visible"><?= e($loadError) ?></div>
        <?php elseif (empty($events)): ?>
          <div class="no-data reveal visible">Aucun evenement disponible.</div>
        <?php else: ?>
          <?php foreach ($events as $index => $event): ?>
            <article class="event-card reveal" data-category-id="<?= e($event['categoryId']) ?>" style="transition-delay: <?= (int) $index * 90 ?>ms">
              <img class="event-image" src="<?= e($event['image']) ?>" alt="<?= e($event['title']) ?>">
              <div class="event-content">
                <span class="event-badge"><?= e(categoryNameById($categories, $event['categoryId'])) ?></span>
                <h3><?= e($event['title']) ?></h3>
                <p><?= e($event['description']) ?></p>
                <p class="meta"><strong>Date :</strong> <?= e(formatDateFr($event['date'])) ?> a <?= e($event['time']) ?></p>
                <p class="meta"><strong>Lieu :</strong> <?= e($event['location']) ?></p>
                <p class="meta"><strong>Places :</strong> <?= e($event['seats']) ?></p>
                <div class="event-actions">
                  <span class="counter"><?= participantCountByEvent($participants, $event['id']) ?> participant(s)</span>
	                  <button class="primary-btn" data-event-id="<?= e($event['id']) ?>" data-event-title="<?= e($event['title']) ?>">Participer</button>
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
          <input id="fullName" name="fullName" required type="text" minlength="3" maxlength="120" placeholder="Votre nom" />
        </div>
        <div>
          <label for="email">Email</label>
          <input id="email" name="email" required type="email" maxlength="160" placeholder="votre@email.com" />
        </div>
        <div>
          <label for="phone">Téléphone</label>
          <input id="phone" name="phone" required type="text" minlength="8" maxlength="8" pattern="[2459][0-9]{7}" placeholder="22111222" />
        </div>
        <button type="submit" class="primary-btn">Valider la participation</button>
      </form>
      <p id="messageBox" class="message"></p>
    </div>
  </div>

	  <?php if ($flashSuccess === 'add' || (isset($_GET['success']) && $_GET['success'] === 'add')): ?>
    <div class="front-success-toast" id="frontSuccessToast">
      <strong>Participation enregistree</strong>
      <span>Votre inscription a ete ajoutee avec succes.</span>
    </div>
  <?php endif; ?>

	  <script src="script.js?v=20260415-front-success"></script>
  <script>
    /* =====================================================
       NAVBAR — animations et comportement au scroll
       ===================================================== */
    (function () {
      var navbar = document.getElementById('navbar');

      /* 1. Effet scroll : la navbar devient plus opaque et compacte */
      window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
          navbar.classList.add('scrolled');
        } else {
          navbar.classList.remove('scrolled');
        }
      });

      /* 2. Marquer le lien actif selon la page courante */
      var currentPage = window.location.pathname.split('/').pop() || 'index.php';
      document.querySelectorAll('.nav-links a').forEach(function (link) {
        if (link.getAttribute('href') === currentPage) {
          link.classList.add('active');
        }
      });

      /* 3. Micro-animation au clic : effet ripple sur le lien cliqué */
      document.querySelectorAll('.nav-links a').forEach(function (link) {
        link.addEventListener('click', function () {
          this.style.transform = 'scale(0.93)';
          var self = this;
          setTimeout(function () { self.style.transform = ''; }, 150);
        });
      });
    })();
  </script>
</body>
</html>
