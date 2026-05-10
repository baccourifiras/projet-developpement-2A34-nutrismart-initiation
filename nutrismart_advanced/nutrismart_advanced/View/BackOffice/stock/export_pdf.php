<?php
/**
 * Vue — Export PDF des stocks
 * Génère un PDF pur PHP sans librairie externe (HTML → navigateur print)
 * Compatible XAMPP sans installation supplémentaire
 */

// Désactiver tout output avant les headers
ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <title>NutriSmart — Export Stocks</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
      color: #0f1a12;
      padding: 30px;
      background: #fff;
    }

    /* En-tête */
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 3px solid #0d3b1f;
      padding-bottom: 14px;
      margin-bottom: 20px;
    }

    .header .logo {
      font-size: 22px;
      font-weight: 700;
      color: #0d3b1f;
      letter-spacing: -0.5px;
    }

    .header .logo span { color: #1a6b3c; }

    .header .meta {
      text-align: right;
      color: #6b7c72;
      font-size: 11px;
      line-height: 1.6;
    }

    /* Titre */
    h1 {
      font-size: 16px;
      color: #0d3b1f;
      margin-bottom: 6px;
    }

    .subtitle {
      color: #6b7c72;
      font-size: 11px;
      margin-bottom: 18px;
    }

    /* Filtre appliqué */
    .filtre-info {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-radius: 6px;
      padding: 8px 12px;
      font-size: 11px;
      color: #065f46;
      margin-bottom: 16px;
    }

    /* Tableau */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    thead th {
      background: #0d3b1f;
      color: #fff;
      padding: 9px 12px;
      text-align: left;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }

    tbody td {
      padding: 8px 12px;
      border-bottom: 1px solid #e8e0d4;
      font-size: 12px;
    }

    tbody tr:nth-child(even) td { background: #f7f3ed; }
    tbody tr:last-child td      { border-bottom: none; }

    .badge {
      display: inline-block;
      background: rgba(13,59,31,.08);
      color: #0d3b1f;
      padding: 2px 8px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 600;
    }

    .seuil {
      font-weight: 700;
      color: #1a6b3c;
    }

    /* Pied de page */
    .footer {
      border-top: 1px solid #e8e0d4;
      padding-top: 10px;
      display: flex;
      justify-content: space-between;
      color: #6b7c72;
      font-size: 10px;
    }

    /* Boutons (cachés à l'impression) */
    .no-print {
      margin-bottom: 20px;
      display: flex;
      gap: 10px;
    }

    .btn-print {
      padding: 9px 20px;
      background: #0d3b1f;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
    }

    .btn-back {
      padding: 9px 20px;
      background: #e8e0d4;
      color: #6b7c72;
      border: none;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
    }

    @media print {
      .no-print { display: none !important; }
      body { padding: 15px; }
      thead th { background: #0d3b1f !important; -webkit-print-color-adjust: exact; }
      tbody tr:nth-child(even) td { background: #f7f3ed !important; -webkit-print-color-adjust: exact; }
    }
  </style>
</head>
<body>

<!-- Boutons impression -->
<div class="no-print">
  <button class="btn-print" onclick="window.print()">🖨 Imprimer / Sauvegarder PDF</button>
  <a href="javascript:history.back()" class="btn-back">← Retour</a>
</div>

<!-- En-tête du document -->
<div class="header">
  <div class="logo">Nutri<span>Smart</span></div>
  <div class="meta">
    <strong>Rapport — Stocks alimentaires</strong><br/>
    Généré le : <?= date('d/m/Y à H:i') ?><br/>
    Total : <?= count($stocks) ?> enregistrement(s)
  </div>
</div>

<h1>Liste des Stocks Alimentaires</h1>
<p class="subtitle">NutriSmart — Eat Smart, Live Smart</p>

<?php if (!empty($search)): ?>
  <div class="filtre-info">
    🔍 Filtre appliqué — Recherche : "<strong><?= htmlspecialchars($search) ?></strong>"
    &nbsp;|&nbsp;
    Tri : <strong><?= match($sort ?? 'id_desc') {
      'date_asc'  => 'Date expiration croissante',
      'date_desc' => 'Date expiration décroissante',
      'produit'   => 'Produit A→Z',
      default     => 'Plus récent'
    } ?></strong>
  </div>
<?php endif; ?>

<!-- Tableau des stocks -->
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Produit</th>
      <th>Catégorie</th>
      <th>Date expiration</th>
      <th>Seuil min.</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($stocks)): ?>
      <tr>
        <td colspan="5" style="text-align:center;padding:24px;color:#6b7c72">
          Aucun stock trouvé.
        </td>
      </tr>
    <?php else: ?>
      <?php foreach ($stocks as $i => $row): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><strong><?= htmlspecialchars($row['produits'], ENT_QUOTES, 'UTF-8') ?></strong></td>
          <td><span class="badge"><?= htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8') ?></span></td>
          <td><?= htmlspecialchars($row['date_expiration'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="seuil"><?= htmlspecialchars((string)$row['seuil_minimum'], ENT_QUOTES, 'UTF-8') ?> unités</td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<!-- Pied de page -->
<div class="footer">
  <span>© <?= date('Y') ?> NutriSmart — Eat Smart Live Smart</span>
  <span>Total : <?= count($stocks) ?> stock(s)</span>
</div>

</body>
</html>
<?php
ob_end_flush();
?>
