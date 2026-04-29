<?php
/**
 * =====================================================================
 *  NutriSmart - View/regimes/details.php
 *  Detail d'un regime : ses suivis et ses recommandations (INNER JOIN).
 * =====================================================================
 */
require_once __DIR__ . '/../../Controller/RegimeC.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: list.php'); exit; }

$data = (new RegimeC())->getDetailsRegime($id);
if (!$data) { header('Location: list.php'); exit; }

$activeMenu = 'regimes';
$pageTitle  = 'NutriSmart - Details du regime';
include __DIR__ . '/../templates/header.php';
?>

<header class="page-header">
  <p class="badge">Detail</p>
  <h1>Regime #<?= (int) $data['id_regime'] ?> - <?= htmlspecialchars($data['type_regime']) ?></h1>
  <p class="subtitle">Suivis et recommandations associes a ce regime.</p>
</header>

<section class="section">
  <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px;">
    <div class="stats-card"><span>Calories cible</span><strong><?= (int) $data['calories_cible'] ?></strong></div>
    <div class="stats-card"><span>Date debut</span><strong><?= htmlspecialchars($data['date_debut']) ?></strong></div>
    <div class="stats-card"><span>Poids initial</span><strong><?= (float) $data['poids_initial'] ?> kg</strong></div>
    <div class="stats-card"><span>Duree</span><strong><?= (int) $data['duree'] ?> j</strong></div>
  </div>
</section>

<section class="section">
  <h2>Suivis (<?= count($data['suivis']) ?>)</h2>
  <?php if (empty($data['suivis'])) : ?>
    <p class="empty-box">Aucun suivi pour ce regime.</p>
  <?php else : ?>
    <table class="data-table">
      <thead>
        <tr><th>Date</th><th>Poids</th><th>Calories consommees</th></tr>
      </thead>
      <tbody>
        <?php foreach ($data['suivis'] as $s) : ?>
          <tr>
            <td><?= htmlspecialchars($s['date']) ?></td>
            <td><?= (float) $s['poids'] ?> kg</td>
            <td><?= (int) $s['calories_consommees'] ?> kcal</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<section class="section">
  <h2>Recommandations (<?= count($data['recommandations']) ?>)</h2>
  <?php if (empty($data['recommandations'])) : ?>
    <p class="empty-box">Aucune recommandation pour ce regime.</p>
  <?php else : ?>
    <ul class="reco-list">
      <?php foreach ($data['recommandations'] as $h) : ?>
        <li>
          <span class="reco-date"><?= htmlspecialchars($h['date']) ?></span>
          <span class="reco-text"><?= htmlspecialchars($h['recommandation']) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>

<p>
  <a href="list.php" class="btn-secondary">&larr; Retour a la liste</a>
  <a href="edit.php?id=<?= (int) $data['id_regime'] ?>" class="btn-primary">Modifier</a>
</p>

<?php include __DIR__ . '/../templates/footer.php'; ?>
