<?php if (isset($_GET['success'])): ?>
  <?php $m = ['add' => 'Liste de courses ajoutee avec succes.', 'update' => 'Liste de courses modifiee.', 'delete' => 'Liste supprimee.']; ?>
  <div class="msg-ok"><?= $m[$_GET['success']] ?? '' ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <h2>
      Toutes mes listes
      <span class="count-badge"><?= count($listes) ?></span>
    </h2>
    <a href="<?= $retourAdd ?>" class="btn btn-vert">+ Nouvelle liste</a>
  </div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Articles a acheter</th>
        <th>Budget</th>
        <th>Date creation</th>
        <th>Stock associe</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($listes)): ?>
        <tr class="vide">
          <td colspan="6">
            Aucune liste de courses enregistree.<br>
            <a href="<?= $retourAdd ?>" style="color:var(--green);font-weight:600;margin-top:8px;display:inline-block">Creer ma premiere liste</a>
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($listes as $row):
          $urlUpdate = "index.php?page=liste_courses&action=update&id={$row['id']}" . ($space === 'back' ? '&space=back' : '');
          $urlDelete = "index.php?page=liste_courses&action=delete&id={$row['id']}" . ($space === 'back' ? '&space=back' : '');
          $stockHtml = $row['stock_type']
            ? "<span style='color:var(--green);font-weight:500'>" . htmlspecialchars($row['stock_type'], ENT_QUOTES, 'UTF-8') . "</span>"
            : "<span style='color:var(--muted)'>-</span>";
        ?>
        <tr>
          <td><span class="row-num"><?= $row['id'] ?></span></td>
          <td><span class="articles-text"><?= htmlspecialchars($row['articles_a_acheter'], ENT_QUOTES, 'UTF-8') ?></span></td>
          <td><span class="badge-budget"><?= htmlspecialchars((string)$row['budget'], ENT_QUOTES, 'UTF-8') ?> TND</span></td>
          <td><span class="date-text"><?= htmlspecialchars($row['date_creation'], ENT_QUOTES, 'UTF-8') ?></span></td>
          <td><?= $stockHtml ?></td>
          <td>
            <div class="actions-group">
              <a href="<?= $urlUpdate ?>" class="btn btn-bleu btn-sm">Modifier</a>
              <a href="<?= $urlDelete ?>" class="btn btn-rouge btn-sm"
                 onclick="return confirm('Supprimer cette liste ?')">Supprimer</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
