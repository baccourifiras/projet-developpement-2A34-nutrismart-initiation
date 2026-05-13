<?php if (isset($_GET['success'])): ?>
  <?php $m = ['add'=>'Liste ajoutée.','update'=>'Liste modifiée.','delete'=>'Liste supprimée.']; ?>
  <div class="msg-ok">✅ <?= $m[$_GET['success']] ?? '' ?></div>
<?php endif; ?>

<?php $totalBudget = getTotalBudget(); ?>

<div class="card">
  <div class="card-header">
    <h2>Toutes mes listes <span class="count-badge"><?= $total ?></span></h2>
    <div style="display:flex;gap:8px;align-items:center">
      <span style="background:linear-gradient(135deg,var(--lime-soft),rgba(74,222,128,.15));color:var(--forest);border:1px solid rgba(74,222,128,.4);padding:6px 14px;border-radius:999px;font-size:13px;font-weight:600">
        💰 Total : <?= number_format($totalBudget,2) ?> TND
      </span>
      <a href="<?= $retourAdd ?>" class="btn btn-vert">+ Nouvelle liste</a>
    </div>
  </div>

  <!-- ══ RECHERCHE + TRI + EXPORT ══ -->
  <form method="GET" action="index.php"
        style="display:flex;align-items:center;gap:10px;padding:16px 24px;border-bottom:1px solid var(--sand);flex-wrap:wrap;">
    <input type="hidden" name="page" value="liste_courses"/>
    <?php if ($space==='back'): ?><input type="hidden" name="space" value="back"/><?php endif; ?>

    <input type="text" name="search" value="<?= htmlspecialchars($search??'',ENT_QUOTES) ?>"
           placeholder="Rechercher des articles..."
           style="padding:9px 14px;border:1.5px solid var(--sand);border-radius:10px;font-size:13px;flex:1;min-width:180px;outline:none;font-family:'DM Sans',sans-serif;"
           onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--sand)'"/>

    <select name="sort" style="padding:9px 14px;border:1.5px solid var(--sand);border-radius:10px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;background:var(--white);">
      <option value="id_desc"    <?= ($sort??'id_desc')==='id_desc'    ?'selected':'' ?>>Plus récent</option>
      <option value="budget_asc" <?= ($sort??'')==='budget_asc'  ?'selected':'' ?>>Budget ↑</option>
      <option value="budget_desc"<?= ($sort??'')==='budget_desc' ?'selected':'' ?>>Budget ↓</option>
      <option value="date_asc"   <?= ($sort??'')==='date_asc'    ?'selected':'' ?>>Date ↑</option>
      <option value="date_desc"  <?= ($sort??'')==='date_desc'   ?'selected':'' ?>>Date ↓</option>
    </select>

    <button type="submit" style="padding:9px 18px;background:linear-gradient(135deg,var(--green),#15803d);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">🔍 Rechercher</button>
    <a href="index.php?page=liste_courses<?= $space==='back'?'&space=back':'' ?>" style="padding:9px 14px;background:var(--sand);color:var(--muted);border-radius:10px;font-size:13px;font-weight:500;text-decoration:none;">✕ Reset</a>
    <a href="index.php?page=liste_courses&action=export_pdf<?= $space==='back'?'&space=back':'' ?>&search=<?= urlencode($search??'') ?>&sort=<?= urlencode($sort??'id_desc') ?>"
       style="padding:9px 18px;background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;">📄 PDF</a>
  </form>

  <table>
    <thead>
      <tr><th>#</th><th>Articles</th><th>Budget</th><th>Date création</th><th>Stock associé</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php if (empty($listes)): ?>
        <tr><td colspan="6" style="text-align:center;padding:48px;color:var(--muted)">
          <?= !empty($search) ? 'Aucun résultat pour "<strong>'.htmlspecialchars($search).'</strong>".' : 'Aucune liste enregistrée.' ?>
        </td></tr>
      <?php else: ?>
        <?php foreach ($listes as $row):
          $urlUpdate    = "index.php?page=liste_courses&action=update&id={$row['id']}" . ($space==='back'?'&space=back':'');
          $urlDelete    = "index.php?page=liste_courses&action=delete&id={$row['id']}" . ($space==='back'?'&space=back':'');
          $stockHtml    = $row['stock_type'] ? "<span style='color:var(--green);font-weight:500'>".htmlspecialchars($row['stock_type'],ENT_QUOTES,'UTF-8')."</span>" : "<span style='color:var(--muted)'>—</span>";
        ?>
        <tr>
          <td><span class="row-num"><?= $row['id'] ?></span></td>
          <td><span class="articles-text"><?= htmlspecialchars($row['articles_a_acheter'],ENT_QUOTES,'UTF-8') ?></span></td>
          <td><span class="badge-budget"><?= htmlspecialchars((string)$row['budget'],ENT_QUOTES,'UTF-8') ?> TND</span></td>
          <td><span class="date-text"><?= htmlspecialchars($row['date_creation'],ENT_QUOTES,'UTF-8') ?></span></td>
          <td><?= $stockHtml ?></td>
          <td>
            <div class="actions-group">
              <a href="<?= $urlUpdate ?>" class="btn btn-bleu btn-sm">Modifier</a>
              <a href="<?= $urlDelete ?>" class="btn btn-rouge btn-sm" onclick="return confirm('Supprimer cette liste ?')">Supprimer</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- ══ PAGINATION ══ -->
  <?php
  $paginationUrl = 'index.php?page=liste_courses' . ($space==='back'?'&space=back':'') . '&search=' . urlencode($search??'') . '&sort=' . urlencode($sort??'id_desc');
  renderPagination($curPage, $nbPages, $paginationUrl);
  ?>
</div>
