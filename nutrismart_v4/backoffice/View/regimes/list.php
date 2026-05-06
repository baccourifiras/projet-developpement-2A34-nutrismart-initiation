<?php
/**
 * =====================================================================
 *  NutriSmart - View/regimes/list.php
 *  Liste des regimes avec recherche et tri.
 * =====================================================================
 */
require_once __DIR__ . '/../../Controller/RegimeC.php';

$regimes = (new RegimeC())->afficherRegimes();

/* ---- Recherche ---- */
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $regimes = array_filter($regimes, function($r) use ($search) {
        return stripos($r['type_regime'], $search) !== false
            || stripos($r['date_debut'],  $search) !== false;
    });
}

/* ---- Tri ---- */
$sortCol = $_GET['sort'] ?? 'date_debut';
$sortDir = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
$allowed = ['id_regime','type_regime','calories_cible','date_debut','poids_initial','duree'];
if (!in_array($sortCol, $allowed)) { $sortCol = 'date_debut'; }

usort($regimes, function($a, $b) use ($sortCol, $sortDir) {
    $va = $a[$sortCol]; $vb = $b[$sortCol];
    $cmp = is_numeric($va) ? ($va <=> $vb) : strcmp($va, $vb);
    return $sortDir === 'asc' ? $cmp : -$cmp;
});

/* ---- Helper lien de tri ---- */
function sortLink(string $col, string $label, string $current, string $dir): string {
    $nextDir = ($current === $col && $dir === 'asc') ? 'desc' : 'asc';
    $search  = htmlspecialchars($_GET['search'] ?? '');
    $arrow   = $current === $col ? ($dir === 'asc' ? ' ▲' : ' ▼') : '';
    return "<a href=\"?sort={$col}&dir={$nextDir}&search={$search}\" class=\"sort-link\">{$label}{$arrow}</a>";
}

$activeMenu = 'regimes';
$pageTitle  = 'NutriSmart - Liste des regimes';
include __DIR__ . '/../templates/header.php';
?>

<header class="page-header">
  <p class="badge">Gestion</p>
  <h1>Liste des regimes</h1>
  <p class="subtitle">Gerer les regimes alimentaires (cut, bulk, equilibre).</p>
</header>

<section class="section">
  <!-- Barre recherche + bouton ajouter -->
  <div class="list-toolbar">
    <form method="get" class="search-form">
      <input type="hidden" name="sort" value="<?= htmlspecialchars($sortCol) ?>">
      <input type="hidden" name="dir"  value="<?= htmlspecialchars($sortDir) ?>">
      <input type="search" name="search" placeholder="Rechercher par type ou date…"
             value="<?= htmlspecialchars($search) ?>" class="search-input">
      <button type="submit" class="btn-primary">Rechercher</button>
      <?php if ($search !== '') : ?>
        <a href="list.php" class="btn-secondary">Effacer</a>
      <?php endif; ?>
    </form>
    <a href="add.php" class="btn-primary">+ Ajouter un regime</a>
  </div>

  <?php if (empty($regimes)) : ?>
    <div class="empty-box">
      <h2>Aucun regime trouvé</h2>
      <p><?= $search ? 'Aucun résultat pour "'.htmlspecialchars($search).'".' : 'Commencez par ajouter un regime.' ?></p>
    </div>
  <?php else : ?>
    <p class="result-count"><?= count($regimes) ?> regime(s) affiché(s)</p>
    <table class="data-table">
      <thead>
        <tr>
          <th><?= sortLink('id_regime',      'ID',             $sortCol, $sortDir) ?></th>
          <th><?= sortLink('type_regime',    'Type',           $sortCol, $sortDir) ?></th>
          <th><?= sortLink('calories_cible', 'Calories cible', $sortCol, $sortDir) ?></th>
          <th><?= sortLink('date_debut',     'Date debut',     $sortCol, $sortDir) ?></th>
          <th><?= sortLink('poids_initial',  'Poids initial',  $sortCol, $sortDir) ?></th>
          <th><?= sortLink('duree',          'Duree (j)',      $sortCol, $sortDir) ?></th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($regimes as $r) : ?>
          <tr>
            <td><?= (int) $r['id_regime'] ?></td>
            <td><strong><?= htmlspecialchars($r['type_regime']) ?></strong></td>
            <td><?= (int) $r['calories_cible'] ?> kcal</td>
            <td><?= htmlspecialchars($r['date_debut']) ?></td>
            <td><?= (float) $r['poids_initial'] ?> kg</td>
            <td><?= (int) $r['duree'] ?></td>
            <td>
              <a href="details.php?id=<?= (int) $r['id_regime'] ?>" class="btn-secondary">Details</a>
              <a href="edit.php?id=<?= (int) $r['id_regime'] ?>"    class="btn-secondary">Modifier</a>
              <a href="delete.php?id=<?= (int) $r['id_regime'] ?>"
                 class="btn-danger"
                 onclick="return confirm('Supprimer ce regime et toutes ses donnees liees ?');">
                Supprimer
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
