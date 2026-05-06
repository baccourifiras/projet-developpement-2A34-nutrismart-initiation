<?php
/**
 * =====================================================================
 *  NutriSmart - backoffice/View/templates/header.php
 *  Sidebar + main content pour les pages CRUD.
 *  Les pages sont 2 niveaux plus bas que backoffice/ :
 *    backoffice/View/regimes/list.php
 *  D'ou les chemins en "../../".
 * =====================================================================
 */
$pageTitle  = $pageTitle  ?? 'NutriSmart - Administration';
$activeMenu = $activeMenu ?? ''; // 'dashboard' | 'regimes' | 'suivis' | 'historiques'
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="../../style.css">
</head>
<body>

  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark">NS</div>
      <div>
        <h1>NutriSmart</h1>
        <p class="brand-slogan">Eat Smart Live Smart</p>
        <p class="sidebar-text">Administration - Regimes, suivis et recommandations.</p>
      </div>
    </div>

    <nav class="menu">
      <a href="../../index.php" <?= $activeMenu === 'dashboard' ? 'class="active"' : '' ?>>Tableau de bord</a>
      <a href="../regimes/list.php"     <?= $activeMenu === 'regimes'     ? 'class="active"' : '' ?>>Regimes</a>
      <a href="../suivis/list.php"      <?= $activeMenu === 'suivis'      ? 'class="active"' : '' ?>>Suivis</a>
      <a href="../historiques/list.php" <?= $activeMenu === 'historiques' ? 'class="active"' : '' ?>>Recommandations</a>
      <a href="../../../frontoffice/regimes.php">Voir le Front Office</a>
    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-chip">Backoffice CRUD</div>
    </div>
  </aside>

  <main class="main content">
