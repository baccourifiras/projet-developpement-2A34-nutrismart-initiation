<?php
/**
 * View : /views/backoffice/ingredients/show.php
 * Variable : $ingredient (avec clé 'recettes')
 */
$baseUrl = '/' . basename(BASE_PATH);
?>

<div class="page-head">
  <div>
    <p class="breadcrumb">Ingrédients · Détail</p>
    <h2><?= e($ingredient['nom']) ?></h2>
  </div>
  <div style="display:flex;gap:8px;">
    <a href="<?= e($baseUrl) ?>/backoffice/ingredients.php" class="btn-icon btn-view">← Liste</a>
    <a href="<?= e($baseUrl) ?>/backoffice/ingredient_form.php?id=<?= (int)$ingredient['id'] ?>" class="btn-icon btn-edit">✏ Modifier</a>
  </div>
</div>

<section class="panel">
  <div class="detail-meta">
    <span class="chip">🏷️ <?= e($ingredient['categorie']) ?></span>
    <span class="chip">📦 Stock : <?= e($ingredient['quantite_stock']) ?> <?= e($ingredient['unite']) ?></span>
    <span class="chip">📅 <?= e(date('d/m/Y', strtotime($ingredient['date_ajout']))) ?></span>
    <span class="chip">🍽️ <?= count($ingredient['recettes']) ?> recettes</span>
  </div>

  <h3 style="margin-top:18px;font-family:'Outfit',sans-serif">Recettes utilisant cet ingrédient</h3>

  <?php if (empty($ingredient['recettes'])): ?>
    <div class="empty-state" style="margin-top:14px">
      <div class="icon">🍽️</div>
      <p>Cet ingrédient n'est utilisé dans aucune recette pour le moment.</p>
    </div>
  <?php else: ?>
    <div class="table-wrapper" style="margin-top:14px">
      <table class="table">
        <thead><tr><th>Recette</th><th>Durée</th><th>Niveau</th><th>Quantité utilisée</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($ingredient['recettes'] as $r): ?>
          <tr>
            <td><strong><?= e($r['nom']) ?></strong></td>
            <td><?= e($r['duree']) ?> min</td>
            <td>
              <?php $cls = $r['niveau']==='facile'?'chip-easy':($r['niveau']==='moyen'?'chip-medium':'chip-hard'); ?>
              <span class="chip <?= $cls ?>"><?= e(ucfirst($r['niveau'])) ?></span>
            </td>
            <td><?= e($r['quantite']) ?> <?= e($r['unite']) ?></td>
            <td><a class="btn-icon btn-view" href="recette_show.php?id=<?= (int)$r['id'] ?>">👁 Voir</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>
