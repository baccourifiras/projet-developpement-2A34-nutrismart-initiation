<?php
/**
 * =====================================================================
 *  NutriSmart - backoffice/index.php
 *  Tableau de bord : projet + gestion + statistiques.
 * =====================================================================
 */
require_once __DIR__ . '/Config/config.php';
require_once __DIR__ . '/Controller/SuiviC.php';
require_once __DIR__ . '/Controller/HistoriqueC.php';

$nbRegimes = 0;
$nbSuivis  = 0;
$nbRecos   = 0;
$derniersSuivis = [];
$dernieresRecos = [];
$statsByType    = [];
$avgCalories    = 0;
$avgPoids       = 0;

try {
    $pdo = Config::getConnexion();
    $nbRegimes = (int) $pdo->query("SELECT COUNT(*) FROM regime")->fetchColumn();
    $nbSuivis  = (int) $pdo->query("SELECT COUNT(*) FROM suivi_regime")->fetchColumn();
    $nbRecos   = (int) $pdo->query("SELECT COUNT(*) FROM historique_recommandation")->fetchColumn();

    $derniersSuivis = (new SuiviC())->dernieresEntrees(5);
    $dernieresRecos = (new HistoriqueC())->dernieresRecommandations(5);

    // Stats par type de regime
    $statsByType = $pdo->query(
        "SELECT r.type_regime,
                COUNT(DISTINCT r.id_regime)   AS nb_regimes,
                COUNT(s.id_suivi)             AS nb_suivis,
                ROUND(AVG(s.calories_consommees),0) AS avg_cal,
                ROUND(AVG(s.poids),1)         AS avg_poids
         FROM regime r
         LEFT JOIN suivi_regime s ON s.id_regime = r.id_regime
         GROUP BY r.type_regime
         ORDER BY nb_suivis DESC"
    )->fetchAll();

    $avgCalories = (float) $pdo->query("SELECT ROUND(AVG(calories_consommees),0) FROM suivi_regime")->fetchColumn();
    $avgPoids    = (float) $pdo->query("SELECT ROUND(AVG(poids),1) FROM suivi_regime")->fetchColumn();
} catch (Throwable $e) {
    // BD pas encore importee
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NutriSmart - Backoffice</title>
  <link rel="stylesheet" href="style.css">
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
      <a href="index.php" class="active">Tableau de bord</a>
      <a href="View/regimes/list.php">Regimes</a>
      <a href="View/suivis/list.php">Suivis</a>
      <a href="View/historiques/list.php">Recommandations</a>
      <a href="../frontoffice/regimes.php">Voir le Front Office</a>
    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-chip">Backoffice CRUD</div>
    </div>
  </aside>

  <main class="main content">

    <!-- PROJET -->
    <section class="panel intro-panel">
      <p class="kicker">Projet</p>
      <h2>Bienvenue dans le Backoffice NutriSmart</h2>
      <p class="note">
        Gestion complete des regimes alimentaires, des suivis journaliers
        et des recommandations, avec MVC + PDO et jointures SQL.
      </p>
    </section>

    <!-- GESTION -->
    <section class="panel">
      <div class="panel-header">
        <div>
          <p class="kicker kicker-soft">Gestion</p>
          <h2>Acces rapide</h2>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:20px; margin-top:14px;">
        <a href="View/regimes/list.php" class="dash-card">
          <h3>Gerer les regimes</h3>
          <p>Cut, bulk, equilibre - calories, duree et poids.</p>
          <span class="btn-primary">Ouvrir</span>
        </a>

        <a href="View/suivis/list.php" class="dash-card">
          <h3>Gerer les suivis</h3>
          <p>Pesees quotidiennes et calories consommees.</p>
          <span class="btn-primary">Ouvrir</span>
        </a>

        <a href="View/historiques/list.php" class="dash-card">
          <h3>Recommandations</h3>
          <p>Historique des conseils nutritionnels.</p>
          <span class="btn-primary">Ouvrir</span>
        </a>

        <a href="../frontoffice/regimes.php" class="dash-card">
          <h3>Front Office</h3>
          <p>Vue publique du site NutriSmart.</p>
          <span class="btn-secondary">Visiter</span>
        </a>
      </div>
    </section>

    <!-- STATISTIQUES -->
    <section class="panel">
      <div class="panel-header">
        <div>
          <p class="kicker kicker-soft">Statistiques</p>
          <h2>Vue d'ensemble</h2>
        </div>
      </div>

      <!-- Compteurs globaux -->
      <div class="stats-grid" style="margin-top:18px;">
        <div class="stats-card">
          <span>Regimes</span>
          <strong><?= $nbRegimes ?></strong>
        </div>
        <div class="stats-card">
          <span>Suivis</span>
          <strong><?= $nbSuivis ?></strong>
        </div>
        <div class="stats-card">
          <span>Recommandations</span>
          <strong><?= $nbRecos ?></strong>
        </div>
        <div class="stats-card">
          <span>Moy. calories / suivi</span>
          <strong><?= $avgCalories ?: '—' ?> <small style="font-size:16px;font-weight:500;">kcal</small></strong>
        </div>
        <div class="stats-card">
          <span>Moy. poids / suivi</span>
          <strong><?= $avgPoids ?: '—' ?> <small style="font-size:16px;font-weight:500;">kg</small></strong>
        </div>
      </div>

      <!-- Tableau par type de regime -->
      <?php if (!empty($statsByType)) : ?>
        <h3 style="margin:28px 0 12px; font-size:16px; color:var(--muted); text-transform:uppercase; letter-spacing:.06em;">
          Repartition par type de regime
        </h3>
        <div class="table-scroll">
          <table class="data-table">
            <thead>
              <tr>
                <th>Type de regime</th>
                <th>Nb regimes</th>
                <th>Nb suivis</th>
                <th>Moy. calories</th>
                <th>Moy. poids</th>
                <th>Activite</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $maxSuivis = max(array_column($statsByType, 'nb_suivis') ?: [1]);
                foreach ($statsByType as $st) :
                  $pct = $maxSuivis > 0 ? round($st['nb_suivis'] / $maxSuivis * 100) : 0;
              ?>
                <tr>
                  <td><strong><?= htmlspecialchars($st['type_regime']) ?></strong></td>
                  <td><?= (int)$st['nb_regimes'] ?></td>
                  <td><?= (int)$st['nb_suivis'] ?></td>
                  <td><?= $st['avg_cal'] ? (int)$st['avg_cal'].' kcal' : '—' ?></td>
                  <td><?= $st['avg_poids'] ? (float)$st['avg_poids'].' kg' : '—' ?></td>
                  <td style="min-width:120px;">
                    <div class="stat-bar-bg">
                      <div class="stat-bar-fill" style="width:<?= $pct ?>%"></div>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <!-- ACTIVITE RECENTE -->
    <section class="panel">
      <div class="panel-header">
        <div>
          <p class="kicker kicker-soft">Activite recente</p>
          <h2>Derniers suivis</h2>
        </div>
      </div>

      <?php if (empty($derniersSuivis)) : ?>
        <p class="empty-box">Aucun suivi enregistre pour le moment.</p>
      <?php else : ?>
        <table class="data-table">
          <thead>
            <tr><th>Date</th><th>Regime</th><th>Poids</th><th>Calories</th></tr>
          </thead>
          <tbody>
            <?php foreach ($derniersSuivis as $s) : ?>
              <tr>
                <td><?= htmlspecialchars($s['date']) ?></td>
                <td><strong><?= htmlspecialchars($s['type_regime']) ?></strong></td>
                <td><?= (float) $s['poids'] ?> kg</td>
                <td><?= (int) $s['calories_consommees'] ?> kcal</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>

    <section class="panel">
      <div class="panel-header">
        <div>
          <p class="kicker kicker-soft">Activite recente</p>
          <h2>Dernieres recommandations</h2>
        </div>
      </div>

      <?php if (empty($dernieresRecos)) : ?>
        <p class="empty-box">Aucune recommandation pour le moment.</p>
      <?php else : ?>
        <ul class="reco-list">
          <?php foreach ($dernieresRecos as $h) : ?>
            <li>
              <span class="reco-date"><?= htmlspecialchars($h['date']) ?></span>
              <span class="reco-text"><?= htmlspecialchars($h['recommandation']) ?></span>
              <span class="reco-tag"><?= htmlspecialchars($h['type_regime']) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>

  </main>

  <style>
    .dash-card {
      display: block;
      text-decoration: none;
      color: var(--text);
      background: var(--panel-strong);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 20px;
      transition: transform .15s, box-shadow .15s;
    }
    .dash-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-strong); }
    .dash-card h3 { margin: 0 0 6px; }
    .dash-card p  { margin: 0 0 14px; color: var(--muted); font-size: 14px; }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 16px;
    }

    .table-scroll { overflow-x: auto; }

    .stat-bar-bg {
      height: 8px;
      background: rgba(23,153,95,.12);
      border-radius: 999px;
      overflow: hidden;
    }
    .stat-bar-fill {
      height: 100%;
      background: linear-gradient(90deg, var(--primary), var(--primary-dark));
      border-radius: 999px;
      transition: width .4s ease;
    }

    .reco-list {
      list-style: none;
      padding: 0;
      margin: 10px 0 0;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .reco-list li {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 12px 16px;
      background: var(--panel-strong);
      border: 1px solid var(--border);
      border-radius: 10px;
    }
    .reco-date { font-weight: 600; color: var(--primary-dark); min-width: 100px; }
    .reco-text { flex: 1; color: var(--text); }
    .reco-tag {
      padding: 3px 10px;
      border-radius: 999px;
      background: var(--primary);
      color: #fff;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
    }
  </style>

</body>
</html>
