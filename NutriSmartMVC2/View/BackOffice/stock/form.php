<?php
$isUpdate = !empty($stockData['id']);
$retour   = $retourUrl ?? 'index.php?page=stock';
?>

<div class="form-page-header">
  <h1><?= $isUpdate ? 'Modifier le Stock' : 'Ajouter un Stock' ?></h1>
  <a href="<?= $retour ?>" class="btn btn-gris">Retour</a>
</div>

<div class="form-box">
  <form method="POST" id="fStock">

    <div class="form-group">
      <label for="produits">Nom du produit <span style="color:var(--danger)">*</span></label>
      <input type="text" id="produits" name="produits"
             value="<?= htmlspecialchars($stockData['produits'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['produits']) ? 'champ-err' : '' ?>"
             placeholder="Ex : Tomates cerises, Lait demi-ecreme"/>
      <span class="err-msg" id="e-produits"><?= $errors['produits'] ?? '' ?></span>
    </div>

    <div class="form-group">
      <label for="type">Categorie <span style="color:var(--danger)">*</span></label>
      <input type="text" id="type" name="type"
             value="<?= htmlspecialchars($stockData['type'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['type']) ? 'champ-err' : '' ?>"
             placeholder="Ex : Legumes, Laitier, Viande"/>
      <span class="err-msg" id="e-type"><?= $errors['type'] ?? '' ?></span>
    </div>

    <div class="form-group">
      <label for="date_expiration">Date d'expiration <span style="color:var(--danger)">*</span> <small>(JJ/MM/AAAA)</small></label>
      <input type="text" id="date_expiration" name="date_expiration"
             value="<?= htmlspecialchars($stockData['date_expiration_fmt'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['date_expiration']) ? 'champ-err' : '' ?>"
             placeholder="Ex : 31/12/2026"/>
      <span class="err-msg" id="e-date_expiration"><?= $errors['date_expiration'] ?? '' ?></span>
    </div>

    <div class="form-group">
      <label for="seuil_minimum">Seuil minimum <span style="color:var(--danger)">*</span> <small>en unites</small></label>
      <input type="text" id="seuil_minimum" name="seuil_minimum"
             value="<?= htmlspecialchars($stockData['seuil_minimum'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['seuil_minimum']) ? 'champ-err' : '' ?>"
             placeholder="Ex : 5"/>
      <span class="err-msg" id="e-seuil_minimum"><?= $errors['seuil_minimum'] ?? '' ?></span>
    </div>

    <div class="form-actions">
      <a href="<?= $retour ?>" class="btn btn-gris">Annuler</a>
      <button type="submit" class="btn btn-vert">
        <?= $isUpdate ? 'Enregistrer les modifications' : 'Ajouter le stock' ?>
      </button>
    </div>

  </form>
</div>
