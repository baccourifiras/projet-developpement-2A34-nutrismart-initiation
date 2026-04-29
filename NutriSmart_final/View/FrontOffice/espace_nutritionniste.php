<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Espace Nutritionniste</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
  <link rel="stylesheet" href="public/css/style.css" />
</head>
<body>

  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">NutriSmart</div>
      <div class="slogan">Eat Smart Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="index.php?page=accueil">Accueil</a>
      <!-- $userPrenom est prepare par PageController::espace_nutritionniste() -->
      <span>Bonjour <?php echo htmlspecialchars($userPrenom); ?></span>
      <form action="index.php" method="POST" style="display:inline;margin:0;">
        <input type="hidden" name="action" value="deconnexion" />
        <button type="submit">Se deconnecter</button>
      </form>
    </div>
  </nav>

  <header class="page-header">
    <p class="badge">Espace Nutritionniste</p>
    <!-- Affichage de la variable passee par le Controller (Chapitre 3 - echo) -->
    <h1>Bienvenue, <?php echo htmlspecialchars($userPrenom); ?> !</h1>
    <p class="subtitle">Gerez vos consultations et suivez vos clients.</p>
  </header>

  <main class="container">
    <section class="section">
      <div class="section-header">
        <h2>Mon espace professionnel</h2>
        <p class="section-sub">Vos outils de gestion</p>
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:1.5rem;justify-content:center;padding:2rem 0;">
        <div style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.07);padding:2rem;min-width:200px;text-align:center;">
          <div style="font-size:2.4rem;margin-bottom:.8rem;">&#128101;</div>
          <h3>Mes Clients</h3>
          <p style="color:#64748b;font-size:.88rem;">Consultez et gerez vos clients.</p>
        </div>
        <div style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.07);padding:2rem;min-width:200px;text-align:center;">
          <div style="font-size:2.4rem;margin-bottom:.8rem;">&#128203;</div>
          <h3>Plans Nutritionnels</h3>
          <p style="color:#64748b;font-size:.88rem;">Creez les plans alimentaires.</p>
        </div>
        <div style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.07);padding:2rem;min-width:200px;text-align:center;">
          <div style="font-size:2.4rem;margin-bottom:.8rem;">&#128197;</div>
          <h3>Rendez-vous</h3>
          <p style="color:#64748b;font-size:.88rem;">Planifiez vos consultations.</p>
        </div>
      </div>
    </section>
  </main>

  <footer class="page-footer">
    <p>&copy; 2026 NutriSmart</p>
  </footer>

</body>
</html>
