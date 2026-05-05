<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <title>NutriSmart — Export Listes de Courses</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:Arial,sans-serif;font-size:12px;color:#0f1a12;padding:30px;background:#fff}
    .header{display:flex;align-items:center;justify-content:space-between;border-bottom:3px solid #0d3b1f;padding-bottom:14px;margin-bottom:20px}
    .logo{font-size:22px;font-weight:700;color:#0d3b1f} .logo span{color:#1a6b3c}
    .meta{text-align:right;color:#6b7c72;font-size:11px;line-height:1.6}
    h1{font-size:16px;color:#0d3b1f;margin-bottom:6px}
    .subtitle{color:#6b7c72;font-size:11px;margin-bottom:18px}
    .total-box{background:#f0fdf4;border:1px solid #86efac;border-radius:6px;padding:10px 16px;margin-bottom:16px;font-size:13px;color:#0d3b1f;font-weight:600}
    table{width:100%;border-collapse:collapse;margin-bottom:20px}
    thead th{background:#0d3b1f;color:#fff;padding:9px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.8px}
    tbody td{padding:8px 12px;border-bottom:1px solid #e8e0d4;font-size:12px}
    tbody tr:nth-child(even) td{background:#f7f3ed}
    tbody tr:last-child td{border-bottom:none}
    .badge-green{display:inline-block;background:rgba(74,222,128,.2);color:#0d3b1f;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600}
    .footer{border-top:1px solid #e8e0d4;padding-top:10px;display:flex;justify-content:space-between;color:#6b7c72;font-size:10px}
    .no-print{margin-bottom:20px;display:flex;gap:10px}
    .btn-print{padding:9px 20px;background:#0d3b1f;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer}
    .btn-back{padding:9px 20px;background:#e8e0d4;color:#6b7c72;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none}
    @media print{.no-print{display:none!important} thead th{background:#0d3b1f!important;-webkit-print-color-adjust:exact} tbody tr:nth-child(even) td{background:#f7f3ed!important;-webkit-print-color-adjust:exact}}
  </style>
</head>
<body>

<div class="no-print">
  <button class="btn-print" onclick="window.print()">🖨 Imprimer / Sauvegarder PDF</button>
  <a href="javascript:history.back()" class="btn-back">← Retour</a>
</div>

<div class="header">
  <div class="logo">Nutri<span>Smart</span></div>
  <div class="meta">
    <strong>Rapport — Listes de Courses</strong><br/>
    Généré le : <?= date('d/m/Y à H:i') ?><br/>
    Total : <?= count($listes) ?> liste(s)
  </div>
</div>

<h1>Listes de Courses</h1>
<p class="subtitle">NutriSmart — Eat Smart, Live Smart</p>

<?php $total = array_sum(array_column($listes, 'budget')); ?>
<div class="total-box">💰 Budget total : <?= number_format($total, 2) ?> TND</div>

<table>
  <thead>
    <tr>
      <th>#</th><th>Articles à acheter</th><th>Budget (TND)</th><th>Date création</th><th>Stock associé</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($listes)): ?>
      <tr><td colspan="5" style="text-align:center;padding:24px;color:#6b7c72">Aucune liste.</td></tr>
    <?php else: ?>
      <?php foreach ($listes as $i => $row): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><strong><?= htmlspecialchars($row['articles_a_acheter'],ENT_QUOTES,'UTF-8') ?></strong></td>
          <td><span class="badge-green"><?= number_format((float)$row['budget'],2) ?></span></td>
          <td><?= htmlspecialchars($row['date_creation'],ENT_QUOTES,'UTF-8') ?></td>
          <td><?= $row['stock_type'] ? htmlspecialchars($row['stock_type'],ENT_QUOTES,'UTF-8') : '—' ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<div class="footer">
  <span>© <?= date('Y') ?> NutriSmart — Eat Smart Live Smart</span>
  <span>Budget total : <?= number_format($total, 2) ?> TND | <?= count($listes) ?> liste(s)</span>
</div>

</body>
</html>
<?php ob_end_flush(); ?>
