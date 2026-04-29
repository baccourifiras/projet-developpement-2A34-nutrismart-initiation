<?php
/**
 * =====================================================================
 *  NutriSmart - View/regimes/add.php
 *  Formulaire d'ajout d'un regime.
 *  Validation : JS externe + PHP (isset/empty).
 *  Aucun attribut HTML5 (required, pattern, min, ...).
 * =====================================================================
 */
require_once __DIR__ . '/../../Controller/RegimeC.php';
require_once __DIR__ . '/../../Model/Regime.php';

$erreurs = [];
$old = [
    'type_regime'    => 'equilibre',
    'calories_cible' => '',
    'date_debut'     => '',
    'poids_initial'  => '',
    'duree'          => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old['type_regime']    = trim($_POST['type_regime']    ?? '');
    $old['calories_cible'] = trim($_POST['calories_cible'] ?? '');
    $old['date_debut']     = trim($_POST['date_debut']     ?? '');
    $old['poids_initial']  = trim($_POST['poids_initial']  ?? '');
    $old['duree']          = trim($_POST['duree']          ?? '');

    $typesValides = ['cut', 'bulk', 'equilibre'];

    if (!isset($_POST['type_regime']) || empty($old['type_regime']) || !in_array($old['type_regime'], $typesValides, true)) {
        $erreurs[] = 'Le type de regime doit etre : cut, bulk ou equilibre.';
    }
    if (!isset($_POST['calories_cible']) || empty($old['calories_cible']) || !is_numeric($old['calories_cible']) || $old['calories_cible'] <= 0) {
        $erreurs[] = 'Les calories cible doivent etre un nombre positif.';
    }
    if (!isset($_POST['date_debut']) || empty($old['date_debut']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $old['date_debut'])) {
        $erreurs[] = 'La date de debut est obligatoire (format YYYY-MM-DD).';
    }
    if (!isset($_POST['poids_initial']) || empty($old['poids_initial']) || !is_numeric($old['poids_initial']) || $old['poids_initial'] <= 0) {
        $erreurs[] = 'Le poids initial doit etre un nombre positif.';
    }
    if (!isset($_POST['duree']) || empty($old['duree']) || !is_numeric($old['duree']) || $old['duree'] <= 0) {
        $erreurs[] = 'La duree doit etre un entier positif.';
    }

    if (empty($erreurs)) {
        $r = new Regime(
            $old['type_regime'],
            (int)   $old['calories_cible'],
            $old['date_debut'],
            (float) $old['poids_initial'],
            (int)   $old['duree']
        );
        (new RegimeC())->ajouterRegime($r);
        header('Location: list.php');
        exit;
    }
}

$activeMenu = 'regimes';
$pageTitle  = 'NutriSmart - Ajouter un regime';
include __DIR__ . '/../templates/header.php';
?>

<header class="page-header">
  <p class="badge">Creation</p>
  <h1>Ajouter un regime</h1>
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

  <form id="formRegime" method="post" action="add.php" novalidate class="crud-form">
    <div class="form-group">
      <label for="type_regime">Type de regime</label>
      <select id="type_regime" name="type_regime">
        <option value="">-- Choisir --</option>
        <?php foreach (['cut', 'bulk', 'equilibre'] as $t) : ?>
          <option value="<?= $t ?>" <?= $old['type_regime'] === $t ? 'selected' : '' ?>><?= $t ?></option>
        <?php endforeach; ?>
      </select>
      <span class="field-error" data-error-for="type_regime"></span>
    </div>

    <div class="form-group">
      <label for="calories_cible">Calories cible (kcal)</label>
      <input type="text" id="calories_cible" name="calories_cible" value="<?= htmlspecialchars($old['calories_cible']) ?>">
      <span class="field-error" data-error-for="calories_cible"></span>
    </div>

    <div class="form-group">
      <label for="date_debut">Date de debut (YYYY-MM-DD)</label>
      <input type="text" id="date_debut" name="date_debut" value="<?= htmlspecialchars($old['date_debut']) ?>">
      <span class="field-error" data-error-for="date_debut"></span>
    </div>

    <div class="form-group">
      <label for="poids_initial">Poids initial (kg)</label>
      <input type="text" id="poids_initial" name="poids_initial" value="<?= htmlspecialchars($old['poids_initial']) ?>">
      <span class="field-error" data-error-for="poids_initial"></span>
    </div>

    <div class="form-group">
      <label for="duree">Duree (jours)</label>
      <input type="text" id="duree" name="duree" value="<?= htmlspecialchars($old['duree']) ?>">
      <span class="field-error" data-error-for="duree"></span>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">Enregistrer</button>
      <a href="list.php" class="btn-secondary">Annuler</a>
    </div>
  </form>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
