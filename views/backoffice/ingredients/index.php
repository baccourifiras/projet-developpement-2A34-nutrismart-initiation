<?php
/**
 * View : /views/backoffice/ingredients/index.php
 * Variables : $result, $opts, $stats, $categories
 */
$baseUrl = '/' . basename(BASE_PATH);
?>

<section class="dashboard-grid">
  <div class="widget">
    <span class="icon">🥕</span>
    <p class="label">Total ingrédients</p>
    <p class="value"><?= e($stats['total']) ?></p>
  </div>
  <div class="widget danger">
    <span class="icon">⚠️</span>
    <p class="label">En rupture</p>
    <p class="value"><?= e($stats['en_rupture']) ?></p>
    <p class="sub">Stock <= 0</p>
  </div>
  <?php $top = $stats['par_categorie'][0] ?? null; ?>
  <div class="widget success">
    <span class="icon">🏷️</span>
    <p class="label">Catégorie principale</p>
    <p class="value" style="font-size:24px"><?= e($top ? $top['categorie'] : '—') ?></p>
    <p class="sub"><?= $top ? ($top['n'].' éléments') : '' ?></p>
  </div>
</section>

<div class="page-head">
  <div>
    <p class="breadcrumb">Gestion · Ingrédients</p>
    <h2>Liste des ingrédients</h2>
  </div>
  <a class="primary-btn" href="<?= e($baseUrl) ?>/backoffice/ingredient_form.php">+ Nouvel ingrédient</a>
</div>

<form class="admin-toolbar" method="get" action="ingredients.php">
  <input type="text" name="q" placeholder="🔍 Rechercher par nom…"
         value="<?= e($opts['q']) ?>" data-live-search>
  <select name="categorie">
    <option value="">Toutes catégories</option>
    <?php foreach ($categories as $c): ?>
      <option value="<?= e($c) ?>" <?= $opts['categorie']===$c?'selected':'' ?>><?= e($c) ?></option>
    <?php endforeach; ?>
  </select>
  <input type="number" name="stock_min" min="0" step="0.01" placeholder="Stock min" value="<?= e($opts['stock_min']) ?>">
  <button type="submit" class="btn-link">Filtrer</button>
  <a class="btn-link btn-export" href="ingredients_export.php?format=csv&<?= e(http_build_query($opts)) ?>">📄 CSV</a>
  <a class="btn-link btn-export" href="ingredients_export.php?format=excel&<?= e(http_build_query($opts)) ?>">📊 Excel</a>
  <a class="btn-link btn-export" href="ingredients_export.php?format=pdf&<?= e(http_build_query($opts)) ?>">📕 PDF</a>
</form>

<?php if ($result['total'] === 0): ?>
  <div class="empty-state">
    <div class="icon">🥕</div>
    <h3>Aucun ingrédient trouvé</h3>
    <p>Ajustez vos critères ou créez un nouvel ingrédient.</p>
  </div>
<?php else: ?>
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <?php
            $cols = [
              'nom'            => 'Nom',
              'categorie'      => 'Catégorie',
              'quantite_stock' => 'Stock',
              'date_ajout'     => 'Ajouté le',
            ];
            foreach ($cols as $key => $label):
              $isSorted = $opts['sort'] === $key;
              $cls = 'sortable' . ($isSorted ? ' sorted-' . strtolower($opts['dir']) : '');
          ?>
            <th class="<?= $cls ?>" data-sort="<?= e($key) ?>">
              <?= e($label) ?>
              <span class="arrow"><?= $isSorted ? ($opts['dir']==='asc'?'▲':'▼') : '↕' ?></span>
            </th>
          <?php endforeach; ?>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($result['rows'] as $r): ?>
          <tr>
            <td><strong><?= e($r['nom']) ?></strong></td>
            <td><span class="chip" style="background:rgba(124,58,237,.12);color:#5b21b6"><?= e($r['categorie']) ?></span></td>
            <td><?= e(rtrim(rtrim((string)$r['quantite_stock'], '0'), '.')) ?> <?= e($r['unite']) ?></td>
            <td><?= e(date('d/m/Y', strtotime($r['date_ajout']))) ?></td>
            <td>
              <a class="btn-icon btn-view" href="ingredient_show.php?id=<?= (int)$r['id'] ?>">👁</a>
              <a class="btn-icon btn-edit" href="ingredient_form.php?id=<?= (int)$r['id'] ?>">✏</a>
              <form action="ingredient_delete.php" method="post" style="display:inline"
                    data-confirm="Supprimer l'ingrédient « <?= e($r['nom']) ?> » ? Toutes les associations aux recettes seront perdues.">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn-icon btn-delete" type="submit">🗑</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if ($result['pages'] > 1): ?>
    <nav class="pagination">
      <?php
        $base = $_GET; unset($base['page']);
        $qs = http_build_query($base);
        $cur = $result['page']; $tot = $result['pages'];
      ?>
      <?php if ($cur > 1): ?>
        <a href="?<?= e($qs) ?>&page=<?= $cur-1 ?>">‹</a>
      <?php else: ?><span class="disabled">‹</span><?php endif; ?>

      <?php for ($i = 1; $i <= $tot; $i++): ?>
        <?php if ($i == $cur): ?><span class="current"><?= $i ?></span>
        <?php else: ?><a href="?<?= e($qs) ?>&page=<?= $i ?>"><?= $i ?></a><?php endif; ?>
      <?php endfor; ?>

      <?php if ($cur < $tot): ?>
        <a href="?<?= e($qs) ?>&page=<?= $cur+1 ?>">›</a>
      <?php else: ?><span class="disabled">›</span><?php endif; ?>
    </nav>
  <?php endif; ?>
<?php endif; ?>
