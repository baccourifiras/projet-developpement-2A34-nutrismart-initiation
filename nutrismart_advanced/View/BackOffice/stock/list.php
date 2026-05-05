<?php if (isset($_GET['success'])): ?>
  <?php $m = ['add'=>'Stock ajoute.','update'=>'Stock modifie.','delete'=>'Stock supprime.']; ?>
  <div class="msg-ok">✅ <?= $m[$_GET['success']] ?? '' ?></div>
<?php endif; ?>

<?php
$expires = array_filter($stocks, fn($s) => $s['statut'] === 'expired');
$warning = array_filter($stocks, fn($s) => $s['statut'] === 'warning');
?>
<?php if (!empty($expires)): ?>
  <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:12px;padding:12px 18px;margin-bottom:12px;font-size:13px;color:#dc2626;display:flex;align-items:center;gap:10px">
    ⛔ <strong><?= count($expires) ?> stock(s) expiré(s)</strong> — à retirer immédiatement.
    <a href="index.php?page=dashboard<?= $space==='back'?'&space=back':'' ?>" style="margin-left:auto;color:#dc2626;font-weight:600;font-size:12px">Voir le tableau de bord →</a>
  </div>
<?php endif; ?>
<?php if (!empty($warning)): ?>
  <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:12px;padding:12px 18px;margin-bottom:12px;font-size:13px;color:#d97706">
    ⚠️ <strong><?= count($warning) ?> stock(s)</strong> expirent dans les 7 prochains jours.
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <h2>
      Mes stocks alimentaires
      <span class="count-badge"><?= $total ?></span>
    </h2>
    <a href="<?= $retourAdd ?>" class="btn btn-vert">+ Ajouter</a>
  </div>

  <!-- ══ BARRE RECHERCHE + TRI + EXPORT ══ -->
  <form method="GET" action="index.php"
        style="display:flex;align-items:center;gap:10px;padding:16px 24px;border-bottom:1px solid var(--sand);flex-wrap:wrap;">
    <input type="hidden" name="page" value="stock"/>
    <?php if ($space==='back'): ?><input type="hidden" name="space" value="back"/><?php endif; ?>

    <input type="text" name="search" value="<?= htmlspecialchars($search??'',ENT_QUOTES) ?>"
           placeholder="Rechercher un produit..."
           style="padding:9px 14px;border:1.5px solid var(--sand);border-radius:10px;font-size:13px;flex:1;min-width:180px;outline:none;font-family:'DM Sans',sans-serif;"
           onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--sand)'"/>

    <select name="sort" style="padding:9px 14px;border:1.5px solid var(--sand);border-radius:10px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;background:var(--white);">
      <option value="id_desc"  <?= ($sort??'id_desc')==='id_desc'  ?'selected':'' ?>>Plus récent</option>
      <option value="date_asc" <?= ($sort??'')==='date_asc'  ?'selected':'' ?>>Expiration ↑</option>
      <option value="date_desc"<?= ($sort??'')==='date_desc' ?'selected':'' ?>>Expiration ↓</option>
      <option value="produit"  <?= ($sort??'')==='produit'   ?'selected':'' ?>>Produit A→Z</option>
    </select>

    <button type="submit" style="padding:9px 18px;background:linear-gradient(135deg,var(--green),#15803d);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">🔍 Rechercher</button>
    <a href="index.php?page=stock<?= $space==='back'?'&space=back':'' ?>" style="padding:9px 14px;background:var(--sand);color:var(--muted);border-radius:10px;font-size:13px;font-weight:500;text-decoration:none;">✕ Reset</a>
    <a href="index.php?page=stock&action=export_pdf<?= $space==='back'?'&space=back':'' ?>&search=<?= urlencode($search??'') ?>&sort=<?= urlencode($sort??'id_desc') ?>"
       style="padding:9px 18px;background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;">📄 PDF</a>
  </form>

  <table>
    <thead>
      <tr>
        <th>#</th><th>Produit</th><th>Catégorie</th><th>Date expiration</th><th>Seuil</th><th>Statut</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($stocks)): ?>
        <tr><td colspan="7" style="text-align:center;padding:48px;color:var(--muted)">
          <?= !empty($search) ? 'Aucun résultat pour "<strong>'.htmlspecialchars($search).'</strong>".' : 'Aucun stock enregistré.' ?>
        </td></tr>
      <?php else: ?>
        <?php foreach ($stocks as $row):
          $urlUpdate = "index.php?page=stock&action=update&id={$row['id']}" . ($space==='back'?'&space=back':'');
          $urlDelete = "index.php?page=stock&action=delete&id={$row['id']}" . ($space==='back'?'&space=back':'');
          $rowBg = match($row['statut']) { 'expired'=>'background:rgba(220,38,38,.04);', 'warning'=>'background:rgba(245,158,11,.04);', default=>'' };
        ?>
        <tr style="<?= $rowBg ?>">
          <td><span class="row-num"><?= $row['id'] ?></span></td>
          <td style="font-weight:500"><?= htmlspecialchars($row['produits'],ENT_QUOTES,'UTF-8') ?></td>
          <td><span style="background:rgba(13,59,31,.06);color:var(--forest);padding:4px 11px;border-radius:999px;font-size:12px;font-weight:600"><?= htmlspecialchars($row['type'],ENT_QUOTES,'UTF-8') ?></span></td>
          <td><span class="date-text"><?= htmlspecialchars($row['date_expiration'],ENT_QUOTES,'UTF-8') ?></span></td>
          <td><span style="font-weight:600;color:var(--green)"><?= $row['seuil_minimum'] ?></span> <span style="color:var(--muted);font-size:12px">u.</span></td>
          <td>
            <?php if ($row['statut']==='expired'): ?>
              <span style="background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600">⛔ Expiré</span>
            <?php elseif ($row['statut']==='warning'): ?>
              <span style="background:#fffbeb;color:#d97706;border:1px solid #fcd34d;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600">⚠ Bientôt</span>
            <?php else: ?>
              <span style="background:#f0fdf4;color:#16a34a;border:1px solid #86efac;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600">✓ OK</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="actions-group">
              <a href="<?= $urlUpdate ?>" class="btn btn-bleu btn-sm">Modifier</a>
              <a href="<?= $urlDelete ?>" class="btn btn-rouge btn-sm" onclick="return confirm('Supprimer ce stock ?')">Supprimer</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- ══ PAGINATION ══ -->
  <?php
  $paginationUrl = 'index.php?page=stock' . ($space==='back'?'&space=back':'') . '&search=' . urlencode($search??'') . '&sort=' . urlencode($sort??'id_desc');
  renderPagination($curPage, $nbPages, $paginationUrl);
  ?>
</div>
