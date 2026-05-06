<?php
/**
 * =====================================================================
 *  NutriSmart - View/suivis/add.php
 * =====================================================================
 */
require_once __DIR__ . '/../../Controller/SuiviC.php';
require_once __DIR__ . '/../../Controller/RegimeC.php';
require_once __DIR__ . '/../../Model/Suivi.php';

$regimes = (new RegimeC())->listerRegimes();

$erreurs = [];
$old = [
    'id_regime'           => '',
    'date'                => '',
    'poids'               => '',
    'calories_consommees' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old['id_regime']           = trim($_POST['id_regime']           ?? '');
    $old['date']                = trim($_POST['date']                ?? '');
    $old['poids']               = trim($_POST['poids']               ?? '');
    $old['calories_consommees'] = trim($_POST['calories_consommees'] ?? '');

    if (!isset($_POST['id_regime']) || empty($old['id_regime']) || !is_numeric($old['id_regime'])) {
        $erreurs[] = 'Vous devez choisir un regime.';
    }
    if (!isset($_POST['date']) || empty($old['date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $old['date'])) {
        $erreurs[] = 'La date est obligatoire (format YYYY-MM-DD).';
    }
    if (!isset($_POST['poids']) || empty($old['poids']) || !is_numeric($old['poids']) || $old['poids'] <= 0) {
        $erreurs[] = 'Le poids doit etre un nombre positif.';
    }
    if (!isset($_POST['calories_consommees']) || empty($old['calories_consommees']) || !is_numeric($old['calories_consommees']) || $old['calories_consommees'] < 0) {
        $erreurs[] = 'Les calories consommees doivent etre un nombre positif ou zero.';
    }

    if (empty($erreurs)) {
        $s = new Suivi(
            (int)   $old['id_regime'],
            $old['date'],
            (float) $old['poids'],
            (int)   $old['calories_consommees']
        );
        (new SuiviC())->ajouterSuivi($s);
        header('Location: list.php');
        exit;
    }
}

$activeMenu = 'suivis';
$pageTitle  = 'NutriSmart - Ajouter un suivi';
include __DIR__ . '/../templates/header.php';
?>

<header class="page-header">
  <p class="badge">Creation</p>
  <h1>Ajouter un suivi</h1>
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

    <form id="formSuivi" method="post" action="add.php" novalidate class="crud-form">
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
        <label for="date">Date (YYYY-MM-DD)</label>
        <input type="text" id="date" name="date" value="<?= htmlspecialchars($old['date']) ?>">
        <span class="field-error" data-error-for="date"></span>
      </div>

      <div class="form-group">
        <label for="poids">Poids (kg)</label>
        <input type="text" id="poids" name="poids" value="<?= htmlspecialchars($old['poids']) ?>">
        <span class="field-error" data-error-for="poids"></span>
      </div>

      <div class="form-group">
        <label for="calories_consommees">Calories consommees (kcal)</label>
        <input type="text" id="calories_consommees" name="calories_consommees" value="<?= htmlspecialchars($old['calories_consommees']) ?>">
        <span class="field-error" data-error-for="calories_consommees"></span>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary">Enregistrer</button>
        <a href="list.php" class="btn-secondary">Annuler</a>
      </div>
    </form>

  <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
