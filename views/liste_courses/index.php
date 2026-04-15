<?php
$pageTitle   = 'Mes Listes de Courses';
$currentPage = 'liste_courses';
require __DIR__ . '/../shared/front_header.php';
?>

<div class="page-header">
  <p class="ph-kicker">🛒 Planification des achats</p>
  <h1>Mes <span>Listes de Courses</span></h1>
  <p>Créez et gérez vos listes d'achats avec budget.</p>
</div>

<div class="container">

  <?php if (isset($_GET['success'])): ?>
    <?php $msgs = ['created'=>'✅ Liste créée !','updated'=>'✅ Liste mise à jour !','deleted'=>'🗑 Liste supprimée.']; ?>
    <div class="alert alert-success"><?= $msgs[$_GET['success']] ?? 'Opération réussie.' ?></div>
  <?php endif; ?>

  <div class="table-card">
    <div class="table-card-header">
      <h2>Toutes mes listes (<?= count($listes) ?>)</h2>
      <a href="index.php?page=liste_courses&action=create" class="btn btn-primary">➕ Nouvelle liste</a>
    </div>

    <?php if (empty($listes)): ?>
      <div class="empty-state">
        <div class="empty-icon">🛒</div>
        <h3>Aucune liste de courses</h3>
        <p>Créez votre première liste d'achats.</p>
        <a href="index.php?page=liste_courses&action=create" class="btn btn-primary">➕ Créer une liste</a>
      </div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Articles à acheter</th>
          <th>Budget</th>
          <th>Date création</th>
          <th>Stock associé</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($listes as $l): ?>
        <tr>
          <td><span class="badge badge-blue"><?= $l['id'] ?></span></td>
          <td><strong><?= htmlspecialchars($l['articles_a_acheter']) ?></strong></td>
          <td><span class="badge badge-green"><?= number_format($l['budget'], 2) ?> TND</span></td>
          <td><?= htmlspecialchars($l['date_creation']) ?></td>
          <td>
            <?= $l['stock_type']
              ? '<span class="badge badge-purple">'.htmlspecialchars($l['stock_type']).'</span>'
              : '<span style="color:var(--muted)">—</span>'
            ?>
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="index.php?page=liste_courses&action=edit&id=<?= $l['id'] ?>" class="btn btn-sm btn-edit">✏ Modifier</a>
              <a href="index.php?page=liste_courses&action=delete&id=<?= $l['id'] ?>"
                 class="btn btn-sm btn-delete"
                 onclick="return confirm('Supprimer cette liste ?')">🗑 Supprimer</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<footer class="site-footer">© <?= date('Y') ?> NutriSmart — Eat Smart Live Smart</footer>
</body></html>
