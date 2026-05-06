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
  <style>
    /* ── Page d'accueil : styles propres à cette page ── */

    /* Fond animé avec dégradé en mouvement */
    .accueil-body {
      min-height: 100vh;
      background:
        radial-gradient(ellipse at 20% 20%, rgba(16, 185, 129, 0.18) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 80%, rgba(5, 150, 105, 0.12) 0%, transparent 50%),
        linear-gradient(160deg, #f0fdf8 0%, #ecfdf5 50%, #f7fffe 100%);
      overflow-x: hidden;
    }

    /* ── HERO SECTION ── */
    .accueil-hero {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 80px 24px 60px;
      position: relative;
      overflow: hidden;
    }

    /* Cercles décoratifs en arrière-plan */
    .accueil-hero::before {
      content: '';
      position: absolute;
      width: 600px; height: 600px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(16,185,129,0.10), transparent 70%);
      top: -150px; left: -100px;
      animation: floatOrb 10s ease-in-out infinite;
    }
    .accueil-hero::after {
      content: '';
      position: absolute;
      width: 400px; height: 400px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(5,150,105,0.08), transparent 70%);
      bottom: -100px; right: -80px;
      animation: floatOrb 14s ease-in-out infinite reverse;
    }

    @keyframes floatOrb {
      0%, 100% { transform: translate(0, 0) scale(1); }
      50%       { transform: translate(20px, -30px) scale(1.05); }
    }

    /* Badge animé */
    .accueil-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(16, 185, 129, 0.12);
      border: 1px solid rgba(16, 185, 129, 0.3);
      color: #065f46;
      padding: 8px 18px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      margin-bottom: 28px;
      animation: badgePop 0.6s cubic-bezier(0.23,1,0.32,1) 0.2s both;
    }

    @keyframes badgePop {
      from { opacity: 0; transform: scale(0.8) translateY(10px); }
      to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* Titre principal */
    .accueil-title {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(2.8rem, 7vw, 5.5rem);
      font-weight: 900;
      line-height: 1.05;
      color: #052e16;
      margin: 0 0 24px;
      animation: titleReveal 0.8s cubic-bezier(0.23,1,0.32,1) 0.35s both;
    }

    .accueil-title .accent {
      background: linear-gradient(135deg, #059669, #10b981, #34d399);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    @keyframes titleReveal {
      from { opacity: 0; transform: translateY(30px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* Sous-titre */
    .accueil-subtitle {
      font-size: clamp(1rem, 2vw, 1.2rem);
      color: #374151;
      max-width: 640px;
      line-height: 1.75;
      margin: 0 auto 44px;
      font-weight: 400;
      animation: titleReveal 0.8s cubic-bezier(0.23,1,0.32,1) 0.5s both;
    }

    /* Boutons CTA */
    .accueil-cta-row {
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
      justify-content: center;
      animation: titleReveal 0.8s cubic-bezier(0.23,1,0.32,1) 0.65s both;
    }

    .btn-primary-accueil {
      background: linear-gradient(135deg, #059669, #10b981);
      color: #ffffff;
      padding: 15px 36px;
      border-radius: 999px;
      font-family: 'Outfit', sans-serif;
      font-size: 15px;
      font-weight: 700;
      text-decoration: none;
      box-shadow: 0 8px 28px rgba(16,185,129,0.35);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .btn-primary-accueil:hover {
      transform: translateY(-3px) scale(1.03);
      box-shadow: 0 14px 36px rgba(16,185,129,0.45);
    }

    .btn-secondary-accueil {
      background: rgba(255,255,255,0.8);
      color: #065f46;
      padding: 15px 36px;
      border-radius: 999px;
      font-family: 'Outfit', sans-serif;
      font-size: 15px;
      font-weight: 700;
      text-decoration: none;
      border: 1.5px solid rgba(16,185,129,0.35);
      backdrop-filter: blur(8px);
      transition: transform 0.25s ease, background 0.25s ease;
    }
    .btn-secondary-accueil:hover {
      transform: translateY(-3px);
      background: rgba(255,255,255,0.95);
    }

    /* Scroll indicator */
    .scroll-hint {
      position: absolute;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      color: rgba(6,95,70,0.5);
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      animation: scrollBounce 2s ease-in-out infinite;
    }
    .scroll-hint::after {
      content: '';
      width: 1px;
      height: 40px;
      background: linear-gradient(to bottom, rgba(16,185,129,0.5), transparent);
    }
    @keyframes scrollBounce {
      0%, 100% { transform: translateX(-50%) translateY(0); }
      50%       { transform: translateX(-50%) translateY(8px); }
    }

    /* ── SECTION INFO ── */
    .accueil-info {
      padding: 80px 24px;
      max-width: 1100px;
      margin: 0 auto;
    }

    .accueil-info-title {
      font-family: 'Outfit', sans-serif;
      font-size: 2rem;
      font-weight: 900;
      color: #052e16;
      text-align: center;
      margin-bottom: 50px;
    }

    .info-cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
    }

    .info-feature-card {
      background: #ffffff;
      border: 1px solid rgba(16,185,129,0.15);
      border-radius: 24px;
      padding: 32px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.05);
      transition: transform 0.35s ease, box-shadow 0.35s ease;
      position: relative;
      overflow: hidden;
    }
    .info-feature-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 50px rgba(16,185,129,0.15);
    }
    .info-feature-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
      background: linear-gradient(90deg, #10b981, #34d399);
      border-radius: 24px 24px 0 0;
    }

    .feature-icon {
      font-size: 36px;
      margin-bottom: 16px;
      display: block;
    }

    .info-feature-card h3 {
      font-family: 'Outfit', sans-serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: #052e16;
      margin: 0 0 12px;
    }

    .info-feature-card p {
      color: #4b5563;
      line-height: 1.7;
      font-size: 0.95rem;
      margin: 0;
    }

    /* ── STATS ── */
    .accueil-stats {
      background: linear-gradient(135deg, #064e3b, #065f46);
      padding: 60px 24px;
      margin: 0;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 30px;
      max-width: 900px;
      margin: 0 auto;
      text-align: center;
    }

    .stat-item { color: white; }
    .stat-number {
      font-family: 'Outfit', sans-serif;
      font-size: 2.8rem;
      font-weight: 900;
      color: #34d399;
      display: block;
    }
    .stat-label {
      font-size: 0.9rem;
      color: rgba(255,255,255,0.7);
      font-weight: 500;
      letter-spacing: 0.05em;
    }

    /* ── FOOTER ── */
    .accueil-footer {
      background: #052e16;
      color: rgba(255,255,255,0.6);
      text-align: center;
      padding: 30px 24px;
      font-size: 13px;
    }
    .accueil-footer strong { color: #34d399; }
  </style>
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
<<<<<<< HEAD
      <a href="index.php">Événements</a>
      <a href="tache-categories.php">Tâche 1</a>
      <a href="tache-evenements.php">Tâche 2</a>
      <a href="tache-participants.php">Tâche 3</a>
      <a href="tache-design.php">Tâche 4</a>
      <a href="tache-javascript.php">Tâche 5</a>
=======
      <a href="recettes.php">Recettes</a>
      <a href="ingredients.php">Ingrédients</a>
>>>>>>> gestion-regime
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
<<<<<<< HEAD
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
=======
    <h2 class="accueil-info-title">Explorez NutriSmart</h2>

    <div class="info-cards-grid">
      <div class="info-feature-card">
        <span class="feature-icon">🍽️</span>
        <h3><a href="recettes.php">Nos recettes</a></h3>
        <p>Parcourez la bibliothèque complète de recettes saines, filtrez par durée, niveau ou ingrédient.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">🥕</span>
        <h3><a href="ingredients.php">Nos ingrédients</a></h3>
        <p>Découvrez tous les ingrédients utilisés, leurs catégories et les recettes dans lesquelles on les retrouve.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">📅</span>
        <h3><a href="planning.php">Menu de la semaine</a></h3>
        <p>Découvrez ce que nous cuisinons cette semaine, jour par jour, avec les recettes prévues pour chaque repas.</p>
      </div>

      <div class="info-feature-card">
        <span class="feature-icon">📊</span>
        <h3><a href="../backoffice/index.php">Tableau de bord</a></h3>
        <p>Espace d'administration pour gérer les recettes, les ingrédients et exporter vos données (CSV, Excel, PDF).</p>
>>>>>>> gestion-regime
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

  <script>
    /* =====================================================
       NAVBAR - effet scroll + lien actif
       ===================================================== */
    (function () {
      var navbar = document.getElementById('navbar');

      /* Fond sombre quand on scroll */
      window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
          navbar.classList.add('scrolled');
        } else {
          navbar.classList.remove('scrolled');
        }
      });

      /* Marquer le lien actif selon la page courante */
      var currentPage = window.location.pathname.split('/').pop() || 'accueil.php';
      document.querySelectorAll('.nav-links a').forEach(function (link) {
        if (link.getAttribute('href') === currentPage) {
          link.classList.add('active');
        }
      });

      /* Micro-animation clic */
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
