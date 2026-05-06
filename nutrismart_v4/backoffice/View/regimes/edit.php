<?php
/**
 * =====================================================================
 *  NutriSmart - View/regimes/edit.php
 * =====================================================================
 */
require_once __DIR__ . '/../../Controller/RegimeC.php';
require_once __DIR__ . '/../../Model/Regime.php';

$ctrl = new RegimeC();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header('Location: list.php'); exit; }

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type   = trim($_POST['type_regime']    ?? '');
    $cal    = trim($_POST['calories_cible'] ?? '');
    $date   = trim($_POST['date_debut']     ?? '');
    $poids  = trim($_POST['poids_initial']  ?? '');
    $duree  = trim($_POST['duree']          ?? '');

    $typesValides = ['cut', 'bulk', 'equilibre'];

    if (!isset($_POST['type_regime']) || empty($type) || !in_array($type, $typesValides, true)) {
        $erreurs[] = 'Le type de regime doit etre : cut, bulk ou equilibre.';
    }
    if (!isset($_POST['calories_cible']) || empty($cal) || !is_numeric($cal) || $cal <= 0) {
        $erreurs[] = 'Les calories cible doivent etre un nombre positif.';
    }
    if (!isset($_POST['date_debut']) || empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $erreurs[] = 'La date de debut est obligatoire (format YYYY-MM-DD).';
    }
    if (!isset($_POST['poids_initial']) || empty($poids) || !is_numeric($poids) || $poids <= 0) {
        $erreurs[] = 'Le poids initial doit etre un nombre positif.';
    }
    if (!isset($_POST['duree']) || empty($duree) || !is_numeric($duree) || $duree <= 0) {
        $erreurs[] = 'La duree doit etre un entier positif.';
    }

    if (empty($erreurs)) {
        $r = new Regime($type, (int) $cal, $date, (float) $poids, (int) $duree, $id);
        $ctrl->modifierRegime($r);
        header('Location: list.php');
        exit;
    }

    /* Conserve les saisies en cas d'erreur */
    $regime = [
        'id_regime'      => $id,
        'type_regime'    => $type,
        'calories_cible' => $cal,
        'date_debut'     => $date,
        'poids_initial'  => $poids,
        'duree'          => $duree,
    ];
} else {
    $regime = $ctrl->getRegimeById($id);
    if (!$regime) { header('Location: list.php'); exit; }
}

$activeMenu = 'regimes';
$pageTitle  = 'NutriSmart - Modifier le regime';
include __DIR__ . '/../templates/header.php';
?>

<header class="page-header">
  <p class="badge">Edition</p>
  <h1>Modifier le regime #<?= (int) $regime['id_regime'] ?></h1>
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

  <form id="formRegime" method="post"
        action="edit.php?id=<?= (int) $regime['id_regime'] ?>"
        novalidate class="crud-form">

    <div class="form-group">
      <label for="type_regime">Type de regime</label>
      <select id="type_regime" name="type_regime">
        <option value="">-- Choisir --</option>
        <?php foreach (['cut', 'bulk', 'equilibre'] as $t) : ?>
          <option value="<?= $t ?>" <?= $regime['type_regime'] === $t ? 'selected' : '' ?>><?= $t ?></option>
        <?php endforeach; ?>
      </select>
      <span class="field-error" data-error-for="type_regime"></span>
    </div>

    <div class="form-group">
      <label for="calories_cible">Calories cible (kcal)</label>
      <input type="text" id="calories_cible" name="calories_cible" value="<?= htmlspecialchars($regime['calories_cible']) ?>">
      <span class="field-error" data-error-for="calories_cible"></span>
    </div>

    <div class="form-group">
      <label for="date_debut">Date de debut (YYYY-MM-DD)</label>
      <input type="text" id="date_debut" name="date_debut" value="<?= htmlspecialchars($regime['date_debut']) ?>">
      <span class="field-error" data-error-for="date_debut"></span>
    </div>

    <div class="form-group">
      <label for="poids_initial">Poids initial (kg)</label>
      <input type="text" id="poids_initial" name="poids_initial" value="<?= htmlspecialchars($regime['poids_initial']) ?>">
      <span class="field-error" data-error-for="poids_initial"></span>
    </div>

    <div class="form-group">
      <label for="duree">Duree (jours)</label>
      <input type="text" id="duree" name="duree" value="<?= htmlspecialchars($regime['duree']) ?>">
      <span class="field-error" data-error-for="duree"></span>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">Enregistrer les modifications</button>
      <a href="list.php" class="btn-secondary">Annuler</a>
    </div>
  </form>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
