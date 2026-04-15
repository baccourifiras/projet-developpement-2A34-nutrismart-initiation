<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NutriSmart — Backoffice Admin</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    :root {
      --primary:#16a34a; --primary-dark:#15803d; --primary-light:#dcfce7;
      --danger:#dc2626; --danger-light:#fee2e2;
      --warning:#d97706; --warning-light:#fef3c7;
      --sidebar:#0d2418; --sidebar-hover:rgba(255,255,255,.07);
      --text:#0f172a; --muted:#64748b;
      --border:#e2e8f0; --bg:#f8fafc; --surface:#fff;
      --shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06);
    }
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);display:grid;grid-template-columns:260px 1fr;min-height:100vh}
    a{text-decoration:none;color:inherit}

    /* ── Sidebar ── */
    .sidebar{background:var(--sidebar);color:#fff;display:flex;flex-direction:column;gap:0;position:sticky;top:0;height:100vh;overflow-y:auto}
    .sidebar-brand{padding:28px 22px 20px;border-bottom:1px solid rgba(255,255,255,.07)}
    .brand-logo{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--primary),#059669);display:grid;place-items:center;color:#fff;font-weight:800;font-size:18px;margin-bottom:14px;box-shadow:0 4px 12px rgba(22,163,74,.4)}
    .brand-name{font-size:20px;font-weight:800;line-height:1}
    .brand-sub{font-size:11px;color:rgba(255,255,255,.45);font-weight:500;text-transform:uppercase;letter-spacing:.1em;margin-top:4px}
    .sidebar-nav{padding:16px 12px;flex:1}
    .nav-section{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.3);padding:16px 10px 8px}
    .nav-section:first-child{padding-top:4px}
    .sidebar-nav a{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;font-size:14px;font-weight:500;color:rgba(255,255,255,.65);transition:.15s;margin-bottom:2px}
    .sidebar-nav a:hover{background:var(--sidebar-hover);color:#fff}
    .sidebar-nav a.active{background:rgba(22,163,74,.2);color:#4ade80;font-weight:600}
    .sidebar-footer{padding:16px 22px;border-top:1px solid rgba(255,255,255,.07)}
    .readonly-badge{display:inline-block;background:rgba(255,255,255,.08);color:rgba(255,255,255,.5);padding:4px 12px;border-radius:999px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px}
    .btn-goto-front{display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;background:rgba(22,163,74,.15);color:#4ade80;font-weight:600;font-size:14px;transition:.15s}
    .btn-goto-front:hover{background:rgba(22,163,74,.25);color:#86efac}

    /* ── Main ── */
    .main-content{padding:32px 36px;display:flex;flex-direction:column;gap:24px}
    .page-title{font-size:26px;font-weight:800}
    .page-title span{color:var(--primary)}

    /* ── Info banner ── */
    .info-banner{background:var(--primary-light);border:1px solid #86efac;border-radius:12px;padding:14px 18px;font-size:14px;font-weight:500;color:#14532d;display:flex;align-items:center;gap:10px}

    /* ── Stats grid ── */
    .stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
    .stat-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:24px;text-align:center;box-shadow:var(--shadow)}
    .stat-val{font-size:42px;font-weight:800;line-height:1;color:var(--primary)}
    .stat-lbl{font-size:11px;font-weight:600;color:var(--muted);margin-top:6px;text-transform:uppercase;letter-spacing:.06em}

    /* ── Card ── */
    .data-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow);overflow:hidden}
    .data-card-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#fafafa}
    .data-card-header h2{font-size:16px;font-weight:700}
    .readonly-tag{font-size:11px;color:var(--muted);background:#f1f5f9;padding:3px 10px;border-radius:999px;font-weight:600;border:1px solid var(--border)}

    /* ── Table ── */
    table{width:100%;border-collapse:collapse}
    thead th{padding:11px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);background:#f8fafc;border-bottom:1px solid var(--border)}
    tbody td{padding:13px 16px;font-size:14px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    tbody tr:last-child td{border-bottom:none}
    tbody tr:hover td{background:#f8fafc}

    /* ── Badges ── */
    .badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600}
    .badge-green{background:var(--primary-light);color:#14532d}
    .badge-orange{background:var(--warning-light);color:#78350f}
    .badge-red{background:var(--danger-light);color:#7f1d1d}
    .badge-blue{background:#dbeafe;color:#1e3a8a}
    .badge-purple{background:#ede9fe;color:#4c1d95}

    /* ── Alert blocks ── */
    .alert-block{padding:14px 20px;font-size:13px;font-weight:500;border-top:1px solid var(--border)}
    .alert-block:first-child{border-top:none}

    /* ── Empty ── */
    .empty{text-align:center;padding:40px;color:var(--muted);font-size:14px}
  </style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo">NS</div>
    <div class="brand-name">NutriSmart</div>
    <div class="brand-sub">Administration</div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">Tableau de bord</div>
    <a href="#dashboard" class="active">📊 Statistiques globales</a>

    <div class="nav-section">Consultation</div>
    <a href="#stocks">📦 Voir les stocks</a>
    <a href="#listes">🛒 Voir les listes</a>
    <a href="#alertes">🔴 Alertes actives</a>
  </nav>

  <div class="sidebar-footer">
    <div class="readonly-badge">Lecture seule</div>
    <a href="../frontoffice/index.php" class="btn-goto-front">
      🌿 Aller au Front Office
    </a>
  </div>
</aside>

<!-- ══ CONTENU ══ -->
<main class="main-content" id="dashboard">

  <h1 class="page-title">📊 Tableau de bord <span>Admin</span></h1>

  <div class="info-banner">
    ℹ️ Le backoffice est réservé à la <strong>consultation et surveillance</strong>. La gestion des données se fait dans le <strong>Front Office</strong>.
  </div>

  <!-- KPIs -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-val"><?= $totalStocks ?></div>
      <div class="stat-lbl">Total stocks</div>
    </div>
    <div class="stat-card">
      <div class="stat-val"><?= $totalListes ?></div>
      <div class="stat-lbl">Listes de courses</div>
    </div>
    <div class="stat-card">
      <div class="stat-val" style="color:<?= $tauxGaspillage > 20 ? 'var(--danger)' : ($tauxGaspillage > 10 ? 'var(--warning)' : 'var(--primary)') ?>">
        <?= $tauxGaspillage ?>%
      </div>
      <div class="stat-lbl">Taux de gaspillage</div>
    </div>
  </div>

  <!-- Alertes -->
  <?php if (!empty($expirentBientot)): ?>
  <div class="data-card" id="alertes">
    <div class="data-card-header">
      <h2>🔴 Alertes — produits expirant bientôt</h2>
    </div>
    <div class="alert-block" style="background:var(--warning-light)">
      <strong>⚠️ Expirent dans 7 jours :</strong><br><br>
      <?php foreach ($expirentBientot as $e): ?>
        <span class="badge badge-orange" style="margin:3px">
          <?= htmlspecialchars($e['produits']) ?> — <?= $e['date_expiration'] ?>
        </span>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Tableau stocks -->
  <div class="data-card" id="stocks">
    <div class="data-card-header">
      <h2>📦 Tous les stocks (<?= count($tousLesStocks) ?>)</h2>
      <span class="readonly-tag">Lecture seule</span>
    </div>
    <?php if (empty($tousLesStocks)): ?>
      <div class="empty">Aucun stock enregistré.</div>
    <?php else: ?>
    <table>
      <thead>
        <tr><th>#</th><th>Produit</th><th>Catégorie</th><th>Date expiration</th><th>Seuil min.</th><th>Statut</th></tr>
      </thead>
      <tbody>
        <?php foreach ($tousLesStocks as $s):
          $d    = new DateTime($s['date_expiration']);
          $diff = (int)(new DateTime())->diff($d)->format('%r%a');
          if ($diff < 0)      $badge = '<span class="badge badge-red">Expiré</span>';
          elseif ($diff <= 7) $badge = '<span class="badge badge-orange">Bientôt</span>';
          else                $badge = '<span class="badge badge-green">OK</span>';
        ?>
        <tr>
          <td><span class="badge badge-blue"><?= $s['id'] ?></span></td>
          <td><strong><?= htmlspecialchars($s['produits']) ?></strong></td>
          <td><span class="badge badge-purple"><?= htmlspecialchars($s['type']) ?></span></td>
          <td><?= $s['date_expiration'] ?></td>
          <td><?= $s['seuil_minimum'] ?></td>
          <td><?= $badge ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

  <!-- Tableau listes -->
  <div class="data-card" id="listes">
    <div class="data-card-header">
      <h2>🛒 Toutes les listes de courses (<?= count($toutesLesListes) ?>)</h2>
      <span class="readonly-tag">Lecture seule</span>
    </div>
    <?php if (empty($toutesLesListes)): ?>
      <div class="empty">Aucune liste enregistrée.</div>
    <?php else: ?>
    <table>
      <thead>
        <tr><th>#</th><th>Articles</th><th>Budget</th><th>Date</th><th>Stock associé</th></tr>
      </thead>
      <tbody>
        <?php foreach ($toutesLesListes as $l): ?>
        <tr>
          <td><span class="badge badge-blue"><?= $l['id'] ?></span></td>
          <td><?= htmlspecialchars($l['articles_a_acheter']) ?></td>
          <td><span class="badge badge-green"><?= number_format($l['budget'],2) ?> TND</span></td>
          <td><?= htmlspecialchars($l['date_creation']) ?></td>
          <td>
            <?= $l['stock_type']
              ? '<span class="badge badge-purple">'.htmlspecialchars($l['stock_type']).'</span>'
              : '<span style="color:var(--muted)">—</span>'
            ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

</main>
</body>
</html>
