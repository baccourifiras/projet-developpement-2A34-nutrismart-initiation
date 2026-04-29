<?php
/**
 * =====================================================================
 *  NutriSmart - View/historiques/add.php
 * =====================================================================
 */
require_once __DIR__ . '/../../Controller/HistoriqueC.php';
require_once __DIR__ . '/../../Controller/RegimeC.php';
require_once __DIR__ . '/../../Model/Historique.php';

$regimes = (new RegimeC())->listerRegimes();

$erreurs = [];
$old = [
    'id_regime'      => '',
    'recommandation' => '',
    'date'           => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old['id_regime']      = trim($_POST['id_regime']      ?? '');
    $old['recommandation'] = trim($_POST['recommandation'] ?? '');
    $old['date']           = trim($_POST['date']           ?? '');

    if (!isset($_POST['id_regime']) || empty($old['id_regime']) || !is_numeric($old['id_regime'])) {
        $erreurs[] = 'Vous devez choisir un regime.';
    }
    if (!isset($_POST['recommandation']) || empty($old['recommandation'])) {
        $erreurs[] = 'La recommandation est obligatoire.';
    }
    if (!isset($_POST['date']) || empty($old['date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $old['date'])) {
        $erreurs[] = 'La date est obligatoire (format YYYY-MM-DD).';
    }

    if (empty($erreurs)) {
        $h = new Historique(
            (int) $old['id_regime'],
            $old['recommandation'],
            $old['date']
        );
        (new HistoriqueC())->ajouterHistorique($h);
        header('Location: list.php');
        exit;
    }
}

$activeMenu = 'historiques';
$pageTitle  = 'NutriSmart - Ajouter une recommandation';
include __DIR__ . '/../templates/header.php';
?>

<header class="page-header">
  <p class="badge">Creation</p>
  <h1>Ajouter une recommandation</h1>
</header>

<section class="section">
  <?php if (empty($regimes)) : ?>
    <div class="alert-error">
      Vous devez d'abord creer au moins un regime.
      <a href="../regimes/add.php">Creer un regime</a>
    </div>
  <?php else : ?>

    <?php if (!empty($erreurs)) : ?>
      <div class="alert-error">
        <strong>Veuillez corriger :</strong>
        <ul>
          <?php foreach ($erreurs as $e) : ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form id="formHistorique" method="post" action="add.php" novalidate class="crud-form">
      <div class="form-group">
        <label for="id_regime">Regime associe</label>
        <select id="id_regime" name="id_regime">
          <option value="">-- Choisir --</option>
          <?php foreach ($regimes as $r) : ?>
            <option value="<?= (int) $r['id_regime'] ?>"
              <?= (string) $old['id_regime'] === (string) $r['id_regime'] ? 'selected' : '' ?>>
              #<?= (int) $r['id_regime'] ?> - <?= htmlspecialchars($r['type_regime']) ?> (<?= htmlspecialchars($r['date_debut']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <span class="field-error" data-error-for="id_regime"></span>
      </div>

      <div class="form-group">
        <label for="recommandation">Recommandation</label>
        <textarea id="recommandation" name="recommandation" rows="4"><?= htmlspecialchars($old['recommandation']) ?></textarea>
        <span class="field-error" data-error-for="recommandation"></span>
      </div>

      <div class="form-group">
        <label for="date">Date (YYYY-MM-DD)</label>
        <input type="text" id="date" name="date" value="<?= htmlspecialchars($old['date']) ?>">
        <span class="field-error" data-error-for="date"></span>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary">Enregistrer</button>
        <a href="list.php" class="btn-secondary">Annuler</a>
      </div>
    </form>

  <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
