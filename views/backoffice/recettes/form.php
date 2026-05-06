<?php
/**
 * View : /views/backoffice/recettes/form.php
 * Variables : $recette (null si création), $ingredients, $selected, $errors, $unitesList
 */
$baseUrl = '/' . basename(BASE_PATH);
$isEdit  = (bool)$recette;
$action  = $isEdit ? 'recette_update.php?id=' . (int)$recette['id'] : 'recette_store.php';
?>

<div class="page-head">
  <div>
    <p class="breadcrumb">Recettes · <?= $isEdit ? 'Modifier' : 'Nouvelle' ?></p>
    <h2><?= $isEdit ? 'Modifier la recette' : 'Ajouter une recette' ?></h2>
  </div>
  <a href="<?= e($baseUrl) ?>/backoffice/recettes.php" class="btn-icon btn-view">← Retour</a>
</div>

<section class="panel">
  <form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" data-validate>
    <?= csrf_field() ?>

    <div class="form-grid two-columns">
      <div>
        <label for="nom">Nom de la recette *</label>
        <input id="nom" name="nom" type="text"
               value="<?= e($isEdit ? $recette['nom'] : old('nom')) ?>"
               data-rule-required="true" data-rule-min-length="2" data-rule-max-length="150"
               placeholder="Ex : Soupe de potiron">
        <?php if (!empty($errors['nom'])): ?><span class="field-error"><?= e($errors['nom']) ?></span><?php endif; ?>
      </div>

      <div>
        <label for="duree">Durée (minutes) *</label>
        <input id="duree" name="duree" type="number" min="1"
               value="<?= e($isEdit ? $recette['duree'] : old('duree')) ?>"
               data-rule-required="true" data-rule-numeric="true" data-rule-min="1"
               placeholder="30">
        <?php if (!empty($errors['duree'])): ?><span class="field-error"><?= e($errors['duree']) ?></span><?php endif; ?>
      </div>

      <div>
        <label for="niveau">Niveau de difficulté *</label>
        <select id="niveau" name="niveau" data-rule-required="true">
          <option value="">— Choisir —</option>
          <?php foreach (Recette::NIVEAUX as $n):
              $sel = ($isEdit ? $recette['niveau'] : old('niveau')) === $n; ?>
            <option value="<?= e($n) ?>" <?= $sel?'selected':'' ?>><?= e(ucfirst($n)) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['niveau'])): ?><span class="field-error"><?= e($errors['niveau']) ?></span><?php endif; ?>
      </div>

      <div>
        <label for="image">Image (jpg, png, webp, gif – max 4 Mo)</label>
        <input id="image" name="image" type="file" accept="image/*">
        <?php if ($isEdit && !empty($recette['image'])): ?>
          <p style="margin-top:8px;font-size:12px;color:var(--muted)">Image actuelle :
            <a href="<?= e($recette['image']) ?>" target="_blank">voir</a>
          </p>
        <?php endif; ?>
        <?php if (!empty($errors['image'])): ?><span class="field-error"><?= e($errors['image']) ?></span><?php endif; ?>
      </div>

      <div class="full-width">
        <label for="description">Description *</label>
        <textarea id="description" name="description" rows="5"
                  data-rule-required="true" data-rule-min-length="10"
                  placeholder="Décrivez votre recette en quelques lignes..."><?= e($isEdit ? $recette['description'] : old('description')) ?></textarea>
        <?php if (!empty($errors['description'])): ?><span class="field-error"><?= e($errors['description']) ?></span><?php endif; ?>
      </div>

      <!-- Bloc d'association ingrédients (M:N) -->
      <div class="full-width">
        <label>Ingrédients de la recette *</label>
        <div class="assoc-builder">
          <?php foreach ($selected as $sel): ?>
            <div class="assoc-row">
              <select name="ing_id[]" class="ing-select">
                <option value="">— Choisir un ingrédient —</option>
                <?php foreach ($ingredients as $opt): ?>
                  <option value="<?= (int)$opt['id'] ?>"
                          data-unite="<?= e($opt['unite']) ?>"
                          <?= $sel['id_ingredient']==(int)$opt['id']?'selected':'' ?>>
                    <?= e($opt['nom']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <input type="number" name="ing_qty[]" step="0.01" min="0" placeholder="Qté"
                     value="<?= e($sel['quantite']) ?>">
              <select name="ing_uni[]" class="ing-unite">
                <?php foreach ($unitesList as $u): ?>
                  <option value="<?= e($u) ?>" <?= $sel['unite']===$u?'selected':'' ?>><?= e($u) ?></option>
                <?php endforeach; ?>
              </select>
              <button type="button" class="remove-row">✕</button>
            </div>
          <?php endforeach; ?>

          <button type="button" class="add-row">+ Ajouter un ingrédient</button>
        </div>

        <!-- Template HTML5 pour cloner une nouvelle ligne -->
        <template id="assoc-row-template">
          <div class="assoc-row">
            <select name="ing_id[]" class="ing-select">
              <option value="">— Choisir un ingrédient —</option>
              <?php foreach ($ingredients as $opt): ?>
                <option value="<?= (int)$opt['id'] ?>" data-unite="<?= e($opt['unite']) ?>"><?= e($opt['nom']) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="number" name="ing_qty[]" step="0.01" min="0" placeholder="Qté">
            <select name="ing_uni[]" class="ing-unite">
              <?php foreach ($unitesList as $u): ?>
                <option value="<?= e($u) ?>"><?= e($u) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="remove-row">✕</button>
          </div>
        </template>
      </div>
    </div>

    <button type="submit" class="primary-btn">
      <?= $isEdit ? 'Enregistrer les modifications' : 'Ajouter la recette' ?>
    </button>
  </form>
</section>
