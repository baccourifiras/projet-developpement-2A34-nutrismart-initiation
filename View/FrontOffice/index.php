<?php
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
      <div id="categoryList" class="category-grid"></div>
    </section>

    <section id="events" class="section">
      <div class="section-title-row">
        <div>
          <p class="section-kicker">Événements</p>
          <h2 id="eventsTitle">Tous les événements</h2>
        </div>
      </div>
      <div id="eventList" class="event-grid"></div>
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
        <div>
          <label for="fullName">Nom complet</label>
          <input id="fullName" name="fullName" required type="text" placeholder="Votre nom" />
        </div>
        <div>
          <label for="email">Email</label>
          <input id="email" name="email" required type="email" placeholder="votre@email.com" />
        </div>
        <div>
          <label for="phone">Téléphone</label>
          <input id="phone" name="phone" required type="text" placeholder="22 111 222" />
        </div>
        <button type="submit" class="primary-btn">Valider la participation</button>
      </form>
      <p id="messageBox" class="message"></p>
    </div>
  </div>

  <script src="script.js?v=20260415-iife"></script>
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
