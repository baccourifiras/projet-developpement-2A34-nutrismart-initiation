<?php
/**
 * ============================================================
 *  Layout : Back office - HEADER
 *  /views/layouts/back_header.php
 *
 *  Sidebar conservée (style.css backoffice). Les anciens liens
 *  "Tâches" sont remplacés par les sections "Recettes" et
 *  "Ingrédients", et un retour Front office.
 * ============================================================
 */
$pageTitle = $pageTitle ?? 'NutriSmart Admin';
$baseUrl   = '/' . basename(BASE_PATH);
$current   = basename($_SERVER['PHP_SELF']);

// Génération + comptage des notifications de stock bas (badge sidebar)
$_notifModel = new Notification();
$_notifModel->genererAlertesStock();
// Génération des notifications planning (jour vide, ingrédients manquants, etc.)
try {
    $_notifModel->genererAlertesPlanning();
} catch (Throwable $e) {
    // PlanningMenu peut ne pas être encore importé en BDD : on ignore
}
$nbNotifNonLues = $_notifModel->countNonLues();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($pageTitle) ?> &mdash; NutriSmart</title>
  <link rel="stylesheet" href="<?= e($baseUrl) ?>/backoffice/style.css" />
  <link rel="stylesheet" href="<?= e($baseUrl) ?>/assets/css/back-extras.css" />
</head>
<body>
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark">NS</div>
      <div>
        <h1>NutriSmart</h1>
        <p class="brand-slogan">Eat Smart Live Smart</p>
        <p class="sidebar-text">Administration des recettes, ingrédients et nutrition.</p>
      </div>
    </div>

    <nav class="menu">
      <a href="<?= e($baseUrl) ?>/backoffice/index.php"        class="<?= $current==='index.php'?'active':'' ?>">📊 Tableau de bord</a>
      <a href="<?= e($baseUrl) ?>/backoffice/recettes.php"     class="<?= $current==='recettes.php' || $current==='recette_form.php' || $current==='recette_show.php' ?'active':'' ?>">🍽️ Recettes</a>
      <a href="<?= e($baseUrl) ?>/backoffice/ingredients.php"  class="<?= $current==='ingredients.php' || $current==='ingredient_form.php' || $current==='ingredient_show.php' ?'active':'' ?>">🥕 Ingrédients</a>
      <a href="<?= e($baseUrl) ?>/backoffice/planning.php"     class="<?= str_starts_with($current,'planning')?'active':'' ?>">📅 Planning</a>
      <a href="<?= e($baseUrl) ?>/backoffice/notifications.php" class="<?= str_starts_with($current,'notification')?'active':'' ?>">
        🔔 Notifications
        <?php if ($nbNotifNonLues > 0): ?>
          <span class="notif-badge"><?= e($nbNotifNonLues) ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= e($baseUrl) ?>/frontoffice/accueil.php">🏠 Voir le site</a>
    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-chip">Backoffice NutriSmart v2</div>
    </div>
  </aside>

  <main class="main content">
    <?php $msg = flash('success'); $err = flash('error'); ?>
    <?php if ($msg): ?><div class="flash flash-success">✓ <?= e($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="flash flash-error">✕ <?= e($err) ?></div><?php endif; ?>
