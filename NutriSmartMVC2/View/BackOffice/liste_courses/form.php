<?php
$isUpdate = !empty($listeData['id']);
$retour   = $retourUrl ?? 'index.php?page=liste_courses';
?>

<div class="form-page-header">
  <h1><?= $isUpdate ? 'Modifier la Liste' : 'Nouvelle Liste de Courses' ?></h1>
  <a href="<?= $retour ?>" class="btn btn-gris">Retour</a>
</div>

<div class="form-box">
  <form method="POST" id="fListe">

    <div class="form-group">
      <label for="articles_a_acheter">Articles a acheter <span style="color:var(--danger)">*</span></label>
      <input type="text" id="articles_a_acheter" name="articles_a_acheter"
             value="<?= htmlspecialchars($listeData['articles_a_acheter'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['articles_a_acheter']) ? 'champ-err' : '' ?>"
             placeholder="Ex : Tomates, lait, pain, fromage"/>
      <span class="err-msg" id="e-articles_a_acheter"><?= $errors['articles_a_acheter'] ?? '' ?></span>
    </div>

    <div class="form-group">
      <label for="budget">Budget <span style="color:var(--danger)">*</span> <small>en TND</small></label>
      <input type="text" id="budget" name="budget"
             value="<?= htmlspecialchars($listeData['budget'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['budget']) ? 'champ-err' : '' ?>"
             placeholder="Ex : 120"/>
      <span class="err-msg" id="e-budget"><?= $errors['budget'] ?? '' ?></span>
    </div>

    <div class="form-group">
      <label for="date_creation">Date de creation <span style="color:var(--danger)">*</span> <small>(JJ/MM/AAAA)</small></label>
      <input type="text" id="date_creation" name="date_creation"
             value="<?= htmlspecialchars($listeData['date_creation_fmt'] ?? date('d/m/Y'), ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['date_creation']) ? 'champ-err' : '' ?>"
             placeholder="Ex : 15/04/2026"/>
      <span class="err-msg" id="e-date_creation"><?= $errors['date_creation'] ?? '' ?></span>
    </div>

    <div class="form-group">
      <label for="stock_id">Stock associe <small>(optionnel)</small></label>
      <select id="stock_id" name="stock_id">
        <option value="">Aucun stock associe</option>
        <?php foreach ($stocks as $s): ?>
          <option value="<?= $s['id'] ?>" <?= ($listeData['stock_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($s['produits'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($s['type'], ENT_QUOTES, 'UTF-8') ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-actions">
      <a href="<?= $retour ?>" class="btn btn-gris">Annuler</a>
      <button type="submit" class="btn btn-vert">
        <?= $isUpdate ? 'Enregistrer les modifications' : 'Creer la liste' ?>
      </button>
    </div>

  </form>
</div>
