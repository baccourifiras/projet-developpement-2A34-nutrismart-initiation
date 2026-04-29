<?php
// ── Page d'accueil du front office ──
// Affiche une présentation de l'application et des liens vers les différentes sections
// Aucune interaction avec la base de données n'est nécessaire ici, tout est statique
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Bienvenue</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap">
  <link rel="stylesheet" href="style.css" />
</head>
<body class="accueil-body">

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

  <!-- ── HERO ── -->
  <section class="accueil-hero">
    <div class="accueil-badge">🥗 Application nutrition &amp; bien-être</div>

    <h1 class="accueil-title">
      Mangez mieux,<br>
      vivez <span class="accent">NutriSmart</span>
    </h1>

    <p class="accueil-subtitle">
      NutriSmart est une plateforme intelligente pour une alimentation plus saine,
      plus organisée et plus responsable grâce au suivi nutritionnel, aux recettes et à l'anti-gaspillage.
    </p>

    <div class="accueil-cta-row">
      <a href="#info" class="btn-primary-accueil">Découvrir NutriSmart</a>
      <a href="#pages" class="btn-secondary-accueil">Accéder aux pages</a>
    </div>

    <div class="scroll-hint">Défiler</div>
  </section>

  <!-- ── STATS ── -->
  <section class="accueil-stats">
    <div class="stats-grid">
      <div class="stat-item">
        <span class="stat-number">Assistant</span>
        <span class="stat-label">IA nutritionnel</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">Generateur</span>
        <span class="stat-label">Menus personnalisés</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">30%</span>
        <span class="stat-label">Anti-gaspillage</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">Suivi</span>
        <span class="stat-label">des objectifs</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">Reservation</span>
        <span class="stat-label">d'événements</span>
      </div>
    </div>
  </section>

  <!-- ── ACCÈS AUX PAGES ── -->
  <section class="accueil-info" id="pages">
    <h2 class="accueil-info-title">Accéder aux autres pages</h2>

    <div class="info-cards-grid">
      <div class="info-feature-card">
        <span class="feature-icon">📅</span>
        <h3><a href="index.php">Événements</a></h3>
        <p>Consultez les catégories, les événements disponibles et participez à l'activité qui vous intéresse.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">🥗</span>
        <h3><a href="tache-categories.php">Tâche 1 - Achat online</a></h3>
        <p>Accédez à la page réservée à la gestion et à la présentation des catégories.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">🗓️</span>
        <h3><a href="tache-evenements.php">Tâche 2 - Événements</a></h3>
        <p>Ouvrez la page dédiée aux événements nutritionnels et aux informations associées.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">👥</span>
        <h3><a href="tache-participants.php">Tâche 3 - Cource et stocks</a></h3>
        <p>Accédez à l'espace de travail lié aux participants et aux inscriptions.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">🎨</span>
        <h3><a href="tache-design.php">Tâche 4 - Système de défis</a></h3>
        <p>Ouvrez la page de travail consacrée au design et à l'amélioration visuelle.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">⚡</span>
        <h3><a href="tache-javascript.php">Tâche 5 - Espace communauté</a></h3>
        <p>Accédez à la page réservée aux interactions et au comportement JavaScript.</p>
      </div>
    </div>
  </section>

  <!-- ── INFO ── -->
  <section class="accueil-info" id="info">
    <h2 class="accueil-info-title">Pourquoi NutriSmart ?</h2>

    <div class="info-cards-grid">
      <div class="info-feature-card">
        <span class="feature-icon">🤖</span>
        <h3>Personnalisation intelligente</h3>
        <p>Des recommandations adaptées au profil, aux objectifs et aux préférences de chaque utilisateur.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">🛒</span>
        <h3>Courses simplifiées</h3>
        <p>Génération automatique de listes de courses avec une organisation claire pour préparer les repas plus facilement.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">🥦</span>
        <h3>Recettes intelligentes</h3>
        <p>Proposition de repas optimisés selon les habitudes alimentaires, les objectifs et les ingrédients disponibles.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">🌱</span>
        <h3>Réduction du gaspillage</h3>
        <p>Suggestions basées sur le stock du frigo et les dates d'expiration pour consommer de manière plus responsable.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">📊</span>
        <h3>Suivi nutritionnel</h3>
        <p>Suivi des calories, des nutriments et de la progression vers les objectifs alimentaires.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">🧭</span>
        <h3>Vision durable</h3>
        <p>Un projet qui relie santé, innovation, consommation responsable et alimentation plus durable.</p>
      </div>
    </div>
  </section>

  <!-- ── CONCLUSION ── -->
  <section class="accueil-info" id="conclusion">
    <h2 class="accueil-info-title">Conclusion</h2>

    <div class="info-cards-grid">
      <div class="info-feature-card">
        <span class="feature-icon">✅</span>
        <h3>Une solution complète</h3>
        <p>NutriSmart réunit recettes, courses, suivi nutritionnel et analyse intelligente dans une seule plateforme.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">💡</span>
        <h3>Innovation et santé</h3>
        <p>L'intelligence artificielle aide à proposer un accompagnement personnalisé pour améliorer les habitudes alimentaires.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">🌍</span>
        <h3>Alimentation responsable</h3>
        <p>Le projet encourage une alimentation plus saine, plus intelligente et plus durable tout en réduisant le gaspillage alimentaire.</p>
      </div>
    </div>
  </section>

  <!-- ── FOOTER ── -->
  <footer class="accueil-footer">
    <p>© 2026 <strong>NutriSmart</strong> — Eat Smart Live Smart —</p>
  </footer>
  <script src="script.js?v=20260428-structure"></script>
</body>
</html>

