<?php
/**
 * =====================================================================
 *  NutriSmart - View/suivis/edit.php
 * =====================================================================
 */
require_once __DIR__ . '/../../Controller/SuiviC.php';
require_once __DIR__ . '/../../Controller/RegimeC.php';
require_once __DIR__ . '/../../Model/Suivi.php';

$ctrl    = new SuiviC();
$regimes = (new RegimeC())->listerRegimes();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: list.php'); exit; }

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idR   = trim($_POST['id_regime']           ?? '');
    $date  = trim($_POST['date']                ?? '');
    $poids = trim($_POST['poids']               ?? '');
    $cal   = trim($_POST['calories_consommees'] ?? '');

    if (!isset($_POST['id_regime']) || empty($idR) || !is_numeric($idR)) {
        $erreurs[] = 'Vous devez choisir un regime.';
    }
    if (!isset($_POST['date']) || empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $erreurs[] = 'La date est obligatoire (format YYYY-MM-DD).';
    }
    if (!isset($_POST['poids']) || empty($poids) || !is_numeric($poids) || $poids <= 0) {
        $erreurs[] = 'Le poids doit etre un nombre positif.';
    }
    if (!isset($_POST['calories_consommees']) || empty($cal) || !is_numeric($cal) || $cal < 0) {
        $erreurs[] = 'Les calories consommees doivent etre un nombre positif ou zero.';
    }

    if (empty($erreurs)) {
        $s = new Suivi((int) $idR, $date, (float) $poids, (int) $cal, $id);
        $ctrl->modifierSuivi($s);
        header('Location: list.php');
        exit;
    }

    $suivi = [
        'id_suivi'            => $id,
        'id_regime'           => $idR,
        'date'                => $date,
        'poids'               => $poids,
        'calories_consommees' => $cal,
    ];
} else {
    $suivi = $ctrl->getSuiviById($id);
    if (!$suivi) { header('Location: list.php'); exit; }
}

$activeMenu = 'suivis';
$pageTitle  = 'NutriSmart - Modifier le suivi';
include __DIR__ . '/../templates/header.php';
?>

<header class="page-header">
  <p class="badge">Edition</p>
  <h1>Modifier le suivi #<?= (int) $suivi['id_suivi'] ?></h1>
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

  <form id="formSuivi" method="post"
        action="edit.php?id=<?= (int) $suivi['id_suivi'] ?>"
        novalidate class="crud-form">

    <div class="form-group">
      <label for="id_regime">Regime associe</label>
      <select id="id_regime" name="id_regime">
        <option value="">-- Choisir --</option>
        <?php foreach ($regimes as $r) : ?>
          <option value="<?= (int) $r['id_regime'] ?>"
            <?= (string) $suivi['id_regime'] === (string) $r['id_regime'] ? 'selected' : '' ?>>
            #<?= (int) $r['id_regime'] ?> - <?= htmlspecialchars($r['type_regime']) ?> (<?= htmlspecialchars($r['date_debut']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
      <span class="field-error" data-error-for="id_regime"></span>
    </div>

    <div class="form-group">
      <label for="date">Date (YYYY-MM-DD)</label>
      <input type="text" id="date" name="date" value="<?= htmlspecialchars($suivi['date']) ?>">
      <span class="field-error" data-error-for="date"></span>
    </div>

    <div class="form-group">
      <label for="poids">Poids (kg)</label>
      <input type="text" id="poids" name="poids" value="<?= htmlspecialchars($suivi['poids']) ?>">
      <span class="field-error" data-error-for="poids"></span>
    </div>

    <div class="form-group">
      <label for="calories_consommees">Calories consommees (kcal)</label>
      <input type="text" id="calories_consommees" name="calories_consommees" value="<?= htmlspecialchars($suivi['calories_consommees']) ?>">
      <span class="field-error" data-error-for="calories_consommees"></span>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">Enregistrer les modifications</button>
      <a href="list.php" class="btn-secondary">Annuler</a>
    </div>
  </form>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
