<?php
/**
 * View : /views/backoffice/recettes/index.php
 * Variables disponibles : $result, $opts, $stats
 */
$baseUrl = '/' . basename(BASE_PATH);
?>

<!-- Widgets dashboard -->
<section class="dashboard-grid">
  <div class="widget">
    <span class="icon">🍽️</span>
    <p class="label">Total recettes</p>
    <p class="value"><?= e($stats['total']) ?></p>
    <p class="sub">Durée moyenne : <?= e($stats['duree_moy']) ?> min</p>
  </div>
  <div class="widget success">
    <span class="icon">✅</span>
    <p class="label">Faciles</p>
    <p class="value"><?= e($stats['facile']) ?></p>
  </div>
  <div class="widget warn">
    <span class="icon">⚙️</span>
    <p class="label">Niveau moyen</p>
    <p class="value"><?= e($stats['moyen']) ?></p>
  </div>
  <div class="widget danger">
    <span class="icon">🔥</span>
    <p class="label">Difficiles</p>
    <p class="value"><?= e($stats['difficile']) ?></p>
  </div>
</section>

<div class="page-head">
  <div>
    <p class="breadcrumb">Gestion · Recettes</p>
    <h2>Liste des recettes</h2>
  </div>
  <a class="primary-btn" href="<?= e($baseUrl) ?>/backoffice/recette_form.php">+ Nouvelle recette</a>
</div>

<!-- Toolbar : recherche + filtres + exports -->
<form class="admin-toolbar" method="get" action="recettes.php">
  <input type="text" name="q" placeholder="🔍 Rechercher (nom, description)…"
         value="<?= e($opts['q']) ?>" data-live-search>
  <select name="niveau">
    <option value="">Tous niveaux</option>
    <?php foreach (Recette::NIVEAUX as $n): ?>
      <option value="<?= e($n) ?>" <?= $opts['niveau']===$n?'selected':'' ?>><?= e(ucfirst($n)) ?></option>
    <?php endforeach; ?>
  </select>
  <input type="number" name="duree_max" min="0" placeholder="Durée max (min)" value="<?= e($opts['duree_max']) ?>">
  <button type="submit" class="btn-link">Filtrer</button>
  <a class="btn-link btn-export"
     href="recettes_export.php?format=csv&<?= e(http_build_query($opts)) ?>">📄 CSV</a>
  <a class="btn-link btn-export"
     href="recettes_export.php?format=excel&<?= e(http_build_query($opts)) ?>">📊 Excel</a>
  <a class="btn-link btn-export"
     href="recettes_export.php?format=pdf&<?= e(http_build_query($opts)) ?>">📕 PDF</a>
</form>

<?php if ($result['total'] === 0): ?>
  <div class="empty-state">
    <div class="icon">🍽️</div>
    <h3>Aucune recette trouvée</h3>
    <p>Essayez d'élargir vos critères ou ajoutez une nouvelle recette.</p>
  </div>
<?php else: ?>
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Image</th>
          <?php
            $sortableCols = [
              'nom'           => 'Nom',
              'duree'         => 'Durée',
              'niveau'        => 'Niveau',
              'date_creation' => 'Créée le',
            ];
            foreach ($sortableCols as $key => $label):
              $isSorted = $opts['sort'] === $key;
              $cls = 'sortable' . ($isSorted ? ' sorted-' . strtolower($opts['dir']) : '');
          ?>
            <th class="<?= $cls ?>" data-sort="<?= e($key) ?>">
              <?= e($label) ?>
              <span class="arrow">
                <?= $isSorted ? ($opts['dir']==='asc' ? '▲' : '▼') : '↕' ?>
              </span>
            </th>
          <?php endforeach; ?>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($result['rows'] as $r): ?>
          <tr>
            <td>
              <?php if (!empty($r['image'])): ?>
                <img class="thumb" src="<?= e($r['image']) ?>" alt="">
              <?php else: ?>
                <div class="thumb" style="background:#e5f0e8;display:grid;place-items:center;">🍽️</div>
              <?php endif; ?>
            </td>
            <td><strong><?= e($r['nom']) ?></strong></td>
            <td><?= e($r['duree']) ?> min</td>
            <td>
              <?php $cls = $r['niveau']==='facile'?'chip-easy':($r['niveau']==='moyen'?'chip-medium':'chip-hard'); ?>
              <span class="chip <?= $cls ?>"><?= e(ucfirst($r['niveau'])) ?></span>
            </td>
            <td><?= e(date('d/m/Y', strtotime($r['date_creation']))) ?></td>
            <td>
              <a class="btn-icon btn-view"   href="recette_show.php?id=<?= (int)$r['id'] ?>">👁</a>
              <a class="btn-icon btn-edit"   href="recette_form.php?id=<?= (int)$r['id'] ?>">✏</a>
              <form action="recette_delete.php" method="post" style="display:inline"
                    data-confirm="Supprimer la recette « <?= e($r['nom']) ?> » ?">
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

  <!-- Pagination -->
  <?php if ($result['pages'] > 1): ?>
    <nav class="pagination">
      <?php
        $base = $_GET; unset($base['page']);
        $qs   = http_build_query($base);
        $cur  = $result['page'];
        $tot  = $result['pages'];
      ?>
      <?php if ($cur > 1): ?>
        <a href="?<?= e($qs) ?>&page=<?= $cur-1 ?>">‹ Précédent</a>
      <?php else: ?>
        <span class="disabled">‹ Précédent</span>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $tot; $i++): ?>
        <?php if ($i == $cur): ?>
          <span class="current"><?= $i ?></span>
        <?php else: ?>
          <a href="?<?= e($qs) ?>&page=<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
      <?php endfor; ?>

      <?php if ($cur < $tot): ?>
        <a href="?<?= e($qs) ?>&page=<?= $cur+1 ?>">Suivant ›</a>
      <?php else: ?>
        <span class="disabled">Suivant ›</span>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
<?php endif; ?>
