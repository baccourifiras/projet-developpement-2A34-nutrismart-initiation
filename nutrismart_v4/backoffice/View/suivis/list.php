<?php
/**
 * =====================================================================
 *  NutriSmart - View/suivis/list.php
 *  Liste des suivis avec recherche et tri.
 * =====================================================================
 */
require_once __DIR__ . '/../../Controller/SuiviC.php';

$suivis = (new SuiviC())->afficherSuivis();

/* ---- Recherche ---- */
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $suivis = array_filter($suivis, function($s) use ($search) {
        return stripos($s['type_regime'], $search) !== false
            || stripos($s['date'],        $search) !== false;
    });
}

/* ---- Tri ---- */
$sortCol = $_GET['sort'] ?? 'date';
$sortDir = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
$allowed = ['id_suivi','date','poids','calories_consommees','type_regime'];
if (!in_array($sortCol, $allowed)) { $sortCol = 'date'; }

usort($suivis, function($a, $b) use ($sortCol, $sortDir) {
    $va = $a[$sortCol]; $vb = $b[$sortCol];
    $cmp = is_numeric($va) ? ($va <=> $vb) : strcmp($va, $vb);
    return $sortDir === 'asc' ? $cmp : -$cmp;
});

function sortLink(string $col, string $label, string $current, string $dir): string {
    $nextDir = ($current === $col && $dir === 'asc') ? 'desc' : 'asc';
    $search  = htmlspecialchars($_GET['search'] ?? '');
    $arrow   = $current === $col ? ($dir === 'asc' ? ' ▲' : ' ▼') : '';
    return "<a href=\"?sort={$col}&dir={$nextDir}&search={$search}\" class=\"sort-link\">{$label}{$arrow}</a>";
}

$activeMenu = 'suivis';
$pageTitle  = 'NutriSmart - Liste des suivis';
include __DIR__ . '/../templates/header.php';
?>

<header class="page-header">
  <p class="badge">Gestion</p>
  <h1>Liste des suivis</h1>
  <p class="subtitle">Chaque suivi est relie a un regime (jointure SQL).</p>
</header>

<section class="section">
  <div class="list-toolbar">
    <form method="get" class="search-form">
      <input type="hidden" name="sort" value="<?= htmlspecialchars($sortCol) ?>">
      <input type="hidden" name="dir"  value="<?= htmlspecialchars($sortDir) ?>">
      <input type="search" name="search" placeholder="Rechercher par regime ou date…"
             value="<?= htmlspecialchars($search) ?>" class="search-input">
      <button type="submit" class="btn-primary">Rechercher</button>
      <?php if ($search !== '') : ?>
        <a href="list.php" class="btn-secondary">Effacer</a>
      <?php endif; ?>
    </form>
    <a href="add.php" class="btn-primary">+ Ajouter un suivi</a>
  </div>

  <?php if (empty($suivis)) : ?>
    <div class="empty-box">
      <h2>Aucun suivi trouvé</h2>
      <p><?= $search ? 'Aucun résultat pour "'.htmlspecialchars($search).'".' : 'Creez d\'abord un regime puis ajoutez des suivis.' ?></p>
    </div>
  <?php else : ?>
    <p class="result-count"><?= count($suivis) ?> suivi(s) affiché(s)</p>
    <table class="data-table">
      <thead>
        <tr>
          <th><?= sortLink('id_suivi',             'ID',                   $sortCol, $sortDir) ?></th>
          <th><?= sortLink('date',                 'Date',                 $sortCol, $sortDir) ?></th>
          <th><?= sortLink('poids',                'Poids',                $sortCol, $sortDir) ?></th>
          <th><?= sortLink('calories_consommees',  'Calories consommees',  $sortCol, $sortDir) ?></th>
          <th><?= sortLink('type_regime',          'Regime',               $sortCol, $sortDir) ?></th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($suivis as $s) : ?>
          <tr>
            <td><?= (int) $s['id_suivi'] ?></td>
            <td><?= htmlspecialchars($s['date']) ?></td>
            <td><?= (float) $s['poids'] ?> kg</td>
            <td><?= (int) $s['calories_consommees'] ?> kcal</td>
            <td><strong><?= htmlspecialchars($s['type_regime']) ?></strong> (cible <?= (int) $s['calories_cible'] ?>)</td>
            <td>
              <a href="edit.php?id=<?= (int) $s['id_suivi'] ?>"    class="btn-secondary">Modifier</a>
              <a href="delete.php?id=<?= (int) $s['id_suivi'] ?>"
                 class="btn-danger"
                 onclick="return confirm('Supprimer ce suivi ?');">
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
