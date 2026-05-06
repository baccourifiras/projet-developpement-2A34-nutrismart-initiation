<?php
/**
 * View : /views/backoffice/recettes/show.php
 * Variable : $recette (avec clé 'ingredients')
 */
$baseUrl = '/' . basename(BASE_PATH);
?>

<div class="page-head">
  <div>
    <p class="breadcrumb">Recettes · Détail</p>
    <h2><?= e($recette['nom']) ?></h2>
  </div>
  <div style="display:flex;gap:8px;">
    <a href="<?= e($baseUrl) ?>/backoffice/recettes.php" class="btn-icon btn-view">← Liste</a>
    <a href="<?= e($baseUrl) ?>/backoffice/recette_form.php?id=<?= (int)$recette['id'] ?>" class="btn-icon btn-edit">✏ Modifier</a>
  </div>
</div>

<section class="detail-card">
  <div class="img-wrap" style="<?= $recette['image']?'background-image:url(\''.e($recette['image']).'\')':'' ?>">
    <?php if (empty($recette['image'])): ?>
      <div style="display:grid;place-items:center;height:200px;font-size:50px;">🍽️</div>
    <?php endif; ?>
  </div>
  <div>
    <div class="detail-meta">
      <span class="chip">⏱ <?= e($recette['duree']) ?> minutes</span>
      <span class="chip">📊 <?= e(ucfirst($recette['niveau'])) ?></span>
      <span class="chip">📅 <?= e(date('d/m/Y', strtotime($recette['date_creation']))) ?></span>
      <span class="chip">🥕 <?= count($recette['ingredients']) ?> ingrédients</span>
    </div>
    <p style="line-height:1.7;color:var(--muted);"><?= nl2br(e($recette['description'])) ?></p>

    <h3 style="margin-top:24px;font-family:'Outfit',sans-serif;">Ingrédients utilisés</h3>
    <?php if (empty($recette['ingredients'])): ?>
      <p style="color:var(--muted)">Aucun ingrédient lié pour le moment.</p>
    <?php else: ?>
      <div class="table-wrapper" style="margin-top:10px;">
        <table class="table">
          <thead>
            <tr><th>Ingrédient</th><th>Catégorie</th><th>Quantité</th><th>Unité</th></tr>
          </thead>
          <tbody>
          <?php foreach ($recette['ingredients'] as $i): ?>
            <tr>
              <td><strong><?= e($i['nom']) ?></strong></td>
              <td><?= e($i['categorie']) ?></td>
              <td><?= e($i['quantite']) ?></td>
              <td><?= e($i['unite']) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>
