<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Espace Client</title>
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
      <!-- $userPrenom est prepare par PageController::espace_client() -->
      <span>Bonjour <?php echo htmlspecialchars($userPrenom); ?></span>
      <form action="index.php" method="POST" style="display:inline;margin:0;">
        <input type="hidden" name="action" value="deconnexion" />
        <button type="submit">Se deconnecter</button>
      </form>
    </div>
  </nav>

  <header class="page-header">
    <p class="badge">Espace Client</p>
    <!-- Affichage de la variable passee par le Controller (Chapitre 3 - echo) -->
    <h1>Bienvenue, <?php echo htmlspecialchars($userPrenom); ?> !</h1>
    <p class="subtitle">Suivez votre programme nutritionnel.</p>
  </header>

  <main class="container">
    <section class="section">
      <div class="section-header">
        <h2>Mon espace</h2>
        <p class="section-sub">Vos informations et votre programme</p>
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:1.5rem;justify-content:center;padding:2rem 0;">
        <div style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.07);padding:2rem;min-width:200px;text-align:center;">
          <div style="font-size:2.4rem;margin-bottom:.8rem;">&#127825;</div>
          <h3>Mon Plan Alimentaire</h3>
          <p style="color:#64748b;font-size:.88rem;">Consultez votre programme nutritionnel.</p>
        </div>
        <div style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.07);padding:2rem;min-width:200px;text-align:center;">
          <div style="font-size:2.4rem;margin-bottom:.8rem;">&#128200;</div>
          <h3>Ma Progression</h3>
          <p style="color:#64748b;font-size:.88rem;">Suivez l'evolution de vos objectifs.</p>
        </div>
        <div style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.07);padding:2rem;min-width:200px;text-align:center;">
          <div style="font-size:2.4rem;margin-bottom:.8rem;">&#128197;</div>
          <h3>Mes Rendez-vous</h3>
          <p style="color:#64748b;font-size:.88rem;">Gerez vos prochaines consultations.</p>
        </div>
      </div>
    </section>
  </main>

  <footer class="page-footer">
    <p>&copy; 2026 NutriSmart</p>
  </footer>

</body>
</html>
