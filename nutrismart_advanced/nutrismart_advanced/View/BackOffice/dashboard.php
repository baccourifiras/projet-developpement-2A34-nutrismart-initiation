<?php
// Préparer les données pour Chart.js
$labelsCategorie = json_encode(array_column($stats['parCategorie'], 'type'));
$dataCategorie   = json_encode(array_column($stats['parCategorie'], 'nb'));
$labelsMois      = json_encode(array_column($budgetParMois, 'mois_label'));
$dataMois        = json_encode(array_column($budgetParMois, 'total'));
?>

<style>
.dash-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 28px;
}

.dash-card {
  background: var(--ivory);
  border: 1px solid var(--sand);
  border-radius: var(--radius-lg);
  padding: 22px 24px;
  box-shadow: var(--shadow-sm);
  display: flex;
  flex-direction: column;
  gap: 8px;
  position: relative;
  overflow: hidden;
  animation: fadeUp .4s cubic-bezier(.16,1,.3,1) both;
}

.dash-card:nth-child(2) { animation-delay:.06s }
.dash-card:nth-child(3) { animation-delay:.12s }
.dash-card:nth-child(4) { animation-delay:.18s }

.dash-card .dc-label {
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .8px;
  color: var(--muted);
}

.dash-card .dc-value {
  font-family: 'Playfair Display', serif;
  font-size: 36px;
  font-weight: 700;
  color: var(--forest);
  line-height: 1;
}

.dash-card .dc-sub {
  font-size: 12px;
  color: var(--muted);
}

.dash-card.red  { border-left: 4px solid #dc2626; }
.dash-card.orange { border-left: 4px solid #f59e0b; }
.dash-card.green  { border-left: 4px solid var(--green); }
.dash-card.blue   { border-left: 4px solid #2563eb; }

.dash-card .dc-icon {
  position: absolute;
  right: 20px;
  top: 18px;
  font-size: 32px;
  opacity: .15;
}

.charts-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 28px;
}

.chart-box {
  background: var(--ivory);
  border: 1px solid var(--sand);
  border-radius: var(--radius-lg);
  padding: 24px;
  box-shadow: var(--shadow-sm);
  animation: fadeUp .5s cubic-bezier(.16,1,.3,1) both;
}

.chart-box h3 {
  font-family: 'Playfair Display', serif;
  font-size: 15px;
  color: var(--forest);
  margin-bottom: 18px;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Alertes stocks */
.alertes-box {
  background: var(--ivory);
  border: 1px solid var(--sand);
  border-radius: var(--radius-lg);
  padding: 24px;
  box-shadow: var(--shadow-sm);
  animation: fadeUp .6s cubic-bezier(.16,1,.3,1) both;
}

.alertes-box h3 {
  font-family: 'Playfair Display', serif;
  font-size: 15px;
  color: var(--forest);
  margin-bottom: 16px;
}

.alerte-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid rgba(232,224,212,.5);
  font-size: 13px;
}

.alerte-row:last-child { border-bottom: none; }

.badge-expired { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
.badge-warning { background: #fffbeb; color: #d97706; border: 1px solid #fcd34d; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
.badge-ok      { background: #f0fdf4; color: #16a34a; border: 1px solid #86efac; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
</style>

<div class="container">

  <!-- ══ TITRE ══ -->
  <div style="margin-bottom:24px">
    <h1 style="font-family:'Playfair Display',serif;font-size:26px;color:var(--forest);letter-spacing:-.5px">
      Tableau de <span style="color:var(--green);font-style:italic">Bord</span>
    </h1>
    <p style="color:var(--muted);font-size:13px;margin-top:4px">Vue d'ensemble de NutriSmart</p>
  </div>

  <!-- ══ CARDS STATS ══ -->
  <div class="dash-grid">

    <div class="dash-card green">
      <div class="dc-icon">📦</div>
      <div class="dc-label">Total Stocks</div>
      <div class="dc-value"><?= $stats['total'] ?></div>
      <div class="dc-sub">produits enregistrés</div>
    </div>

    <div class="dash-card red">
      <div class="dc-icon">⚠️</div>
      <div class="dc-label">Stocks Expirés</div>
      <div class="dc-value"><?= $stats['expires'] ?></div>
      <div class="dc-sub">à retirer immédiatement</div>
    </div>

    <div class="dash-card orange">
      <div class="dc-icon">⏰</div>
      <div class="dc-label">Expirent Bientôt</div>
      <div class="dc-value"><?= $stats['bientot'] ?></div>
      <div class="dc-sub">dans les 7 prochains jours</div>
    </div>

    <div class="dash-card blue">
      <div class="dc-icon">💰</div>
      <div class="dc-label">Budget Total</div>
      <div class="dc-value" style="font-size:24px"><?= number_format($totalBudget, 2) ?></div>
      <div class="dc-sub">TND — toutes les listes</div>
    </div>

  </div>

  <!-- ══ GRAPHIQUES ══ -->
  <div class="charts-grid">

    <!-- Camembert — stocks par catégorie -->
    <div class="chart-box">
      <h3>🥧 Stocks par catégorie</h3>
      <canvas id="chartCategorie" height="220"></canvas>
    </div>

    <!-- Barres — budget par mois -->
    <div class="chart-box">
      <h3>📊 Budget des courses par mois</h3>
      <canvas id="chartMois" height="220"></canvas>
    </div>

  </div>

  <!-- ══ ALERTES EXPIRATION ══ -->
  <?php
  $alertes = array_filter($stocksRecents, fn($s) => $s['statut'] !== 'ok');
  if (!empty($alertes)):
  ?>
  <div class="alertes-box">
    <h3>🚨 Alertes d'expiration (<?= count($alertes) ?> produit(s))</h3>
    <?php foreach ($alertes as $s): ?>
      <div class="alerte-row">
        <div>
          <strong><?= htmlspecialchars($s['produits']) ?></strong>
          <span style="color:var(--muted);font-size:12px;margin-left:8px"><?= htmlspecialchars($s['type']) ?></span>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
          <span style="color:var(--muted);font-size:12px">Expire le : <?= $s['date_expiration'] ?></span>
          <?php if ($s['statut'] === 'expired'): ?>
            <span class="badge-expired">⛔ Expiré</span>
          <?php else: ?>
            <span class="badge-warning">⚠ Bientôt</span>
          <?php endif; ?>
          <a href="index.php?page=stock&action=delete&id=<?= $s['id'] ?><?= $space === 'back' ? '&space=back' : '' ?>"
             class="btn btn-rouge btn-sm"
             onclick="return confirm('Supprimer ce stock expiré ?')">Supprimer</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</div>

<!-- ══ CHART.JS ══ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Palette de couleurs vertes
const palette = [
  '#0d3b1f','#1a6b3c','#4ade80','#bbf7d0',
  '#c9a84c','#6b7c72','#2563eb','#dc2626'
];

// ── Camembert — catégories ──
const ctxCat = document.getElementById('chartCategorie').getContext('2d');
new Chart(ctxCat, {
  type: 'doughnut',
  data: {
    labels: <?= $labelsCategorie ?>,
    datasets: [{
      data:            <?= $dataCategorie ?>,
      backgroundColor: palette,
      borderWidth:     2,
      borderColor:     '#fdfcf9',
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'bottom',
        labels: { font: { family: 'DM Sans', size: 12 }, padding: 16 }
      }
    }
  }
});

// ── Barres — budget par mois ──
const ctxMois = document.getElementById('chartMois').getContext('2d');
new Chart(ctxMois, {
  type: 'bar',
  data: {
    labels: <?= $labelsMois ?>,
    datasets: [{
      label:           'Budget (TND)',
      data:            <?= $dataMois ?>,
      backgroundColor: 'rgba(26,107,60,.75)',
      borderColor:     '#0d3b1f',
      borderWidth:     1,
      borderRadius:    6,
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: {
        beginAtZero: true,
        grid: { color: 'rgba(232,224,212,.5)' },
        ticks: { font: { family: 'DM Sans', size: 11 } }
      },
      x: {
        grid: { display: false },
        ticks: { font: { family: 'DM Sans', size: 11 } }
      }
    }
  }
});
</script>
