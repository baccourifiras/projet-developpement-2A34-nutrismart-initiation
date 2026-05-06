<?php
/**
 * =====================================================================
 *  NutriSmart - frontoffice/suivis.php
 *  Page publique : tableau des suivis avec le type de regime (INNER JOIN).
 * =====================================================================
 */
require_once __DIR__ . '/../backoffice/Controller/SuiviC.php';

$suivis = (new SuiviC())->afficherSuivis();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NutriSmart - Suivis</title>
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
      <a href="../backoffice/index.php" class="nav-dashboard">Administration</a>
    </div>
  </nav>

  <header class="page-header">
    <p class="badge">Journal</p>
    <h1>Suivis quotidiens</h1>
    <p class="subtitle">Pesees et calories consommees par regime.</p>
  </header>

  <main class="container">
    <section class="section">
      <?php if (empty($suivis)) : ?>
        <div class="empty-box">
          <h2>Aucun suivi pour l instant</h2>
          <p>Revenez bientot.</p>
        </div>
      <?php else : ?>
        <div class="table-container">
          <table class="modern-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Regime</th>
                <th>Poids</th>
                <th>Calories consommees</th>
                <th>Calories cible</th>
                <th>Progression</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($suivis as $s) : 
                $consumed = (int) $s['calories_consommees'];
                $target = (int) $s['calories_cible'];
                $percentage = $target > 0 ? round(($consumed / $target) * 100) : 0;
                $status = $percentage > 110 ? 'over' : ($percentage < 90 ? 'under' : 'good');
              ?>
                <tr>
                  <td>
                    <div class="date-cell">
                      <span class="date-day"><?= date('d', strtotime($s['date'])) ?></span>
                      <span class="date-month"><?= date('M Y', strtotime($s['date'])) ?></span>
                    </div>
                  </td>
                  <td>
                    <span class="regime-badge regime-<?= strtolower($s['type_regime']) ?>">
                      <?= htmlspecialchars(strtoupper($s['type_regime'])) ?>
                    </span>
                  </td>
                  <td>
                    <div class="metric-cell">
                      <span class="metric-value"><?= (float) $s['poids'] ?></span>
                      <span class="metric-unit">kg</span>
                    </div>
                  </td>
                  <td>
                    <div class="metric-cell">
                      <span class="metric-value"><?= $consumed ?></span>
                      <span class="metric-unit">kcal</span>
                    </div>
                  </td>
                  <td>
                    <div class="metric-cell">
                      <span class="metric-value"><?= $target ?></span>
                      <span class="metric-unit">kcal</span>
                    </div>
                  </td>
                  <td>
                    <div class="progress-cell">
                      <div class="progress-bar">
                        <div class="progress-fill progress-<?= $status ?>" style="width: <?= min($percentage, 100) ?>%"></div>
                      </div>
                      <span class="progress-text"><?= $percentage ?>%</span>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <script src="script.js"></script>
</body>
</html>
