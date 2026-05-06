<?php
/**
 * View : /views/frontoffice/ingredients/show.php
 * Variable : $ingredient (avec 'recettes')
 */
$baseUrl = '/' . basename(BASE_PATH);
?>

<main style="max-width:1100px;margin:0 auto;padding:120px 24px 40px;">

  <a href="ingredients.php"
     style="display:inline-flex;align-items:center;gap:6px;color:var(--primary-dark);
            font-weight:700;text-decoration:none;margin-bottom:20px;">
    ← Tous les ingrédients
  </a>

  <header style="background:var(--surface-strong);border:1px solid var(--border);
                 border-radius:var(--radius-lg);padding:36px;box-shadow:var(--shadow);
                 text-align:center;margin-bottom:30px;">
    <span class="chip chip-ingredients" style="font-size:13px;"><?= e($ingredient['categorie']) ?></span>
    <h1 style="margin:14px 0 8px;font-family:'Outfit',sans-serif;font-size:2.2rem;font-weight:900;color:var(--text);">
      <?= e($ingredient['nom']) ?>
    </h1>
    <p style="color:var(--muted);font-size:1rem;">
      Stock disponible :
      <strong style="color:var(--primary-dark)">
        <?= e(rtrim(rtrim((string)$ingredient['quantite_stock'],'0'),'.')) ?> <?= e($ingredient['unite']) ?>
      </strong>
    </p>
  </header>

  <section>
    <h2 style="font-family:'Outfit',sans-serif;font-weight:800;font-size:1.6rem;
               color:var(--text);margin:0 0 20px;">
      🍽️ Recettes utilisant cet ingrédient
      <span style="font-size:14px;color:var(--muted);font-weight:600;">
        (<?= count($ingredient['recettes']) ?>)
      </span>
    </h2>

    <?php if (empty($ingredient['recettes'])): ?>
      <div class="empty-state">
        <div class="icon">🍽️</div>
        <p>Cet ingrédient n'apparaît dans aucune recette pour le moment.</p>
      </div>
    <?php else: ?>
      <div class="recipe-grid">
        <?php foreach ($ingredient['recettes'] as $r): ?>
          <article class="recipe-card">
            <div class="recipe-card-image"
                 style="<?= $r['image']?'background-image:url(\''.e($r['image']).'\')':'' ?>">
              <?php if (empty($r['image'])): ?>
                <div style="display:grid;place-items:center;height:100%;font-size:50px;">🍽️</div>
              <?php endif; ?>
            </div>
            <div class="recipe-card-body">
              <h3><?= e($r['nom']) ?></h3>
              <div class="recipe-meta">
                <span class="chip chip-time">⏱ <?= e($r['duree']) ?> min</span>
                <?php $cls = $r['niveau']==='facile'?'chip-easy':($r['niveau']==='moyen'?'chip-medium':'chip-hard'); ?>
                <span class="chip <?= $cls ?>"><?= e(ucfirst($r['niveau'])) ?></span>
                <span class="chip chip-ingredients">
                  <?= e($r['quantite']) ?> <?= e($r['unite']) ?>
                </span>
              </div>
            </div>
            <div class="recipe-card-footer">
              <span></span>
              <a class="btn-link" href="recette.php?id=<?= (int)$r['id'] ?>">Voir →</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>
