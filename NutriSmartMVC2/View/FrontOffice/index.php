<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/Model/Stock.php';
require_once dirname(__DIR__, 2) . '/Model/ListeCourses.php';

$stocks = Stock::getStocks();
$listes = ListeCourses::getListes();
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"/><title>NutriSmart</title></head><body>
<h2>Stocks</h2>
<table border="1" cellpadding="8">
  <tr><th>Type</th><th>Produits</th><th>Expiration</th><th>Seuil</th></tr>
  <?php foreach ($stocks as $s): ?>
    <tr><td><?= htmlspecialchars($s['type']) ?></td><td><?= htmlspecialchars($s['produits']) ?></td><td><?= $s['date_expiration'] ?></td><td><?= $s['seuil_minimum'] ?></td></tr>
  <?php endforeach; ?>
</table>
<h2>Listes de courses</h2>
<table border="1" cellpadding="8">
  <tr><th>Articles</th><th>Budget</th><th>Date</th><th>Stock</th></tr>
  <?php foreach ($listes as $l): ?>
    <tr><td><?= htmlspecialchars($l['articles_a_acheter']) ?></td><td><?= $l['budget'] ?> TND</td><td><?= $l['date_creation'] ?></td><td><?= htmlspecialchars($l['stock_type'] ?? '-') ?></td></tr>
  <?php endforeach; ?>
</table>
</body></html>
