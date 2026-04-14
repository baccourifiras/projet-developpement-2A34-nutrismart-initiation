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
  <title>NutriSmart - Tâche 4 — Design CSS</title>
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

  <!-- En-tête de la page -->
  <header class="page-header">
    <p class="badge">Page de travail</p>
    <h1>Tâche 4 — Design CSS</h1>
    <p class="subtitle">Page réservée au camarade qui travaille sur le design CSS.</p>
  </header>

  <!-- Contenu principal (à compléter) -->
  <main class="container">
    <section class="section">
      <div class="empty-box">
        <h2>Tâche 4 — Design CSS</h2>
        <p>Page réservée au camarade qui travaille sur le design CSS.</p>
        <p>Cette page est prête — ajoutez votre code ici.</p>
      </div>
    </section>
  </main>

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
