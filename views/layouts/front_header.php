<?php
/**
 * ============================================================
 *  Layout : Front office - HEADER
 *  /views/layouts/front_header.php
 *
 *  Réutilise EXACTEMENT la navbar existante (style.css du projet)
 *  + les fontes Outfit + le script de scroll/active link.
 *  Les liens "tâches" sont remplacés par "Recettes" et "Ingrédients".
 * ============================================================
 */
$pageTitle = $pageTitle ?? 'NutriSmart';
$baseUrl   = '/' . basename(BASE_PATH);

// Bandeau "nouveautés" : récupère les recettes ajoutées dans les 7 derniers jours
try {
    $_recettesRecentes = (new Notification())->recettesRecentes(7);
} catch (Throwable $e) {
    $_recettesRecentes = []; // ex: avant import du module notifications
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($pageTitle) ?> &mdash; NutriSmart</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
  <link rel="stylesheet" href="<?= e($baseUrl) ?>/frontoffice/style.css" />
  <link rel="stylesheet" href="<?= e($baseUrl) ?>/assets/css/front-extras.css" />
</head>
<body>

  <!-- Navbar (existante, conservée à l'identique) -->
  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">NutriSmart</div>
      <div class="slogan">Eat Smart Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="<?= e($baseUrl) ?>/frontoffice/accueil.php">Accueil</a>
      <a href="<?= e($baseUrl) ?>/frontoffice/recettes.php">Recettes</a>
      <a href="<?= e($baseUrl) ?>/frontoffice/ingredients.php">Ingrédients</a>
      <a href="<?= e($baseUrl) ?>/frontoffice/planning.php">Planning</a>
      <a href="<?= e($baseUrl) ?>/backoffice/index.php" class="nav-dashboard">Dashboard</a>
    </div>
  </nav>

  <!-- Bandeau "nouveautés" frontoffice -->
  <?php if (!empty($_recettesRecentes)): ?>
    <div class="news-banner">
      <span class="news-icon">🆕</span>
      <span class="news-text">
        <strong><?= count($_recettesRecentes) ?> nouvelle<?= count($_recettesRecentes)>1?'s':'' ?>
        recette<?= count($_recettesRecentes)>1?'s':'' ?></strong> cette semaine —
      </span>
      <span class="news-list">
        <?php foreach ($_recettesRecentes as $i => $r): ?>
          <a href="<?= e($baseUrl) ?>/frontoffice/recette.php?id=<?= (int)$r['id'] ?>">
            <?= e($r['nom']) ?>
          </a><?= $i < count($_recettesRecentes) - 1 ? ' · ' : '' ?>
        <?php endforeach; ?>
      </span>
      <button type="button" class="news-close" aria-label="Fermer">×</button>
    </div>
  <?php endif; ?>

  <!-- Flash messages globaux -->
  <?php $msg = flash('success'); $err = flash('error'); ?>
  <?php if ($msg || $err): ?>
    <div class="flash-wrap">
      <?php if ($msg): ?><div class="flash flash-success">✓ <?= e($msg) ?></div><?php endif; ?>
      <?php if ($err): ?><div class="flash flash-error">✕ <?= e($err) ?></div><?php endif; ?>
    </div>
  <?php endif; ?>
