<?php
/**
 * View : /views/backoffice/ingredients/form.php
 * Variables : $ingredient, $errors, $categories, $unitesList
 */
$baseUrl = '/' . basename(BASE_PATH);
$isEdit  = (bool)$ingredient;
$action  = $isEdit ? 'ingredient_update.php?id=' . (int)$ingredient['id'] : 'ingredient_store.php';
?>

<div class="page-head">
  <div>
    <p class="breadcrumb">Ingrédients · <?= $isEdit ? 'Modifier' : 'Nouveau' ?></p>
    <h2><?= $isEdit ? 'Modifier l\'ingrédient' : 'Ajouter un ingrédient' ?></h2>
  </div>
  <a href="<?= e($baseUrl) ?>/backoffice/ingredients.php" class="btn-icon btn-view">← Retour</a>
</div>

<section class="panel">
  <form method="post" action="<?= e($action) ?>" data-validate>
    <?= csrf_field() ?>

    <div class="form-grid two-columns">
      <div>
        <label for="nom">Nom *</label>
        <input id="nom" name="nom" type="text"
               value="<?= e($isEdit ? $ingredient['nom'] : old('nom')) ?>"
               data-rule-required="true" data-rule-min-length="2" data-rule-max-length="120"
               placeholder="Ex : Tomate cerise">
        <?php if (!empty($errors['nom'])): ?><span class="field-error"><?= e($errors['nom']) ?></span><?php endif; ?>
      </div>

      <div>
        <label for="categorie">Catégorie *</label>
        <select id="categorie" name="categorie" data-rule-required="true">
          <?php foreach ($categories as $c):
            $sel = ($isEdit ? $ingredient['categorie'] : (old('categorie') ?: 'Autre')) === $c; ?>
            <option value="<?= e($c) ?>" <?= $sel?'selected':'' ?>><?= e($c) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['categorie'])): ?><span class="field-error"><?= e($errors['categorie']) ?></span><?php endif; ?>
      </div>

      <div>
        <label for="quantite_stock">Quantité en stock *</label>
        <input id="quantite_stock" name="quantite_stock" type="number" step="0.01" min="0"
               value="<?= e($isEdit ? $ingredient['quantite_stock'] : (old('quantite_stock', '0'))) ?>"
               data-rule-required="true" data-rule-numeric="true" data-rule-min="0">
        <?php if (!empty($errors['quantite_stock'])): ?><span class="field-error"><?= e($errors['quantite_stock']) ?></span><?php endif; ?>
      </div>

      <div>
        <label for="unite">Unité *</label>
        <select id="unite" name="unite" data-rule-required="true">
          <?php foreach ($unitesList as $u):
            $sel = ($isEdit ? $ingredient['unite'] : (old('unite') ?: 'g')) === $u; ?>
            <option value="<?= e($u) ?>" <?= $sel?'selected':'' ?>><?= e($u) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['unite'])): ?><span class="field-error"><?= e($errors['unite']) ?></span><?php endif; ?>
      </div>
    </div>

    <button type="submit" class="primary-btn">
      <?= $isEdit ? 'Enregistrer les modifications' : 'Ajouter l\'ingrédient' ?>
    </button>
  </form>
</section>
