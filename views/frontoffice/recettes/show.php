<?php
/**
 * View : /views/frontoffice/recettes/show.php
 * Variable : $recette (avec 'ingredients')
 */
$baseUrl = '/' . basename(BASE_PATH);
?>

<main style="max-width:1100px;margin:0 auto;padding:120px 24px 40px;">

  <a href="recettes.php"
     style="display:inline-flex;align-items:center;gap:6px;color:var(--primary-dark);
            font-weight:700;text-decoration:none;margin-bottom:20px;">
    ← Toutes les recettes
  </a>

  <article class="detail-hero">
    <div class="detail-hero-image"
         style="<?= $recette['image']?'background-image:url(\''.e($recette['image']).'\')':'' ?>">
      <?php if (empty($recette['image'])): ?>
        <div style="display:grid;place-items:center;height:100%;font-size:80px;">🍽️</div>
      <?php endif; ?>
    </div>
    <div class="detail-hero-body">
      <h1><?= e($recette['nom']) ?></h1>
      <div class="recipe-meta">
        <span class="chip chip-time">⏱ <?= e($recette['duree']) ?> minutes</span>
        <?php $cls = $recette['niveau']==='facile'?'chip-easy':($recette['niveau']==='moyen'?'chip-medium':'chip-hard'); ?>
        <span class="chip <?= $cls ?>">📊 <?= e(ucfirst($recette['niveau'])) ?></span>
        <span class="chip chip-ingredients">🥕 <?= count($recette['ingredients']) ?> ingrédients</span>
      </div>
      <div class="description-block">
        <?= nl2br(e($recette['description'])) ?>
      </div>
    </div>
  </article>

  <section style="background:var(--surface-strong);border:1px solid var(--border);
                  border-radius:var(--radius-lg);padding:30px;box-shadow:var(--shadow);">
    <h2 style="margin:0 0 18px;font-family:'Outfit',sans-serif;font-weight:800;
               font-size:1.6rem;color:var(--text);">
      Liste des ingrédients
    </h2>

    <?php if (empty($recette['ingredients'])): ?>
      <p style="color:var(--muted)">Aucun ingrédient renseigné pour cette recette.</p>
    <?php else: ?>
      <ul class="ingredient-list">
        <?php foreach ($recette['ingredients'] as $i): ?>
          <li>
            <span class="ing-name">
              <a href="ingredient.php?id=<?= (int)$i['id'] ?>"
                 style="color:inherit;text-decoration:none;border-bottom:1px dashed rgba(31,164,99,.4);">
                <?= e($i['nom']) ?>
              </a>
            </span>
            <span class="ing-qty"><?= e(rtrim(rtrim((string)$i['quantite'], '0'), '.')) ?> <?= e($i['unite']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</main>
