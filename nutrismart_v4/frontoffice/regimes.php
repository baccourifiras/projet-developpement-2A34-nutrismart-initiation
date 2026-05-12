<?php
/**
 * =====================================================================
 *  NutriSmart - frontoffice/regimes.php
 *  Page publique : liste des regimes (lecture seule).
 * =====================================================================
 */
require_once __DIR__ . '/../backoffice/Controller/RegimeC.php';

$regimes = (new RegimeC())->afficherRegimes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NutriSmart - Regimes</title>
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
    <p class="badge">Catalogue</p>
    <h1>Nos regimes</h1>
    <p class="subtitle">Decouvrez les regimes alimentaires proposes par NutriSmart.</p>
  </header>

  <main class="container">
    <section class="section">
      <?php if (empty($regimes)) : ?>
        <div class="empty-box">
          <h2>Aucun regime pour l instant</h2>
          <p>Revenez bientot.</p>
        </div>
      <?php else : ?>
        <div class="recipes-grid">
          <?php 
          // Map regime types to images
          $regimeImages = [
            'cut'       => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=400&h=300&fit=crop',
            'bulk'      => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=400&h=300&fit=crop',
            'equilibre' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=300&fit=crop'
          ];
          
          foreach ($regimes as $r) : 
            $type = strtolower(trim($r['type_regime']));
            $image = $regimeImages[$type] ?? 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=400&h=300&fit=crop';
          ?>
            <article class="recipe-card">
              <div class="recipe-image" style="background-image: url('<?= $image ?>');">
                <div class="recipe-badge"><?= htmlspecialchars(strtoupper($r['type_regime'])) ?></div>
              </div>
              <div class="recipe-content">
                <h2><?= htmlspecialchars(strtoupper($r['type_regime'])) ?></h2>
                <p class="recipe-desc">
                  Regime debutant le <?= htmlspecialchars($r['date_debut']) ?>,
                  pour une duree de <?= (int) $r['duree'] ?> jours.
                </p>

                <div class="recipe-meta">
                  <span class="meta-chip"><strong><?= (int) $r['calories_cible'] ?></strong> kcal cible</span>
                  <span class="meta-chip"><strong><?= (float) $r['poids_initial'] ?></strong> kg depart</span>
                  <span class="meta-chip"><strong><?= (int) $r['duree'] ?></strong> jours</span>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <script src="script.js"></script>
</body>
</html>
