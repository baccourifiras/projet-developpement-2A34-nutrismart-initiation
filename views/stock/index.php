<?php
$pageTitle   = 'Mes Stocks';
$currentPage = 'stock';
require __DIR__ . '/../shared/front_header.php';
?>

<div class="page-header">
  <p class="ph-kicker">📦 Gestion des stocks</p>
  <h1>Mes <span>Stocks</span></h1>
  <p>Ajoutez et gérez vos produits. Le taux de gaspillage est calculé automatiquement.</p>
</div>

<div class="container">

  <?php if (isset($_GET['success'])): ?>
    <?php $msgs = ['created'=>'✅ Produit ajouté avec succès !','updated'=>'✅ Produit modifié !','deleted'=>'🗑 Produit supprimé.']; ?>
    <div class="alert alert-success"><?= $msgs[$_GET['success']] ?? 'Opération réussie.' ?></div>
  <?php endif; ?>

  <!-- Statistiques automatiques -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-val"><?= count($stocks) ?></div>
      <div class="stat-lbl">Produits en stock</div>
    </div>
    <div class="stat-card">
      <div class="stat-val" style="color:<?= $tauxGaspillage > 20 ? 'var(--danger)' : ($tauxGaspillage > 10 ? 'var(--warning)' : 'var(--primary)') ?>">
        <?= $tauxGaspillage ?>%
      </div>
      <div class="stat-lbl">Taux de gaspillage (auto)</div>
    </div>
    <div class="stat-card">
      <div class="stat-val" style="color:<?= count($expirent) > 0 ? 'var(--warning)' : 'var(--primary)' ?>">
        <?= count($expirent) ?>
      </div>
      <div class="stat-lbl">Expirent dans 7 jours</div>
    </div>
  </div>

  <?php if (!empty($expirent)): ?>
    <div class="alert alert-warning">⚠️ <strong><?= count($expirent) ?> produit(s)</strong> expirent dans moins de 7 jours !</div>
  <?php endif; ?>

  <!-- Tableau CRUD -->
  <div class="table-card">
    <div class="table-card-header">
      <h2>Tous mes produits (<?= count($stocks) ?>)</h2>
      <a href="index.php?page=stock&action=create" class="btn btn-primary">➕ Ajouter un produit</a>
    </div>

    <?php if (empty($stocks)): ?>
      <div class="empty-state">
        <div class="empty-icon">📦</div>
        <h3>Aucun produit enregistré</h3>
        <p>Commencez par ajouter vos premiers stocks alimentaires.</p>
        <a href="index.php?page=stock&action=create" class="btn btn-primary">➕ Ajouter un produit</a>
      </div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Produit</th>
          <th>Catégorie</th>
          <th>Date d'expiration</th>
          <th>Seuil minimum</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($stocks as $s):
          $d    = new DateTime($s['date_expiration']);
          $diff = (int)(new DateTime())->diff($d)->format('%r%a');
          if ($diff < 0)      $statut = '<span class="badge badge-red">Expiré</span>';
          elseif ($diff <= 7) $statut = '<span class="badge badge-orange">Expire bientôt</span>';
          else                $statut = '<span class="badge badge-green">OK</span>';
        ?>
        <tr>
          <td><span class="badge badge-blue"><?= $s['id'] ?></span></td>
          <td><strong><?= htmlspecialchars($s['produits']) ?></strong></td>
          <td><span class="badge badge-purple"><?= htmlspecialchars($s['type']) ?></span></td>
          <td><?= htmlspecialchars($s['date_expiration']) ?></td>
          <td><?= htmlspecialchars($s['seuil_minimum']) ?></td>
          <td><?= $statut ?></td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="index.php?page=stock&action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-edit">✏ Modifier</a>
              <a href="index.php?page=stock&action=delete&id=<?= $s['id'] ?>"
                 class="btn btn-sm btn-delete"
                 onclick="return confirm('Voulez-vous vraiment supprimer ce produit ?')">🗑 Supprimer</a>
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
