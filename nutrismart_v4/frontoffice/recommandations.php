<?php
/**
 * =====================================================================
 *  NutriSmart - frontoffice/recommandations.php
 *  Page publique : historique des recommandations (INNER JOIN).
 * =====================================================================
 */
require_once __DIR__ . '/../backoffice/Controller/HistoriqueC.php';

$histos = (new HistoriqueC())->afficherHistoriques();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NutriSmart - Recommandations</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">NutriSmart</div>
      <div class="slogan">Eat Smart Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="regimes.php">Regimes</a>
      <a href="suivis.php">Suivis</a>
      <a href="recommandations.php">Recommandations</a>
      <a href="generateur-recettes.php">Recettes IA</a>
      <a href="analyse-nutrition.php">Analyse Nutrition</a>
      <a href="../backoffice/index.php" class="nav-dashboard">Administration</a>
    </div>
  </nav>

  <header class="page-header">
    <p class="badge">Conseils</p>
    <h1>Recommandations</h1>
    <p class="subtitle">Conseils nutritionnels associes a chaque regime.</p>
  </header>

  <main class="container">
    <section class="section">
      <?php if (empty($histos)) : ?>
        <div class="empty-box">
          <h2>Aucune recommandation</h2>
          <p>Revenez bientot.</p>
        </div>
      <?php else : ?>
        <ul class="public-reco-list">
          <?php foreach ($histos as $h) : ?>
            <li class="public-reco-card">
              <div class="public-reco-head">
                <span class="public-reco-tag"><?= htmlspecialchars($h['type_regime']) ?></span>
                <span class="public-reco-date"><?= htmlspecialchars($h['date']) ?></span>
              </div>
              <p class="public-reco-text"><?= htmlspecialchars($h['recommandation']) ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>
  </main>

  <script src="script.js"></script>
</body>
</html>
