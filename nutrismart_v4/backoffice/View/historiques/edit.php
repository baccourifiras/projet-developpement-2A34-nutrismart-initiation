<?php
/**
 * =====================================================================
 *  NutriSmart - View/historiques/edit.php
 * =====================================================================
 */
require_once __DIR__ . '/../../Controller/HistoriqueC.php';
require_once __DIR__ . '/../../Controller/RegimeC.php';
require_once __DIR__ . '/../../Model/Historique.php';

$ctrl    = new HistoriqueC();
$regimes = (new RegimeC())->listerRegimes();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: list.php'); exit; }

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idR  = trim($_POST['id_regime']      ?? '');
    $reco = trim($_POST['recommandation'] ?? '');
    $date = trim($_POST['date']           ?? '');

    if (!isset($_POST['id_regime']) || empty($idR) || !is_numeric($idR)) {
        $erreurs[] = 'Vous devez choisir un regime.';
    }
    if (!isset($_POST['recommandation']) || empty($reco)) {
        $erreurs[] = 'La recommandation est obligatoire.';
    }
    if (!isset($_POST['date']) || empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $erreurs[] = 'La date est obligatoire (format YYYY-MM-DD).';
    }

    if (empty($erreurs)) {
        $h = new Historique((int) $idR, $reco, $date, $id);
        $ctrl->modifierHistorique($h);
        header('Location: list.php');
        exit;
    }

    $historique = [
        'id_historique'  => $id,
        'id_regime'      => $idR,
        'recommandation' => $reco,
        'date'           => $date,
    ];
} else {
    $historique = $ctrl->getHistoriqueById($id);
    if (!$historique) { header('Location: list.php'); exit; }
}

$activeMenu = 'historiques';
$pageTitle  = 'NutriSmart - Modifier la recommandation';
include __DIR__ . '/../templates/header.php';
?>

<header class="page-header">
  <p class="badge">Edition</p>
  <h1>Modifier la recommandation #<?= (int) $historique['id_historique'] ?></h1>
</header>

<section class="section">
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

  <form id="formHistorique" method="post"
        action="edit.php?id=<?= (int) $historique['id_historique'] ?>"
        novalidate class="crud-form">

    <div class="form-group">
      <label for="id_regime">Regime associe</label>
      <select id="id_regime" name="id_regime">
        <option value="">-- Choisir --</option>
        <?php foreach ($regimes as $r) : ?>
          <option value="<?= (int) $r['id_regime'] ?>"
            <?= (string) $historique['id_regime'] === (string) $r['id_regime'] ? 'selected' : '' ?>>
            #<?= (int) $r['id_regime'] ?> - <?= htmlspecialchars($r['type_regime']) ?> (<?= htmlspecialchars($r['date_debut']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
      <span class="field-error" data-error-for="id_regime"></span>
    </div>

    <div class="form-group">
      <label for="recommandation">Recommandation</label>
      <textarea id="recommandation" name="recommandation" rows="4"><?= htmlspecialchars($historique['recommandation']) ?></textarea>
      <span class="field-error" data-error-for="recommandation"></span>
    </div>

    <div class="form-group">
      <label for="date">Date (YYYY-MM-DD)</label>
      <input type="text" id="date" name="date" value="<?= htmlspecialchars($historique['date']) ?>">
      <span class="field-error" data-error-for="date"></span>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">Enregistrer les modifications</button>
      <a href="list.php" class="btn-secondary">Annuler</a>
    </div>
  </form>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
