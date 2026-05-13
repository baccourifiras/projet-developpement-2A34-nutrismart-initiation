<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/Model/Stock.php';
require_once dirname(__DIR__, 2) . '/Model/ListeCourses.php';

$stocks = getStocksAvecStatut();
$listes = getListes();
$stats  = getStatsStock();
$totalBudget = getTotalBudget();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>NutriSmart — FrontOffice</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:Arial,sans-serif;background:#f7f3ed;color:#0f1a12}
    .nav{background:#0d3b1f;padding:14px 30px;display:flex;align-items:center;justify-content:space-between}
    .nav .logo{color:#4ade80;font-size:20px;font-weight:700}
    .nav a{color:rgba(255,255,255,.7);text-decoration:none;margin-left:16px;font-size:14px}
    .nav a:hover{color:#fff}
    .hero{background:linear-gradient(135deg,#0d3b1f,#1a4a2a,#1a6b3c);padding:50px 30px;text-align:center;color:#fff}
    .hero h1{font-size:32px;font-weight:700;margin-bottom:10px}
    .hero h1 span{color:#4ade80;font-style:italic}
    .hero p{color:rgba(255,255,255,.7);font-size:15px}
    .container{max-width:1100px;margin:0 auto;padding:32px 24px}
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px}
    .stat-card{background:#fdfcf9;border:1px solid #e8e0d4;border-radius:14px;padding:20px;text-align:center;box-shadow:0 2px 8px rgba(13,59,31,.08)}
    .stat-card .val{font-size:32px;font-weight:700;color:#0d3b1f;line-height:1}
    .stat-card .lbl{font-size:12px;color:#6b7c72;margin-top:6px}
    h2{font-size:20px;color:#0d3b1f;margin-bottom:16px;font-weight:700}
    table{width:100%;border-collapse:collapse;background:#fdfcf9;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(13,59,31,.08);margin-bottom:28px}
    thead th{background:#0d3b1f;color:#fff;padding:11px 16px;text-align:left;font-size:12px;text-transform:uppercase;letter-spacing:.8px}
    tbody td{padding:12px 16px;border-bottom:1px solid #e8e0d4;font-size:14px}
    tbody tr:last-child td{border-bottom:none}
    tbody tr:hover td{background:rgba(26,107,60,.03)}
    .badge-ok{background:#f0fdf4;color:#16a34a;border:1px solid #86efac;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600}
    .badge-exp{background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600}
    .badge-warn{background:#fffbeb;color:#d97706;border:1px solid #fcd34d;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600}
    .budget-badge{background:linear-gradient(135deg,#bbf7d0,rgba(74,222,128,.15));color:#0d3b1f;border:1px solid rgba(74,222,128,.4);padding:4px 12px;border-radius:999px;font-size:13px;font-weight:600}
    footer{text-align:center;padding:20px;color:#6b7c72;border-top:1px solid #e8e0d4;background:#fdfcf9;font-size:13px}
  </style>
</head>
<body>

<nav class="nav">
  <div class="logo">🌿 NutriSmart</div>
  <div>
    <a href="#stocks">Stocks</a>
    <a href="#listes">Listes</a>
    <a href="../index.php?page=dashboard">📊 Dashboard</a>
    <a href="../index.php?page=stock&space=back">⚙ Admin</a>
  </div>
</nav>

<div class="hero">
  <h1>Gérez votre <span>alimentation</span> intelligemment</h1>
  <p>NutriSmart — suivez vos stocks et planifiez vos achats</p>
</div>

<div class="container">

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card"><div class="val"><?= $stats['total'] ?></div><div class="lbl">Stocks totaux</div></div>
    <div class="stat-card"><div class="val" style="color:#dc2626"><?= $stats['expires'] ?></div><div class="lbl">Stocks expirés</div></div>
    <div class="stat-card"><div class="val" style="color:#d97706"><?= $stats['bientot'] ?></div><div class="lbl">Expirent bientôt</div></div>
    <div class="stat-card"><div class="val" style="font-size:22px"><?= number_format($totalBudget,2) ?> <span style="font-size:14px">TND</span></div><div class="lbl">Budget total courses</div></div>
  </div>

  <!-- Stocks -->
  <h2 id="stocks">📦 Mes Stocks Alimentaires</h2>
  <table>
    <thead>
      <tr><th>Produit</th><th>Catégorie</th><th>Date expiration</th><th>Seuil min.</th><th>Statut</th></tr>
    </thead>
    <tbody>
      <?php if (empty($stocks)): ?>
        <tr><td colspan="5" style="text-align:center;padding:32px;color:#6b7c72">Aucun stock enregistré.</td></tr>
      <?php else: ?>
        <?php foreach ($stocks as $s): ?>
          <tr>
            <td><strong><?= htmlspecialchars($s['produits'],ENT_QUOTES,'UTF-8') ?></strong></td>
            <td><span style="background:rgba(13,59,31,.06);color:#0d3b1f;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600"><?= htmlspecialchars($s['type'],ENT_QUOTES,'UTF-8') ?></span></td>
            <td><?= htmlspecialchars($s['date_expiration'],ENT_QUOTES,'UTF-8') ?></td>
            <td><?= $s['seuil_minimum'] ?> unités</td>
            <td>
              <?php if ($s['statut']==='expired'): ?><span class="badge-exp">⛔ Expiré</span>
              <?php elseif ($s['statut']==='warning'): ?><span class="badge-warn">⚠ Bientôt</span>
              <?php else: ?><span class="badge-ok">✓ OK</span><?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Listes de courses -->
  <h2 id="listes">🛒 Mes Listes de Courses</h2>
  <table>
    <thead>
      <tr><th>Articles</th><th>Budget</th><th>Date création</th><th>Stock associé</th></tr>
    </thead>
    <tbody>
      <?php if (empty($listes)): ?>
        <tr><td colspan="4" style="text-align:center;padding:32px;color:#6b7c72">Aucune liste enregistrée.</td></tr>
      <?php else: ?>
        <?php foreach ($listes as $l): ?>
          <tr>
            <td><?= htmlspecialchars($l['articles_a_acheter'],ENT_QUOTES,'UTF-8') ?></td>
            <td><span class="budget-badge"><?= number_format((float)$l['budget'],2) ?> TND</span></td>
            <td><?= htmlspecialchars($l['date_creation'],ENT_QUOTES,'UTF-8') ?></td>
            <td><?= $l['stock_type'] ? '<span style="color:#1a6b3c;font-weight:500">'.htmlspecialchars($l['stock_type'],ENT_QUOTES,'UTF-8').'</span>' : '<span style="color:#6b7c72">—</span>' ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

</div>

<footer>© <?= date('Y') ?> NutriSmart — Eat Smart Live Smart</footer>

</body>
</html>
