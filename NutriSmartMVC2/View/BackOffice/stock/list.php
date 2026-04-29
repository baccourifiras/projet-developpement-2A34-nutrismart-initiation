<?php if (isset($_GET['success'])): ?>
  <?php $m = ['add' => 'Stock ajoute avec succes.', 'update' => 'Stock modifie.', 'delete' => 'Stock supprime.']; ?>
  <div class="msg-ok"><?= $m[$_GET['success']] ?? '' ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <h2>
      Mes stocks alimentaires
      <span class="count-badge"><?= count($stocks) ?></span>
    </h2>
    <a href="<?= $retourAdd ?>" class="btn btn-vert">+ Ajouter un stock</a>
  </div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Produit</th>
        <th>Categorie</th>
        <th>Date expiration</th>
        <th>Seuil min.</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($stocks)): ?>
        <tr class="vide">
          <td colspan="6">
            Aucun stock enregistre.<br>
            <a href="<?= $retourAdd ?>" style="color:var(--green);font-weight:600;margin-top:8px;display:inline-block">Ajouter mon premier stock</a>
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($stocks as $row):
          $urlUpdate = "index.php?page=stock&action=update&id={$row['id']}" . ($space === 'back' ? '&space=back' : '');
          $urlDelete = "index.php?page=stock&action=delete&id={$row['id']}" . ($space === 'back' ? '&space=back' : '');
        ?>
        <tr>
          <td><span class="row-num"><?= $row['id'] ?></span></td>
          <td style="font-weight:500"><?= htmlspecialchars($row['produits'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><span style="display:inline-flex;align-items:center;gap:5px;background:rgba(13,59,31,.06);color:var(--forest);padding:4px 11px;border-radius:999px;font-size:12px;font-weight:600"><?= htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8') ?></span></td>
          <td><span class="date-text"><?= htmlspecialchars($row['date_expiration'], ENT_QUOTES, 'UTF-8') ?></span></td>
          <td><span style="font-weight:600;color:var(--green)"><?= htmlspecialchars((string)$row['seuil_minimum'], ENT_QUOTES, 'UTF-8') ?></span> <span style="color:var(--muted);font-size:12px">unites</span></td>
          <td>
            <div class="actions-group">
              <a href="<?= $urlUpdate ?>" class="btn btn-bleu btn-sm">Modifier</a>
              <a href="<?= $urlDelete ?>" class="btn btn-rouge btn-sm"
                 onclick="return confirm('Supprimer ce stock ?')">Supprimer</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
