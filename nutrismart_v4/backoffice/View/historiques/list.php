<?php
/**
 * =====================================================================
 *  NutriSmart - View/historiques/list.php
 *  Liste des recommandations avec recherche et tri.
 * =====================================================================
 */
require_once __DIR__ . '/../../Controller/HistoriqueC.php';

$histos = (new HistoriqueC())->afficherHistoriques();

/* ---- Recherche ---- */
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $histos = array_filter($histos, function($h) use ($search) {
        return stripos($h['recommandation'], $search) !== false
            || stripos($h['type_regime'],    $search) !== false
            || stripos($h['date'],           $search) !== false;
    });
}

/* ---- Tri ---- */
$sortCol = $_GET['sort'] ?? 'date';
$sortDir = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
$allowed = ['id_historique','date','recommandation','type_regime'];
if (!in_array($sortCol, $allowed)) { $sortCol = 'date'; }

usort($histos, function($a, $b) use ($sortCol, $sortDir) {
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

$activeMenu = 'historiques';
$pageTitle  = 'NutriSmart - Liste des recommandations';
include __DIR__ . '/../templates/header.php';
?>

<header class="page-header">
  <p class="badge">Gestion</p>
  <h1>Historique des recommandations</h1>
  <p class="subtitle">Chaque recommandation est reliee a un regime (jointure SQL).</p>
</header>

<section class="section">
  <div class="list-toolbar">
    <form method="get" class="search-form">
      <input type="hidden" name="sort" value="<?= htmlspecialchars($sortCol) ?>">
      <input type="hidden" name="dir"  value="<?= htmlspecialchars($sortDir) ?>">
      <input type="search" name="search" placeholder="Rechercher par recommandation, regime ou date…"
             value="<?= htmlspecialchars($search) ?>" class="search-input">
      <button type="submit" class="btn-primary">Rechercher</button>
      <?php if ($search !== '') : ?>
        <a href="list.php" class="btn-secondary">Effacer</a>
      <?php endif; ?>
    </form>
    <a href="add.php" class="btn-primary">+ Ajouter une recommandation</a>
  </div>

  <?php if (empty($histos)) : ?>
    <div class="empty-box">
      <h2>Aucune recommandation trouvée</h2>
      <p><?= $search ? 'Aucun résultat pour "'.htmlspecialchars($search).'".' : 'Ajoutez d\'abord un regime puis des recommandations.' ?></p>
    </div>
  <?php else : ?>
    <p class="result-count"><?= count($histos) ?> recommandation(s) affichée(s)</p>
    <table class="data-table">
      <thead>
        <tr>
          <th><?= sortLink('id_historique', 'ID',              $sortCol, $sortDir) ?></th>
          <th><?= sortLink('date',          'Date',            $sortCol, $sortDir) ?></th>
          <th><?= sortLink('recommandation','Recommandation',  $sortCol, $sortDir) ?></th>
          <th><?= sortLink('type_regime',   'Regime',          $sortCol, $sortDir) ?></th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($histos as $h) : ?>
          <tr>
            <td><?= (int) $h['id_historique'] ?></td>
            <td><?= htmlspecialchars($h['date']) ?></td>
            <td><?= htmlspecialchars($h['recommandation']) ?></td>
            <td><strong><?= htmlspecialchars($h['type_regime']) ?></strong> (#<?= (int) $h['id_regime'] ?>)</td>
            <td>
              <a href="edit.php?id=<?= (int) $h['id_historique'] ?>"    class="btn-secondary">Modifier</a>
              <a href="delete.php?id=<?= (int) $h['id_historique'] ?>"
                 class="btn-danger"
                 onclick="return confirm('Supprimer cette recommandation ?');">
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
